<?php

namespace App\Products\Controller\Output;

final class Request
{
    private const URI = 'http://localhost:8000/products';
    public $type;
    public $url;
    public $body;

    private function __construct(string $type, string $url, array $body = null)
    {
        $this->type = $type;
        $this->url = $url;
    }

    public static function detailedProduct(int $id): self
    {
        return new self('GET', self::URI. '/' . $id);
    }

    public static function updateProduct(int $id): self
    {
        return new self('PUT', self::URI. '/' . $id);
    }

    public static function listProducts(): self
    {
        return new self('GET', self::URI);
    }

    public static function createProduct(): self
    {
        return new self('POST', self::URI, ['name' => 'string', 'price' => 'float']);
    }
}
