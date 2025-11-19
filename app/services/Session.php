<?php

namespace App\Services;

class Session
{
    private const SESSION_LIFETIME = 1800; // 30 minutes

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $cookieParams = [
                'lifetime' => self::SESSION_LIFETIME,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict',
            ];

            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_secure', '1');
            ini_set('session.use_strict_mode', '1');

            session_name('COMMUNITYSESSID');
            session_set_cookie_params($cookieParams);
            session_start();
        }

        self::enforceExpiry();
    }

    public static function get(string $key, $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        if (isset($_SESSION['_flash'][$key])) {
            unset($_SESSION['_flash'][$key]);
        }

        return $value;
    }

    public static function refresh(): void
    {
        $_SESSION['_expires_at'] = time() + self::SESSION_LIFETIME;
        $_SESSION['_last_active'] = time();
    }

    public static function isExpired(): bool
    {
        $expiresAt = $_SESSION['_expires_at'] ?? null;
        return is_int($expiresAt) && $expiresAt < time();
    }

    public static function invalidate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, [
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => $params['samesite'] ?? 'Strict',
                ]);
            }

            session_destroy();
        }

        if (session_status() === PHP_SESSION_NONE) {
            self::start();
            return;
        }

        self::enforceExpiry();
    }

    private static function enforceExpiry(): void
    {
        if (self::isExpired()) {
            self::invalidate();
            return;
        }

        self::refresh();
    }
}
