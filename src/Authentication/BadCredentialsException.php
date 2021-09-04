<?php

namespace App\Authentication;

use RuntimeException;

final class BadCredentialsException extends RuntimeException
{
    public function __construct()
    {
        $this->message = 'Bad credentials';
    }
}
