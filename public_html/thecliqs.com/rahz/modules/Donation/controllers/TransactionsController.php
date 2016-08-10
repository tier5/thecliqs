<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 22.08.12
 * Time: 12:57
 * To change this template use File | Settings | File Templates.
 */
class Donation_TransactionsController extends Core_Controller_Action_Standard
{
  public function init()
  {

  }

  public function indexAction()
  {


  }

  public function listAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->donation_id = $donation_id = $this->_getParam('donation_id');
    $this->view->donation = $donation = Engine_Api::_()->getItem('donation', $donation_id);
    if (!$donation){
      return $this->_forward('requiresubject', 'error', 'core');
    }
    $this->view->subject = $page = $donation->getPage();
    if ($page) {
      if(!$page->getDonationPrivacy($donation->type)){
        return $this->_forward('requireauth', 'error', 'core');
      }
    } elseif (!$donation->isOwner($viewer)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    /**
     * @var $table Donation_Model_DbTable_Transactions
     */
    $this->view->form = $form = new Donation_Form_Transaction_Filter();

    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
    } else {
      $values = array();
    }
    if (empty($values['order'])) {
      $values['order'] = 'transaction_id';
    }
    if (empty($values['direction'])) {
      $values['direction'] = 'DESC';
    }

    $this->view->values = $values;
    $this->view->order = $values['order'];
    $this->view->direction = $values['direction'];

    $this->view->donation = $donation = Engine_Api::_()->getItem('donation', $donation_id);

    if ($donation == null) {
      $this->_helper->content
        ->setNoRender()
        ->setEnabled();
    }
    $table = Engine_Api::_()->getDbTable('transactions', 'donation');
    $prefix = $table->getTablePrefix();
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('t' => $table->info('name')))
      ->where('item_id = ?', $donation_id)
      ->join(array('g' => $prefix . 'payment_gateways'), 'g.gateway_id = t.gateway_id', array('gateway' => 'title'))
    ;
    if (!empty($values['name'])) {
      $select
        ->where('name LIKE ?', '%' . $values['name'] . '%');
    }
    if (!empty($values['order'])) {
      if (empty($values['direction'])) {
        $values['direction'] = 'DESC';
      }
      $select->order($values['order'] . ' ' . $values['direction']);
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');

    $userIds = array();
    $orderIds = array();
    foreach ($paginator as $item) {
      if (!empty($item->user_id)) {
        $userIds[] = $item->user_id;
      }

      if (!empty($item->order_id)) {
        $orderIds[] = $item->order_id;
      }
    }

    $userIds = array_unique($userIds);
    $orderIds = array_unique($orderIds);

    // Preload users
    $users = array();
    if (!empty($userIds)) {
      foreach (Engine_Api::_()->getItemTable('user')->find($userIds) as $user) {
        $users[$user->user_id] = $user;
      }
    }
    $this->view->users = $users;

    // Preload orders
    $orders = array();
    if (!empty($orderIds)) {
      foreach (Engine_Api::_()->getItemTable('transaction')->find($orderIds) as $order) {
        $orders[$order->order_id] = $order;
      }
    }
    $this->view->orders = $orders;
  }

  public function detailAction()
  {
    $item_id = $this->_getParam('transaction_id');

    if (null == ($item = Engine_Api::_()->getItem('transaction', $item_id))) {
      $this->_forward('success', 'utility', 'core', array(
        'redirect' => $this->view->url(array(
          'controller' => 'transactions',
          'action' => 'list',
          'donation_id' => $this->_getParam('donation_id'), 'default', true
          )),
        'redirectTime'   => 1000,
        'messages'       => $this->view->translate("DONATION_No order found with the provided id.")
      ));
    }

    $this->view->item = $item;
    $this->view->user = Engine_Api::_()->getItem('user', $item->user_id);

    $this->view->donation = Engine_Api::_()->getItem('donation', $item->item_id);

    //Transaction details
    $table = Engine_Api::_()->getDbTable('transactions', 'donation');
    $select = $table
      ->select()
      ->where('gateway_id = ?', $item->gateway_id)
      ->where('gateway_transaction_id = ?', $item->gateway_transaction_id);

    if (null == ($transaction = $table->fetchRow($select))) {
      $this->_forward('success', 'utility', 'core', array(
        'redirect' => $this->view->url(array(
          'controller' => 'transactions',
          'action' => 'list',
          'donation_id' => $this->_getParam('donation_id'), 'default', true
        )),
        'redirectTime'   => 1000,
        'messages'       => $this->view->translate("DONATION_No order found with the provided id.")
      ));
    }
    $this->view->transaction = $transaction;
  }
}
