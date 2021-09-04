<?php

namespace App\Core;

use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use FastRoute\Dispatcher;

final class Router
{
    private $dispatcher;

    public function __construct(RouteCollector $routes) {
        $this->dispatcher = new GroupCountBased($routes->getData());
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return response(['message' => 'Not found'], 404);

            case Dispatcher::METHOD_NOT_ALLOWED:
                return response(['message' => 'Method not allowed'], 405);

            case Dispatcher::FOUND:
                $params = array_values($routeInfo[2] ?? []);

                return $routeInfo[1]($request, ...$params);
        }

        throw new LogicException('Something went wrong with routing');
    }
}
