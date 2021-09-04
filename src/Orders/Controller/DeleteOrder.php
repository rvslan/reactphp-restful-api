<?php

namespace App\Orders\Controller;

use App\Orders\Controller\Output\Request;
use App\Orders\OrderNotFound;
use App\Orders\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

final class DeleteOrder
{
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        return $this->storage->delete((int)$id)
            ->then(
                function () {
                    $response = [
                        'request' => Request::createOrder(),
                    ];

                    return response($response, 200);
                }
            )
            ->otherwise(function(OrderNotFound $error) {
                return response(['message' => $error->getMessage()], 404);
            })
            ->otherwise(function(Exception $error) {
                return response(['message' => $error->getMessage()], 500);
            });
    }
}
