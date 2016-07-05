<?php

defined('BASE_PATH') or define('BASE_PATH', __DIR__);
include __DIR__ . '/Lib/Loader.php';
spl_autoload_register('\Lib\Loader::autoLoad');
spl_autoload_register('\Lib\Loader::autoLoadThirdParty');
var_dump($argc, $argv);
new Demos\SwooleServerDemo('login');
