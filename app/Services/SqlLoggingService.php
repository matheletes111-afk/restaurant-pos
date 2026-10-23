<?php

namespace App\Services;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Throwable;

class SqlLoggingService
{
    /**
     * Recursion guard to prevent infinite loops if logging triggers queries.
     */
    protected static bool $isHandling = false;

    /**
     * Cache request context per HTTP request to eliminate redundant overhead.
     */
    protected static ?array $cachedRequestContext = null;

    /**
     * Handle executed query event (Logs WARNING for slow or unbounded queries).
     */
    public static function handleQueryExecuted(QueryExecuted $event): void
    {
        // 1. Check master toggle & guard against recursion
        if (self::$isHandling || !config('sql_logging.enabled', true) || !config('sql_logging.warning_enabled', false)) {
            return;
        }

        $time = (float) $event->time;
        $slowThreshold = (float) config('sql_logging.slow_threshold_ms', 500);
        $isSlow = ($time >= $slowThreshold);

        // Check for unbounded UPDATE / DELETE
        $isUnbounded = false;
        if (config('sql_logging.warn_on_unbounded', true)) {
            $upper = strtoupper(ltrim($event->sql));
            if (str_starts_with($upper, 'UPDATE ') || str_starts_with($upper, 'DELETE ')) {
                if (!preg_match('/\bWHERE\b/i', $upper)) {
                    $isUnbounded = true;
                }
            }
        }

        // FAST EXIT: Exit immediately if neither slow nor unbounded
        if (!$isSlow && !$isUnbounded) {
            return;
        }

        self::$isHandling = true;

        try {
            $formattedSql = self::formatSql($event->sql, $event->bindings);
            $context = self::getRequestContext();
            $caller = self::getCaller();

            $reasons = [];
            if ($isSlow) {
                $reasons[] = "Slow Query ({$time} ms >= threshold {$slowThreshold} ms)";
            }
            if ($isUnbounded) {
                $reasons[] = "Unbounded Query (UPDATE/DELETE without WHERE clause)";
            }
            $reasonText = implode(' & ', $reasons);

            $warnMsg = sprintf(
                "[SQL WARNING] %s on connection [%s] in %s ms | Caller: %s\nQuery: %s",
                $reasonText,
                $event->connectionName,
                $time,
                $caller ?: 'Unknown',
                $formattedSql
            );

            self::writeLog('sql_warning', 'warning', $warnMsg, [
                'type' => $isSlow && $isUnbounded ? 'slow_and_unbounded' : ($isSlow ? 'slow_query' : 'unbounded_query'),
                'time_ms' => $time,
                'threshold_ms' => $slowThreshold,
                'connection' => $event->connectionName,
                'caller' => $caller,
                'formatted_sql' => $formattedSql,
                'raw_sql' => $event->sql,
                'bindings' => $event->bindings,
                'request' => $context,
            ]);
        } catch (\Throwable $e) {
            // Fail silently to never break the application
        } finally {
            self::$isHandling = false;
        }
    }

    /**
     * Handle transaction rolled back (Logged as WARNING).
     */
    public static function handleTransactionRolledBack(TransactionRolledBack $event): void
    {
        if (self::$isHandling || !config('sql_logging.enabled', true) || !config('sql_logging.log_transactions', true) || !config('sql_logging.warning_enabled', true)) {
            return;
        }

        self::$isHandling = true;
        try {
            $caller = self::getCaller();
            $msg = sprintf("[SQL WARNING] [Transaction Rolled Back] on connection [%s] | Caller: %s", $event->connectionName, $caller ?: 'Unknown');
            self::writeLog('sql_warning', 'warning', $msg, [
                'event' => 'transaction_rolled_back',
                'connection' => $event->connectionName,
                'caller' => $caller,
                'request' => self::getRequestContext(),
            ]);
        } finally {
            self::$isHandling = false;
        }
    }

    /**
     * Log QueryException (Syntax errors, column missing, constraint violation, deadlocks, etc.) as ERROR.
     */
    public static function logQueryException(QueryException $e): void
    {
        if (self::$isHandling || !config('sql_logging.enabled', true) || !config('sql_logging.error_enabled', true)) {
            return;
        }

        self::$isHandling = true;
        try {
            $formattedSql = self::formatSql($e->getSql(), $e->getBindings());
            $caller = self::getCaller($e->getTrace());
            $context = self::getRequestContext();
            $sanitizedInput = self::getSanitizedInput();
            $trace = self::formatTrace($e);

            $errorMsg = sprintf(
                "[SQL ERROR] QueryException: %s\nFailed SQL: %s\nCaller: %s\nLocation: %s:%s",
                $e->getMessage(),
                $formattedSql,
                $caller ?: 'Unknown',
                $e->getFile(),
                $e->getLine()
            );

            self::writeLog('sql_error', 'error', $errorMsg, [
                'error_type' => 'QueryException',
                'sqlstate' => $e->getCode(),
                'message' => $e->getMessage(),
                'formatted_sql' => $formattedSql,
                'raw_sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'caller' => $caller,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $context,
                'input' => $sanitizedInput,
                'trace' => $trace,
            ]);
        } catch (\Throwable $ignored) {
        } finally {
            self::$isHandling = false;
        }
    }

