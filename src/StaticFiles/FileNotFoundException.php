<?php

namespace App\StaticFiles;

final class FileNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        $this->message = 'File not found.';
    }
}
