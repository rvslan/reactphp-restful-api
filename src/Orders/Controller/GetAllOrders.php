<?php

namespace App\Orders\Controller;

use App\Orders\Controller\Output\Request;
use App\Orders\Order;
use App\Orders\Storage;
use Psr\Http\Message\ServerRequestInterface;
use App\Orders\Controller\Output\Order as OutputOrder;

final class GetAllOrders
{
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->storage->getAll()
            ->then(function (array $orders) {
                $response = [
                    'orders' => $this->mapOrders(...$orders),
                    'count' => count($orders),
                ];

                return response($response);
            });
    }

    private function mapOrders(Order ...$orders): array
    {
        return array_map(function (Order $order) {
            return OutputOrder::fromEntity($order, Request::detailedOrder($order->id));
        }, $orders);
    }
}
