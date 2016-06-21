<?php namespace Demos;
class SwooleServerDemo
{
    private $server;
    public function __construct()
    {
        $this->server = new \swoole_server("127.0.0.1", 9501);
        $this->server->set(array(
            'worker_num' => 4,
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
        echo "Get message from client: {$fd}: {$data} \n";
    }
    
    public function onClose(\swoole_server $server, $fd)
    {
        echo "Client {$fd} close connection\n";
    }
}

