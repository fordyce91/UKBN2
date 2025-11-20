<?php

declare(strict_types=1);

namespace App\Services;

class Security
{
    public static function applyHeaders(array $config): void
    {
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: DENY");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header("Permissions-Policy: accelerometer=(), camera=(), geolocation=(), microphone=(), payment=()");
        header("X-XSS-Protection: 0");
        header("Content-Security-Policy: " . ($config['csp'] ?? "default-src 'self';"));

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }
}
