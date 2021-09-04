<?php

use Narrowspark\MimeType\MimeTypeFileExtensionGuesser;
use React\Filesystem\Node\FileInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;

function response(array $data = [], $status = 200) {
    return new Response(
        $status,
        ['Content-Type' => 'application/json'],
        json_encode($data)
    );
}

function responseWithFile(FileInterface $file, $status = 200): PromiseInterface
{
    return $file->getContents()
        ->then(function ($contents) use ($status, $file) {
            return new Response(
                $status,
                ['Content-Type' => MimeTypeFileExtensionGuesser::guess($file->getPath())],
                $contents
            );
        },
    function (Exception $exception) {
        return response(['message' => array_values($exception->getMessages())], 500);
    });
}

function request($request, $value)
{
    return $request->getParsedBody()[$value];
}