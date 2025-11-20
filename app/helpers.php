<?php

declare(strict_types=1);

use App\Services\Request;

if (!function_exists('e')) {
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('sanitize_text')) {
    function sanitize_text(string $value): string
    {
        return trim(strip_tags($value));
    }
}

if (!function_exists('request')) {
    function request(): Request
    {
        return Request::instance();
    }
}
