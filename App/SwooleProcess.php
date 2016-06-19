<?php namespace App;

class SwooleProcess
{
    private $daemonProcess;
    public function __construct()
    {
        $this->daemonProcess = new \swoole_process(function(\swoole_process $worker){
        });
    }
}
