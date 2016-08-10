<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminStatisticsController.php
 * @author     Minh Nguyen
 */
class Ynauction_AdminStatisticsController extends Core_Controller_Action_Admin
{
   public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_statistics');
  }
  public function indexAction()
  {
        $page = $this->_getParam('page',1);
        $this->view->form = $formFilter = new Ynauction_Form_Admin_Transaction();
        if($formFilter->isValid($this->_getAllParams()))
        {
            $filterValues = $formFilter->getValues();
            $this->view->filterValues = $filterValues;
        }
        $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.page', 10);
        $filterValues['limit'] = $limit;
        $this->view->transtracking = Ynauction_Api_Cart::getTrackingTransaction($filterValues);
        $this->view->transtracking->setCurrentPageNumber($page);
        $this->view->filterValues = $filterValues; 
    }
}