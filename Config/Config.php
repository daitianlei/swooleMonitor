<?php namespace Config;

class Config
{
    const SYSTEM_CONFIG_PATH = BASE_PATH . '/Config';
    const GOD_DAEMON_SOCK_PATH = '/tmp/daitianlei.sock';
    
    public static $serverList = array(
        
        'login' => array(
            'cli' => '/usr/local/php/bin/php',
            'iniPath' => '/etc/php/php.ini',
            'cwd' => '/root/swoole/Demos',
            'startScript' => 'SwooleServerDemo.php',
        ),
    );
    
    
}