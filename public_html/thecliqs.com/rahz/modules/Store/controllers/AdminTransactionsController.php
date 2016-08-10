<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminTransactionsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->menu       = $this->_getParam('action');
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_transactions');
    $this->view->api = Engine_Api::_()->store();
  }

  public function indexAction()
  {
    // Test curl support
    if (!function_exists('curl_version') ||
      !($info = curl_version())
    ) {
      $this->view->error = $this->view->translate('The PHP extension cURL ' .
        'does not appear to be installed, which is required ' .
        'for interaction with payment gateways. Please contact your ' .
        'hosting provider.');
    }
    // Test curl ssl support
    elseif (!($info['features'] & CURL_VERSION_SSL) ||
      !in_array('https', $info['protocols'])
    ) {
      $this->view->error = $this->view->translate('The installed version of ' .
        'the cURL PHP extension does not support HTTPS, which is required ' .
        'for interaction with payment gateways. Please contact your ' .
        'hosting provider.');
    }
    // Check for enabled payment gateways
    elseif (Engine_Api::_()->getDbtable('gateways', 'store')->getEnabledGatewayCount() <= 0) {
      $this->view->error = $this->view->translate('There are currently no ' .
        'enabled payment gateways. You must %1$sadd one%2$s before this ' .
        'page is available.', '<a href="' .
        $this->view->escape($this->view->url(array('module'    => 'store',
                                                   'controller'=> 'settings',
                                                   'action'    => 'gateway'), 'admin_default')) .
        '">', '</a>');
    }

    // Make form
    $this->view->formFilter = $formFilter = new Store_Form_Admin_Transaction_Filter();

    // Process form
    if ($formFilter->isValid($this->_getAllParams())) {
      $filterValues = $formFilter->getValues();
    } else {
      $filterValues = array();
    }
    if (empty($filterValues['order'])) {
      $filterValues['order'] = 'transaction_id';
    }
    if (empty($filterValues['direction'])) {
      $filterValues['direction'] = 'DESC';
    }
    $this->view->filterValues = $filterValues;
    $this->view->order        = $filterValues['order'];
    $this->view->direction    = $filterValues['direction'];

    /**
     * Initialize select
     *
     * @var $table Store_Model_DbTable_Transactions
     */
    $table  = Engine_Api::_()->getDbtable('transactions', 'store');
    $prefix = $table->getTablePrefix();
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'))
      ->joinInner($prefix . 'store_orders', $prefix . 'store_transactions.order_id = ' . $prefix . 'store_orders.order_id', array($prefix . 'store_orders.ukey'));

    // Add filter values
    if (!empty($filterValues['gateway_id'])) {
      $select->where($prefix . 'store_transactions.gateway_id = ?', $filterValues['gateway_id']);
    }
    if (!empty($filterValues['item_type'])) {
      $select->where($prefix . 'store_transactions.item_type = ?', $filterValues['item_type']);
    }
    if (!empty($filterValues['state'])) {
      $select->where($prefix . 'store_transactions.state = ?', $filterValues['state']);
    }
    if (!empty($filterValues['member'])) {
      $select
        ->joinRight('engine4_users', $prefix . 'users.user_id=' . $prefix . 'store_transactions.user_id', array())
        ->where('displayname LIKE ?', '%' . $filterValues['member'] . '%');
    }
    if (!empty($filterValues['ukey'])) {
      $select
        ->where($prefix . 'store_orders.ukey LIKE ?', '%' . $filterValues['ukey'] . '%');
    }
    if (!empty($filterValues['order'])) {
      if (empty($filterValues['direction'])) {
        $filterValues['direction'] = 'DESC';
      }
      $select->order($filterValues['order'] . ' ' . $filterValues['direction']);
    }
    $select
      ->where($prefix . 'store_transactions.order_id != ?', 0);

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Preload info
    $gatewayIds = array();
    $userIds    = array();
    foreach ($paginator as $transaction) {
      if (!empty($transaction->gateway_id)) {
        $gatewayIds[] = $transaction->gateway_id;
      }
      if (!empty($transaction->user_id)) {
        $userIds[] = $transaction->user_id;
      }
    }
    $gatewayIds = array_unique($gatewayIds);
    $userIds    = array_unique($userIds);

    // Preload gateways
    $gateways = array();
    if (!empty($gatewayIds)) {
      foreach (Engine_Api::_()->getItemTable('store_gateway')->find($gatewayIds) as $gateway) {
        $gateways[$gateway->gateway_id] = $gateway;
      }
    }
    $this->view->gateways = $gateways;

    // Preload users
    $users = array();
    if (!empty($userIds)) {
      foreach (Engine_Api::_()->getItemTable('user')->find($userIds) as $user) {
        $users[$user->user_id] = $user;
      }
    }
    $this->view->users = $users;
  }

  public function detailAction()
  {
    // Missing transaction
    if (!($transaction_id = $this->_getParam('transaction_id'))) {
      return;
    }

    $transactionTb = Engine_Api::_()->getDbTable('transactions', 'store');
    $select        = $transactionTb
      ->select()
      ->where('transaction_id = ?', $transaction_id)
      ->where('order_id != ?', 0);

    /**
     * @var $order Store_Model_Order
     * @var $transaction Store_Model_Transaction
     */
    if (
      null == ($transaction = $transactionTb->fetchRow($select)) ||
      null == ($order = Engine_Api::_()->getItem('store_order', $transaction->order_id))
    ) {
      return;
    }

    //Shipping Details
    if ($order->item_type == 'store_cart' && isset($order->shipping_details)) {
      if (isset($order->shipping_details['location_id_1']) &&
        null != ($country = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_1']))
      ) {
        $this->view->country = $country;

        if (isset($order->shipping_details['location_id_2']) &&
          null != ($state = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_2']))
        ) {
          $this->view->state = $state;
        }
      }
    }
    $this->view->page_id       = $transaction->getStoreId();
    $this->view->items         = $items = $order->getItems($this->view->page_id);
    $this->view->store_enabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
    $this->view->type          = 'products';
    $this->view->transaction   = $transaction;
    $this->view->order         = $order;
    $this->view->gateway       = Engine_Api::_()->getItem('store_gateway', $transaction->gateway_id);
    $this->view->user          = Engine_Api::_()->getItem('user', $transaction->user_id);
  }

  public function detailTransactionAction()
  {
    $transaction_id = $this->_getParam('transaction_id');
    $transactionTb  = Engine_Api::_()->getDbTable('transactions', 'store');
    $select         = $transactionTb
      ->select()
      ->where('transaction_id = ?', $transaction_id);
    $transaction    = $transactionTb->fetchRow($select);

    $gateway = Engine_Api::_()->getItem('store_gateway', $transaction->gateway_id);

    $link = null;
    if (!empty($transaction->gateway_transaction_id)) {
      $link = $gateway->getPlugin()->getTransactionDetailLink($transaction->gateway_transaction_id);
    }

    if ($link) {
      return $this->_helper->redirector->gotoUrl($link, array('prependBase' => false));
    } else {
      die();
    }
  }
}