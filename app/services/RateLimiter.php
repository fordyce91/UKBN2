<?php

namespace App\Services;

class RateLimiter
{
    public static function tooManyAttempts(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $bucket = self::getBucket($key, $decaySeconds);
        return $bucket['attempts'] >= $maxAttempts && $bucket['expires_at'] > time();
    }

    public static function hit(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $bucket = self::getBucket($key, $decaySeconds);
        $bucket['attempts']++;
        self::storeBucket($key, $bucket);

        return $bucket['attempts'] > $maxAttempts;
    }

    public static function clear(string $key): void
    {
        unset($_SESSION['_rate_limit'][$key]);
    }

    private static function getBucket(string $key, int $decaySeconds): array
    {
        $bucket = $_SESSION['_rate_limit'][$key] ?? ['attempts' => 0, 'expires_at' => time() + $decaySeconds];

        if ($bucket['expires_at'] < time()) {
            $bucket = ['attempts' => 0, 'expires_at' => time() + $decaySeconds];
        }

        return $bucket;
    }

    private static function storeBucket(string $key, array $bucket): void
    {
        $_SESSION['_rate_limit'][$key] = $bucket;
    }
}
