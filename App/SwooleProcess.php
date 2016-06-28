<?php namespace App;

use Lib\Log;
use Monolog\Logger;

class SwooleProcess
{
    private $daemonProcess;

    /**
     * SwooleProcess constructor.
     */
    public function __construct()
    {
        swoole_set_process_name('swoole daemon');
        $this->daemonProcess = new \swoole_process('\App\SwooleMonitor::start', true);
        $this->daemonProcess->start();
        $ret = \swoole_process::wait();
        Log::write(sprintf('swoole process wait ret: [%s]', var_export($ret, true)), Logger::INFO);
        \swoole_process::daemon(true, true);
    }
}
