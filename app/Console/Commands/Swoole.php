<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Swoole extends Command
{
    /**
     * 服务
     *
     * @var string
     */
    protected $server = null;

    /**
     * The name and signature of the console command.
     * 网址 https://www.swoole.com/
     * @var string
     */
    protected $signature = 'swoole:test {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'swoole测试练习';

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
     * 地址 https://www.jianshu.com/p/4205ba61ebaa
     * @return mixed
     */
    public function handle()
    {
        $arg = $this->argument('action');
        switch($arg)
        {
            case 'start':
                $this->info('swoole observer started');
                $this->start();
                break;
        }
    }

    private function start()
    {
        $server = new \swoole_server("127.0.0.1", 9503);
        $server->on('connect', function ($server, $fd){
            $this->info("connection open: {$fd}\n");
        });
        $server->on('receive', function ($server, $fd, $reactor_id, $data) {
            $server->send($fd, "Swoole: {$data}");
            $server->close($fd);
        });
        $server->on('close', function ($server, $fd) {
            $this->info("connection close: {$fd}\n");
        });
        $server->start();
    }
}
