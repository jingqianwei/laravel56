<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TimeTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'time-task:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '定时任务测试';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('这是定时任务写的日志');
    }
}
