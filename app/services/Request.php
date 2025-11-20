<?php

declare(strict_types=1);

namespace App\Services;

class Request
{
    private static ?Request $instance = null;
    private string $id;
    private array $input;

    private function __construct()
    {
        $this->id = bin2hex(random_bytes(16));
        $this->input = $this->sanitizeArray(array_merge($_GET, $_POST));
    }

    public static function instance(): Request
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function all(): array
    {
        return $this->input;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->input[$key] ?? $default;
    }

    public function only(array $keys): array
    {
        $filtered = [];
        foreach ($keys as $key) {
            $filtered[$key] = $this->input[$key] ?? null;
        }
        return $filtered;
    }

    public function ip(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : 'unknown';
    }

    public function userAgent(): string
    {
        return (string) ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
    }

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        return (string) ($_SERVER['REQUEST_URI'] ?? '/');
    }

    public function validate(array $rules): array
    {
        return Validator::validate($this->input, $rules);
    }

    private function sanitizeArray(array $data): array
    {
        $clean = [];
        foreach ($data as $key => $value) {
            $clean[$key] = $this->sanitizeValue($value);
        }

        return $clean;
    }

    private function sanitizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return $this->sanitizeArray($value);
        }

        if (is_numeric($value)) {
            return $value + 0;
        }

        return trim(strip_tags((string) $value));
    }
}
