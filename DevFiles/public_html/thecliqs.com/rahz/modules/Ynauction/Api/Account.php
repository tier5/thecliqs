<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Account.php
 * @author     Minh Nguyen
 */
class Ynauction_Api_Account extends Core_Api_Abstract
{
    public function getCurrentInfo($user_id){
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction');
        $select = $table->select() ->setIntegrityCheck(false)
                        ->from('engine4_ynauction_payment_accounts as account','account.*')  
                        ->joinRight('engine4_users','engine4_users.user_id =  account.user_id','engine4_users.*')
                        ->where('account.payment_type = 2')
                        ->where('engine4_users.user_id = ?',$user_id);
        $result =   $table->fetchAll($select)->toArray();
        return @$result[0];
    }
    public function getCurrentAccount($user_id){
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction');
        $select = $table->select() ->setIntegrityCheck(false)
                        ->from('engine4_ynauction_payment_accounts as account','account.*')  
                         ->where('account.payment_type = 2')
                        ->where('account.user_id = ?',$user_id);
        $result =   $table->fetchAll($select)->toArray();
        return @$result[0];
    }
    public function getHistorySellerSelect($params){
            $tt_table  = Engine_Api::_()->getDbTable('transactionTrackings', 'ynauction');
            $tt_name   = $tt_table->info('name');
            $a_table = Engine_Api::_()->getDbTable('products', 'ynauction');
            $a_name  = $a_table->info('name');
            $select   = $tt_table->select()->setIntegrityCheck(false)
                        ->from($tt_table,array('count(*) as count',"$tt_name.amount"))
                        ->joinLeft($a_name,"$a_name.product_id = $tt_name.item_id",array("$a_name.*"))
                        ->where("$a_name.user_id = ? ",$params["user_id"])->where("$tt_name.params = 'buy'")
                        ->where("$tt_name.approved = 1")
                        ->group("$tt_name.item_id");
          return $select;
    }
    public function getHistorySeller($params){
        $sellerPaginator = Zend_Paginator::factory(Ynauction_Api_Account::getHistorySellerSelect($params));
        if( !empty($params['page']) )
        {
          $sellerPaginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $sellerPaginator->setItemCountPerPage($params['limit']);
        }   
        return $sellerPaginator;
    }
    public function updateinfo($avals=array()){
        $user   = Engine_Api::_()->user()->getViewer();
        $user->displayname = $avals['displayname'];
        return $user->save();   
    }
    public function updateusername_account($request_user_id,$account_username){ 
        $info = Ynauction_Api_Account::getCurrentInfo($request_user_id);
        if($info)
        {
            $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction');
            $data = array(
                'account_username' => $account_username
            );
            $where = $table->getAdapter()->quoteInto('user_id = ?', $request_user_id);
            return $table->update($data, $where);
        }
        else
        {
            $result['account_username']  = $account_username;
            Ynauction_Api_Account::insertAccount($result);
        }
    }
    public function updatebid($user_id,$bids){ 
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction');
        $data = array(
            'bids' => $bids - 1
        );
        $where = $table->getAdapter()->quoteInto('user_id = ?', $user_id);
        return $table->update($data, $where);
    }
    public function addbid($user_id, $bids = 0)
    {
        $info = Ynauction_Api_Account::getCurrentInfo($user_id);
        if($info)
        {
            $bids_old = $info['bids'];
            if(!$bids_old)
                $bids_old = 0;
            $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction');
            $data = array(
                'bids' => $bids_old +  $bids
            );
            $where = $table->getAdapter()->quoteInto("user_id = $user_id AND payment_type = 2");
            return $table->update($data, $where);
        }
        else
        {
            $account = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction')->createRow();
            $account->bids = $bids; 
            $account->account_status = 1; 
            $account->payment_type = 2;
            $account->user_id = $user_id;
            $account->save(); 
        }
    }
    public function addblock0($user_id = 0, $bids = 0,$block_id = 0)
    {
        $info = Ynauction_Api_Account::getCurrentInfo($user_id);
        if($info)
        {
            $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction');
            $data = array(
                'bids' => $info['bids'] +  $bids,
                'block_id' => $block_id,
                'last_buy_blockfree' => date('Y-m-d H:i:s')
            );
            $where = $table->getAdapter()->quoteInto("user_id = $user_id AND payment_type = 2");
            return $table->update($data, $where);
        }
        else
        {
            $account = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction')->createRow();
            $account->bids = $bids; 
            $account->account_status = 1; 
            $account->payment_type = 2;
            $account->user_id = $user_id;
            $account->block_id = $block_id;
            $account->last_buy_blockfree = date('Y-m-d H:i:s');;
            $account->save(); 
        }
    }
    public function insertAccount($results = array()){
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $info = Ynauction_Api_Account::getCurrentInfo($user_id);
        if(!$info)
        {
            $account = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction')->createRow();  
            $account->account_username = $results['account_username'];
            //$account->account_password = $results['password'];
            $account->account_status = 1;
            $account->payment_type = 2;
            $account->user_id = $user_id;
            $account->save(); 
            return 1;
        }
        else
        {
            $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'ynauction');
            $data = array(
                'account_username' => $results['account_username']
            );
            $where = $table->getAdapter()->quoteInto("user_id = $user_id AND payment_type = 2");
            return $table->update($data, $where);
        }
    }
}   
?>
