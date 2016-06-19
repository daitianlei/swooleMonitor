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
     * @param $className
     */
    public static function autoLoad($className)
    {
        require BASE_PATH . '/' . str_replace('\\', '/', $className) . '.php';
    }

}