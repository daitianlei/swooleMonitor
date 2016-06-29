<?php namespace App;
use Lib\Log;

/**
 * Created by PhpStorm.
 * User: daitianlei
 * Date: 16/6/19
 * Time: 下午8:52
 */
class SwooleMonitor
{
    public function __construct()
    {
    }
    
    public static function start(\swoole_process $worker)
    {
        Log::write("Swoole Monitor Starting ! \n", Log::INFO);
        $worker->exec('php', '-v');
    }
}
