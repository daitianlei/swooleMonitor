<?php namespace Lib;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class Log
{
    const DEBUG = Logger::DEBUG;
    const ERROR = Logger::ERROR;
    const INFO  = Logger::INFO;
    private $_log;
    private $_logHandler;
    
    private static $_instance;

    private function __construct()
    {
        $this->_log = new Logger('Monolog');
        $this->_logHandler = new StreamHandler(__DIR__ . '/../Log/swooleMonitor.log', Logger::DEBUG);
        $this->_log->pushHandler($this->_logHandler);
    }
    
    private static function getInstance()
    {
        if (!isset(static::$_instance)) {
            static::$_instance = new self();
        }
        return static::$_instance;
    }

    public static function write($msg, $level=Logger::DEBUG)
    {
        static::getInstance()->_log->log($level, $msg);
    }
}
