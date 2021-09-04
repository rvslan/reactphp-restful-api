<?php

use App\Authentication\Authenticator;
use App\Authentication\Guard;
use App\Authentication\SignInController;
use App\Authentication\SignUpController;
use App\Core\ErrorHandler;
use App\Core\JsonRequestDecoder;
use App\Orders\Controller\CreateOrder\Controller as CreateOrder;
use App\Orders\Controller\GetAllOrders;
use App\Orders\Controller\GetOrderById;
use App\Products\Controller\CreateProduct;
use App\Orders\Controller\DeleteOrder;
use App\Products\Controller\DeleteProduct;
use App\Products\Controller\GetAllProducts;
use App\Products\Controller\GetProductById;
use App\Products\Controller\UpdateProduct;
use App\Core\Router;
use App\Core\Uploader;
use Dotenv\Dotenv;
use FastRoute\RouteCollector;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteParser\Std;
use React\Http\Server as HttpServer;
use React\MySQL\Factory as MySQLFactory;
use App\StaticFiles\Controller as StaticFilesController;
use App\Products\Storage as ProductsStorage;
use App\Orders\Storage as OrdersStorage;
use App\Authentication\Storage as AuthenticationStorage;
use App\StaticFiles\Webroot;
use React\EventLoop\Loop;
use React\Filesystem\Filesystem;

require 'vendor/autoload.php';

$env = Dotenv::createMutable(__DIR__);
$env->load();

$loop = Loop::get();

// Mysql init
$mysql = new MySQLFactory($loop);
$uri = "{$_ENV['DB_USER']}:{$_ENV['DB_PASS']}@{$_ENV['DB_HOST']}/{$_ENV['DB_NAME']}";
$jwtKey = $_ENV['JWT_KEY'];
$connection = $mysql->createLazyConnection($uri);

$filesystem = Filesystem::create($loop);
$uploader = new Uploader($filesystem, __DIR__);

$products = new ProductsStorage($connection);
$orders = new OrdersStorage($connection);

$guard = new Guard($jwtKey);
$routes = new RouteCollector(new Std, new GroupCountBased());

$routes->get('/uploads/{file:.*\.\w+}', new StaticFilesController(new Webroot($filesystem, __DIR__)));

$routes->get('/products', new GetAllProducts($products));
$routes->post('/products', $guard->protect(new CreateProduct($products, $uploader)));
$routes->get('/products/{id:\d+}', new GetProductById($products));
$routes->put('/products/{id:\d+}', $guard->protect(new UpdateProduct($products)));
$routes->delete('/products/{id:\d+}', $guard->protect(new DeleteProduct($products)));

$routes->get('/orders', $guard->protect(new GetAllOrders($orders)));
$routes->post('/orders', $guard->protect(new CreateOrder($orders, $products)));
$routes->get('/orders/{id:\d+}', $guard->protect(new GetOrderById($orders)));
$routes->delete('/orders/{id:\d+}', $guard->protect(new DeleteOrder($orders)));

$users = new AuthenticationStorage($connection);
$authenticator = new Authenticator($users, $jwtKey);
$routes->post('/auth/signup', new SignUpController($users));
$routes->post('/auth/signin', new SignInController($authenticator));

$server = new HttpServer($loop, new ErrorHandler(), new JsonRequestDecoder(), new Router($routes));

$socket = new React\Socket\SocketServer('127.0.0.1:8000', [], $loop);

$server->listen($socket);

echo 'listening on ' . str_replace('tcp', 'http', $socket->getAddress()) . PHP_EOL;

$loop->run();
