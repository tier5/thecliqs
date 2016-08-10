<?php

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));  
include APPLICATION_PATH . '/application/modules/Ynsocialads/cli.php'; 


ini_set('display_errors',1);
ini_set('error_reporting',-1);
ini_set('display_startup_error',1);

$params = $_REQUEST;
		$logFile = APPLICATION_PATH . '/temporary/log/paypal-callback.log';
		file_put_contents($logFile, date('c') . ': ' . print_r($params, true), FILE_APPEND);
if($_REQUEST['txn_id'])
{
	$money_req = Engine_Api::_() -> getItem('ynsocialads_moneyrequest', $_REQUEST['money_req']);
	$money_req -> status = 'approved';
	$money_req -> payment_transaction_id = $_REQUEST['txn_id'];
	$money_req -> save();
	
	$virtualTable = Engine_Api::_() -> getItemTable('ynsocialads_virtual');
	$row = $virtualTable -> GetRowByUser($money_req->user_id);
	$row -> total = $row -> total - $_REQUEST['payment_gross'];
	$row -> save();
}
