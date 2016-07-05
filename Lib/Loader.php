<?php namespace Lib;
/**
 * Created by PhpStorm.
 * User: daitianlei
 * Date: 16/6/19
 * Time: 下午8:53
 */
class Loader
{
    /**
     * load local file
     * @param $className
     */
    public static function autoLoad($className)
    {
        $loadingFile = BASE_PATH . '/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($loadingFile)) {
            require $loadingFile;
        }
    }
    
    public static function autoLoadThirdParty($className)
    {
        $loadingFile = BASE_PATH . '/ThirdParty/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($loadingFile)) {
            require $loadingFile;
        }
    }

}