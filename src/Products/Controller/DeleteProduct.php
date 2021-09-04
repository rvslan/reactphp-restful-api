<?php

namespace App\Products\Controller;

use App\Products\ProductNotFound;
use App\Products\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Products\Controller\Output\Request;

final class DeleteProduct
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
                        'request' => Request::createProduct(),
                    ];

                    return response($response, 204);
                }
            )
            ->otherwise(function(ProductNotFound $error) {
                return response(['message' => $error->getMessage()], 404);
            })
            ->otherwise(function(Exception $error) {
                return response(['message' => $error->getMessage()], 500);
            });
    }
}
