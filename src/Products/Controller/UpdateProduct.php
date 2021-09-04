<?php

namespace App\Products\Controller;

use App\Products\ProductNotFound;
use App\Products\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Products\Controller\Output\Request;

final class UpdateProduct
{
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        $input = new Input($request);
        $input->validate();

        return $this->storage->update((int)$id, $input->name(), $input->price())
            ->then(
                function () use ($id) {
                    $response = [
                        'request' => Request::detailedProduct((int) $id)
                    ];

                    return response($response);
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
