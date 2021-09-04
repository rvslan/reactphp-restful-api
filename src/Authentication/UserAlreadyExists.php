<?php

namespace App\Authentication;

use RuntimeException;

final class UserAlreadyExists extends RuntimeException
{
    public function __construct()
    {
        $this->message = 'User already exists';
    }
}
