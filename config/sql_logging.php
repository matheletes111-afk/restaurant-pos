<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Master Toggle for SQL Logging
    |--------------------------------------------------------------------------
    | Set to false to disable all SQL logging across the application.
    */
    'enabled' => env('SQL_LOG_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Listen to Live Query Events
    |--------------------------------------------------------------------------
    | When false (recommended for production/speed), DB::listen is not attached,
    | ensuring 100% native speed with 0% overhead.
    | All SQL errors are still 100% captured in sql-error.log via Handler.php.
    | Set to true only when you want to actively log slow queries or query history.
    */
    'listen_queries' => env('SQL_LOG_LISTEN', false),

    'warning_enabled' => env('SQL_LOG_WARNING_ENABLED', false),
    'error_enabled' => env('SQL_LOG_ERROR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Slow Query Threshold (in milliseconds)
    |--------------------------------------------------------------------------
    | Any SQL query that takes longer than this execution time will trigger
    | a WARNING log with full query details and calling controller/model.
    */
    'slow_threshold_ms' => (float) env('SQL_SLOW_THRESHOLD_MS', 500),

    /*
    |--------------------------------------------------------------------------
    | Warn on Unbounded Queries
    |--------------------------------------------------------------------------
    | When true, any UPDATE or DELETE statement without a WHERE clause
    | will trigger a WARNING log to help detect risky accidental queries.
    */
    'warn_on_unbounded' => env('SQL_WARN_UNBOUNDED', true),

    /*
    |--------------------------------------------------------------------------
    | Transaction Logging
    |--------------------------------------------------------------------------
    | When true, transaction events (Begin, Commit, Rollback) will be logged.
    | Rollbacks are logged as WARNING, Begin & Commit as INFO.
    */
    'log_transactions' => env('SQL_LOG_TRANSACTIONS', true),

    /*
    |--------------------------------------------------------------------------
    | Sensitive Fields Masking
    |--------------------------------------------------------------------------
    | Fields in request payloads that must be redacted when logging context.
    */
    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'current_password',
        'secret',
        'token',
        'api_key',
        'razorpay_signature',
        'razorpay_payment_id',
        'card_number',
        'cvv',
    ],
];
