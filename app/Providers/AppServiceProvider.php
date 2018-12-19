<?php

namespace App\Providers;

use DateTime;
use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 迁移生成的默认字符串长度
        Schema::defaultStringLength(191);
        //以后执行数据库操作，都会把sql语句记录下来
        DB::listen(function($sql) {
            foreach ($sql->bindings as $i => $binding) {
                if ($binding instanceof DateTime) {
                    $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                } else {
                    if (is_string($binding)) {
                        $sql->bindings[$i] = "'$binding'";
                    }
                }
            }

            // Insert bindings into query
            $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);

            $query = vsprintf($query, $sql->bindings);

            // Save the query to file
            $logFile = fopen(
                storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'),
                'a+'
            );
            fwrite($logFile, date('Y-m-d H:i:s') . ':' . $query . PHP_EOL);
            fclose($logFile);
        });

        //监听sql
        DB::listen(function($query) {
            $bindings = $query->bindings;
            $sql = $query->sql;
            foreach ($bindings as $replace){
                $value = is_numeric($replace) ? $replace : "'".$replace."'";
                $sql = preg_replace('/\?/', $value, $sql, 1);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
