<?php

declare(strict_types=1);

namespace App\Services;

use ErrorException;

class ErrorHandler
{
    public static function register(string $env): void
    {
        set_error_handler(static fn ($severity, $message, $file, $line) => self::handleError($severity, $message, $file, $line));
        set_exception_handler(static fn ($exception) => self::handleException($exception, $env));
        register_shutdown_function(static fn () => self::handleShutdown($env));
    }

    private static function handleError(int $severity, string $message, string $file, int $line): void
    {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    private static function handleException(\Throwable $exception, string $env): void
    {
        Logger::get()->error($exception->getMessage(), [
            'exception' => [
                'type' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $env === 'production' ? null : $exception->getTraceAsString(),
            ],
        ]);

        http_response_code(500);
        $message = trim($exception->getMessage()) ?: 'Something went wrong. Please try again later.';
        echo View::render('errors/500', [
            'title' => 'Server Error',
            'message' => $message,
            'requestId' => Request::instance()->id(),
            'details' => $env === 'production' ? null : [
                'type' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ],
        ]);
    }

    private static function handleShutdown(string $env): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            self::handleException(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']), $env);
        }
    }
}
