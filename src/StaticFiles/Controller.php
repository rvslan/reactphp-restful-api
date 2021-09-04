<?php

namespace App\StaticFiles;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use React\Http\Message\Response;

final class Controller
{
    public function __construct(Webroot $webroot)
    {
        $this->webroot = $webroot;
    }

    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        return $this->webroot->file($request->getUri()->getPath())
            ->then(function (File $file) {
                return new Response(
                    200,
                    ['Content-Type' => $file->mimeType],
                    $file->contents
                );
            })->otherwise(
                function (FileNotFoundException $exception) {
                    return response([], 404);
                }
            )
            ->otherwise(
                function (Exception $exception) {
                    return response(['message' => array_values($exception->getMessages())], 500);
                }
            );
    }
}
