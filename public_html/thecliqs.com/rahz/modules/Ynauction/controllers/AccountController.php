<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AccountController.php
 * @author     Minh Nguyen
 */
class Ynauction_AccountController extends Core_Controller_Action_Standard
{
   public function init(){
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('ynauction_main', array(), 'ynauction_main_account');
    $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
    $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.page', 10); 
  } 
   public function createAction(){
   	$this -> _helper -> content -> setEnabled();  
     // only members can create account
    if( !$this->_helper->requireUser()->isValid() ) return;  
     $this->view->form = new Ynauction_Form_CreateAccount();   
     $is_account= Ynauction_Api_Account::getCurrentInfo(Engine_Api::_()->user()->getViewer()->getIdentity());
     if($is_account['account_username']!=null)
          $result=1;  
     if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
      $db = Engine_Api::_()->getDbTable('paymentAccounts', 'ynauction')->getAdapter();
      $db->beginTransaction();
      try {
        $result = $this->view->form->saveValues();
        $this->view->result = $result;
        $db->commit();
        if($result)
            return $this->_redirect('auction/account');
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    }  
  }
   public function indexAction()
   {
   		$this -> _helper -> content -> setEnabled();  
       $info_user = Engine_Api::_()->user()->getViewer();  
       if(!Engine_Api::_()->ynauction()->checkBecome($info_user->getIdentity()))
        {
            return $this->_helper->requireAuth->forward();
        }
       if( !$this->_helper->requireUser()->isValid() || !Engine_Api::_()->ynauction()->checkBecome($info_user->getIdentity())) return; 
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();  
        $info_account = Ynauction_Api_Account::getCurrentAccount($user_id);   
        $user_group_id = Engine_Api::_()->user()->getViewer()->level_id;
         $params = array_merge($this->_paginate_params, array(
        'user_id' => $user_id,
        ));
        $this->view->HistorySeller = $his = Ynauction_Api_Account::getHistorySeller($params);
        $this->view->info_user = $info_user;
        $this->view->info_account = $info_account;
  }
  public function hisBidsAction()
  {
       //tat di layout
       $this->_helper->layout->disableLayout();
       //khong su dung view
       $this->_helper->viewRenderer->setNoRender(TRUE);
       $product     = Engine_Api::_()->getItem('ynauction_product', $this->getRequest()->getParam('auction_id'));
       $this->view->product = $product;
       echo $this->view->partial('_refresfList.tpl', array('product'=>$product));
       return; 
  }  
   public function editAction(){
   		$this -> _helper -> content -> setEnabled(); 
       $user_id = Engine_Api::_()->user()->getViewer()->getIdentity(); 
       if( !$this->_helper->requireUser()->isValid() || !Engine_Api::_()->ynauction()->checkBecome($user_id) ) return;       
        $info = Ynauction_Api_Account::getCurrentInfo($user_id);
        $user =  Engine_Api::_()->user()->getViewer();
        $this->view->user = $user;
        $result = null;
        if(isset($_POST['submit']))
          {
              $aVals = $this->getRequest()->getParam('val');
              $aVals['displayname'] =  strip_tags($aVals['full_name']);
              $this->view->info =  $aVals;
              $is_validate=0;
              $is_email=0; 
              if(trim($aVals['full_name']) == "")
              {
                    $is_validate=1;
                    $this->view->error = 'Please enter full name!';
              }
              if(trim($aVals['account_username']==""))
              {
                    $is_validate=1;
                    $this->view->error = 'Please enter finance account';
              }
              if($is_email==0)
              {
                    $email = $aVals['account_username'];
                    $regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
                    if(!preg_match($regexp, $email))
                    {
                        $is_validate=1;
                       $this->view->error = 'Finance account email is not valid!';
                       $this->view->empty = 'emailAccount';
                    }
              }
              if($is_validate==0)
              {
                  $result = Ynauction_Api_Account::updateinfo($aVals);
                  Ynauction_Api_Account::updateusername_account($user_id,$aVals['account_username']);
                  $info_account = Ynauction_Api_Account::getCurrentAccount($user_id);
                  if($info_account != null)
                  {
                        if($info_account['payment_type'] == 1)
                        {
                            $params['admin_account'] = $aVals['account_username'];
                            $params['is_from_finance'] = 1;
                            Mp3music_Api_Gateway::saveSettingGateway('paypal',$params);   
                        }    
                  }
                   
                   $info = Ynauction_Api_Account::getCurrentInfo($user_id);
              }
              else
                $info = $aVals;
          }
          $this->view->info =  $info;
          $this->view->result = $result;
  }
   public function buybidAction(){ 
      if (!$this->_helper->requireUser()->isValid()) { return;}
       $_SESSION['payment_sercurity'] = Ynauction_Api_Cart::getSecurityCode();
       $method_payment = array('direct'=>'Directly','multi'=>'Multipartite payment');  
       $paymentForm =  '';
       $gateway_name = "paypal";
       $gateway = Ynauction_Api_Cart::loadGateWay($gateway_name);
       $settings = Ynauction_Api_Cart::getSettingsGateWay($gateway_name);
       $params = array();
       $params = array_merge(array('req3' => 'cancel','req4'=> $_SESSION['payment_sercurity']), $params);
       $cancelUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble($params, 'ynauction_winning', true);
       $_SESSION['url']['cancel'] = $cancelUrl;
       $returnUrl = $this->selfURL().'application/modules/Ynauction/externals/scripts/redirect2.php?pstatus=success&req4='.$_SESSION['payment_sercurity'].'&req5=';
       $cancelUrl = $this->selfURL().'application/modules/Ynauction/externals/scripts/redirect2.php?pstatus=cancel&req4='.$_SESSION['payment_sercurity'].'&req5=';
       $notifyUrl = $this->selfURL().'application/modules/Ynauction/externals/scripts/callback2.php?action=callback&req4='.$_SESSION['payment_sercurity'].'&req5=';
       list($receiver,$paramsPay) = Ynauction_Api_Cart::getParamsPay($gateway_name,$returnUrl,$cancelUrl,$method_payment,$notifyUrl);
       $_SESSION['receiver'] = $receiver;
       $method_payment = 'directly';
       $paymentForm = "https://www.sandbox.paypal.com";
       if ($settings['env'] == 'sandbox')
       {
           $paymentForm = "https://www.sandbox.paypal.com";
       }
       else
       {
           $paymentForm = "https://www.paypal.com";
       } 
       $this->view->paymentForm = $paymentForm;      
       $this->view->method = $method_payment;    
       $this->view->sercurity = $_SESSION['payment_sercurity'];         
       $this->view->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.currency', 'USD');    
       $this->view->paramPay = $paramsPay;   
       $this->view->receiver = $receiver[0];    
       $this->view->blocks = Engine_Api::_()->ynauction()->getBlocks(); 
  }
  public function buyblock0Action()
  {
          $block_id = $this->_getParam('block'); 
          $bids = $this->_getParam('bids'); 
          $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();     
          Ynauction_Api_Account::addblock0($user_id,$bids,$block_id);
          // Redirect
         return $this->_redirect('auction/account');
  }
   public function selfURL() 
  {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
  }
  public function makebillAction()
  {
         //tat di layout
        $this->_helper->layout->disableLayout();
       //khong su dung view
        $receiver = $_SESSION['receiver']; 
        $block =  Engine_Api::_()->ynauction()->getBlock($this->_getParam('block')); 
        $bill =  Ynauction_Api_Cart::makeBillFromCart($block,$receiver[0],2); 
    }
  public function approveAction()
  {
      if( !$this->_helper->requireUser()->isValid() ) return;
      $tran_id = $this->_getParam('tran_id'); 
      $tran = Engine_Api::_()->getItem('ynauction_transaction_tracking', $tran_id);
      $auction = Engine_Api::_()->getItem('ynauction_product', $tran->item_id);
       $viewer = Engine_Api::_()->user()->getViewer();   
       if($tran)
          {
               $tran->approved = 1;
               $tran->save(); 
               $notify =  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.notify', 1);
                if($notify == 1)
                {
                     //send notify 
                     //Send sell
                     $productOwner = Engine_Api::_()->getItem('user', $tran->user_buyer);
                     $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                     @$notifyApi->addNotification($productOwner, $viewer, $auction, 'ynauction_approved_bought', array(
          'label' => $auction->title
        ));
                } 
               $auction->stop = 1;
               $auction->status = 2;
               $auction->end_time = date('Y-m-d H:i:s');
               $auction->save();
          }
          $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format'=> 'smoothbox',
                  'messages' => array($this->view->translate('Approve successfully.'))
                  ));
  }
  public function denyAction()
  {
      if( !$this->_helper->requireUser()->isValid() ) return;
      $tran_id = $this->_getParam('tran_id'); 
      $tran = Engine_Api::_()->getItem('ynauction_transaction_tracking', $tran_id);
      $auction = Engine_Api::_()->getItem('ynauction_product', $tran->item_id);
      $viewer = Engine_Api::_()->user()->getViewer();
       if($tran)
          { 
              $tran->approved = -1; 
              $tran->save();
              $notify =  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.notify', 1);
                if($notify == 1)
                {
                     //send notify 
                     //Send sell
                     $productOwner = Engine_Api::_()->getItem('user', $tran->user_buyer);
                     $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                     @$notifyApi->addNotification($productOwner, $viewer, $auction, 'ynauction_denied_bought', array(
          'label' => $auction->title
        ));
                } 
          }
          $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format'=> 'smoothbox',
                  'messages' => array($this->view->translate('Deny successfully.'))
                  ));
  }
}
 
