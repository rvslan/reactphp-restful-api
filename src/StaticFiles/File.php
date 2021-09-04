<?php

namespace App\StaticFiles;

final class File
{
    public $contents;
    public $mimeType;

    public function __construct(string $contents, string $mimeType)
    {
        $this->contents = $contents;
        $this->mimeType = $mimeType;
    }
}
