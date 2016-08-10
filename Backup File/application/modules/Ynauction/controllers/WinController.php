<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copy right 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: WinController.php
 * @author     Minh Nguyen
 */
class Ynauction_WinController extends Core_Controller_Action_Standard
{
     public function init(){
     // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('ynauction_main', array(), 'ynauction_main_managewinning');
  } 
     public function indexAction()
      {
      	$this -> _helper -> content -> setEnabled(); 
          if( !$this->_helper->requireUser()->isValid() ) return;
       $values = $this ->_getAllParams();
          $values['where'] = "status <> 0 AND status <> 3";
          $values['bider_id'] = $viewer = $this->_helper->api()->user()->getViewer()->getIdentity();       
          $values['page']   = $this->getRequest()->getParam('page', 1);
          $values['limit'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.page', 10);
          $paginator = Engine_Api::_()->ynauction()->getProductsPaginator($values);
          $this->view->wins = $paginator;
            $view = $this->view;
          $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');      
      }
     public function viewTransactionAction(){  
      if (!$this->_helper->requireUser()->isValid()) { return;}   
       //tat di layout
       $this->_helper->layout->disableLayout();
       $user_id     = $this->getRequest()->getParam('id');  
       $user_name     = $this->getRequest()->getParam('username');
       $this->view->user_name =  $user_name;
        if (!$this->_helper->requireUser()->isValid()) { return;} 
          $params = array_merge($this->_paginate_params, array(
        'user_id' => $user_id
        ));
       $this->view->history= $his = Mp3music_Api_Cart::getTrackingTransaction($params);
       $his->setItemCountPerPage(1000000000000);
  }
     public function transactionAction(){  
       $user_id = Engine_Api::_()->user()->getViewer()->getIdentity(); 
        if (!$this->_helper->requireUser()->isValid()) { return;} 
          $params = array_merge($this->_paginate_params, array(
        'user_id' => $user_id,
        ));
       $this->view->history = Mp3music_Api_Cart::getTrackingTransaction($params);
  }
     public function checkoutAction() {  
       $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
       $product = Engine_Api::_()->getItem('ynauction_product', $this->_getParam('auction'));  
       if (!$this->_helper->requireUser()->isValid()) { return;}   
       if($viewer_id != $product->bider_id)
            $this->view->notView = true;
       $session_id_cart = $this->getRequest()->getParam('session_id');
        if( !Engine_Api::_()->core()->hasSubject('product') ) {
          Engine_Api::_()->core()->setSubject($product);
        }
        // Check auth
        if( !$this->_helper->requireSubject()->isValid() ) {
          return;
        }
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
       $returnUrl = $this->selfURL().'application/modules/Ynauction/externals/scripts/redirect.php?pstatus=success&req4='.$_SESSION['payment_sercurity'].'&req5=';
       $cancelUrl = $this->selfURL().'application/modules/Ynauction/externals/scripts/redirect.php?pstatus=cancel&req4='.$_SESSION['payment_sercurity'].'&req5=';
       $notifyUrl = $this->selfURL().'application/modules/Ynauction/externals/scripts/callback.php?action=callback&req4='.$_SESSION['payment_sercurity'].'&req5=';
       list($receiver,$paramsPay) = Ynauction_Api_Cart::getParamsPay($gateway_name,$returnUrl,$cancelUrl,$method_payment,$notifyUrl);
       $account = Ynauction_Api_Cart::getFinanceAccount($product->user_id,2);
       //Send money for Product Owner.
       if($account)
            $receiver[0]['email'] = $account['account_username'] ;
       $_SESSION['receiver'] = $receiver;
       $method_payment = 'directly';
       $paymentForm = "https://www.sandbox.paypal.com/cgi-bin/webscr";
       if ($settings['env'] == 'sandbox')
       {
           $paymentForm = "https://www.sandbox.paypal.com/cgi-bin/webscr";
       }
       else
       {
           $paymentForm = "https://www.paypal.com/cgi-bin/webscr";
       } 
       $this->view->paymentForm = $paymentForm;      
       $this->view->method = $method_payment;    
       $this->view->sercurity = $_SESSION['payment_sercurity'];         
       $this->view->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.currency', 'USD');    
       $this->view->paramPay = $paramsPay;   
       $this->view->receiver = $receiver[0];    
       $this->view->product = $product;
  }
     public function makebillAction(){
         //tat di layout
        $this->_helper->layout->disableLayout();
       //khong su dung view
        $receiver = $_SESSION['receiver']; 
        $product = Engine_Api::_()->getItem('ynauction_product', $this->_getParam('auction'));             
        $bill =  Ynauction_Api_Cart::makeBillFromCart($product,$receiver[0],1);
    }
     public function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
      }   
  
}

 
