<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PackageController.php 27.07.11 12:29 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Page_PackageController extends Core_Controller_Action_Standard
{
  /**
   *@var Page_Model_Page
   */
  protected $_page;

  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;

  /**
   * @var Payment_Model_Package
   */
  protected $_package;



  public function init()
  {
    $this->_session = new Zend_Session_Namespace('Page_Subscription');

    // If no user, redirect to home?
    if (!$this->_helper->requireUser()->isValid())
    {
      return $this->_redirector();
    };

    /**
     * @var $viewer User_Model_User
     */
    $viewer = Engine_Api::_()->user()->getViewer();

    // If there are no enabled packages
    if( Engine_Api::_()->getDbtable('packages', 'page')->getEnabledPackageCount() <= 0 )
    {
      return $this->_redirector();
    }


    $page_id = (int) $this->_getParam('page_id', $this->_session->page_id);

    /**
     * If no page, redirect to browse?
     *
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->getItem('page', $page_id);

    if ( $page && !$page->isOwner($viewer) )
    {
      return $this->_redirector();
    }

    $this->_page = $page;
    $this->_session->page_id = $page_id;
  }

  public function indexAction()
  {
    return $this->_forward('choose');
  }

  public function chooseAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    // Make form
    $this->view->packages = Engine_Api::_()->getDbtable('packages', 'page')->getPackages(array('not_payed' => true));
    $this->view->page = $this->_page;
    $this->view->available_modules = Engine_Api::_()->getDbtable('modules', 'page')->getAvailableModules();
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_main');
    if( !$this->_page ) {
      foreach( $navigation as $page ) {
        if(get_class($page) == 'Zend_Navigation_Page_Mvc' && $page->getRoute() == 'page_create') {
          $page->setActive(true);
        }
      }
    }

    $this->view->payed_packages = Engine_Api::_()->getDbtable('packages', 'page')->getPackages(array('payed' => true));

    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'page');

    $params = $this->getRequest()->getPost();

    if( !($packageId = $params['package_id']) ||
      !($package = Engine_Api::_()->getItem('page_package', $packageId)) ) {
      return;
    }

    $this->view->package = $package;


    $page = $this->_page;

    // When choose (not create)
    if( $page ) {
      $currentSubscription = $subscriptionsTable->fetchRow(array(
        'page_id = ?' => $page->getIdentity(),
        'active = ?' => true,
      ));

      // Cancel any other existing subscriptions
      $subscriptionsTable->cancelAll($page, 'User cancelled the subscription.', $currentSubscription);
    }

    // Insert the new temporary subscription
    $db = $subscriptionsTable->getAdapter();
    $db->beginTransaction();

    $page_id = $this->_session->page_id;

    if( !empty($params['is_active']) && $params['is_active'] ) {

      $subscription_id = $params['subscription_id'];
      $subscription = Engine_Api::_()->getItem('page_subscription', $subscription_id);
      $subscription->page_id = $page_id;
      if( $currentSubscription )
        $currentSubscription->cancel();

      $subscription->save();
      $subscription->upgradePage();
      $db->commit();
      return $this->_helper->redirector->gotoRoute(array('action'=>'edit', 'page_id' => $page_id), 'page_team');
    } else {
      try {
        $subscription = $subscriptionsTable->createRow();
        $subscription->setFromArray(array(
          'package_id' => $package->getIdentity(),
          'page_id' => $page_id,
          'status' => 'initial',
          'active' => false, // Will set to active on payment success
          'creation_date' => new Zend_Db_Expr('NOW()'),
        ));
        $subscription->save();

        $subscription_id = $subscription->subscription_id;
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
    }


    // If the package is free, let's set it active now and cancel the other
    if( $package->isFree()) {
      if( $page && $currentSubscription ) {
        $currentSubscription->cancel();
      }

      $subscription->setActive(true);
      $subscription->onPaymentSuccess();

      if( !$page ) {
        return $this->_helper->redirector->gotoRoute(array('id'=>$subscription->getIdentity()), 'page_create');
      }


      $this->_page = $subscription->getPage();
    }


    $this->_session->subscription_id = $subscription_id;

    // Check if the user is good (this will happen if they choose a free plan)

    if( $package->isFree() || !empty($params['is_active']) && $params['is_active'] ) {
      return $this->_finishPayment($package->isFree() ? 'free' : 'active');
    }

    // Otherwise redirect to the payment page
    $this->_redirectCustom(array('route' => 'page_package', 'action' => 'gateway', 'subscription_id' => $subscription_id));
  }

  public function gatewayAction()
  {
    // If there are no enabled gateways or packages, disable
    if( Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'choose'));
    }

    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if( !$subscriptionId ||
      !($subscription = Engine_Api::_()->getItem('page_subscription', $subscriptionId))  ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'choose'));
    }
    $this->view->subscription = $subscription;

    // Check subscription status
    if( $this->_checkSubscriptionStatus($subscription) ) {
      return;
    }

    // Get subscription
    if( //!$this->_page ||
      !($subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id)) ||
      !($subscription = Engine_Api::_()->getItem('page_subscription', $subscriptionId)) ||
      //$subscription->page_id != $this->_page->getIdentity() ||
      !($package = Engine_Api::_()->getItem('page_package', $subscription->package_id)) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'choose'));
    }
    $this->view->subscription = $subscription;
    $this->view->package = $package;

    // Unset certain keys
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);

    // Gateways
    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    $gatewaySelect = $gatewayTable->select()
      ->where('enabled = ?', 1)
      //->where('title = ?', 'PayPal')
    ;
    $gateways = $gatewayTable->fetchAll($gatewaySelect);

    $gatewayPlugins = array();
    foreach( $gateways as $gateway ) {
      $gatewayPlugins[] = array(
        'gateway' => $gateway,
        'plugin' => $gateway->getGateway(),
      );
    }
    $this->view->gateways = $gatewayPlugins;
  }

  public function processAction()
  {
    // Get gateway
    $gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
    if( !$gatewayId ||
      !($gateway = Engine_Api::_()->getItem('payment_gateway', $gatewayId)) ||
      !($gateway->enabled) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
    }

    /**
     * @var $gateway Payment_Model_Gateway
     */
    $this->view->gateway = $gateway;

    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if( !$subscriptionId ||
      !($subscription = Engine_Api::_()->getItem('page_subscription', $subscriptionId))  ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'choose'));
    }

    /**
     * @var $subscription Page_Model_Subscription
     */
    $this->view->subscription = $subscription;

    /**
     * Get package
     *
     * @var $package Page_Model_Package
     */
    $package = $subscription->getPackage();
    if( !$package || $package->isFree() ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'choose'));
    }
    $this->view->package = $package;

    // Check subscription?
    if( $this->_checkSubscriptionStatus($subscription) ) {
      return;
    }

    // Process

    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    if( !empty($this->_session->order_id) ) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if( $previousOrder && $previousOrder->state == 'pending' ) {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }

    /**
     * @var $user User_Model_User;
     */
    $user = Engine_Api::_()->user()->getViewer();
    $ordersTable->insert(array(
      'user_id' => $user->getIdentity(),
      'gateway_id' => $gateway->gateway_id,
      'state' => 'pending',
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'source_type' => 'page_subscription',
      'source_id' => $subscription->subscription_id,
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    // Unset certain keys
    unset($this->_session->package_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);


    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();

    /**
     * @var $plugin Page_Plugin_Gateway_PayPal
     */
    $plugin = $gateway->getPlugin();

    // Get Store gateway plugin
    $str = str_replace('Payment', 'Page', get_class($plugin));
    $plugin = new $str( $gateway );


    // Prepare host info
    $schema = 'http://';
    if( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];


    // Prepare transaction
    $params = array();
    $params['vendor_order_id'] = $order_id;
    $params['return_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $order_id
      //. '?gateway_id=' . $this->_gateway->gateway_id
      //. '&subscription_id=' . $this->_subscription->subscription_id
      . '&state=' . 'return';
    $params['cancel_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $order_id
      //. '?gateway_id=' . $this->_gateway->gateway_id
      //. '&subscription_id=' . $this->_subscription->subscription_id
      . '&state=' . 'cancel';
    $params['ipn_url'] = $schema . $host
      . $this->view->url(array('action' => 'index', 'controller' => 'ipn'))
      . '?order_id=' . $order_id;
    //. '?gateway_id=' . $this->_gateway->gateway_id
    //. '&subscription_id=' . $this->_subscription->subscription_id;

    // Process transaction
    $transaction = $plugin->createPageSubscription($subscription, $package, $params);

    // Pull transaction params
    $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $this->view->transactionData = $transactionData = $transaction->getData();



    // Handle redirection
    if( $transactionMethod == 'GET' ) {
      $transactionUrl .= '?' . http_build_query($transactionData);
      return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
    }

    // Post will be handled by the view script
  }

  public function returnAction()
  {
    // Get order
    if( //!$this->_page ||
      !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
      !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
      $order->source_type != 'page_subscription' ||
      !($subscription = $order->getSource()) ||
      !($package = $subscription->getPackage()) ||
      !($gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id)) ) {

      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();

    /**
     * @var $plugin Page_Plugin_Gateway_PayPal
     */
    $plugin = $gateway->getPlugin();

    // Get Store gateway plugin
    $str = str_replace('Payment', 'Page', get_class($plugin));
    $plugin = new $str( $gateway );

    // Process return
    unset($this->_session->errorMessage);
    try {
      $status = $plugin->onPageSubscriptionReturn($order, $this->_getAllParams());
    } catch( Page_Model_Exception $e ) {
      $status = 'failure';
      $this->_session->errorMessage = $e->getMessage();
    }

    if( $subscription->page_id == 0 ) {
      return $this->_helper->redirector->gotoRoute(array('id' => $subscription->subscription_id), 'page_create', true);
    }
    return $this->_finishPayment($status);
  }

  protected function _checkSubscriptionStatus(
    Zend_Db_Table_Row_Abstract $subscription = null)
  {
    if( !$this->_page ) {
      return false;
    }

    if( null === $subscription ) {
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'page');
      $subscription = $subscriptionsTable->fetchRow(array(
        'page_id = ?' => $this->_page->getIdentity(),
        'active = ?' => true,
      ));
    }

    if( !$subscription ) {
      return false;
    }

    if( $subscription->status == 'active' ||
      $subscription->status == 'trial' ) {
      if( !$subscription->getPackage()->isFree() ) {
        $this->_finishPayment('active');
      } else {
        $this->_finishPayment('free');
      }
      return true;
    } else if( $subscription->status == 'pending' ) {
      $this->_finishPayment('pending');
      return true;
    }

    return false;
  }

  protected function _finishPayment($state = 'active')
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // No page?
//    if( !$this->_page ) {
//      return $this->_helper->redirector->gotoRoute(array(), 'page_create', true);
//    }

    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $page_id = $this->_session->page_id;
    $this->_session->unsetAll();
    $this->_session->page_id = $page_id;
    $this->_session->errorMessage = $errorMessage;

    // Redirect
    if( $state == 'free' ) {
      return $this->_helper->redirector->gotoRoute(array('action'=>'edit', 'page_id' => $page_id), 'page_team');
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state));
    }
  }

  public function finishAction()
  {
    $this->view->page = $this->_page;
    $this->view->status = $status = $this->_getParam('state');
    $this->view->error = $this->_session->errorMessage;
  }

  protected function _redirector()
  {
    $this->_session->unsetAll();
    return $this->_helper->redirector->gotoRoute(array(), 'page_browse', true);
  }
}