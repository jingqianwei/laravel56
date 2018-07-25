<?php

namespace App\Http\Controllers;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class SendController extends Controller
{
    public function storeSend()
    {
        $queue = 'hello';
        $connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin');
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);


        $msg = new AMQPMessage('Hello World!');
        $channel->basic_publish($msg, '', 'hello');

        echo " [x] Sent 'Hello World!'\n";

        $channel->close();
        $connection->close();
    }

    public function amqMessage()
    {
        $exchange = 'router'; // 交换器，在我理解，如果两个队列使用一个交换器就代表着两个队列是同步的，这个队列里存在的消息，在另一个队列里也会存在
        $queue = 'test'; // 队列名称
        $connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin'); // 创建连接
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, 'direct', false, true, false);
        $channel->queue_bind($queue, $exchange); // 队列和交换器绑定
        $messageBody = 'hello world！'; // 要推送的消息
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange); // 推送消息
        $channel->close();
        $connection->close();

    }
}
