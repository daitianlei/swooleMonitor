<?php namespace App;
use Config\Config;

class SwooleServer
{
    private $server;
    public function __construct()
    {
        $this->server = new \swoole_server(Config::GOD_DAEMON_SOCK_PATH, 0, SWOOLE_BASE, SWOOLE_UNIX_STREAM);
        $this->server->set(array(
            'work_num' => 1,
            'daemonize' => true, 
        ));
        $this->server->on('Start', array($this, 'onStart'));
        $this->server->on('Connect', array($this, 'onConnect'));
        $this->server->on('Receive', array($this, 'onReceive'));
        $this->server->on('Close', array($this, 'onClose'));

        $this->server->start();
    }
    
    public function onStart(\swoole_server $server)
    {
        echo "Start\n";
    }

    public function onConnect(\swoole_server $server, $fd)
    {
        $server->send($fd, "Hello, {$fd}");
    }

    public function onReceive(\swoole_server $server, $fd, $fromId, $data)
    {
    }

    public function onClose(\swoole_server $server, $fd)
    {
        echo "Client {$fd} close connection\n";
    }
}
