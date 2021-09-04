<?php

namespace App\Authentication;

use Exception;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;

final class SignInController
{
    private $authenticator;

    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $input = new Input($request);
        $input->validate();

        return $this->authenticator->authenticate($input->email(), $input->password())
            ->then(function ($jwt) {
                return response(['token' => $jwt]);
            })
            ->otherwise(
                function (BadCredentialsException $exception) {
                    return response(['message' => $exception->getMessage()], 401);
                }
            )
            ->otherwise(function (UserNotFoundException $exception) {
                return response(['message' => $exception->getMessage()], 401);
            })
            ->otherwise(
                function (Exception $exception) {
                    return response(['message' => $exception->getMessage()], 500);
                }
            );
    }
}
