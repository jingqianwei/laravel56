<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class GetMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:message';  // 这里是生成命令的名称

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '这里是这个命令的描述';

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
        // 这里写具体代码,这里可以创建一个配置文件，然后使用配置文件控制消息队列的配置信息
        $queue = 'hello'; //跟存入的数据要一致
        $connection = new AMQPStreamConnection('localhost', '5672', 'admin', 'admin');
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $message = $channel->basic_get($queue);
        $channel->basic_ack($message->delivery_info['delivery_tag']);
        if ($message->body) {
            print_r($message->body);
            \Log::info('queueInfo:' . $message->body);
        }
        $channel->close();
        $connection->close();
    }

    public function getMessage()
    {
        $exchange = 'router';
        $queue = 'test';
        $connection = new AMQPStreamConnection('localhost', '5672', 'admin', 'admin');
        $channel = $connection->channel();
        $message = $channel->basic_get($queue); //取出消息
        print_r($message->body);
        $channel->basic_ack($message->delivery_info['delivery_tag']); // 确认取出消息后会发送一个ack来确认取出来了，然后会从rabbitmq中将这个消息移除，如果删掉这段代码，会发现rabbitmq中的消息还是没有减少
        $channel->close();
        $connection->close();
    }
}
