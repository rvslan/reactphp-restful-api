<?php

require __DIR__ . '/vendor/autoload.php';

(\Dotenv\Dotenv::createMutable(__DIR__))->load();

return [
    'dbname' => $_ENV['DB_NAME'],
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS'],
    'host' => $_ENV['DB_HOST'],
    'driver' => $_ENV['DB_DRIVER'],
];