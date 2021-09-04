<?php

namespace App\Core;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Respect\Validation\Exceptions\NestedValidationException;
use Throwable;

use function React\Promise\resolve;

final class ErrorHandler
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            return resolve($next($request))
                ->then(
                    function (Response $response) {
                        return $response;
                    },
                    function (Throwable $error) {
                        return $this->handleThrowable($error);
                    }
                );
        } catch (NestedValidationException $exception) {
            return response(['message' => array_values($exception->getMessages())], 400);
        } catch (Throwable $error) {
            return $this->handleThrowable($error);
        }
    }

    private function handleThrowable(Throwable $error): Response
    {
        echo "Error: ", $error->getTraceAsString(), PHP_EOL;

        return response(['message' => $error->getMessage()], 500);
    }
}
