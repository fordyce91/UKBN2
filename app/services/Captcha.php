<?php

declare(strict_types=1);

namespace App\Services;

class Captcha
{
    private const SESSION_KEY = '_captcha_answer';

    public static function enabled(): bool
    {
        return filter_var(Env::get('CAPTCHA_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
    }

    public static function prompt(): ?string
    {
        if (!self::enabled()) {
            return null;
        }

        $first = random_int(1, 9);
        $second = random_int(1, 9);
        $_SESSION[self::SESSION_KEY] = $first + $second;

        return "What is {$first} + {$second}?";
    }

    public static function verify(?string $answer): bool
    {
        if (!self::enabled()) {
            return true;
        }

        $expected = $_SESSION[self::SESSION_KEY] ?? null;
        unset($_SESSION[self::SESSION_KEY]);

        if ($expected === null) {
            return false;
        }

        return (int) $answer === (int) $expected;
    }
}
