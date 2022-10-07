<?php

namespace App\Providers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // デバッグ環境の場合はSQLログを出力する（/log/sql.log）
        if (env('APP_DEBUG')) {
            DB::listen(function ($query) {
                $sql = $query->sql;
                for ($i = 0; $i < count($query->bindings); $i++) {
                    $sql = preg_replace("/\?/", "'" . $query->bindings[$i] . "'", $sql, 1);
                }
                // Log::channel('sql')->debug($sql);
                $sqlLog = new Logger('SQL');
                $sqlLog->pushHandler(new StreamHandler(storage_path('logs/sql.log')), Logger::DEBUG);
                $sqlLog->debug($sql);
            });
        }
    }
}
