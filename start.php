<?php

defined('BASE_PATH') or define('BASE_PATH', __DIR__);
include __DIR__ . '/Lib/Loader.php';
spl_autoload_register('\Lib\Loader::autoLoad');
include __DIR__ . '/vendor/autoload.php';
var_dump($argc, $argv);
new Demos\SwooleServerDemo('login');