<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class ErrorLogger
{
    /**
     * Log error dengan context yang aman
     */
    public static function log(string $message, array $context = [], string $level = 'error'): void
    {
        $enrichedContext = array_merge([
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_id' => auth()->id() ?? null,
            'timestamp' => now()->toDateTimeString(),
        ], $context);

        // Sanitize sensitive data
        $enrichedContext = self::sanitizeContext($enrichedContext);

        Log::channel(config('logging.default'))->$level($message, $enrichedContext);
    }

    /**
     * Log error dengan stack trace
     */
    public static function logWithTrace(string $message, \Throwable $exception, array $context = []): void
    {
        $context['exception'] = get_class($exception);
        $context['exception_message'] = $exception->getMessage();
        $context['file'] = $exception->getFile();
        $context['line'] = $exception->getLine();
        
        if (config('app.debug')) {
            $context['trace'] = collect($exception->getTrace())->take(5)->toArray();
        }

        self::log($message, $context, 'error');
    }

    /**
     * Sanitize context untuk menghapus sensitive data
     */
    protected static function sanitizeContext(array $context): array
    {
        $sensitiveKeys = config('errors.sensitive_fields', [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            'api_key',
            'api_secret',
            'secret',
            'credit_card',
            'cvv',
            'authorization',
        ]);

        foreach ($context as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $context[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $context[$key] = self::sanitizeContext($value);
            }
        }

        return $context;
    }

    /**
     * Log database query error
     */
    public static function logDatabaseError(\Throwable $exception, array $context = []): void
    {
        $context['database_error'] = true;
        $context['sql_state'] = $exception->getCode() ?? null;
        
        self::logWithTrace('Database Error Occurred', $exception, $context);
    }

    /**
     * Log API error
     */
    public static function logApiError(string $endpoint, int $statusCode, string $message, array $context = []): void
    {
        $context['api_error'] = true;
        $context['endpoint'] = $endpoint;
        $context['status_code'] = $statusCode;
        
        self::log($message, $context, $statusCode >= 500 ? 'error' : 'warning');
    }

    /**
     * Log validation error
     */
    public static function logValidationError(array $errors, array $context = []): void
    {
        $context['validation_errors'] = $errors;
        
        self::log('Validation Failed', $context, 'info');
    }

    /**
     * Log security event
     */
    public static function logSecurityEvent(string $event, array $context = []): void
    {
        $context['security_event'] = true;
        $context['event_type'] = $event;
        
        self::log('Security Event: ' . $event, $context, 'warning');
    }
}

