<?php namespace App;

class SwooleTimer
{
    public function __construct()
    {
        echo "swoole timer start! ";
    }

    /**
     *
     */
    public static function test()
    {
        $str = 'Say ';
        swoole_timer_after(1000, function($timer_id, $params) use ($str) {
            echo $str . $timer_id. $params;
        }, ' You');
    }
}
