<?php

namespace App\Products\Controller;

use App\Products\Controller\Output\Product as OutputProduct;
use App\Products\Product;
use App\Products\Storage;
use Psr\Http\Message\ServerRequestInterface;
use App\Products\Controller\Output\Request;

final class GetAllProducts
{
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->storage->getAll()
            ->then(function (array $products) {
                $response = [
                    'products' => $this->mapProducts(...$products),
                    'count' => count($products),
                ];

                return response($response);
            });
    }

    private function mapProducts(Product ...$products): array
    {
        return array_map(function (Product $product) {
            return OutputProduct::fromEntity($product, Request::detailedProduct($product->id));
        }, $products);
    }
}
