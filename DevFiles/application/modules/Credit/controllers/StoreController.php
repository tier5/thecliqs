<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: StoreController.php 11.06.12 17:34 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_StoreController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('credit_main');

    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')) {
      $this->_redirectCustom($this->view->url(array(), 'credit_general', true));
    }
  }

  public function indexAction()
  {
    $this->view->ukey       = $order_ukey = $this->_getParam('vendor_order_id', '');
    $this->view->return_url = $return_url = $this->_getParam('return_url', '');
    $this->view->cancel_url = $cancel_url = $this->_getParam('cancel_url', '');

    if (!$cancel_url || !$return_url || !$order_ukey || !Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')) {
      $this->_redirectCustom($this->view->url(array(), 'credit_general', true));
    }

    /**
     * @var $order Store_Model_Order
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->order = $order = Engine_Api::_()->getDbTable('orders', 'store')->getOrderByUkey($order_ukey);

    if (
      $order == null ||
      $order->getUser() == null ||
      $order->getUser()->getIdentity() != $viewer->getIdentity() ||
      $order->status != 'initial')
    {
      $this->view->status = 'failed';
      $this->_redirectCustom($return_url . '&status=failed');
    }

    $this->view->orderItems = $orderItems = $order->getItems();
    if (!$orderItems->count()) {
      $this->view->status = 'failed';
      return ;
    }

    // Shipping Details
    $this->view->api     = Engine_Api::_()->store();
    $detailsTbl          = Engine_Api::_()->getDbTable('details', 'store');
    $locationsTbl        = Engine_Api::_()->getDbTable('locations', 'store');
    $this->view->details = $details = $detailsTbl->getDetails($viewer);
    $this->view->country = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_1']));
    $this->view->region  = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_2']));

    $this->view->balance = Engine_Api::_()->getItem('credit_balance', $viewer->getIdentity())->current_credit;
  }

  public function payAction()
  {
    /**
     * @var $order Store_Model_Order
     * @var $api_credit Credit_Api_Core
     * @var $api_store Store_Api_Core
     */
    $this->view->ukey = $order_ukey = $this->_getParam('ukey', '');
    if ($order_ukey == '') {
      $this->view->status = 0;
      $this->view->message = $this->view->translate('Invalid data');
    }

    $order = Engine_Api::_()->getDbTable('orders', 'store')->getOrderByUkey($order_ukey);
    if ($order == null) {
      $this->view->status = 0;
      $this->view->message = $this->view->translate('Invalid data');
      return ;
    }

    $api_store = Engine_Api::_()->store();
    $api_credit = Engine_Api::_()->credit();
    $totalCredits = $api_store->getCredits($order->item_amt + $order->tax_amt + $order->shipping_amt);

    $buyer = $order->getUser();

    if ($buyer == null) {
      $this->view->status = 0;
      $this->view->message = $this->view->translate('Invalid data');
      return ;
    }

    $buyerBalance = Engine_Api::_()->getItem('credit_balance', $buyer->getIdentity());

    if ($totalCredits > $buyerBalance->current_credit) {
      $this->view->status = 0;
      $this->view->message = $this->view->translate('You do not have enough credits to buy products');
      return ;
    }

    $confirm_id = $api_credit->buyProducts($buyer, $order_ukey, (-1)*$totalCredits);
    $this->view->data = array('status' => 'completed', 'ukey' => $order_ukey, 'confirm_id' => $confirm_id);
    $this->view->status = 1;
  }
}
