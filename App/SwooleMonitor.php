<?php namespace App;
use Config\Config;
use Lib\Log;

/**
 * Created by PhpStorm.
 * User: daitianlei
 * Date: 16/6/19
 * Time: 下午8:52
 */
class SwooleMonitor
{
    private static $monitor;
    private $masterPidPath;
    public function __construct()
    {
        $this->masterPidPath = '/var/run/swoole_test_master.pid';
    }

    /**
     * @return SwooleMonitor
     */
    public static function getInstance()
    {
        if (!isset(static::$monitor)) {
            static::$monitor = new self(); 
        }
        return static::$monitor;
    }
    
    public static function run()
    {
        global $argc, $argv;
        if ($argc != 3) {
            Log::write('failed: params number not match', Log::ERROR);
            SwooleNotice::showUsage();
            return false;
        }
        $serverName = $argv[1];
        // 检查配置文件  
        if (!array_key_exists($serverName, Config::$serverList)) {
            SwooleNotice::showError('server not exist!');
            return false;
        }
        $serverConfig = Config::$serverList[$argv[1]];
        switch ($argv[2]) {
            case 'start':
                static::start($serverName, $serverConfig);
                break;
            case 'stop':
                static::stop($serverName);
                break;
            case 'reload':
                static::reload($serverName);
                break;
            case 'status':
                static::status($serverName);
                break;
            default:
                SwooleNotice::showError('command not support!');
        }
        
        return true;
    }
    
    public static function start($serverName, $serverConfig)
    {
        Log::write("Swoole Monitor Starting ! \n", Log::INFO);
        $pid = static::getInstance()->getMasterPid($serverName);
        if ($pid != false) {
            $isRun = static::status($serverName);
             if ($isRun) {
                    return true; 
             }
        }
        
        return static::getInstance()->startServer($serverName, $serverConfig);
    }
    
    private function noticeDaemonServer($type, $serverName)
    {
        $client = new \swoole_client(SWOOLE_UNIX_STREAM, SWOOLE_SOCK_SYNC);
        $client->connect(Config::GOD_DAEMON_SOCK_PATH, 0, 3);
        $noticeData = json_encode(array('type' => $type, 'serverName' => $serverName));
        $client->send($noticeData);
        Log::write('send notice data: ' . $noticeData, Log::INFO);
        $ret = $client->recv();
        Log::write('recv notice data: ' . $ret, Log::INFO);
        $ret = json_decode($ret, true);
        $client->close();
        return $ret;
    }
    
    private function startServer($serverName, $serverConfig)
    {
        $output = array();
        $retNo = 0;
        $cmd = $this->buildCommand($serverConfig);
        if ($cmd == false) {
            return false; 
        }
        exec($cmd, $output, $retNo);
        if ($retNo != 0) {
            SwooleNotice::showError($serverName . " failed to start the server! \n");
            return false;
        }

        SwooleNotice::showError($serverName . " server started! \n");
        $retAdd = $this->noticeDaemonServer('add', $serverName);
        if ($retAdd) {
            SwooleNotice::showError($serverName . " server notice daemon server ! \n" . json_encode($retAdd));
        }
        return true;
    }
    
    private function buildCommand($serverConfig)
    {
        $systemConfig = ' ';
        if (isset($serverConfig['iniPath']) && !empty($serverConfig['iniPath'])) {
            $systemConfig = ' -c ' . $serverConfig['iniPath'];
        }
        if (!isset($serverConfig['cli']) || empty($serverConfig['cli'])) {
            return false;
        }
        if (!isset($serverConfig['cwd']) || empty($serverConfig['cli'])) {
            $serverConfig['cwd'] = posix_getcwd();
        }

        return $serverConfig['cli'] . $systemConfig . ' ' .$serverConfig['cwd'] . '/'. $serverConfig['startScript'];
    }
    
    private function getMasterPid($processName)
    {
        //TODO: 拿到错误的pid怎么处理?
        if (file_exists($this->masterPidPath)) {
            $pid = file_get_contents($this->masterPidPath); 
        } else {
            Log::write('the master pid not exist');
            return false;
        }
        
        return $pid;
    }
    
    public static function stop($serverName)
    {
        Log::write("Swoole Monitor Starting ! \n", Log::INFO);

        $pid = static::getInstance()->getMasterPid($serverName);
        if ($pid === false) {
            return false;
        }

        if (posix_kill($pid, SIGTERM)) {
            SwooleNotice::showMessage($serverName . ' server stopped !' . PHP_EOL);
            return true;
        } else {
            SwooleNotice::showMessage($serverName . ' server failed to stop!' . PHP_EOL);
            return false;
        }
    }
    
    public static function reload($serverName)
    {
        Log::write("Swoole Monitor Starting ! \n", Log::INFO);
        $pid = static::getInstance()->getMasterPid($serverName);
        if ($pid === false) {
            return false;
        }
        
        // swoole reload signal SIGUSR1
        if (posix_kill($pid, SIGUSR1)) {
            SwooleNotice::showMessage($serverName . ' server reload success!' . PHP_EOL);
            return true;
        } else {
            SwooleNotice::showMessage($serverName . ' server failed to reload!' . PHP_EOL);
            return false;
        }
         
    }
    
    public static function status($serverName)
    {
        Log::write("Swoole Monitor status ! \n", Log::INFO);
        $pid = static::getInstance()->getMasterPid($serverName);
        // get status of the process by send default signal
        if ( posix_kill($pid, SIG_DFL)) {
            SwooleNotice::showMessage($serverName . ' server is running !' . PHP_EOL);
            return true;
        } else {
            SwooleNotice::showMessage($serverName . ' server is not running !' . PHP_EOL);
            return false;
        }
        
    }
}
