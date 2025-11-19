<?php

namespace App\Services;

use App\Services\Csrf;

class View
{
    public static string $basePath = __DIR__ . '/../views/';

    public static function render(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        $csrfToken = Csrf::token();
        ob_start();
        include self::$basePath . $view . '.php';
        return ob_get_clean();
    }
}