    /**
     * Log PDOException as ERROR.
     */
    public static function logPdoException(\PDOException $e): void
    {
        if (self::$isHandling || !config('sql_logging.enabled', true) || !config('sql_logging.error_enabled', true)) {
            return;
        }

        self::$isHandling = true;
        try {
            $caller = self::getCaller($e->getTrace());
            $context = self::getRequestContext();
            $trace = self::formatTrace($e);

            $errorMsg = sprintf(
                "[SQL ERROR] PDOException: %s\nCaller: %s\nLocation: %s:%s",
                $e->getMessage(),
                $caller ?: 'Unknown',
                $e->getFile(),
                $e->getLine()
            );

            self::writeLog('sql_error', 'error', $errorMsg, [
                'error_type' => 'PDOException',
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'caller' => $caller,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $context,
                'trace' => $trace,
            ]);
        } catch (\Throwable $ignored) {
        } finally {
            self::$isHandling = false;
        }
    }

    /**
     * Generic fallback to log any database-related Throwable as ERROR.
     */
    public static function logException(Throwable $e, ?string $customMessage = null): void
    {
        if ($e instanceof QueryException) {
            self::logQueryException($e);
            return;
        }

        if ($e instanceof \PDOException) {
            self::logPdoException($e);
            return;
        }

        if (self::$isHandling || !config('sql_logging.enabled', true) || !config('sql_logging.error_enabled', true)) {
            return;
        }

        self::$isHandling = true;
        try {
            $caller = self::getCaller($e->getTrace());
            $prefix = $customMessage ? "[SQL ERROR] {$customMessage}: " : "[SQL ERROR] ";
            $errorMsg = sprintf(
                "%s%s\nCaller: %s\nLocation: %s:%s",
                $prefix,
                $e->getMessage(),
                $caller ?: 'Unknown',
                $e->getFile(),
                $e->getLine()
            );

            self::writeLog('sql_error', 'error', $errorMsg, [
                'exception_class' => get_class($e),
                'message' => $e->getMessage(),
                'caller' => $caller,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => self::getRequestContext(),
                'trace' => self::formatTrace($e),
            ]);
        } catch (\Throwable $ignored) {
        } finally {
            self::$isHandling = false;
        }
    }

    /**
     * Format SQL query by safely interpolating bindings.
     */
    public static function formatSql(string $sql, array $bindings = []): string
    {
        if (empty($bindings)) {
            return $sql;
        }

        // 1. Handle named bindings if present (:param)
        foreach ($bindings as $key => $binding) {
            if (is_string($key)) {
                $val = self::formatBindingValue($binding);
                $sql = preg_replace('/:' . preg_quote($key, '/') . '\b/', $val, $sql);
            }
        }

        // 2. Handle positional bindings (?)
        $i = 0;
        return preg_replace_callback('/\?/', function () use (&$bindings, &$i) {
            if (!array_key_exists($i, $bindings)) {
                return '?';
            }
            $val = $bindings[$i++];
            return self::formatBindingValue($val);
        }, $sql);
    }

