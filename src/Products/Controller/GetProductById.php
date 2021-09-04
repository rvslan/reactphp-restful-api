<?php

namespace App\Products\Controller;

use App\Products\Product;
use App\Products\ProductNotFound;
use App\Products\Storage;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use App\Products\Controller\Output\Product as Output;
use App\Products\Controller\Output\Request;

final class GetProductById
{
    private $storage;

    public function __construct(Storage $storage) {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        return $this->storage
            ->getById((int)$id)
            ->then(function(Product $product) {
                $response = [
                    'product' => Output::fromEntity(
                        $product, Request::updateProduct($product->id)
                    ),
                    'request' => Request::listProducts(),
                ];


                return response($response);
            })
            ->otherwise(function(ProductNotFound $error) {
                return response(['message' => $error->getMessage()], 404);
            })
            ->otherwise(function(Exception $error) {
                return response(['message' => $error->getMessage()], 500);
            });
    }
}
