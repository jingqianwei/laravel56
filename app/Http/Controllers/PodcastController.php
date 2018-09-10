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
        //获取本地日志内容
        $command = 'cat ' . storage_path('logs/laravel.log') . ' | grep 定时任务 | head -1000';
        exec($command, $logs);

        //循环压缩文件夹下的所有文件
        foreach (\File::files(storage_path('backups')) as $file) {
            // 解压
            // $command = 'tar -vczf ' . explode('.', $file)[0] . '.sql.tgz ' . $file;
            // 压缩
            $commands = 'cd ' . storage_path('backups') . ' && tar -vczf ' . basename(explode('.', $file)[0]) . '.sql.tgz ' . basename($file);
            exec($commands);
            file_put_contents(storage_path('backups/' . time() . '.MD5'), md5($file), FILE_APPEND);
            //执行速度太快，做一个延时，来看执行效果
            sleep(2);
        }

        //打印日志内容
        dd($logs);
    }
}
