<?php

namespace App\Providers;

use App\Services\SqlLoggingService;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class SqlLoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = config_path('sql_logging.php');
        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'sql_logging');
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (!config('sql_logging.enabled', true)) {
            return;
        }

        // All SQL ERRORS are 100% captured with 0% overhead via app/Exceptions/Handler.php

        // Only attach live query listeners if explicitly turned on (SQL_LOG_LISTEN=true)
        if (config('sql_logging.listen_queries', false) && config('sql_logging.warning_enabled', false)) {
            $threshold = (float) config('sql_logging.slow_threshold_ms', 1000);

            DB::listen(function (QueryExecuted $event) use ($threshold) {
                if ($event->time >= $threshold) {
                    SqlLoggingService::handleQueryExecuted($event);
                }
            });

            if (config('sql_logging.log_transactions', true)) {
                Event::listen(TransactionRolledBack::class, [SqlLoggingService::class, 'handleTransactionRolledBack']);
            }
        }
    }
}
