<?php namespace Demos;
class SwooleServerDemo
{
    private $server;
    private $serverName;
    public function __construct($serverName)
    {
        $this->serverName = $serverName;
        $this->server = new \swoole_server("127.0.0.1", 9501);
        $this->server->set(array(
            'worker_num' => 2,
            'daemonize' => true,
        ));
        $this->server->on('Start', array($this, 'onStart'));
        $this->server->on('ManagerStart', array($this, 'onManagerStart'));
        $this->server->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->server->on('Connect', array($this, 'onConnect'));
        $this->server->on('Receive', array($this, 'onReceive'));
        $this->server->on('Close', array($this, 'onClose'));
        $this->server->start();
    }

    public function onStart(\swoole_server $server)
    {
        swoole_set_process_name( $this->serverName . ' master');
        file_put_contents('/var/run/swoole_test_master.pid' , $server->master_pid );
    }
    public function onManagerStart(\swoole_server $serv)
    {
        swoole_set_process_name($this->serverName . ' manager');
    }
    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
        swoole_set_process_name($this->serverName . ' worker');
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

