<?php

namespace App\Http\Controllers;

use Queue;
use App\Jobs\ProcessPodcast;
use Illuminate\Http\Request;

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

    /**
     * 测试1
     * @param Request $request
     * @return string
     */
    public function test1(Request $request)
    {
        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        if (!$client->connect('127.0.0.1', '1215', -1))
        {
            exit("connect failed. Error: {$client->errCode}\n");
        }

        $client->send("hello world\n");
        echo $client->recv();
        $client->close();
        //return view('test');#在你的视图文件夹创建test.blade.php
    }

    /**
     * 测试2
     * @param Request $request
     * @return string
     */
    public function test2(Request $request)
    {
        return 'Hello World2:' . $request->get('name');
    }
}
