<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger
{
    private static ?self $instance = null;

    private string $logDirectory;
    private int $maxFiles;

    private function __construct(string $logDirectory, int $maxFiles = 14)
    {
        $this->logDirectory = rtrim($logDirectory, '/');
        $this->maxFiles = $maxFiles;

        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0775, true);
        }
    }

    public static function get(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(__DIR__ . '/../../storage/logs');
        }

        return self::$instance;
    }

    public function log(string $level, string|\Stringable $message, array $context = []): void
    {
        if (!in_array($level, $this->supportedLevels(), true)) {
            return;
        }

        $request = Request::instance();
        $date = new DateTimeImmutable();
        $metadata = json_encode(
            $this->contextExtras($context, $request),
            JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
        );
        $line = sprintf(
            "%s [%s] %s %s\n",
            $date->format(DateTimeImmutable::ATOM),
            strtoupper($level),
            $this->interpolate((string) $message, $context),
            $metadata
        );

        file_put_contents($this->logFilePath($date), $line, FILE_APPEND);
        $this->rotateLogs();
    }

    private function supportedLevels(): array
    {
        return [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG,
        ];
    }

    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $value) {
            if (!is_array($value) && !is_object($value)) {
                $replace['{' . $key . '}'] = (string) $value;
            }
        }

        return strtr($message, $replace);
    }

    private function contextExtras(array $context, Request $request): array
    {
        return array_merge($context, [
            'request_id' => $request->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'uri' => $request->uri(),
            'method' => $request->method(),
            'user_id' => Session::get('user')['id'] ?? null,
        ]);
    }

    private function logFilePath(DateTimeImmutable $date): string
    {
        return sprintf('%s/app-%s.log', $this->logDirectory, $date->format('Y-m-d'));
    }

    private function rotateLogs(): void
    {
        $files = glob($this->logDirectory . '/app-*.log');
        rsort($files);

        if (count($files) <= $this->maxFiles) {
            return;
        }

        foreach (array_slice($files, $this->maxFiles) as $file) {
            @unlink($file);
        }
    }
}
