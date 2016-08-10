<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: BuyOfferController.php 10.09.12 13:57 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_BuyOfferController extends Core_Controller_Action_Standard
{
  /**
   * @var User_Model_User
   */
  protected $_user;

  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;

  /**
   * @var Offers_Model_Order
   */
  protected $_order;

  /**
   * @var Payment_Model_Gateway
   */
  protected $_gateway;

  /**
   * @var Offers_Model_Subscription
   */
  protected $_subscription;

  /**
   * @var Offers_Model_Offer
   */
  protected $_offer;

  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('credit_main');

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('offers')) {
      $this->_redirectCustom($this->view->url(array(), 'credit_general', true));
    }

    // Get user and session
    $this->_user = Engine_Api::_()->user()->getViewer();
    $this->_session = new Zend_Session_Namespace('Offer_Subscription');

    // Get offer
    $offerId = $this->_getParam('offer_id', $this->_getParam('offer_id', $this->_session->offer_id));
    if (!$offerId || !($this->_offer = Engine_Api::_()->getItem('offer', $offerId))) {
      $this->_goBack(false);
    }

    if (!$this->_offer || !$this->_offer->getPrice()) {
      $this->_goBack(false);
    }

    if (!($this->_offer->getCouponsCount() || $this->_offer->coupons_unlimit)) {
      $this->_goBack();
    }

    if ($this->_offer->isSubscribed($this->_user)) {
      $this->_goBack();
    }

    if (!$this->_offer->isOfferCredit()) {
      $this->_goBack();
    }

    // Check viewer and user
    if (!$this->_user || !$this->_user->getIdentity()) {
      $this->_goBack();
    }

    // Check subscription status
    if ($this->_checkOfferStatus()) {
      $this->_goBack();
    }
  }

  public function indexAction()
  {
    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if (!$subscriptionId ||
        !($subscription = Engine_Api::_()->getItem('offers_subscription', $subscriptionId))) {
      $this->_goBack();
    }
    $this->view->subscription = $subscription;

    // Get package
    $offer = $subscription->getOffer();
    $this->view->offer = $offer;
    $this->_session->offer_id = $offer->getIdentity();

    // Process
    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'offers');
    if (!empty($this->_session->order_id)) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }
    $ordersTable->insert(array(
      'user_id' => $this->_user->getIdentity(),
      'gateway_id' => 0,
      'state' => 'pending',
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'source_type' => 'offers_subscription',
      'source_id' => $subscription->subscription_id,
    ));

    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();
    $this->view->order = Engine_Api::_()->getItem('offers_order', $order_id);
    $this->view->credits = Engine_Api::_()->getItem('credit_balance', $this->_user->getIdentity());
  }

  public function payAction()
  {
    /**
     * @var $order Offers_Model_Order
     * @var $subscription Offers_Model_Subscription
     */
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if (!$subscriptionId ||
        !($subscription = Engine_Api::_()->getItem('offers_subscription', $subscriptionId))) {
      $this->_goBack();
    }
    $this->view->subscription = $subscription;
    $offer = $subscription->getOffer();
    $orderId = $this->_getParam('order_id', $this->_session->order_id);
    $order = Engine_Api::_()->getItem('offers_order', $orderId);

    if (!$order) {
      $this->_goBack();
    }

    $credits = Engine_Api::_()->offers()->getCredits($offer->getPrice());
    $balances = Engine_Api::_()->getItem('credit_balance', $this->_user->getIdentity());

    if ($credits <= $balances->current_credit && $this->_getParam('status', '') == 'continue') {
      Engine_Api::_()->credit()->buyOffer($this->_user, (-1)*$credits, $offer->getIdentity());
      if ($offer->getPage()) {
        $owner = $offer->getOwner();
        $owner_balance = Engine_Api::_()->getItem('credit_balance', $owner->getIdentity());
        $owner_balance->setCredits($credits);
        $activity = Engine_Api::_()->getDbTable('actions', 'activity');
        $page = $this->_offer->getPage();
        if ($page) {
          $action = $activity->addActivity($this->_user, $page, 'page_offers_purchase', null, array('link' => $this->_offer->getLink()));
          $activity->attachActivity($action, $this->_offer, Activity_Model_Action::ATTACH_DESCRIPTION);
          $activity->addActivity($this->_user, $this->_offer, 'offers_purchase');
        } else {
          $activity->addActivity($this->_user, $this->_offer, 'offers_purchase');
        }
      }
      $paymentStatus = 'okay';
    } else {
      $paymentStatus = 'pending';
    }

    $order->state = 'complete';
    $order->save();

    // Insert transaction
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'offers');
    $transactionsTable->insert(array(
      'user_id' => $order->user_id,
      'gateway_id' => 0,
      'timestamp' => new Zend_Db_Expr('NOW()'),
      'order_id' => $order->order_id,
      'type' => 'payment',
      'state' => $paymentStatus,
      'amount' => $offer->getPrice(), // @todo use this or gross (-fee)?
      'currency' => Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD')
    ));

    if ($paymentStatus == 'okay') {
      $subscription->onPaymentSuccess();

      // send notification
      if( $subscription->didStatusChange() ) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($this->_user, 'offers_subscription_active', array(
          'subscription_title' => $offer->title,
          'subscription_description' => $offer->description,
          'subscription_terms' => $offer->getOfferDescription('active'),
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        ));
      }
      $this->view->status = 'active';
    } else {
      $subscription->onPaymentPending();

      // send notification
      if( $subscription->didStatusChange() ) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($this->_user, 'offers_subscription_pending', array(
          'subscription_title' => $offer->title,
          'subscription_description' => $offer->description,
          'subscription_terms' => $offer->getOfferDescription(),
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        ));
      }
      $this->view->status = 'pending';
    }

    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);
    unset($this->_session->errorMessage);
  }

  protected function _checkOfferStatus(Zend_Db_Table_Row_Abstract $subscription = null)
  {
    if( !$this->_user ) {
      return false;
    }

    if (null === $subscription) {
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'offers');
      $subscription = $subscriptionsTable->fetchRow(array(
        'user_id = ?' => $this->_user->getIdentity(),
        'offer_id = ?' => $this->_offer->getIdentity(),
        'active = ?' => true,
      ));
    }

    if (!$subscription) {
      return false;
    }

    if ($subscription->status == 'active' || $subscription->status == 'trial') {
      return true;
    } else if ($subscription->status == 'pending') {
      return true;
    }

    return false;
  }

  protected function _goBack($back = true)
  {
    unset($this->_session->offer_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);
    unset($this->_session->errorMessage);

    if ($back) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'offer_id' => $this->_offer->getIdentity()), 'offers_specific', true);
    }
    return $this->_helper->redirector->gotoRoute(array(), 'offers_upcoming', true);
  }
}