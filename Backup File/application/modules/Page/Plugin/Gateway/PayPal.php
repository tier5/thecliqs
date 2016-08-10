<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PayPal.php 27.07.11 15:15 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Plugin_Gateway_PayPal extends Payment_Plugin_Gateway_PayPal
{
  /**
   * Create a transaction for a subscription
   *
   * @param Page_Model_Page $page
   * @param Zend_Db_Table_Row_Abstract $subscription
   * @param Zend_Db_Table_Row_Abstract $package
   * @param array $params
   * @return Engine_Payment_Gateway_Transaction
   */
  public function createPageSubscription(
      Zend_Db_Table_Row_Abstract $subscription,
      Page_Model_Package $package,
      array $params = array())
  {
    // This is a one-time fee
    if( $package->isOneTime() ) {
      $params['driverSpecificParams']['PayPal'] = array(
        'AMT' => $package->price,
        'DESC' => $package->getPackageDescription(),
        'CUSTOM' => $subscription->subscription_id,
        'INVNUM' => $params['vendor_order_id'],
        'ITEMAMT' => $package->price,
        'ITEMS' => array(
          array(
            'NAME' => $package->title,
            'DESC' => $package->getPackageDescription(),
            'AMT' => $package->price,
            'NUMBER' => $subscription->subscription_id,
            'QTY' => 1,
          ),
        )
        //'BILLINGTYPE' => 'RecurringPayments',
        //'BILLINGAGREEMENTDESCRIPTION' => $package->getPackageDescription(),
      );
    }
    // This is a recurring subscription
    else {
      $params['driverSpecificParams']['PayPal'] = array(
        'BILLINGTYPE' => 'RecurringPayments',
        'BILLINGAGREEMENTDESCRIPTION' => $package->getPackageDescription(),
      );
    }

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
		/**
		 * @var $user User_Model_User
		 */
    $user = $order->getUser();

		/**
		 * @var $subscription Page_Model_Subscription
		 */
    $subscription = $order->getSource();

		/**
		 * @var $package Page_Model_Package
		 */
    $package = $subscription->getPackage();


    // Check subscription state
    if( $subscription->status == 'active' ||
        $subscription->status == 'trial') {
      return 'active';
    } else if( $subscription->status == 'pending' ) {
      return 'pending';
    }

    // Check for cancel state - the user cancelled the transaction
    if( $params['state'] == 'cancel' ) {
      // Cancel order and subscription?
      $order->onCancel();
      $subscription->onPaymentFailure();
      // Error
      throw new Payment_Model_Exception('Your payment has been cancelled and ' .
          'not been charged. If this is not correct, please try again later.');
    }

    // Check params
    if( empty($params['token']) ) {
      // Cancel order and subscription?
      $order->onFailure();
      $subscription->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
    }

    // Get details
    try {
      $data = $this->getService()->detailExpressCheckout($params['token']);
    } catch( Exception $e ) {
      // Cancel order and subscription?
      $order->onFailure();
      $subscription->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Payment_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
    }

    // Let's log it
    $this->getGateway()->getLog()->log('ExpressCheckoutDetail: '
        . print_r($data, true), Zend_Log::INFO);


    // One-time
    if( $package->isOneTime() ) {

      // Do payment
      try {
        $rdata = $this->getService()->doExpressCheckoutPayment($params['token'],
              $params['PayerID'], array(
          'PAYMENTACTION' => 'Sale',
          'AMT' => $data['AMT'],
          'CURRENCYCODE' => $this->getGateway()->getCurrency(),
        ));

      } catch( Exception $e ) {
        // Log the error
        $this->getGateway()->getLog()->log('DoExpressCheckoutPaymentError: '
            . $e->__toString(), Zend_Log::ERR);

        // Cancel order and subscription?
        $order->onFailure();
        $subscription->onPaymentFailure();
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
      }

      // Let's log it
      $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
          . print_r($rdata, true), Zend_Log::INFO);

      // Get payment state
      $paymentStatus = null;
      $orderStatus = null;
      switch( strtolower($rdata['PAYMENTINFO'][0]['PAYMENTSTATUS']) ) {
        case 'created':
        case 'pending':
          $paymentStatus = 'pending';
          $orderStatus = 'complete';
          break;

        case 'completed':
        case 'processed':
        case 'canceled_reversal': // Probably doesn't apply
          $paymentStatus = 'okay';
          $orderStatus = 'complete';
          break;

        case 'denied':
        case 'failed':
        case 'voided': // Probably doesn't apply
        case 'reversed': // Probably doesn't apply
        case 'refunded': // Probably doesn't apply
        case 'expired':  // Probably doesn't apply
        default: // No idea what's going on here
          $paymentStatus = 'failed';
          $orderStatus = 'failed'; // This should probably be 'failed'
          break;
      }

      // Update order with profile info and complete status?
      $order->state = $orderStatus;
      $order->gateway_transaction_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];
      $order->save();

      // Insert transaction
      $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'payment');
      $transactionsTable->insert(array(
        'user_id' => $order->user_id,
        'gateway_id' => $this->_gatewayInfo->gateway_id,
        'timestamp' => new Zend_Db_Expr('NOW()'),
        'order_id' => $order->order_id,
        'type' => 'page',
        'state' => $paymentStatus,
        'gateway_transaction_id' => $rdata['PAYMENTINFO'][0]['TRANSACTIONID'],
        'amount' => $rdata['AMT'], // @todo use this or gross (-fee)?
        'currency' => $rdata['PAYMENTINFO'][0]['CURRENCYCODE'],
      ));

      // Check payment status
      if( $paymentStatus == 'okay' || $paymentStatus == 'pending' ) {

        // Update subscription info
        $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
        $subscription->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

        // Payment success
        $subscription->onPaymentSuccess();

        // send notification
        if( $subscription->didStatusChange() ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'page_subscription_active', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        }

        return 'active';
      }
      else if( $paymentStatus == 'pending' ) {

        // Update subscription info
        $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
        $subscription->gateway_profile_id = $rdata['PAYMENTINFO'][0]['TRANSACTIONID'];

        // Payment pending
        $subscription->onPaymentPending();

        // send notification
        if( $subscription->didStatusChange() ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'page_subscription_pending', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        }

        return 'pending';
      }
      else if( $paymentStatus == 'failed' ) {
        // Cancel order and subscription?
        $order->onFailure();
        $subscription->onPaymentFailure();
        // Payment failed
        throw new Payment_Model_Exception('Your payment could not be ' .
            'completed. Please ensure there are sufficient available funds ' .
            'in your account.');
      }
      else {
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
      }
    }

    // Recurring
    else {
      // Check for errors
      if( empty($data) ) {
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
      } else if( empty($data['BILLINGAGREEMENTACCEPTEDSTATUS']) ||
          '0' == $data['BILLINGAGREEMENTACCEPTEDSTATUS'] ) {
        // Cancel order and subscription?
        $order->onCancel();
        $subscription->onPaymentFailure();
        // Error
        throw new Payment_Model_Exception('Your payment has been cancelled and ' .
            'not been charged. If this in not correct, please try again later.');
      } else if( !isset($data['PAYMENTREQUESTINFO'][0]['ERRORCODE']) ||
          '0' != $data['PAYMENTREQUESTINFO'][0]['ERRORCODE'] ) {
        // Cancel order and subscription?
        $order->onFailure();
        $subscription->onPaymentFailure();
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
      }

      // Create recurring payments profile
      $rpData = array(
        'TOKEN' => $params['token'],
        'PROFILEREFERENCE' => $order->order_id,
        'PROFILESTARTDATE' => $data['TIMESTAMP'],
        'DESC' => $package->getPackageDescription(),
        'BILLINGPERIOD' => ucfirst($package->recurrence_type),
        'BILLINGFREQUENCY' => $package->recurrence,
        'INITAMT' => 0,
        'AMT' => $package->price,
        'CURRENCYCODE' => $this->getGateway()->getCurrency(),
      );

      $count = $package->getTotalBillingCycleCount();
      if( $count ) {
        $rpData['TOTALBILLINGCYCLES'] = $count;
      }

      // Create recurring payment profile
      try {
        $rdata = $this->getService()->createRecurringPaymentsProfile($rpData);
      } catch( Exception $e ) {
        // Cancel order and subscription?
        $order->onFailure();
        $subscription->onPaymentFailure();
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
      }

      // Let's log it
      $this->getGateway()->getLog()->log('CreateRecurringPaymentsProfile: '
          . print_r($rdata, true), Zend_Log::INFO);

      // Check returned profile id
      if( empty($rdata['PROFILEID']) ) {
        // Cancel order and subscription?
        $order->onFailure();
        $subscription->onPaymentFailure();
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
      }
      $profileId = $rdata['PROFILEID'];

      // Update order with profile info and complete status?
      $order->state = 'complete';
      $order->gateway_order_id = $profileId;
      $order->save();

      // Check profile status
      if( $rdata['PROFILESTATUS'] == 'ActiveProfile' || $rdata['PROFILESTATUS'] == 'PendingProfile' ) {
        // Enable now
        $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
        $subscription->gateway_profile_id = $rdata['PROFILEID'];
        $subscription->onPaymentSuccess();

        // send notification
        if( $subscription->didStatusChange() ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'page_subscription_active', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        }

        return 'active';

      } else if( $rdata['PROFILESTATUS'] == 'PendingProfile' ) {
        // Enable later
        $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
        $subscription->gateway_profile_id = $rdata['PROFILEID'];
        $subscription->onPaymentPending();

        // send notification
        if( $subscription->didStatusChange() ) {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'page_subscription_pending', array(
            'subscription_title' => $package->title,
            'subscription_description' => $package->description,
            'subscription_terms' => $package->getPackageDescription(),
            'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        }

        return 'pending';

      } else {
        // Cancel order and subscription?
        $order->onFailure();
        $subscription->onPaymentFailure();
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Payment_Model_Exception('There was an error processing your ' .
            'transaction. Please try again later.');
      }
    }
	}
}