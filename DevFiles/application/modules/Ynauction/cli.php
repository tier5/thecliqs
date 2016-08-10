<?php
ini_set('max_execution_time', 3000);
define('_ENGINE_CUR_PATH', dirname(__FILE__));

// Config
if(!defined('_ENGINE_R_MAIN')) {
    define('_ENGINE_R_REWRITE', true);
    define('_ENGINE_R_CONF', true);
    define('_ENGINE_R_INIT', true);
    $indexFile = dirname(dirname(_ENGINE_CUR_PATH)) . DIRECTORY_SEPARATOR . 'index.php';
    //exit($indexFile);

    include_once $indexFile;
}

// Create application, bootstrap, and run

$application = Engine_Api::getInstance()->getApplication();    


$application -> getBootstrap() -> bootstrap('frontcontroller');
$application -> getBootstrap() -> bootstrap('cache');
$application -> getBootstrap() -> bootstrap('db');
$application -> getBootstrap() -> bootstrap('log');
$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('frontcontrollermodules');
$application -> getBootstrap() -> bootstrap('session');
$application -> getBootstrap() -> bootstrap('manifest');
$application -> getBootstrap() -> bootstrap('hooks');
$application -> getBootstrap() -> bootstrap('router');
$application -> getBootstrap() -> bootstrap('view');
$application -> getBootstrap() -> bootstrap('layout');
$application -> getBootstrap() -> bootstrap('modules');
$application -> getBootstrap() -> bootstrap('locale');


