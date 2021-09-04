<?php

namespace App;

use Psr\Http\Message\ServerRequestInterface;

final class ErrorHandler
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            return response(['message' => array_values($exception->getMessages())], 500);
        }
    }
}
