<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Error Pages Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk custom error pages
    |
    */

    'enabled' => env('CUSTOM_ERROR_PAGES_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | Menampilkan informasi debug pada error pages
    | HARUS false di production!
    |
    */

    'show_debug_info' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Log HTTP Errors
    |--------------------------------------------------------------------------
    |
    | Aktifkan logging untuk semua HTTP errors
    |
    */

    'log_http_errors' => env('LOG_HTTP_ERRORS', true),

    /*
    |--------------------------------------------------------------------------
    | Skip Logging for Specific Error Codes
    |--------------------------------------------------------------------------
    |
    | Error codes yang tidak perlu dicatat di log
    | Contoh: 404 Not Found bisa sangat banyak
    |
    */

    'skip_logging_codes' => [
        // 404, // Uncomment untuk skip logging 404 errors
    ],

    /*
    |--------------------------------------------------------------------------
    | Sensitive Fields
    |--------------------------------------------------------------------------
    |
    | Field-field yang akan di-redact dari log
    |
    */

    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'api_key',
        'api_secret',
        'secret',
        'secret_key',
        'private_key',
        'credit_card',
        'card_number',
        'cvv',
        'cvc',
        'ssn',
        'social_security',
        'authorization',
        'auth_token',
        'access_token',
        'refresh_token',
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Notification
    |--------------------------------------------------------------------------
    |
    | Kirim notifikasi untuk error tertentu
    |
    */

    'notifications' => [
        'enabled' => env('ERROR_NOTIFICATIONS_ENABLED', false),
        
        // Notify untuk error codes ini
        'notify_codes' => [
            500, // Internal Server Error
            502, // Bad Gateway
            503, // Service Unavailable
            504, // Gateway Timeout
        ],
        
        // Channel untuk notifikasi (email, slack, etc)
        'channels' => [
            'slack' => env('ERROR_SLACK_WEBHOOK_URL'),
            'email' => env('ERROR_NOTIFICATION_EMAIL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limit untuk mencegah spam pada error pages
    |
    */

    'rate_limit' => [
        'enabled' => true,
        'max_attempts' => 60, // per minute
        'decay_minutes' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Error Messages
    |--------------------------------------------------------------------------
    |
    | Custom pesan untuk error codes tertentu
    |
    */

    'custom_messages' => [
        404 => 'Halaman yang Anda cari tidak ditemukan.',
        500 => 'Terjadi kesalahan pada server. Tim kami telah diberitahu.',
        503 => 'Aplikasi sedang dalam maintenance. Mohon kembali lagi nanti.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Page Theme
    |--------------------------------------------------------------------------
    |
    | Konfigurasi tema untuk error pages
    |
    */

    'theme' => [
        'primary_color' => '#6366f1',
        'danger_color' => '#ef4444',
        'warning_color' => '#f59e0b',
        'success_color' => '#10b981',
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Integration
    |--------------------------------------------------------------------------
    |
    | Integrasi dengan service monitoring
    |
    */

    'monitoring' => [
        'sentry' => [
            'enabled' => env('SENTRY_ENABLED', false),
            'dsn' => env('SENTRY_LARAVEL_DSN'),
        ],
        
        'bugsnag' => [
            'enabled' => env('BUGSNAG_ENABLED', false),
            'api_key' => env('BUGSNAG_API_KEY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Retention
    |--------------------------------------------------------------------------
    |
    | Berapa lama log error disimpan
    |
    */

    'log_retention_days' => env('ERROR_LOG_RETENTION_DAYS', 30),

];
