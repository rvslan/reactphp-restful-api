<?php

namespace App\Products\Controller;

use App\Core\Uploader;
use App\Products\Product;
use App\Products\Storage;
use Psr\Http\Message\ServerRequestInterface;
use App\Products\Controller\Output\Product as Output;
use App\Products\Controller\Output\Request;
use React\Promise\PromiseInterface;
use function React\Promise\resolve;

final class CreateProduct
{
    private $storage;
    private $uploader;

    public function __construct(Storage $storage, Uploader $uploader)
    {
        $this->storage = $storage;
        $this->uploader = $uploader;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $input = new Input($request);
        $input->validate();

        return $this->upload($input)
            ->then(function($pathToFile) use ($input) {
                return $this->storage->create($input->name(), $input->price(), $pathToFile);
            })
            ->then(function (Product $product) {
                $response = [
                    'product' => Output::fromEntity($product, Request::detailedProduct($product->id)),
                ];

                return response($response);
            });
    }

    private function upload(Input $input): PromiseInterface
    {
       if ($input->image() === null) {
           return resolve();
       }

       return $this->uploader->upload($input->image());
    }
}
