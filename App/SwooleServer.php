<?php namespace App;
use Config\Config;
use Lib\Error;
use Lib\Log;

class SwooleServer
{
    const SERVER_LIST_HEART_BEAT = 1000; // ms
    private $server;
    public function __construct()
    {
        $this->server = new \swoole_server(Config::GOD_DAEMON_SOCK_PATH, 0, SWOOLE_BASE, SWOOLE_UNIX_STREAM);
        $this->server->set(array(
            'work_num' => 1,
            'daemonize' => true,
            'server_cmd' => array('add', 'delete'),
            'daemon_cmd' => array('ping'),
        ));
        $this->server->on('Start', array($this, 'onStart'));
        $this->server->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->server->on('Connect', array($this, 'onConnect'));
        $this->server->on('Receive', array($this, 'onReceive'));
        $this->server->on('Close', array($this, 'onClose'));

        $this->server->start();
    }
    
    public function onStart(\swoole_server $server)
    {
        swoole_set_process_name('swoole daemon monitor server');
        Log::write("server start");
    }
    
    public function onWorkerStart(\swoole_server $server)
    {
        if (!$server->taskworker) {
            $server->tick(self::SERVER_LIST_HEART_BEAT, function() use ($server) {
                if (isset($server->serverList) && count($server->serverList) > 0) {
                    foreach ($server->serverList as $serv) {
                        if (!SwooleMonitor::status($serv)) {
                             SwooleMonitor::start($serv);
                        }
                    }
                }
            }); 
        }
    }

    public function onConnect(\swoole_server $server, $fd)
    {
        Log::write(sprintf('client connected ! fd:[%d]', $fd));
    }
    private function cmdReceivedAvailable(\swoole_server $server, $recvData)
    {
        if (isset($recvData['type']) && !empty($recvData['type'])) {
            if (in_array($recvData['type'], $server->setting['daemon_cmd'])) {
                return true;
            } else if (in_array($recvData['type'], $server->setting['server_cmd']) && 
                isset($recvData['serverName']) && !empty($recvData['serverName'])) {
                return true; 
            }
        }
        return false;
    }
    public function onReceive(\swoole_server $server, $fd, $fromId, $data)
    {
        Log::write(sprintf('receive from fd: [%d] data: [%s]', $fd, $data));
        if (!isset($server->serverList)) {
            $server->serverList= [];
        }
        
        $recvData = json_decode($data, true);
        if (!$this->cmdReceivedAvailable($server, $recvData)) {
            $response = ['code' => Error::PARAMS_ERR, 'message' => Error::$errMsg[Error::PARAMS_ERR]];
        } else {
            // !isset($recvData['serverName']) || empty($recvData['serverName'])
            if ($recvData['type'] == 'add') {
                if (in_array($recvData['serverName'], $server->serverList)) {
                    $response = ['code' => Error::SERVER_EXIST, 'message' => Error::$errMsg[Error::SERVER_EXIST]];
                } else {
                    $server->serverList[] = $recvData['serverName'];
                    $response = ['code' => Error::SUCCESS, 'message' => Error::$errMsg[Error::SUCCESS]];
                }
            } elseif (strcmp($recvData['type'], 'delete') == 0) {
                if (in_array($recvData['serverName'], $server->serverList)) {
                    unset($server->serverList[$recvData['serverName']]);
                    $response = ['code' => Error::SUCCESS, 'message' => Error::$errMsg[Error::SUCCESS]];
                } else {
                    $response = ['code' => Error::SERVER_NOT_EXIST, 'message' => Error::$errMsg[Error::SERVER_NOT_EXIST]];
                }
                
            } elseif (strcmp($recvData['type'], 'ping') == 0) {
                $response = ['code' => Error::SUCCESS, 'message' => Error::$errMsg[Error::SUCCESS]];
            } else {
                $response = ['code' => Error::PARAMS_ERR, 'message' => Error::$errMsg[Error::PARAMS_ERR]];
            }
        }
        
        $server->send($fd, json_encode($response)); 
        $server->close($fd);
        return true;
    }

    public function onClose(\swoole_server $server, $fd)
    {
        Log::write("Client {$fd} close connection\n");
    }
}
