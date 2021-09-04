<?php

namespace App\Authentication;

use Exception;
use Psr\Http\Message\ServerRequestInterface;

final class SignUpController
{
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $input = new Input($request);

        return $this->storage->create($input->email(), $input->hashedPassword())
            ->then(
                function () {
                    return response();
                }
            )
            ->otherwise(function (UserAlreadyExists $exception) {
                return response(['message' => $exception->getMessage()], 400);
            })
            ->otherwise(
                function (Exception $exception) {
                    return response(['message' => $exception->getMessage()], 500);
                }
            );
    }
}
