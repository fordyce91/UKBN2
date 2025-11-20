<?php

use App\Services\Env;

return [
    'csp' => "default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self'; img-src 'self' data:; font-src 'self' data:; frame-ancestors 'none'; base-uri 'self'; form-action 'self';", // Content Security Policy
    'captcha_enabled' => filter_var(Env::get('CAPTCHA_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'rate_limits' => [
        'login' => ['attempts' => 5, 'decay' => 300],
        'register' => ['attempts' => 3, 'decay' => 600],
        'password-reset' => ['attempts' => 5, 'decay' => 600],
    ],
];
