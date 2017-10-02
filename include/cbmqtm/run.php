#!/usr/bin/php
<?php
ini_set('display_errors', 0);

// Define a simple Auto Loader:
// Add the current application and the PHP Simple Daemon ./Core library to the existing include path
// Then set an __autoload function that uses Zend Framework naming conventions.
define("BASE_PATH", __DIR__);
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(BASE_PATH),
    realpath(BASE_PATH . '/Core'),
    get_include_path(),
)));

function __autoload($class_name)
{
    $class_name = str_replace('\\', '/', $class_name);
    $class_name = str_replace('_', '/', $class_name);
    require_once "$class_name.php";
}

function pathify($class_name) {
    return str_replace("_", "/", $class_name) . ".php";
}

require_once 'error_handlers.php';
include_once('include/cbmqtm/cbmqtm_dispatcher.php');

// The run() method will start the daemon loop.
coreBOS_MQTMDispatcher::getInstance()->run();
