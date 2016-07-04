<?php namespace App;
/**
 * Class SwooleTimer
 * Note: 暂未使用
 * @package App
 */
class SwooleTimer
{
    private static $timers;
    private static $swooleTimer;
    private function __construct()
    {
    }
    
    public static function getInstance()
    {
        if (!isset(static::$swooleTimer)) {
            static::$swooleTimer = new self();
        }
        
        return static::$swooleTimer;
    }

}
