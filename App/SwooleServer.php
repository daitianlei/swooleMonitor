<?php namespace App;
use Config\Config;
use Lib\Error;

class SwooleServer
{
    private $server;
    private static $instance;
    private static $serverList = array();
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
        $recvData = json_decode($data, true);
        if (!isset($recvData['type'])  || empty($recvData['type']) ||
            !isset($recvData['serverName']) || empty($recvData['serverName'])) {
            
            $server->send($fd, json_encode(['code' => Error::PARAMS_ERR, 'message' => Error::$errMsg[Error::PARAMS_ERR]]));
            $server->close($fd);
            return true;
        }
        
        if ($recvData['type'] == 'add') {
            if (in_array($recvData['serverName'], $server->serverList)) {
                $server->send($fd, json_encode(['code' => Error::SERVER_EXIST, 'message' => Error::$errMsg[Error::SERVER_EXIST]])); 
            } else {
                $server->serverList[] = $recvData['serverName'];
                $server->send($fd, json_encode(['code' => Error::SUCCESS, 'message' => Error::$errMsg[Error::SUCCESS]]));
            }
        } elseif ($recvData['type'] == 'delete') {
            if (in_array($recvData['serverName'], $server->serverList)) {
                unset($server->serverList[$recvData['serverName']]);
                $server->send($fd, json_encode(['code' => Error::SUCCESS, 'message' => Error::$errMsg[Error::SUCCESS]]));
            } else {
                $server->send($fd, json_encode(['code' => Error::SERVER_NOT_EXIST, 'message' => Error::$errMsg[Error::SERVER_NOT_EXIST]]));
            }
        } else {
            $server->send($fd, json_encode(['code' => Error::PARAMS_ERR, 'message' => Error::$errMsg[Error::PARAMS_ERR]]));
        }
        
        $server->close($fd);
        return true;
    }

    public function onClose(\swoole_server $server, $fd)
    {
        echo "Client {$fd} close connection\n";
    }
}
