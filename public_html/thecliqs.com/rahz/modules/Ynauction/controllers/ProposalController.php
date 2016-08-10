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
class Ynauction_ProposalController extends Core_Controller_Action_Standard
{
      public function indexAction()
      {
      	  $this -> _helper -> content -> setEnabled();
          if( !$this->_helper->requireUser()->isValid() ) return;
		  
   		  $values = $this ->_getAllParams();
          $values['bider_id'] = $viewer = $this->_helper->api()->user()->getViewer()->getIdentity();       
          $values['page']   = $this->getRequest()->getParam('page', 1);
          $values['limit'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.page', 10);
          $paginator = Engine_Api::_()->ynauction()->getBoughtsPaginator($values);
          $this->view->wins = $paginator;
          $view = $this->view;
          $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');      
      }
      
     public function proposalSellerAction(){  
         $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('ynauction_main', array(), 'ynauction_main_manageauction');
      if (!$this->_helper->requireUser()->isValid()) { return;}   
      $product = Engine_Api::_()->getItem('ynauction_product', $this->_getParam('auction'));
      $this->view->proposals = $product->getProposals();
  }    
     public function proposalPriceAction(){  
       $user_id = Engine_Api::_()->user()->getViewer()->getIdentity(); 
       $product = Engine_Api::_()->getItem('ynauction_product', $this->_getParam('auction'));
       $this->view->form = $form = new Ynauction_Form_Proposal(); 
       if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
            $db = Engine_Api::_()->getDbTable('proposals', 'ynauction')->getAdapter();
            $db->beginTransaction();
            try {
                if($product->proposal == 1)
                {
                    $form->addError('A proposal price is already approved for this auction');
                    return;
                }
                if($this->view->form->saveValues($product) == false)
                    return;
                $db->commit();
                $this->view->success     = true;
                $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format'=> 'smoothbox',
                  'messages' => array($this->view->translate('Proposal price successfully.'))
                  ));
                  //return $this->_helper->redirector->gotoRoute(array('action' => 'index'), 'ynauction_proposal', true);
            } catch (Exception $e) {
                $db->rollback();
                $this->view->success = false;
            }
        } 
  }
     public function checkoutAction() {  
         $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('ynauction_main', array(), 'ynauction_main_boughts');
       $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
       $proposal = Engine_Api::_()->getItem('ynauction_proposal', $this->_getParam('proposal_id'));  
       $product = Engine_Api::_()->getItem('ynauction_product', $proposal->product_id);  
       if (!$this->_helper->requireUser()->isValid()) { return;}   
       if($viewer_id != $proposal->ynauction_user_id || $proposal->approved == 0)
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
       $returnUrl = $this->selfURL().'application/modules/Ynauction/externals/scripts/redirect4.php?pstatus=success&req4='.$_SESSION['payment_sercurity'].'&req5=';
       $cancelUrl = $this->selfURL().'application/modules/Ynauction/externals/scripts/redirect4.php?pstatus=cancel&req4='.$_SESSION['payment_sercurity'].'&req5=';
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
       $this->view->proposal = $proposal;
  }
     public function makebillAction(){
         //tat di layout
        $this->_helper->layout->disableLayout();
       //khong su dung view
        $receiver = $_SESSION['receiver']; 
        //$product = Engine_Api::_()->getItem('ynauction_product', $this->_getParam('auction')); 
        $proposal = Engine_Api::_()->getItem('ynauction_proposal', $this->_getParam('proposal_id'));              
        $bill =  Ynauction_Api_Cart::makeBillFromCart($proposal,$receiver[0],4);
    }
     public function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
      }   
    public function approveAction()
  {
      if( !$this->_helper->requireUser()->isValid() ) return;
      $proposal_id = $this->_getParam('proposal_id'); 
      $proposal = Engine_Api::_()->getItem('ynauction_proposal', $proposal_id);
      $auction = Engine_Api::_()->getItem('ynauction_product', $proposal->product_id);
      $viewer = Engine_Api::_()->user()->getViewer();   
       if($proposal)
          {
               $proposal->approved = 1;
               $proposal->save(); 
               
                     //send notify 
                     //Send sell
                     $productOwner = Engine_Api::_()->getItem('user', $proposal->ynauction_user_id);
                     $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                     @$notifyApi->addNotification($productOwner, $viewer, $auction, 'ynauction_approved_proposal', array(
          'label' => $auction->title
        ));
               $auction->proposal = 1;
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
      $proposal_id = $this->_getParam('proposal_id'); 
      $proposal = Engine_Api::_()->getItem('ynauction_proposal', $proposal_id);
      $auction = Engine_Api::_()->getItem('ynauction_product', $proposal->product_id);
      $viewer = Engine_Api::_()->user()->getViewer();   
       if($proposal)
          {
               $proposal->approved = -1;
               $proposal->save(); 
                     //send notify 
                     //Send sell
                     $productOwner = Engine_Api::_()->getItem('user', $proposal->ynauction_user_id);
                     $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                     @$notifyApi->addNotification($productOwner, $viewer, $auction, 'ynauction_denied_proposal', array(
          'label' => $auction->title
        ));
         $auction->proposal = 0;
               $auction->save();
                }  
          $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format'=> 'smoothbox',
                  'messages' => array($this->view->translate('Deny successfully.'))
                  ));
  }
}

 
