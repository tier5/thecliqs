<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     Ynauction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Cart.php
 * @author     Minh Nguyen
 */
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(__FILE__))))));
include_once  APPLICATION_PATH . '/application/modules/Ynauction/externals/scripts/cart/gateway.php';
class Ynauction_Api_Cart extends Core_Api_Abstract
{
     /**
     * get all transaction from date to date
     * 
     * @param mixed $user_id
     * @param mixed $fromDate
     * @param mixed $toDate
     * @param mixed $params
     * @return mixed
     */
     public function getTrackingTransaction($params)
    {
        $trackingPaginator = Zend_Paginator::factory(Ynauction_Api_Cart::getSelectTrackingTransaction($params));
        if( !empty($params['page']) )
        {
          $trackingPaginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $trackingPaginator->setItemCountPerPage($params['limit']);
        }   
        return $trackingPaginator;
    }
     public function getSelectTrackingTransaction($params)
    {
        $t_table  = Engine_Api::_()->getDbTable('transactionTrackings', 'ynauction');
        $t_name   = $t_table->info('name');
        $select   = $t_table->select()->setIntegrityCheck(false)
                            ->from($t_table,array("$t_name.*","DATE( FROM_UNIXTIME( $t_name.transaction_date) ) AS pDate",
                            "(SELECT username FROM engine4_users as pu WHERE pu.user_id = $t_name.user_seller ) as seller_user_name",
                         "(SELECT username FROM engine4_users as pu WHERE pu.user_id = $t_name.user_buyer ) as buyer_user_name",
                         "(SELECT account_username FROM engine4_ynauction_payment_accounts as pu WHERE pu.paymentaccount_id  = $t_name.account_seller_id ) as account_seller_email",
                         "(SELECT account_username FROM engine4_ynauction_payment_accounts as pu WHERE pu.paymentaccount_id  = $t_name.account_buyer_id  ) as account_buyer_email"
                        ));
        if (isset($params['buy']) && $params['buy'] != null)
        {
            $select->where("$t_name.params = ?",$params['buy']);
        }
        if (isset($params['user_id']) && $params['user_id'] != null)
        {
            $select->where("$t_name.user_seller = ?",$params['user_id']);
        }
        if (isset($params['fromDate']) && $params['fromDate'] != null)
        {
            $fromDate =  $params['fromDate'];
            $select->where("DATEDIFF(DATE_FORMAT( FROM_UNIXTIME($t_name.transaction_date),'%Y-%m-%d'),'".$fromDate."')>=0");  
        }
        if (isset($params['toDate']) && $params['toDate'] != null)
        {
            $toDate =  $params['toDate'];
            $select->where("DATEDIFF(DATE_FORMAT( FROM_UNIXTIME($t_name.transaction_date),'%Y-%m-%d'),'".$toDate."')<=0");  
        } 
        $select->order("$t_name.transaction_date DESC");
        return $select;  
     }
     public function getFinanceAccountsPag($params)
     {
        $requestPaginator = Zend_Paginator::factory(Ynauction_Api_Cart::getFinanceAccountsSelects($params));
        if( !empty($params['page']) )
        {
          $requestPaginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $requestPaginator->setItemCountPerPage($params['limit']);
        }   
        return $requestPaginator;
    }
     public function getFinanceAccountsSelects($params = array())
     {
        $p_table  = Engine_Api::_()->getDbTable('paymentAccounts', 'ynauction');
        $p_name   = $p_table->info('name');
         $select   = $p_table->select()->setIntegrityCheck(false)
                    ->from("$p_name as ni",'ni.*')
                    ->join("engine4_users","engine4_users.user_id = ni.user_id",'engine4_users.username')
                    ->where('payment_type <> 1');
                    $select->order('last_check_out ASC') ; 
          return $select;            
    }
    public function getFinanceAccount($user_id = null,$payment_type = null)
    {
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction');
        $select = $table->select();
          
        if($user_id != null)
        {
            $select->where('user_id = ?',$user_id);
        }
        if($payment_type != null)
        {
             $select->where('payment_type = ?',$payment_type);  
        }
        $accounts =   $table->fetchAll($select)->toArray();   
         return @$accounts[0];    
    }
    /**
    * insert or update finance account
    * 
    * @param mixed $account
    */
    public function saveFinanceAccount($account)
    {
        if(isset($account['paymentaccount_id']) && $account['paymentaccount_id']>0)
        {
            //update info of this account
             $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction');
             $data = array(
            'account_username' => $account['account_username']
            );
             $where = $table->getAdapter()->quoteInto('paymentaccount_id = ?', $account['paymentaccount_id']);
             $table->update($data, $where);
        }
        else
        {
           $acc = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction')->createRow();  
            $acc->account_username = $account['account_username'];
            $acc->payment_type = $account['payment_type'];
            $acc->account_status = 1;
            $acc->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $acc->save();

        }
        return $account;
    }  
    public function getFinanceAccounts($aConds = array(),$sSort = 'last_check_out ASC', $iPage = '', $sLimit = '', $bCount = true)
    {
        $p_table  = Engine_Api::_()->getDbTable('paymentAccounts', 'ynauction');
        $p_name   = $p_table->info('name');
        $iCnt = ($bCount ? 0 : 1);
        $items = array();
        if ($bCount ){
             $select   = $p_table->select()
                        ->from("$p_name as ni")
                        ->joinLeft("engine4_users","engine4_users.user_id = ni.user_id",'')
                        ->where($aConds); 
            $iCnt = count($p_table->fetchAll($select)->toArray());

        }
        if ($iCnt){
             $select   = $p_table->select()->setIntegrityCheck(false)
                        ->from("$p_name as ni",'ni.*')
                        ->joinLeft("engine4_users","engine4_users.user_id = ni.user_id",'engine4_users.username')
                        ->where($aConds)
                        ->order($sSort) ; 
            $items = $p_table->fetchAll($select)->toArray();
        }
        if (!$bCount)
        {
            return $items;
        }
        return array($iCnt, $items);
    }
    public function setDefaultValueAccount($account)
    {
        if(!isset($account['account_username']))
        {
            $account['account_username'] = 'your_email_account@payment.com';
        }
        if(!isset($account['account_password']))
        {
            $account['account_password'] = '';
        }
        if(!isset($account['user_id']))
        {
            $account['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
        }
        if(!isset($account['payment_type']))
        {
            $account['payment_type'] =2;
        }
        if(!isset($account['is_save_password']))
        {
            $account['is_save_password'] =0;
        }
        if(!isset($account['total_amount']))
        {
            $account['total_amount'] = 0;
        }
        if(!isset($account['last_check_out']))
        {
            $account['total_amount'] = '';
        }
        return $account;
    }         
    /**
    * Get Security Code  for transcation 
    * 
    */
    public function getSecurityCode()
    {
        $sid = 'abcdefghiklmnopqstvxuyz0123456789ABCDEFGHIKLMNOPQSTVXUYZ';
        $max =  strlen($sid) - 1;
        $res = "";
        for($i = 0; $i<16; ++$i){
            $res .=  $sid[mt_rand(0, $max)];
        }  
        return $res;
    }
    /**
    * get params payment for gateway.
    * 
    * @param mixed $gateway_name
    * @param mixed $returnUrl
    * @param mixed $cancelUrl
    * @param mixed $receivers
    */
    public function getReceivers($gateway_name ='paypal',$method_payment = 'directly',$request = false)
    {
        $settings = Ynauction_Api_Gateway::getSettingGateway($gateway_name);
        switch($gateway_name)
        {
            case 'paypal':
            default:
                $settings['params'] = unserialize($settings['params']);
                $receivers = array(
                    array('email' => @$settings['admin_account'],'invoice' => Ynauction_Api_Cart::getSecurityCode()),
                 );
            break; 
        }
        return $receivers;
    }
    /**
    * Return param format of payment gateway.
    * 
    * @param mixed $gateway_name
    * @param mixed $returnUrl
    * @param mixed $cancelUrl
    * @param mixed $method_payment
    */
    public function getParamsPay($gateway_name = 'paypal',$returnUrl,$cancelUrl,$method_payment = 'multi',$notifyUrl = '')
    {
         $receivers = Ynauction_Api_Cart::getReceivers($gateway_name,$method_payment);
         $invoice = "";
         foreach ($receivers as $rec)
         {
             $invoice .='-'.$rec['invoice'];
         }
         if ($invoice !="")
         {
             $invoice = substr($invoice,1);
         }
         switch($gateway_name)
         {
             case 'paypal':
             default:
                $paramsPay = array(
                'actionType' => 'PAY',
                'cancelUrl'  => $cancelUrl.$invoice,
                'returnUrl'  => $returnUrl.$invoice,
                'currencyCode' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.currency', 'USD'),
                'sender'=>'',
                'feesPayer'=>'EACHRECEIVER',//feesPayer value {SENDER, PRIMARYRECEIVER, EACHRECEIVER}
                'ipnNotificationUrl'=> $notifyUrl.$invoice,
                'memo'=> '',
                'pin'=> '',
                'preapprovalKey'=> '',
                'reverseAllParallelPaymentsOnError'=> '',
                'receivers' => $receivers,
                );
             break;
         }
         return array($receivers,$paramsPay);
    }
    /**
    * move all items from cart to user's download list
    * 
    * @param mixed $cartlist
    */
    public function generateTime($value,$option,$first = true)
    {   
        if ( $option == 'month')
        {
            if ( $first == true)
                $day = '01';
            else
                $day = '31';
            $date = date('Y');
            if ($value <10 )
                $date.='-0'.$value.'-'.$day;
            else
                $date.='-'.$value.'-'.$day;
            
            $time = strtotime($date);
            return $time;
        }
        if ( $option =='year')
        {
            if ( $first == true)
                $day = '-01-01';
            else
                $day = '-12-31';
            
            $date = $value.$day;
            $time = strtotime($date);
            return $time;
        }
    }
    public function getSumAmountTransaction($type='buy')
    {
        $t_table = Engine_Api::_()->getDbTable('transactionTrackings', 'ynauction');
        $t_name  = $t_table->info('name');
        
        $select = $t_table->select()
                    ->from("$t_name as his",array('sum(amount) as total','params'))
                    ->where('params = ?',$type)
                    ->where('transaction_status = 1')
                    ->group('params');
        $res =  $t_table->fetchAll($select)->toArray(); 
        if($res == NULL)
           $res[0]['total'] = 0; 
        return $res[0];
    }
      /**
    * load gateway user uses
    * 
    * @param mixed $gateway_name
    */
    public function loadGateWay($gateway_name = 'paypal')
    {
        $gateway = new gateway();
        $p = $gateway->load($gateway_name);
        return $p;
    }
    /**
    * get default settings from db of gateway
    * 
    * @param mixed $gateway_name
    */
    public function getSettingsGateWay($gateway_name = 'paypal')
    {
        $settings = Ynauction_Api_Gateway::getSettingGateway($gateway_name);        
        $settings['params'] = unserialize($settings['params']);
        if ( isset($settings['params']['use_proxy']) && $settings['params']['use_proxy'] == 1)
            $use_proxy = true;
        else
            $use_proxy = false;
        $mode = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.mode', 1); 
        switch($gateway_name)
        {
            case 'paypal':
            default:
                if($mode == 1 )
                {
                    $m = 'sandbox';
                }
                else
                {
                    $m='real';
                }
                $aSetting = array(
                    'env' =>$m,
                    'api_username' =>$settings['params']['api_username'],
                    'api_password' =>$settings['params']['api_password'],
                    'api_signature' =>$settings['params']['api_signature'],
                    'use_proxy' =>$use_proxy,
                );
                break;
            
        }
        
        return $aSetting;
    }
     public function makeBillFromCart($product1,$receiver,$type = 1)
    {
        if($type == 4)
        {
            $product = Engine_Api::_()->getItem('ynauction_product', $product1->product_id);  
        } 
        else
            $product = $product1;
        $insert_item = array();
         list($iCnt,$receiver_account) = Ynauction_Api_Cart::getFinanceAccounts('ni.account_username = "'.$receiver['email'].'"');
         if ( !isset($receiver_account[0]) || @$receiver_account[0]['paymentaccount_id']<=0)
         {
             return -1;
         }
         $db = Engine_Db_Table::getDefaultAdapter();
         $db->beginTransaction(); 
          $b_table = Engine_Api::_()->getDbTable('bills', 'ynauction'); 
          $bill = $b_table->createRow();
          $bill->invoice     = $receiver['invoice'];
          $bill->sercurity    = $_SESSION['payment_sercurity'];              
          $bill->user_id     = Engine_Api::_()->user()->getViewer()->getIdentity();
          $bill->finance_account_id       = 0;
          $bill->emal_receiver  = $receiver['email'];
          $bill->payment_receiver_id  = $receiver_account[0]['paymentaccount_id'];
          $bill->date_bill  = time();
          $bill->bill_status  = 0;
          $bill->owner_id  = $product->user_id; 
          if($type != 2)
          {
            $bill->item_id  = $product->product_id;
          }
          else
          {
            $bill->item_id  = $product->block_id;
          }
          if($type == 1)
          {
            $bill->amount  = $product->bid_price;
            $bill->currency  = $product->currency_symbol; 
          }
          else if($type == 0)
          {
            $bill->amount  = $product->total_fee; 
            $bill->owner_id  = 1; 
            $bill->currency  = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.currency', 'USD');
          }
          else if($type == 4)
          {
               $bill->currency  = $product->currency_symbol; 
               $bill->amount  = $product1->proposal_price; 
          } 
          else 
          {
            $bill->currency  = $product->currency_symbol; 
            $bill->amount  = $product->price; 
          }
          
          $bill->type  = $type; 
          $viewer = Engine_Api::_()->user()->getViewer();
          $autoApprove = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynauction_product', $viewer, 'auto_approve');
          if($autoApprove && $type == 0)
          {
             $bill->auto_approve = 1; 
          }
          $bill->save(); 
         try {
              $db->commit();
          } catch (Exception $ex) {
              $db->rollback();
              break;
          }  
         return 1;
         
    }
}   
?>
