<?php

namespace App\Authentication;

use RuntimeException;

final class UserNotFoundException extends RuntimeException
{
    public function __construct()
    {
        $this->message = 'User not found';
    }
}
