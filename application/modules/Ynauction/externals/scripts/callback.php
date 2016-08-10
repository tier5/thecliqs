<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));  
include APPLICATION_PATH . '/application/modules/Ynauction/cli.php'; 

$logger = new Zend_Log(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/auction-callback.'.date('Y-m-d').'.log'));
$logger->log(var_export($_REQUEST,true),Zend_Log::DEBUG);

$file = APPLICATION_PATH . '/application/settings/database.php';
$options = include $file;
$db =  $options['params'];

    $connection = mysql_connect($db['host'], $db['username'], $db['password']);
    $prefix = $options['tablePrefix'];
    if (!$connection)
        die("can't connect server");

    $db_selected = mysql_select_db($db['dbname']);
    if (!$db_selected)
        die ("have not database");
   mysql_query("SET character_set_client=utf8", $connection);
        mysql_query("SET character_set_connection=utf8",  $connection);    
   $action = $_REQUEST['action'];   
   switch($action)
   {
       case 'callback':
            
            $req4 = @mysql_escape_string($_REQUEST['req4']);
            $req5 = @mysql_escape_string($_REQUEST['req5']);
            $status = @mysql_escape_string($_REQUEST['status']);
            $payer_status = @mysql_escape_string(($_REQUEST['payer_status']));
            $payment_status = @mysql_escape_string(($_REQUEST['payment_status']));
            $payment_gross = @mysql_escape_string(($_REQUEST['payment_gross']));
            $mc_gross = @mysql_escape_string(($_REQUEST['mc_gross']));
            $mc_currency = @mysql_escape_string(($_REQUEST['mc_currency']));
            $receiver_email = @mysql_escape_string(($_REQUEST['receiver_email']));
            //get bill
            $sql = "SELECT * FROM ".$prefix."ynauction_bills WHERE "
                   ."sercurity ='".$req4 ."' AND invoice = '".$req5."'"
                   ." LIMIT 0,1"    
                   ;
            
            $result = mysql_query($sql) or die(mysql_error()."<b>SQL was: </b>$sql");
            if($result)
            {
                $billtmp = mysql_fetch_row($result);
                $bill = array(
                        'bill_id'=>$billtmp[0],
                        'invoice'=>$billtmp[1],
                        'sercurity'=>$billtmp[2],
                        'user_id'=>$billtmp[3],
                        'finance_account_id'=>$billtmp[4],
                        'emal_receiver'=>$billtmp[5],
                        'payment_receiver_id'=>$billtmp[6],
                        'date_bill'=>$billtmp[7],
                        'bill_status'=>$billtmp[8],
                        'item_id'=>$billtmp[9],
                        'amount'=>$billtmp[10],
                        'owner_id'=>$billtmp[11],      
                        'currency'=>$billtmp[12],      
                        'type'=>$billtmp[13]      
                        );
                if($bill['bill_status'] == 0 
                && ($status == 'COMPLETED' || $payment_status =='Completed') 
                && $payer_status =='verified'
                && ($bill['amount'] == $payment_gross || $bill['amount'] == $mc_gross)  
                && $bill['emal_receiver'] == $receiver_email
                && $bill['currency'] == $mc_currency)
                {
                     //update status of bill
                    updateBillStatus($prefix,$bill,1);
                    if($bill['type'] == 1)
                    {
                        updateSold($bill,$prefix,2);
                    }
                    if($bill['type'] == 4)
                    {
                        updateSold($bill,$prefix,3);
                    }
                    $bill['bill_status'] = 1;
                     //saveTracking
                    saveTrackingPayIn($bill,$prefix); 
                    /** 
                    * Call Event from Affiliate
                     */
                    
                    $module = 'ynaffiliate';
                    $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
                        $mselect = $modulesTable->select()
                        ->where('enabled = ?', 1)
                        ->where('name  = ?', $module);
                    $module_result = $modulesTable->fetchRow($mselect);
                    if(count($module_result) > 0)    {
                        $params['module'] = 'ynauction';
                        $params['user_id'] = $bill['user_id'];
                        $params['rule_name'] = 'buy_ynauction';
                        $params['currency'] = $bill['currency'];
                        $params['total_amount'] = number_format($bill['amount'],2);
                        Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
                    }
                    /**
                     * End Call Event from Affiliate
                     */ 
                    // User credit integration
                    $module = 'yncredit';
                    $mselect = $modulesTable->select()->where('enabled = ?', 1)->where('name  = ?', $module);
                    $module_result = $modulesTable->fetchRow($mselect);
                    if(count($module_result) > 0)    
                    {
                       $params['rule_name'] = 'ynauction_buy';
					   $product = Engine_Api::_()->getItem('ynauction_product', $bill['item_id']);
                       $params['item_id'] = $product -> getIdentity();
                       $params['item_type'] = $product -> getType();
                       Engine_Hooks_Dispatcher::getInstance()->callEvent('onPurchaseItemAfter', $params);
                    } 
                }
                
            }
            
       break;
       default:
            die('No action');
}

function getFinanceAccount($user_id = null,$payment_type = null,$prefix)
{
    $query = "1 AND 1";
    if($user_id)
    {
        $query .= " AND user_id = ".$user_id ;
    }
    if($payment_type)
    {
        $query .=" AND payment_type = ".$payment_type;    
    }
    
    $sql = "SELECT * FROM ".$prefix."ynauction_payment_accounts WHERE "
                   .$query
                   ." LIMIT 0,1"    
                   ;

            $result = mysql_query($sql) or die(mysql_error()."<b>SQL was: </b>$sql");
            if($result)
            {
                $acc1 = mysql_fetch_row($result);
                $acc = array(
                    'paymentaccount_id'=>$acc1[0],
                    'account_username'=>$acc1[1],
                    'account_password'=>$acc1[2],
                    'user_id'=>$acc1[3],
                    'payment_type'=>$acc1[4],
                    'last_check_out'=>$acc1[5],
                    'account_status'=>$acc1[6],
                    );
                return $acc;
            }
    return null;
}
function saveTrackingPayIn($bill,$prefix)
{
        if($bill['type'] == 4)
        {
            $approve = 1;
        }
        else
        {
            $approve = 0;
        }
        $acc = getFinanceAccount($bill['user_id'],2,$prefix);  
        $accSell = getFinanceAccount($bill['owner_id'],2,$prefix); 
        $sql_insert = "INSERT INTO ".$prefix."ynauction_transaction_trackings"
                    ."(transaction_date,user_seller,user_buyer,item_id,
                       amount,account_seller_id,account_buyer_id,transaction_status,type,approved,params) VALUES "
                    ."("
                    .$bill['date_bill']
                    .",'".$bill['owner_id']."'"
                    .",'".$bill['user_id']."'"
                    .",'".$bill['item_id']."'"
                    .",'".$bill['amount']."'"
                    .",'".$accSell['paymentaccount_id']."'"
                    .",'".$acc['paymentaccount_id']."'"
                    .",'".$bill['bill_status']."'"
                    .",".$bill['type']
                    .",".$approve
                    .",'"."buy"."'" 
                    . ")";
        $result =  mysql_query($sql_insert); 
        if (!$result) {
            echo ('Invalid query: ' . mysql_error());
        }
}
function updateBillStatus($prefix,$bill,$status)
{
    $sql_update = "UPDATE ".$prefix."ynauction_bills SET"
                  ." `bill_status` = '1'"
                  ." WHERE `bill_id` = ".$bill['bill_id']
                  ;
    
   $result =  mysql_query($sql_update); 
   if (!$result) {
       echo ('Invalid query: ' . mysql_error());
   }
    
}
function updateSold($bill,$prefix,$status)
{ 
   $sql_update = "UPDATE ".$prefix."ynauction_products SET"
                  ." `status` = '".$status."',"
                  ." `stop` = '1'," 
                  ." `bid_price` = '".$bill['amount'] ."' ,"
                  ." `bider_id` = '".$bill['user_id'] ."' "
                  ." WHERE `product_id` = ".$bill['item_id']
                  ; 
    $result =  mysql_query($sql_update); 
    if (!$result) {
        echo ('Invalid query: ' . mysql_error());
    }

}

?>
