<?php

namespace App\Orders\Controller;

use App\Orders\Controller\Output\Request;
use App\Orders\Controller\Output\Order as OrderOutput;
use App\Orders\OrderNotFound;
use App\Orders\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Orders\Order;

final class GetOrderById
{
    private $storage;

    public function __construct(Storage $storage) {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        return $this->storage
            ->getById((int)$id)
            ->then(function(Order $order) {
                $response = [
                    'order' => OrderOutput::fromEntity(
                        $order, Request::deleteOrder($order->id)
                    ),
                    'request' => Request::listOrders(),
                ];

                return response($response);
            })
            ->otherwise(function(OrderNotFound $error) {
                return response(['message' => $error->getMessage()], 404);
            })
            ->otherwise(function(Exception $error) {
                return response(['message' => $error->getMessage()], 500);
            });
    }
}
