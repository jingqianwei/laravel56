<?php

namespace App\Console;

use App\Console\Commands\GetMessage;
use App\Console\Commands\GuzzleDemo;
use App\Console\Commands\Swoole;
use App\Console\Commands\TimeTask;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        GuzzleDemo::class,
        GetMessage::class,
        Swoole::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        //每一分钟执行一次
        $schedule->command('time-task:test')->everyMinute();

        //每周一的 23:00执行计划任务
        $schedule->command('db:backup')->mondays()->at('23:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
