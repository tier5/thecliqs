<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: 2Checkout.php 27.07.11 15:15 ulan t $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Plugin_Gateway_2Checkout extends Payment_Plugin_Gateway_2Checkout
{

  public function createPageSubscription( Zend_Db_Table_Row_Abstract $subscription, Page_Model_Package $package, array $params = array() )
  {
    // Do stuff to params
    $params['fixed'] = true;
    $params['skip_landing'] = true;


    try {
      $info = $this->getService()->detailVendorProduct($package->getGatewayIdentity());
    } catch( Exception $e ) {
      $info = false;
    }
    // Create
    if( !$info ) {
      $this->getService()->createProduct($package->getGatewayParams());
    }
    $productInfo = $this->getService()->detailVendorProduct($package->getGatewayIdentity());

    $params['product_id'] = $productInfo['product_id'];
    $params['quantity'] = 1;

    // Create transaction
    $transaction = $this->createTransaction($params);

    return $transaction;
  }

  /**
   * Process return of subscription transaction
   *
   * @param Page_Model_Order $order
   * @param array $params
   */
  public function onPageSubscriptionReturn(
    Payment_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if( $order->gateway_id != $this->_gatewayInfo->gateway_id ) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    // Get related info
    $user = $order->getUser();
    $subscription = $order->getSource();
    $package = $subscription->getPackage();

    // Check subscription state
    if( $subscription->status == 'active' ||
      $subscription->status == 'trial') {
      return 'active';
    } else if( $subscription->status == 'pending' ) {
      return 'pending';
    }

    // Let's log it
    $this->getGateway()->getLog()->log('Return: '
      . print_r($params, true), Zend_Log::INFO);

    // Check for processed
    if( empty($params['credit_card_processed']) ) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      $order->onFailure();
      $subscription->onPaymentFailure();

      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure product ids match
    if( $params['merchant_product_id'] != $package->getGatewayIdentity() ) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      $order->onFailure();
      $subscription->onPaymentFailure();

      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure order ids match
    if( $params['order_id'] != $order->order_id &&
      $params['merchant_order_id'] != $order->order_id ) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      $order->onFailure();
      $subscription->onPaymentFailure();

      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure vendor ids match
    if( $params['sid'] != $this->getGateway()->getVendorIdentity() ) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      $order->onFailure();
      $subscription->onPaymentFailure();

      throw new Payment_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    // Validate return
    try {
      $this->getGateway()->validateReturn($params);
    } catch( Exception $e ) {
      if( !$this->getGateway()->getTestMode() ) {
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        $order->onFailure();
        $subscription->onPaymentFailure();

        throw new Payment_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
      } else {
        echo $e; // For test mode
      }
    }

    // @todo process total?

    // Update order with profile info and complete status?
    $order->state = 'complete';
    $order->gateway_order_id = $params['order_number'];
    $order->save();

// Get benefit setting
    $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'payment')
      ->getBenefitStatus($user);

    // Enable now
    if( $giveBenefit ) {

      // Update subscription
      $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
      $subscription->gateway_profile_id = $params['order_number']; // This is the same as sale_id
      $subscription->onPaymentSuccess();

      // send notification
      if( $subscription->didStatusChange() ) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
          'subscription_title' => $package->title,
          'subscription_description' => $package->description,
          'subscription_terms' => $package->getPackageDescription(),
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        ));
      }

      return 'active';
    }

    // Enable later
    else {

      // Update subscription
      $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
      $subscription->gateway_profile_id = $params['order_number']; // This is the same as sale_id
      $subscription->onPaymentPending();

      // send notification
      if( $subscription->didStatusChange() ) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_pending', array(
          'subscription_title' => $package->title,
          'subscription_description' => $package->description,
          'subscription_terms' => $package->getPackageDescription(),
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        ));
      }

      return 'pending';
    }
  }
}