    /**
     * Format a single binding value for safe display.
     */
    protected static function formatBindingValue($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_numeric($value)) {
            return (string) $value;
        }
        if ($value instanceof \DateTimeInterface) {
            return "'" . $value->format('Y-m-d H:i:s') . "'";
        }
        $str = (string) $value;
        return "'" . addcslashes($str, "\0..\37'\x7f..\xff\\") . "'";
    }

    /**
     * Locate the file and line in app/ that initiated the query (capped to first 12 frames for speed).
     */
    public static function getCaller(?array $trace = null): ?string
    {
        // Limit backtrace to top 15 frames max - avoids huge memory and CPU loops
        $trace = $trace ?? debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);
        $appPath = rtrim(str_replace('\\', '/', app_path()), '/') . '/';
        $basePath = rtrim(str_replace('\\', '/', base_path()), '/') . '/';

        foreach ($trace as $frame) {
            if (empty($frame['file'])) {
                continue;
            }

            $normalizedFile = str_replace('\\', '/', $frame['file']);

            if (str_starts_with($normalizedFile, $appPath)) {
                if (str_contains($normalizedFile, 'SqlLogging')) {
                    continue;
                }

                $relative = str_replace($basePath, '', $normalizedFile);
                $line = $frame['line'] ?? 0;
                $class = $frame['class'] ?? '';
                $type = $frame['type'] ?? '';
                $function = $frame['function'] ?? '';

                if ($class && $function) {
                    return "{$relative}:{$line} ({$class}{$type}{$function})";
                }

                return "{$relative}:{$line}";
            }
        }

        // Fallback: check if invoked from routes
        foreach ($trace as $frame) {
            if (empty($frame['file'])) {
                continue;
            }
            $normalizedFile = str_replace('\\', '/', $frame['file']);
            if (str_contains($normalizedFile, '/routes/')) {
                $relative = str_replace($basePath, '', $normalizedFile);
                $line = $frame['line'] ?? 0;
                return "{$relative}:{$line}";
            }
        }

        return null;
    }

    /**
     * Get request details (HTTP or CLI) for debugging context (cached once per request).
     */
    public static function getRequestContext(): array
    {
        if (self::$cachedRequestContext !== null) {
            return self::$cachedRequestContext;
        }

        try {
            if (app()->runningInConsole()) {
                self::$cachedRequestContext = [
                    'environment' => 'cli',
                    'command' => implode(' ', $_SERVER['argv'] ?? ['artisan']),
                ];
                return self::$cachedRequestContext;
            }

            $request = request();
            
            // Avoid triggering database queries when checking user
            $userId = null;
            $restaurantId = null;
            $role = null;

            try {
                if (auth()->hasUser()) {
                    $u = auth()->user();
                    $userId = $u->id ?? null;
                    $restaurantId = $u->restaurant_id ?? null;
                    $role = $u->role ?? null;
                } else {
                    $userId = auth()->id();
                }
            } catch (\Throwable $e) {
                // Ignore auth inspection errors
            }

            self::$cachedRequestContext = [
                'environment' => 'http',
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'route' => optional($request->route())->getName(),
                'ip' => $request->ip(),
                'user_id' => $userId,
                'restaurant_id' => $restaurantId,
                'role' => $role,
            ];

            return self::$cachedRequestContext;
        } catch (\Throwable $e) {
            return ['context_error' => $e->getMessage()];
        }
    }

    /**
     * Sanitize request inputs so passwords, tokens, and secrets are not leaked in logs.
     */
    public static function getSanitizedInput(): array
    {
        try {
            if (app()->runningInConsole()) {
                return [];
            }

            $inputs = request()->all();
            return self::deepSanitize($inputs);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Recursively mask sensitive fields in arrays.
     */
    protected static function deepSanitize(array $data): array
    {
        $sensitive = config('sql_logging.sensitive_fields', [
            'password', 'password_confirmation', 'current_password', 'secret', 'token', 'api_key', 'razorpay_signature'
        ]);

        foreach ($data as $key => $val) {
            if (in_array(strtolower((string) $key), $sensitive, true)) {
                $data[$key] = '********';
            } elseif (is_array($val)) {
                $data[$key] = self::deepSanitize($val);
            }
        }

        return $data;
    }

    /**
     * Format application stack trace frames for fast root-cause identification.
     */
    protected static function formatTrace(Throwable $e): array
    {
        $appPath = rtrim(str_replace('\\', '/', app_path()), '/') . '/';
        $basePath = rtrim(str_replace('\\', '/', base_path()), '/') . '/';
        $appFrames = [];

        foreach ($e->getTrace() as $frame) {
            if (empty($frame['file'])) {
                continue;
            }

            $normalized = str_replace('\\', '/', $frame['file']);
            if (str_starts_with($normalized, $appPath)) {
                $relative = str_replace($basePath, '', $normalized);
                $line = $frame['line'] ?? 0;
                $class = $frame['class'] ?? '';
                $type = $frame['type'] ?? '';
                $func = $frame['function'] ?? '';
                $appFrames[] = "{$relative}:{$line} ({$class}{$type}{$func})";
            }
        }

        return array_slice($appFrames, 0, 8);
    }

    /**
     * Write safely to the designated logging channel with fallback.
     */
    protected static function writeLog(string $channel, string $level, string $message, array $context = []): void
    {
        try {
            Log::channel($channel)->{$level}($message, $context);
        } catch (\Throwable $e) {
            try {
                Log::{$level}("[{$channel}] " . $message, $context);
            } catch (\Throwable $ignored) {
            }
        }
    }
}
