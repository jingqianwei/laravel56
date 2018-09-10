<?php

namespace App\Http\Controllers;

use Queue;
use App\Jobs\ProcessPodcast;

class PodcastController extends Controller
{
    /**
     * 存储一个新的播客节目。
     *
     * @return void
     */
    public function store()
    {
        //ProcessPodcast::dispatch()->onQueue('pod');

        for($i = 0; $i < 100; $i++) {
            Queue::push(new ProcessPodcast("哈哈哈".$i));
        }
    }

    public function exec()
    {
        $logs = '';
        //获取本地日志
        $command = 'cat ' . storage_path('logs/laravel.log') . ' | grep 定时任务 | head -1000';
        exec($command, $logs);
        dd($logs);
    }
}
