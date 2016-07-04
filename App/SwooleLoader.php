<?php namespace App;

/**
 * Class SwooleLoader
 * Note: 暂未使用
 * 加载Swoole 对应的监控程序
 * @package App
 * 
 */
class SwooleLoader
{
    private $sysConfigPath; 
    public function __construct($configPath)
    {
        $this->configPath = $configPath;
    }
    
    public function loadServerConfig()
    {
    }

    /**
     * 读取配置文件
     * @param $path
     * @return mixed
     */
    private function parse($path)
    {
        return json_decode(file_get_contents($path), true); 
    }
}
