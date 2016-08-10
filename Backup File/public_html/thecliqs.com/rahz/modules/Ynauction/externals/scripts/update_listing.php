<?php

define('DEBUG',false);

include dirname(dirname(dirname(__FILE__))) . '/cli.php';

$strynauctions = $_REQUEST['products'];
$arrynauctions  = split(',',$strynauctions);
$Users =  new User_Model_DbTable_Users;
$arrJsons = array();
for($i = 0; $i < count($arrynauctions) - 1; $i ++)
{
    $arrynauction = split(';',$arrynauctions[$i]);
    $ynauction_id = $arrynauction[1];
    $flagStart = $arrynauction[2];
    $pro = Engine_Api::_() -> getItem('product', $ynauction_id);
    $now = date('Y-m-d H:i:s');
    $userDeleted = 0;
    if($pro -> bider_id == -1) {
	    $userDeleted = 1;

    }
    $bid = Engine_Api::_() -> ynauction() -> getBid($ynauction_id);
    $price = 0;
    $username = "";
    $time = 0;
    if($bid) {
	    $timebid = $bid -> bid_time;
	    $subtime = time() - strtotime($timebid);
	    $time = $pro -> bid_time - $subtime % $pro -> bid_time;
	    $bider = Engine_Api::_() -> getItem('user', $bid -> ynauction_user_id);
	    if($bider -> getIdentity() > 0) {

		    $username = '<a href=\"' . $bider -> getHref() . '\">' . $bider -> username . '</a>';
	    }
    } else {
	    $time = 0;
    }
    if($pro -> bid_price < $pro -> starting_bidprice){
	    $price = $pro -> starting_bidprice;
    }	
    else{
	    $price = $pro -> bid_price;
    }
    $view = Zend_Registry::get('Zend_View');
    $price = $view -> locale() -> toCurrency($price, Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynauction.currency', 'USD'));
    $his = "";
    $lasts = Engine_Api::_() -> ynauction() -> getBidHis($pro -> product_id, 10);
    foreach($lasts as $last) {
	    $bider = $Users -> find($last -> ynauction_user_id)->current();

	    $his .= '<div class=\"ynauction_userbid\">' . $bider -> username . '<span class = \"ynauction_datetime\">' . $view -> locale() -> toDateTime($last -> bid_time) . '</span></div>';
    }


    if($pro -> status == 0 && $pro -> stop == 0 && $pro -> is_delete == 0 && $pro -> display_home == 1) {
	    if($pro -> start_time > $now) {
		    $start_time = $pro -> start_time;
		    $time = strtotime($start_time) - time(); ;
		    $min = floor($time / 60);
		    $sec = $time % 60;
            $data = array(
                            'min' => $min,
                            'sec' => $sec,
                            'price' => $price,
                            'username' => $username,
                            'flag' => 1,
                            'his' => $his,
                            'userDelete' => $userDeleted,
                            'index' => $arrynauction[0]
                        );
	    } else {
		    if($time > 0) {
			    $min = floor($time / 60);
			    $sec = $time % 60;
                $data = array(
                            'min' => $min,
                            'sec' => $sec,
                            'price' => $price,
                            'username' => $username,
                            'flag' => 0,
                            'his' => $his,
                            'userDelete' => $userDeleted,
                            'index' => $arrynauction[0] 
                        );
		    } else
            {
                $data = array(
                            'min' => 0,
                            'sec' => 0,
                            'price' => $price,
                            'username' => $username,
                            'flag' => 0,
                            'his' => $his,
                            'userDelete' => $userDeleted,
                            'index' => $arrynauction[0] 
                        );
            }
	    }
    } else {
        $data = array(
                            'min' => 0,
                            'sec' => 0,
                            'price' => $price,
                            'username' => $username,
                            'flag' => 0,
                            'his' => $his,
                            'remove' => 1,
                            'userDelete' => $userDeleted,
                            'index' => $arrynauction[0] 
                        );
    } 
    $arrJsons[] = $data;
}
echo Zend_Json::encode($arrJsons);    
