<?php

define('DEBUG',false);

include dirname(dirname(dirname(__FILE__))) . '/cli.php';

$ynauction_id = (int)$_REQUEST['product_id'];
$flagStart = (int)$_REQUEST['flagStart'];
$Users =  new User_Model_DbTable_Users;

$pro = Engine_Api::_() ->getItem('ynauction_product', $ynauction_id);
$now = date('Y-m-d H:i:s');
$userDeleted = 0;
if($pro -> bider_id == -1) {
	$userDeleted = 1;

}

$view = Zend_Registry::get('Zend_View');
$bid = Engine_Api::_() -> ynauction() -> getBid($ynauction_id);
$price = 0;
$username = "";
$time = strtotime($pro->end_time) -  time();
$bider_id = 0;
if($bid) {
	$bider = Engine_Api::_() -> getItem('user', $bid -> ynauction_user_id);
	if($bider -> getIdentity() > 0) {

		$username = "<a href='" . $bider -> getHref() . "'>" . $bider -> getTitle() . "</a>";
		$point = strpos($username, "profile/");
		if($point > 0)
		{
			$username = substr($username, $point);
			$username = "<a href='".$username;
		}
	}
	$bider_id = $bid -> ynauction_user_id;
} 
if($pro -> bid_price < $pro -> starting_bidprice){
	$price = $pro -> starting_bidprice;
}	
else{
	$price = $pro -> bid_price;
}
$min_incre = $view -> locale() -> toCurrency($price + $pro ->minimum_increment, $pro->currency_symbol);
if($pro ->maximum_increment <= 0)
    $max_incre = $view->translate("or more");
else
    $max_incre = $view->translate("or up to ").$view -> locale() -> toCurrency($price + $pro ->maximum_increment, $pro->currency_symbol);
$min_incre_num = $price + $pro ->minimum_increment; 
$max_incre_num = $price + $pro ->maximum_increment; 
$price = $view -> locale() -> toCurrency($price,$pro->currency_symbol);
$bids = $pro->total_bids;
if($pro -> status == 0 && $pro -> stop == 0 && $pro -> is_delete == 0 && $pro -> display_home == 1 && $pro->approved == 1) 
{
		if($time > 0) {
			$min = floor($time / 60);
			$sec = $time % 60;
			echo '{"bider_id":"' . $bider_id .'","min":"' . $min . '", "sec":"' . $sec . '","price":"' . $price . '","min_incre":"' . $min_incre. '","max_incre":"' . $max_incre. '","max_incre_num":"' . $max_incre_num . '","min_incre_num":"' . $min_incre_num. '","username":"' . $username . '","flag":"0","bids":"' . $bids . '","userDelete":"' . $userDeleted . '","proposal":"'. $pro->proposal .'"}';
		} else
			echo '{"bider_id":"' . $bider_id .'","min":"0", "sec":"0","price":"' . $price . '","min_incre":"' . $min_incre . '","max_incre":"' . $max_incre. '","max_incre_num":"' . $max_incre_num . '","min_incre_num":"' . $min_incre_num. '","username":"' . $username . '","flag":"0","bids":"' . $bids . '","userDelete":"' . $userDeleted .'","proposal":"'. $pro->proposal . '"}';
} else {
	echo '{"bider_id":"' . $bider_id .'","min":"0", "sec":"0","price":"' . $price . '","min_incre":"' . $min_incre . '","max_incre":"' . $max_incre. '","max_incre_num":"' . $max_incre_num . '","min_incre_num":"' . $min_incre_num. '","username":"' . $username . '","flag":"0","bids":"' . $bids . '","remove":"1","userDelete":"' . $userDeleted .'","proposal":"'. $pro->proposal . '"}';
}  
