<?php

use App\Services\Env;

return [
    'name' => Env::get('APP_NAME', 'Community Hub'),
    'base_url' => Env::get('APP_URL', 'http://localhost'),
    'env' => Env::get('APP_ENV', 'local'),
];
