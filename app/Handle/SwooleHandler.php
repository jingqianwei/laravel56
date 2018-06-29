<?php
/**
 * Created by PhpStorm.
 * User: chinwe
 * Date: 18-6-24
 * Time: 上午3:48
 */

namespace App\Handle;


class SwooleHandler
{
    /**
     * 开始连接
     *
     * @param $server
     */
    public function onStart($server)
    {

    }

    /**
     * 监听连接
     *
     * @param $server
     * @param $fd
     */
    public function onConnect($server, $fd)
    {

    }

    /**
     * 监听事件
     *
     * @param $server
     * @param $fd
     * @param $from_id
     * @param $data
     */
    public function onReceive($server, $fd, $from_id, $data)
    {

    }

    /**
     * 关闭连接
     *
     * @param $server
     * @param $fd
     */
    public function onClose($server, $fd)
    {

    }
}