<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminPaymentsettingsController.php
 * @author     Minh Nguyen
 */
class Ynauction_AdminPaymentsettingsController extends Core_Controller_Action_Admin
{
   protected $_paginate_params = array();
   public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_paymentsettings');
  }
  public function indexAction()
  {
      $select = Engine_Api::_()->getDbtable('gateways', 'ynauction')->select();
      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }
  public function editAction()
  {
    // Get gateway
    $gateway = Engine_Api::_()->getDbtable('gateways', 'ynauction')
      ->find($this->_getParam('gateway_id'))
      ->current();

    // Make form
    //if($gateway['gateway_name'] == 'Paypal')
        $this->view->form = $form = new Ynauction_Form_Admin_Payment_Paypal();
    //else
    //    $this->view->form = $form = new Groupbuy_Form_Admin_Payment_2checkout();
    $form->removeElement('api_password');
    $form->removeElement('api_username');
    $form->removeElement('api_signature');
    // Populate form
    $form->populate($gateway->toArray());
   
   if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
      if( !$this->_getParam('admin_account')) {
        return;
    }
    $email = $this->_getParam('admin_account'); 
    //if($gateway['gateway_name'] == 'Paypal')   
    //{
        if(trim($email) == "")
          {
               $form->getElement('admin_account')->addError('Please enter valid email!'); 
                return ;
          }
          else if(trim($email) != "")
          {
              $regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";                                                                                                            
            if(!preg_match($regexp, $email))
            {
                $form->getElement('admin_account')->addError('Please enter valid email!'); 
                return ;
            }
          }
    //}
    // Process
    $val = $form->getValues();
    $enabled = (bool) $val['is_active']; 
    $params['admin_account'] = $val['admin_account'];
    $params['is_active'] = $enabled;
    //$params['currency'] = $val['currency']; 
    $params['params'] = $val;
    $params['api_app_id'] ="";
    Ynauction_Api_Gateway::saveSettingGateway($gateway['gateway_name'],$params);  
    $admin = Ynauction_Api_Cart::getFinanceAccount(null,1,$gateway['gateway_id']);  
    $admin['account_username'] = $val['admin_account'];
    $admin['gateway_id'] = $gateway['gateway_id'];
    $admin['payment_type'] = 1;
    $admin = Ynauction_Api_Cart::saveFinanceAccount($admin);
    $form->addNotice('Your changes have been saved.');
  }
}