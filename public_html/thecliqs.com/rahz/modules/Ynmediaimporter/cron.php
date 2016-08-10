<?php

/**
 * @link
 * http://worcesterwideweb.com/2008/03/17/php-5-and-imagecreatefromjpeg-recoverable-error-premature-end-of-jpeg-file/
 */
ini_set('gd.jpeg_ignore_warning', 1);

ini_set('display_startup_errors', 0);
ini_set('display_errors', 0);
ini_set('error_reporting', E_ALL);

$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('locale');
$view = Zend_Registry::get('Zend_View');

/**
 * following step to speed up & beat performance
 * 1. check album limit
 * 2. check quota limit
 * 3. get nodes of this schedulers
 * 4. get all items of current schedulers.
 * 5. process each node
 * 5.1 check required quota
 * 5.2 fetch data to pubic file
 * 5.3 store to file model
 * 6. check status of schedulers, if scheduler is completed == (remaining == 0)
 * 6.1 udpate feed and message.
 */

/**
 * Unlimited time.
 */
set_time_limit(0);

/**
 * default 20
 * @var int
 */
$api = Engine_Api::_()->getApi('settings','core');
$limitUserPerCron = intval($api->getSetting('ynmediaimporter.numberphoto',20));

/**
 * default 100
 * @var int
 */
$limitQueuePerCron = intval($api->getSetting('ynmediaimporter.numberqueue',100));;

/**
 * process number queue.
 */
$tableScheduler = Engine_Api::_() -> getDbTable('Schedulers', 'Ynmediaimporter');

/**
 * get scheduler from tables data.
 */
$select = $tableScheduler -> select() -> where('status<3') -> order('last_run') -> limit($limitQueuePerCron);

$schedulers = $tableScheduler -> fetchAll($select);

foreach ($schedulers as $scheduler)
{
    Ynmediaimporter::processScheduler($scheduler, 0, $limitUserPerCron, 1, 0);
}

echo "success!";
exit(0);
