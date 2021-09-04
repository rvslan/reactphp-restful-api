<?php

namespace App\Orders\Controller\CreateOrder;

use App\Orders\Order;
use App\Orders\Storage as OrdersStorage;
use App\Products\Storage as ProductsStorage;
use Psr\Http\Message\ServerRequestInterface;
use App\Orders\Controller\Output\Order as OutputOrder;
use App\Orders\Controller\Output\Request;
use App\Products\Product;
use App\Products\ProductNotFound;
use Exception;

final class Controller
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * Controller constructor.
     */
    public function __construct(OrdersStorage $ordersStorage, ProductsStorage $productsStorage)
    {
        $this->ordersStorage = $ordersStorage;
        $this->productsStorage = $productsStorage;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $input = new Input($request);
        $input->validate();

        return $this->productsStorage
            ->getById($input->productId())
            ->then(function (Product $product) use ($input) {
                return $this->ordersStorage
                    ->create($product->id, $input->quantity());
            })
            ->then(
                function (Order $order) {
                    $response = [
                        'order' =>  OutputOrder::fromEntity($order, Request::listOrders())
                    ];

                    return response($response, 201);
                }
            )
            ->otherwise(function (ProductNotFound $error) {
                return response(['message' => $error->getMessage()], 404);
            })
            ->otherwise(function (Exception $error) {
                return response(['message' => $error->getMessage()], 500);
            });
    }
}
