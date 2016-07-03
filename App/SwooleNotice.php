<?php namespace App;

class SwooleNotice
{
    
    public static function showError($errMsg)
    {
        echo "Error: " . PHP_EOL;
        echo "\t $errMsg". PHP_EOL;
    }
    
    public static function showMessage($message)
    {
        echo "Error: " . PHP_EOL;
        echo "\t $message". PHP_EOL;
    }
    
    public static function showUsage()
    {
        echo "Usage: " . PHP_EOL;
        echo "\t php index.php serverName Operation " . PHP_EOL;
    }
}
