<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminAccountmanageController.php
 * @author     Minh Nguyen
 */
class Ynauction_AdminAccountmanageController extends Core_Controller_Action_Admin
{
   protected $_paginate_params = array();
   public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_accountmanage');
      $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
     $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.page', 10);
  }
  public function indexAction()
  {
        $params = array_merge($this->_paginate_params, array());  
        $accounts = Ynauction_Api_Cart::getFinanceAccountsPag($params);
        $this->view->accounts = $accounts;  
    }
}