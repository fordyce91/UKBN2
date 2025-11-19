<?php

use App\Services\Env;

return [
    'driver' => 'mysql',
    'host' => Env::get('DB_HOST', '127.0.0.1'),
    'database' => Env::get('DB_DATABASE', 'community_hub'),
    'username' => Env::get('DB_USERNAME', 'root'),
    'password' => Env::get('DB_PASSWORD', ''),
    'charset' => Env::get('DB_CHARSET', 'utf8mb4'),
    'options' => [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_STRINGIFY_FETCHES => false,
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
        \PDO::ATTR_TIMEOUT => (int) Env::get('DB_TIMEOUT', 5),
    ],
];
