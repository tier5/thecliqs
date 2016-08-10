<?php
class Yncredit_UpgradeSubscriptionController extends Core_Controller_Action_Standard
{
  /**
   * @var User_Model_User
   */
  protected $_user;

  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;

  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('yncredit_main');

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('payment')) {
      $this->_redirectCustom($this->view->url(array(), 'yncredit_general', true));
    }

    // Get user and session
    $this->_user = Engine_Api::_()->user()->getViewer();
    $this->_session = new Zend_Session_Namespace('Payment_Subscription');

    // Check viewer and user
	if (!$this -> _user || !$this -> _user -> getIdentity()) {
		if (!empty($this -> _session -> user_id)) {
			$this -> _user = Engine_Api::_() -> getItem('user', $this -> _session -> user_id);
		}
		// If no user, redirect to home?
		if (!$this -> _user || !$this -> _user -> getIdentity()) {
			$this -> _session -> unsetAll();
			return $this -> _helper -> redirector -> gotoRoute(array(), 'default', true);
		}
	}
	if( !Engine_Api::_()->authorization()->isAllowed('yncredit', $this -> _user, 'use_credit') )
	{
		return $this -> _helper -> redirector -> gotoRoute(array(), 'default', true);
	}
  }

  public function confirmAction()
  {
    $this->view->package_id = $package_id = $this->_getParam('package_id', null);

    if ($package_id !== null) {
      $this->_helper->layout->setLayout('default-simple');
    }

    // Process
    $this->view->result = true;

    if ($package_id === null && isset($this->_session->subscription_id)) {
      $subscription = Engine_Api::_()->getItem('payment_subscription', $this->_session->subscription_id);
      $package_id = $subscription->package_id;
      $this->view->cancel_url = Zend_Controller_Front::getInstance()->getRouter()
        ->assemble(
          array(
            'action' => 'index',
            'controller' => 'settings',
            'module' => 'payment'
          ), 'default', true);
    }

    // Get packages
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $this->view->package = $package = $packagesTable->fetchRow(array(
      'enabled = ?' => 1,
      'package_id = ?' => $package_id,
    ));

    // Check if it exists
    if (!$package) {
      $this->view->message = Zend_Registry::get('Zend_View')->translate('Please choose one now below.');
      return;
    }

    $level = Engine_Api::_()->getItem('authorization_level', $this->_user->level_id);
    if (in_array($level->type, array('admin', 'moderator'))) {
      $this->view->message = Zend_Registry::get('Zend_View')->translate('Subscriptions are not required for administrators and moderators.');
      return;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
    $credits = ceil($package->price * $defaultPrice);

    $balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
    if (!$balance) {
      $currentBalance = 0;
    } else {
      $currentBalance = $balance->current_credit;
    }
    $this->view->currentBalance = $currentBalance;
    $this->view->enoughCredits = $this->_checkEnoughCredits($credits);
    $this->view->packageDescription = Engine_Api::_()->yncredit()->getPackageDescription($package);

    // Get current subscription and package
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
    $currentSubscription = $subscriptionsTable->fetchRow(array(
      'user_id = ?' => $this -> _user -> getIdentity(),
      'active = ?' => true,
    ));

    // Get current package
    $currentPackage = null;
    if ($currentSubscription) {
      $currentPackage = $packagesTable->fetchRow(array(
        'package_id = ?' => $currentSubscription->package_id,
      ));
    }

    // Check if they are the same
    if ($currentPackage && $package->package_id == $currentPackage->package_id) {
      return $this->_helper->redirector->gotoRoute(array(), 'yncredit_general', true);
    }

    // Check method
    if (!$this->getRequest()->isPost()) {
      return;
    }

    // Cancel any other existing subscriptions
    Engine_Api::_()->getDbtable('subscriptions', 'payment')
      ->cancelAll($this -> _user, 'User cancelled the subscription.', $currentSubscription);


    // Insert the new temporary subscription
    $db = $subscriptionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $subscription = $subscriptionsTable->createRow();
      $subscription->setFromArray(array(
        'package_id' => $package->package_id,
        'user_id' => $this -> _user->getIdentity(),
        'status' => 'initial',
        'notes' => 'credit',
        'active' => false, // Will set to active on payment success
        'creation_date' => new Zend_Db_Expr('NOW()'),
      ));
      $subscription->save();

      // If the package is free, let's set it active now and cancel the other
      if ($package->isFree()) {
        $subscription->setActive(true);
        $subscription->onPaymentSuccess();
        if ($currentSubscription) {
          $currentSubscription->cancel();
        }
      }

      $subscription_id = $subscription->subscription_id;

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    // Check if the subscription is ok
    if ($package->isFree() && $subscriptionsTable->check($this -> _user)) {
      return $this->_helper->redirector->gotoRoute(array(), 'yncredit_general', true);
    }

    // Prepare subscription session
    $session = new Zend_Session_Namespace('Payment_Subscription');
    $session->is_change = true;
    $session->user_id = $this -> _user->getIdentity();
    $session->subscription_id = $subscription_id;

    // Redirect to subscription handler
    return $this->_helper->redirector->gotoRoute(array('action' => 'process'));
  }

  public function processAction()
  {
    // Get gateway
    $this->view->gateway = $gateway = Engine_Api::_()->getDbTable('gateways', 'payment')->fetchRow(array('title = ?' => 'Testing'));

    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if (!$subscriptionId ||
      !($subscription = Engine_Api::_()->getItem('payment_subscription', $subscriptionId))
    ) {
      return $this->_helper->redirector->gotoRoute(array(), 'yncredit_general', true);
    }
    /**
     * @var $subscription Payment_Model_Subscription
     */
    $this->view->subscription = $subscription;

    // Get package
    $package = $subscription->getPackage();
    if (!$package || $package->isFree()) {
      return $this->_helper->redirector->gotoRoute(array(), 'yncredit_general', true);
    }
    $this->view->package = $package;

    // Check subscription?
    if ($this->_checkSubscriptionStatus($subscription)) {
      return;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $defaultPrice = $settings->getSetting('yncredit.credit_price', 100);
    $credits = ceil($package->price * $defaultPrice);

    if (!$this->_checkEnoughCredits($credits)) {
      $this->_forward('success', 'utility', 'core', array(
        'parentRedirect' => Zend_Controller_Front::getInstance()
          ->getRouter()
          ->assemble(
            array(),
            'yncredit_general', true
          ),
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('CREDIT_not-enough-credit'))
      ));
    }

    // Process

    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    if (!empty($this->_session->order_id)) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }
    $ordersTable->insert(array(
      'user_id' => $this->_user->getIdentity(),
      'gateway_id' => $gateway->gateway_id,
      'state' => 'pending',
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'source_type' => 'payment_subscription',
      'source_id' => $subscription->subscription_id,
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    Engine_Api::_()->yncredit()-> spendCredits($this->_user, (-1) * $credits, $package->getTitle(), 'upgrade_subscription');
    $order = Engine_Api::_()->getItem('payment_order', $order_id);
    $order->state = 'complete';
    $order->save();
    $subscription->onPaymentSuccess();

    $this->_finishPayment('active');
  }

  protected function _checkSubscriptionStatus(
    Zend_Db_Table_Row_Abstract $subscription = null)
  {
    if (!$this->_user) {
      return false;
    }

    if (null === $subscription) {
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
      $subscription = $subscriptionsTable->fetchRow(array(
        'user_id = ?' => $this->_user->getIdentity(),
        'active = ?' => true,
      ));
    }

    if (!$subscription) {
      return false;
    }

    if ($subscription->status == 'active' ||
      $subscription->status == 'trial'
    ) {
      if (!$subscription->getPackage()->isFree()) {
        $this->_finishPayment('active');
      } else {
        $this->_finishPayment('free');
      }
      return true;
    } else if ($subscription->status == 'pending') {
      $this->_finishPayment('pending');
      return true;
    }

    return false;
  }

  protected function _checkEnoughCredits($credits)
  {
    $balance = Engine_Api::_()->getItem('yncredit_balance', $this->_user->getIdentity());
    if (!$balance) {
      return false;
    }
    $currentBalance = $balance->current_credit;
    if ($currentBalance < $credits) {
      return false;
    }
    return true;
  }

  protected function _finishPayment($state = 'active')
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // No user?
    if (!$this->_user) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Log the user in, if they aren't already
    if (($state == 'active' || $state == 'free') &&
      $this->_user &&
      !$this->_user->isSelf($viewer) &&
      !$viewer->getIdentity()
    ) {
      Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
      Engine_Api::_()->user()->setViewer();
      $viewer = $this->_user;
    }

    // Handle email verification or pending approval
    if ($viewer->getIdentity() && !$viewer->enabled) {
      Engine_Api::_()->user()->setViewer(null);
      Engine_Api::_()->user()->getAuth()->getStorage()->clear();

      $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
      $confirmSession->approved = $viewer->approved;
      $confirmSession->verified = $viewer->verified;
      $confirmSession->enabled = $viewer->enabled;
      return $this->_helper->_redirector->gotoRoute(array('action' => 'confirm'), 'user_signup', true);
    }

    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $userIdentity = $this->_session->user_id;
    $this->_session->unsetAll();
    $this->_session->user_id = $userIdentity;
    $this->_session->errorMessage = $errorMessage;

    // Redirect
    if ($state == 'free') {
      return $this->_helper->redirector->gotoRoute(array(), 'yncredit_general', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state));
    }
  }

  public function finishAction()
  {
    $this->view->status = $status = $this->_getParam('state');
    $this->view->error = $this->_session->errorMessage;
  }
}