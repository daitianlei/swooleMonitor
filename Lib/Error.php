<?php namespace Lib;

class Error
{
    const SUCCESS = 0;
    const SYSTEM_ERR = 100001;
    const PARAMS_ERR = 100002;
    
    const SERVER_EXIST = 200001;
    const SERVER_NOT_EXIST = 200002;
    
    public static $errMsg = array(
        self::SUCCESS => 'success',
        self::SYSTEM_ERR => 'system error',
        self::PARAMS_ERR => 'params error',
        
        self::SERVER_EXIST => 'server exist',
        self::SERVER_NOT_EXIST => 'server not exist',
    );
}
