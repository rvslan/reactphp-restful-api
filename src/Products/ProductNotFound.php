<?php

namespace App\Products;

use RuntimeException;

final class ProductNotFound extends RuntimeException
{
    public function __construct()
    {
        $this->message = 'Product not found.';
    }
}
