<?php

namespace App\Orders;

use RuntimeException;

final class OrderNotFound extends RuntimeException
{
    public function __construct()
    {
        $this->message = 'Order not found.';
    }
}
