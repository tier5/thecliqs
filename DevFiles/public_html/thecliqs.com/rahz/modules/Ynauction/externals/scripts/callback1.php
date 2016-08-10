<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));  
include APPLICATION_PATH . '/application/modules/Ynauction/cli.php'; 

$logger = new Zend_Log(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/auction-callback1.'.date('Y-m-d').'.log'));
$logger->log(var_export($_REQUEST,true),Zend_Log::DEBUG);

ini_set('display_errors',1);
ini_set('error_reporting',-1);
ini_set('display_startup_error',1);

try
{         
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
            $Bills  =  new Ynauction_Model_DbTable_Bills;
            $select =  $Bills->select()->where('sercurity=?', $req4)->where('invoice=?',$req5);
            $bill =  $Bills->fetchRow($select);
                if($bill->bill_status == 0 
                && ($status == 'COMPLETED' ||$payment_status =='Completed')
                && $payer_status =='verified'
                && ($bill->amount == $payment_gross || $bill->amount == $mc_gross)
                && $bill->emal_receiver == $receiver_email
                && $bill->currency == $mc_currency)
                {
                     //update status of bill
                    Zend_Registry::get('Zend_Log')->log(print_r('vao 1',true),Zend_Log::DEBUG);
                    updateBillStatus($bill,1);
                    Zend_Registry::get('Zend_Log')->log(print_r('vao 12',true),Zend_Log::DEBUG);
                    updateDisplay($bill);
                    Zend_Registry::get('Zend_Log')->log(print_r('vao 13',true),Zend_Log::DEBUG);
                    $bill->bill_status = 1;
                     //saveTracking
                    Zend_Registry::get('Zend_Log')->log(print_r('vao 14',true),Zend_Log::DEBUG);
                    saveTrackingPayIn($bill); 
                    Zend_Registry::get('Zend_Log')->log(print_r('vao 15',true),Zend_Log::DEBUG);
                    /**
                     * Call Event from Affiliate
                     */
                    
                    $module = 'ynaffiliate';
                    Zend_Registry::get('Zend_Log')->log(print_r('start call event',true),Zend_Log::DEBUG);
                    $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
                        $mselect = $modulesTable->select()
                        ->where('enabled = ?', 1)
                        ->where('name  = ?', $module);
                    $module_result = $modulesTable->fetchRow($mselect);
                    if(count($module_result) > 0)    {
                        Zend_Registry::get('Zend_Log')->log(print_r('count vo',true),Zend_Log::DEBUG);
                        $params['module'] = 'ynauction';
                        $params['user_id'] = $bill->user_id;
                        $params['rule_name'] = 'publish_ynauction';
                        $params['currency'] = $bill->currency;
                        $params['total_amount'] = number_format($bill->amount,2);
                        Zend_Registry::get('Zend_Log')->log(print_r($params,true),Zend_Log::DEBUG);
                        Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
                    }
                    
                    /**
                     * End Call Event from Affiliate
                     */                 
                       

                }     
       break;
       default:
            die('No action');
   }
}
catch(Exception $e){
    $logger->log($e->getMessage(),Zend_Log::ERR);
    //throw $e;
}

function getFinanceAccount($user_id = null,$payment_type = null)
{
    $Table =  new Ynauction_Model_DbTable_PaymentAccounts;
    $select = $Table->select();
    
    if($user_id)
    {
        $select->where('user_id=?', $user_id);
    }
    if($payment_type)
    {
        $select->where('payment_type=?', $payment_type);
    }
        
    $account =  $Table->fetchRow($select);
    
    // check is there finnance account
    if(!is_object($account)){
        throw new Exception("payment account does not exists");
    }
    return $account;
}
function saveTrackingPayIn($bill)
{
    // buyer account
    $account = getFinanceAccount($bill->user_id,2);
    // seller account.  
    $accSell = getFinanceAccount($bill->owner_id,1);
    $table  =  new Ynauction_Model_DbTable_TransactionTrackings;
    $item =    $table->fetchNew();
    // them transaction tracking
    $item->transaction_date =   $bill->date_bill;
    $item->user_seller = $bill->owner_id;
    $item->user_buyer  = $bill->user_id;
    $item->item_id     = $bill->item_id;
    $item->amount      = $bill->amount;
    $item->account_seller_id = $accSell->paymentaccount_id;
    $item->account_buyer_id  = $account->paymentaccount_id;
    $item->transaction_status = 1;
    $item->type = 0;
    $item->params   = sprintf('fee');
    $item->save();
    return $item;
}
function updateBillStatus($bill,$status)
{
    $bill->bill_status = $status;
    $bill->save();
}
function updateDisplay($bill)
{ 
    $viewer = Engine_Api::_()->user()->getViewer();
    $auction =  Engine_Api::_()->getItem('ynauction_product',$bill->item_id);
    if(!is_object($auction)){
        throw new Exception ("the auction does not found!");
    }
    $auction->display_home = 1; 
    if($bill->auto_approve == 1)
    {
        $auction->approved = 1;
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $auction, 'ynauction_new');
        if( $action != null ) {
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $auction);
        }  
    }
    $auction->save();
    return $auction;
}
?>
