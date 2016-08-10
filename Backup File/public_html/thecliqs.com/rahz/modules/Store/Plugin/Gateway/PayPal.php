<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PayPal.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Plugin_Gateway_PayPal extends Experts_Payment_Plugin_Abstract
{
  // General

  /**
   * Get the gateway object
   *
   * @throws Engine_Exception
   * @return Experts_Payment_Gateway
   */
  public function getGateway()
  {
    if (null === $this->_gateway) {
      $class = 'Experts_Payment_Gateway_PayPal';
      Engine_Loader::loadClass($class);
      $gateway = new $class(array(
        'config'   => (array)$this->_gatewayInfo->config,
        'testMode' => $this->_gatewayInfo->test_mode,
        'currency' => Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD'),
      ));
      if (!($gateway instanceof Experts_Payment_Gateway)) {
        throw new Engine_Exception('Plugin class not instance of Experts_Payment_Gateway');
      }
      $this->_gateway = $gateway;
    }

    return $this->_gateway;
  }

  public function createCartTransaction(Store_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Experts_Payment_Plugin_Exception('Gateways do not match');
    }

    // Check that item_type match
    if ($order->item_type != 'store_cart') {
      throw new Experts_Payment_Plugin_Exception("Wrong item_type has been provided. Method requires 'store_cart'");
    }

    // Check if the cart has any items
    if (!$order->hasItems()) {
      throw new Experts_Payment_Plugin_Exception("Provided store_cart doesn't have any order items");
    }

    /**
     * @var $storeApi  Store_Api_Core
     * @var $orderItem Store_Model_Orderitem
     */
    $storeApi = Engine_Api::_()->store();

    // Payment mode: Client-Store(Direct) or Client-Site-Store mode?
    $mode = $storeApi->getPaymentMode();
    $paymentRequest = array();
    if ($mode == 'client_store') {
      $stores = $order->getStores();
      $store_iter = 0;
      foreach ($stores as $page_id => $store) {
        // Prepare request items
        $orderItems = $order->getItems($page_id);
        $requestItems = array();

        foreach ($orderItems as $key => $orderItem) {
          $description = '';
          $product = Engine_Api::_()->getItem('store_product', $orderItem->item_id);
          if ($product) {
            $description = Zend_Registry::get('Zend_View')->string()->truncate($product->getDescription(), 100) . "\n";
          }
          $requestItems = array_merge($requestItems, array(
            'L_PAYMENTREQUEST_' . $store_iter . '_NAME' . $key   => $orderItem->name,
            'L_PAYMENTREQUEST_' . $store_iter . '_NUMBER' . $key => $orderItem->orderitem_id,
            'L_PAYMENTREQUEST_' . $store_iter . '_DESC' . $key   => $description . $storeApi->params_string($orderItem->params),
            'L_PAYMENTREQUEST_' . $store_iter . '_AMT' . $key    => $orderItem->item_amt,
            'L_PAYMENTREQUEST_' . $store_iter . '_QTY' . $key    => $orderItem->qty,
          ));
        }

        if (null == ($api = Engine_Api::_()->getDbTable('apis', 'store')->getApi($page_id, $this->_gatewayInfo->gateway_id)) ||
          !$api->enabled
        ) {
          continue;
        }
        $seller = $api->getEmail();

        // Prepare payment request
        $paymentRequest = array_merge($paymentRequest, array(
          'PAYMENTREQUEST_' . $store_iter . '_SELLERPAYPALACCOUNTID' => $seller,
          'PAYMENTREQUEST_' . $store_iter . '_DESC'                  => $store,
          'PAYMENTREQUEST_' . $store_iter . '_PAYMENTREQUESTID'      => $page_id,
          'PAYMENTREQUEST_' . $store_iter . '_ITEMAMT'               => $order->getStoreInfo($page_id, 'item_amt'), //tj@todo track values only for exist-products
          'PAYMENTREQUEST_' . $store_iter . '_TAXAMT'                => $order->getStoreInfo($page_id, 'tax_amt'),
          'PAYMENTREQUEST_' . $store_iter . '_SHIPPINGAMT'           => $order->getStoreInfo($page_id, 'shipping_amt'),
          'PAYMENTREQUEST_' . $store_iter . '_PAYMENTACTION'         => 'order',
          'PAYMENTREQUEST_' . $store_iter . '_AMT'                   => $order->getStoreInfo($page_id, 'total_amt'),
          'PAYMENTREQUEST_' . $store_iter . '_CURRENCYCODE'          => $order->currency
        ), $requestItems);
        $store_iter ++;
      }

      //Shipping Details
      if (isset($order->shipping_details) &&
        isset($order->shipping_details['location_id_1']) &&
        null != ($country = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_1']))
      ) {
        $shippingDetails = array(
          'ADDROVERRIDE'                       => 1,
          'PAYMENTREQUEST_0_SHIPTOSTREET'      => $order->shipping_details['address_line_1'],
          'PAYMENTREQUEST_0_SHIPTOCITY'        => $order->shipping_details['city'],
          'PAYMENTREQUEST_0_SHIPTOZIP'         => $order->shipping_details['zip'],
          'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => $country->location_code,
          'PAYMENTREQUEST_0_SHIPTOPHONENUM'    => $order->shipping_details['phone_extension'] + '-' + $order->shipping_details['phone'],
        );

        if (isset($order->shipping_details['address_line_2'])) {
          $shippingDetails['PAYMENTREQUEST_0_SHIPTOSTREET2'] = $order->shipping_details['address_line_2'];
        }

        if (isset($order->shipping_details['location_id_2']) &&
          null != ($state = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_2']))
        ) {
          $shippingDetails['PAYMENTREQUEST_0_SHIPTOSTATE'] = $state->location_code;
        }
        $paymentRequest = array_merge($paymentRequest, $shippingDetails);
      }

    } else {
      // Prepare request items
      $orderItems   = $order->getItems();
      $requestItems = array();

      foreach ($orderItems as $key => $orderItem) {
        $description = '';
        $product = Engine_Api::_()->getItem('store_product', $orderItem->item_id);
        if ($product) {
          $description = Zend_Registry::get('Zend_View')->string()->truncate($product->getDescription(), 100) . "\n";
        }
        $requestItems = array_merge($requestItems, array(
          'L_PAYMENTREQUEST_0_NAME'   . $key => $orderItem->name,
          'L_PAYMENTREQUEST_0_NUMBER' . $key => $orderItem->orderitem_id,
          'L_PAYMENTREQUEST_0_DESC'   . $key => $description . $storeApi->params_string($orderItem->params),
          'L_PAYMENTREQUEST_0_AMT'    . $key => $orderItem->item_amt,
          'L_PAYMENTREQUEST_0_QTY'    . $key => $orderItem->qty
        ));
      }

      // Prepare payment request
      $paymentRequest = array_merge($requestItems, array(
        'ITEMAMT'                        => $order->item_amt,
        'TAXAMT'                         => $order->tax_amt,
        'SHIPPINGAMT'                    => $order->shipping_amt,
        'ALLOWNOTE'                      => 1,
        'PAYMENTREQUEST_0_PAYMENTACTION' => 'order',
        'PAYMENTREQUEST_0_AMT'           => $order->total_amt,
        'PAYMENTREQUEST_0_CURRENCYCODE'  => $order->currency,
      ));

      //Shipping Details
      if (isset($order->shipping_details) &&
        isset($order->shipping_details['location_id_1']) &&
        null != ($country = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_1']))
      ) {
        $shippingDetails = array(
          'ADDROVERRIDE'                       => 1,
          'PAYMENTREQUEST_0_SHIPTOSTREET'      => $order->shipping_details['address_line_1'],
          'PAYMENTREQUEST_0_SHIPTOCITY'        => $order->shipping_details['city'],
          'PAYMENTREQUEST_0_SHIPTOZIP'         => $order->shipping_details['zip'],
          'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => $country->location_code,
          'PAYMENTREQUEST_0_SHIPTOPHONENUM'    => $order->shipping_details['phone_extension'] + '-' + $order->shipping_details['phone'],
        );
        if (isset($order->shipping_details['address_line_2'])) {
          $shippingDetails['PAYMENTREQUEST_0_SHIPTOSTREET2'] = $order->shipping_details['address_line_2'];
        }
        if (isset($order->shipping_details['location_id_2']) &&
          null != ($state = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_2']))
        ) {
          $shippingDetails['PAYMENTREQUEST_0_SHIPTOSTATE'] = $state->location_code;
        }

        $paymentRequest = array_merge($paymentRequest, $shippingDetails);
      }
    }

    $params['driverSpecificParams']['PayPal'] = $paymentRequest;
    $transaction = $this->createTransaction($params);
    return $transaction;
  }

  public function onCartTransactionReturn(Store_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Experts_Payment_Plugin_Exception('Gateways do not match');
    }

    // Check that item_type match
    if ($order->item_type != 'store_cart') {
      throw new Experts_Payment_Plugin_Exception("Wrong item_type has been provided. Method requires 'store_cart'");
    }

    // Check if the cart has any items
    if (!$order->hasItems()) {
      throw new Experts_Payment_Plugin_Exception("Provided store_cart doesn't have any order items");
    }


    // Check for cancel state - the user cancelled the transaction
    if ($params['state'] == 'cancel') {
      $order->onPaymentFailure();
      // Error
      throw new Store_Model_Exception('Your payment has been cancelled and ' .
        'not been purchased. If this is not correct, please try again later.');
    }

    // Check params
    if (empty($params['token'])) {
      $order->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    // Get details
    try {
      $data = $this->getService()->detailExpressCheckout($params['token']);
    } catch (Exception $e) {
      // Cancel order and order?
      $order->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Let's log it
    $this->getGateway()->getLog()->log('ExpressCheckoutDetail: '
      . print_r($data, true), Zend_Log::INFO);

    /**
     * @var $storeApi Store_Api_Core
     */

    $storeApi = Engine_Api::_()->store();
    $mode = $storeApi->getPaymentMode();

    $paymentRequest = array();
    $invnum = null;
    foreach ($data['PAYMENTREQUEST'] as $store_iter => $request) {
      $paymentRequest = array_merge($paymentRequest, array(
        'PAYMENTREQUEST_' . $store_iter . '_ITEMAMT'               => $request['ITEMAMT'],
        'PAYMENTREQUEST_' . $store_iter . '_TAXAMT'                => $request['TAXAMT'],
        'PAYMENTREQUEST_' . $store_iter . '_AMT'                   => $request['AMT'],
        'PAYMENTREQUEST_' . $store_iter . '_SHIPPINGAMT'           => $request['SHIPPINGAMT'],
        'PAYMENTREQUEST_' . $store_iter . '_PAYMENTACTION'         => 'sale',
        'PAYMENTREQUEST_' . $store_iter . '_CURRENCYCODE'          => $request['CURRENCYCODE']
      ));

      if ($mode == 'client_store') {
        $paymentRequest = array_merge($paymentRequest, array(
          'PAYMENTREQUEST_' . $store_iter . '_SELLERPAYPALACCOUNTID' => $request['SELLERPAYPALACCOUNTID'],
          'PAYMENTREQUEST_' . $store_iter . '_PAYMENTREQUESTID'      => $request['PAYMENTREQUESTID'],
        ));
      }

      if (isset($request['INVNUM']) && !empty($request['INVNUM'])) {
        $invnum = $request['INVNUM'];
      }
    }

    if ($order->ukey != $invnum) {
      $order->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    try {
      $rdata = $this->getService()->doExpressCheckoutPayment($params['token'], $params['PayerID'], $paymentRequest);
    } catch (Exception $e) {
      // Log the error
      $this->getGateway()->getLog()->log('DoExpressCheckoutPaymentError: '
        . $e->__toString(), Zend_Log::ERR);

      // Cancel order and order?
      $order->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    // Let's log it
    $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
      . print_r($rdata, true), Zend_Log::INFO);

    foreach ($rdata['PAYMENTINFO'] as $info) {
      if ($mode == 'client_store') {
        $orderItems = $order->getItems(isset($info['PAYMENTREQUESTID']) ? $info['PAYMENTREQUESTID'] : null);

        foreach ($orderItems as $orderItem) {
          $orderItem->gateway_transaction_id = $info['TRANSACTIONID'];
          $orderItem->save();
        }
      } else {
        $order->gateway_transaction_id = $info['TRANSACTIONID'];
        $order->save();
      }

      // Get payment status
      $paymentStatus = null;
      $orderStatus   = null;

      switch (strtolower($info['PAYMENTSTATUS'])) {
        case 'created':
        case 'pending':
          $paymentStatus = 'pending';
          break;

        case 'completed':
        case 'processed':
        case 'canceled_reversal': // Probably doesn't apply
          $paymentStatus = 'okay';
          break;

        case 'denied':
        case 'failed':
        case 'voided': // Probably doesn't apply
        case 'reversed': // Probably doesn't apply
        case 'refunded': // Probably doesn't apply
        case 'expired': // Probably doesn't apply
        default: // No idea what's going on here
          $paymentStatus = 'failed';
          break;
      }

      /**
       * Insert transaction
       *
       * @var $transactionsTable Store_Model_DbTable_Transactions
       * */
      $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'store');
      $db                = $transactionsTable->getAdapter();
      $db->beginTransaction();

      try {
        $transactionsTable->insert(array(
          'item_id'                => $order->item_id,
          'item_type'              => $order->item_type,
          'order_id'               => $order->order_id,
          'user_id'                => $order->user_id,
          'timestamp'              => new Zend_Db_Expr('NOW()'),
          'state'                  => $paymentStatus,
          'gateway_id'             => $this->_gatewayInfo->gateway_id,
          'gateway_transaction_id' => $info['TRANSACTIONID'],
          'gateway_fee'            => isset($info['FEEAMT']) ? $info['FEEAMT'] : 0.00,
          'amt'                    => $info['AMT'],
          'currency'               => $info['CURRENCYCODE'],
        ));

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
      }
    }

    $user = $order->getUser();

    // Check payment status
    if ($paymentStatus == 'okay' && $rdata['ACK'] == 'Success') {

      // Payment success
      $order->onPaymentSuccess();

      // send notification
      if ($order->didStatusChange()) {
        try {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_cart_complete', array(
            'order_details' => $order->getDetails(),
            'order_link'    => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        } catch (Exception $e) {
          print_log($e, 'mail');
        }
      }

      return $order->status;
    } else if ($paymentStatus == 'pending') {

      // Payment pending
      $order->onPaymentPending();

      // send notification
      if ($order->didStatusChange()) {
        try {
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_cart_pending', array(
            'order_details' => $order->getDetails(),
            'order_link'    => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        } catch (Exception $e) {
          print_log($e, 'mail');
        }
      }

      return $order->status;
    } else if ($paymentStatus == 'failed') {

      // Payment failed
      $order->onPaymentFailure();

      throw new Store_Model_Exception('Your payment could not be ' .
        'completed. Please ensure there are sufficient available funds ' .
        'in your account.');
    } else {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
  }


  public function onCartTransactionIpn(Store_Model_Order $order, Experts_Payment_Ipn $ipn)
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    // Get related info
    $user = $order->getUser();

    $translate = Zend_Registry::get('Zend_Translate');
    // Get IPN data
    $rawData = $ipn->getRawData();

    $transactionDetails = array(
      array(
        'label' => $translate->_('Gateway Title'),
        'value' => $this->_gatewayInfo->title,
      ),
      array(
        'label' => $translate->_('Payment Type'),
        'value' => $rawData['txn_type']
      ),
      array(
        'label' => $translate->_('Sale ID'),
        'value' => $rawData['txn_id']
      ),
      array(
        'label' => $translate->_('Invoice ID'),
        'value' => $rawData['invoice']
      ),
      array(
        'label' => $translate->_('Invoice Amount'),
        'value' => $rawData['mc_gross']
      ),
      array(
        'label' => $translate->_('Currency'),
        'value' => $rawData['mc_currency']
      )
    );
    $transactionDetails = Engine_Api::_()->store()->params_string($transactionDetails);

    // Chargeback --------------------------------------------------------------
    if (!empty($rawData['case_type']) && $rawData['case_type'] == 'chargeback') {
      $order->onPaymentFailure(); // or should we use pending?
    }

    // Transaction Type --------------------------------------------------------
    else if (!empty($rawData['txn_type'])) {
      if ($rawData['txn_type'] == 'express_checkout') {
        // Only allowed for one-time
        switch ($rawData['payment_status']) {

          case 'Created': // Not sure about this one
            $order->onPaymentSuccess();
            break;
          case 'Pending':
            $order->onPaymentPending();
            if ($order->didStatusChange()) {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_transaction_pending', array(
                'order_details'       => $order->getDetails(),
                'transaction_details' => $transactionDetails,
                'order_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
                  Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
              ));
            }
            break;
          case 'Completed':
          case 'Processed':
          case 'Canceled_Reversal': // Not sure about this one
            $order->onPaymentSuccess();
            // send notification
            if ($order->didStatusChange()) {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_transaction_success', array(
                'order_details'       => $order->getDetails(),
                'transaction_details' => $transactionDetails,
                'order_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
                  Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
              ));
            }
            break;

          case 'Denied':
          case 'Failed':
          case 'Voided':
          case 'Reversed':
            $order->onPaymentFailure();
            // send notification
            if ($order->didStatusChange()) {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_transaction_overdue', array(
                'order_details'       => $order->getDetails(),
                'transaction_details' => $transactionDetails,
                'order_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
                  Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
              ));
            }
            break;

          case 'Refunded':
          case 'Expired': // Not sure about this one
            // Payment Refunded
            $order->onRefund();
            // send notification
            if ($order->didStatusChange()) {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_transaction_refunded', array(
                'order_details'       => $order->getDetails(),
                'transaction_details' => $transactionDetails,
                'order_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
                  Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
              ));
            }
            break;

          default:
            throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
              'payment status %1$s', $rawData['payment_status']));
            break;
        }
      }
      else {
        throw new Engine_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
          'type %1$s', $rawData['txn_type']));
      }
    }

    // Payment Status ----------------------------------------------------------
    else if (!empty($rawData['payment_status'])) {
      switch ($rawData['payment_status']) {

        case 'Created': // Not sure about this one
          $order->onPaymentSuccess();
          break;
        case 'Pending':
          $order->onPaymentPending();
          break;
        case 'Completed':
        case 'Processed':
        case 'Canceled_Reversal': // Not sure about this one
          $order->onPaymentSuccess();
          // send notification
          if ($order->didStatusChange()) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_transaction_success', array(
              'order_details'       => $order->getDetails(),
              'transaction_details' => $transactionDetails,
              'order_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
          }
          break;

        case 'Denied':
        case 'Failed':
        case 'Voided':
        case 'Reversed':
          $order->onPaymentFailure();
          // send notification
          if ($order->didStatusChange()) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_transaction_overdue', array(
              'order_details'       => $order->getDetails(),
              'transaction_details' => $transactionDetails,
              'order_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
          }
          break;

        case 'Refunded':
        case 'Expired': // Not sure about this one
          if ($order->didStatusChange()) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_transaction_refunded', array(
              'order_details'       => $order->getDetails(),
              'transaction_details' => $transactionDetails,
              'order_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
          }
          break;

        default:
          throw new Experts_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
            'payment status %1$s', $rawData['payment_status']));
          break;
      }
    }

    // Unknown -----------------------------------------------------------------
    else {
      throw new Experts_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
        'data structure'));
    }

    return $this;
  }

  /**
   * Create a transaction for a order
   *
   * @param Store_Model_Order $order
   * @param array             $params
   *
   * @throws Experts_Payment_Plugin_Exception
   * @return Experts_Payment_Transaction
   */
  public function createRequestTransaction(Store_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Experts_Payment_Plugin_Exception('Gateways do not match');
    }

    // Check that item_type match
    if ($order->item_type != 'store_request') {
      throw new Experts_Payment_Plugin_Exception("Wrong item_type has been provided. Method requires 'store_request'");
    }

    // Check if the order has item
    if (null == ($request = $order->getItem())) {
      throw new Experts_Payment_Plugin_Exception("Provided store_request doesn't exist");
    }

    // Check if the request has page
    if (null == ($page = $request->getOwner())) {
      throw new Experts_Payment_Plugin_Exception("Provided store doesn't exist");
    }
    // Check if the page owner
    if (null == ($user = $page->getOwner())) {
      throw new Experts_Payment_Plugin_Exception("Store owner doesn't exist or has been deleted");
    }

    // Prepare request item
    $view           = Zend_Registry::get('Zend_View');
    $paymentRequest = array(
      'L_PAYMENTREQUEST_0_NAME0'       => $view->translate('STORE_Money Request'),
      'L_PAYMENTREQUEST_0_DESC0'       => $view->translate('Store Name - %1s, Store Owner - %2s', $page->getTitle(), $user->getTitle()),
      'L_PAYMENTREQUEST_0_AMT0'        => $order->item_amt,

      'ITEMAMT'                        => $order->item_amt,
      'ALLOWNOTE'                      => 1,
      'PAYMENTREQUEST_0_PAYMENTACTION' => 'order',
      'PAYMENTREQUEST_0_AMT'           => $order->total_amt,
      'PAYMENTREQUEST_0_CURRENCYCODE'  => $order->currency,
    );

    //Shipping Details
    $params['driverSpecificParams']['PayPal'] = $paymentRequest;

    $transaction = $this->createTransaction($params);

    return $transaction;
  }


  public function onRequestTransactionReturn(Store_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Experts_Payment_Plugin_Exception('Gateways do not match');
    }

    // Check that item_type match
    if ($order->item_type != 'store_request') {
      throw new Experts_Payment_Plugin_Exception("Wrong item_type has been provided. Method requires 'store_request'");
    }

    // Check if the order has item
    if (null == ($request = $order->getItem())) {
      throw new Experts_Payment_Plugin_Exception("Provided store_request doesn't exist");
    }

    // Check if the request has page
    if (null == ($page = $request->getOwner())) {
      throw new Experts_Payment_Plugin_Exception("Provided store doesn't exist");
    }
    // Check if the page owner
    if (null == ($owner = $page->getOwner())) {
      throw new Experts_Payment_Plugin_Exception("Store owner doesn't exist or has been deleted");
    }


    // Check for cancel state - the user cancelled the transaction
    if ($params['state'] == 'cancel') {
      $order->onPaymentFailure();
      // Error
      throw new Store_Model_Exception('Your transaction has been cancelled. If this is not correct, please try again later.');
    }

    // Check params
    if (empty($params['token'])) {
      $order->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    // Get details
    try {
      $data = $this->getService()->detailExpressCheckout($params['token']);
    } catch (Exception $e) {
      // Cancel order and order?
      $order->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    // Let's log it
    $this->getGateway()->getLog()->log('ExpressCheckoutDetail: '
      . print_r($data, true), Zend_Log::INFO);

    $info = $data['PAYMENTREQUEST'][0];
    if ($order->ukey != $info['INVNUM']) {
      $order->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    //Prepare variables
    $paymentRequest = array(
      'ITEMAMT'                        => $info['ITEMAMT'],
      'PAYMENTREQUEST_0_PAYMENTACTION' => 'sale',
      'PAYMENTREQUEST_0_AMT'           => $info['AMT'],
      'PAYMENTREQUEST_0_CURRENCYCODE'  => $info['CURRENCYCODE']
    );

    try {
      $rdata = $this->getService()->doExpressCheckoutPayment($params['token'], $params['PayerID'], $paymentRequest);
    } catch (Exception $e) {
      // Log the error
      $this->getGateway()->getLog()->log('DoExpressCheckoutPaymentError: '
        . $e->__toString(), Zend_Log::ERR);

      // Cancel order and order?
      $order->onPaymentFailure();
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    // Let's log it
    $this->getGateway()->getLog()->log('DoExpressCheckoutPayment: '
      . print_r($rdata, true), Zend_Log::INFO);

    $info = $rdata['PAYMENTINFO'][0];

    $order->gateway_transaction_id = $info['TRANSACTIONID'];
    $order->save();

    // Get payment status
    $paymentStatus = null;
    $orderStatus   = null;

    switch (strtolower($info['PAYMENTSTATUS'])) {
      case 'created':
      case 'pending':
        $paymentStatus = 'pending';
        break;

      case 'completed':
      case 'processed':
      case 'canceled_reversal': // Probably doesn't apply
        $paymentStatus = 'okay';
        break;

      case 'denied':
      case 'failed':
      case 'voided': // Probably doesn't apply
      case 'reversed': // Probably doesn't apply
      case 'refunded': // Probably doesn't apply
      case 'expired': // Probably doesn't apply
      default: // No idea what's going on here
        $paymentStatus = 'failed';
        break;
    }

    /**
     * Insert transaction
     *
     * @var $transactionsTable Store_Model_DbTable_Transactions
     * */
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'store');
    $db                = $transactionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $transactionsTable->insert(array(
        'item_id'                => $order->item_id,
        'item_type'              => $order->item_type,
        'order_id'               => $order->order_id,
        'user_id'                => $order->user_id,
        'timestamp'              => new Zend_Db_Expr('NOW()'),
        'state'                  => $paymentStatus,
        'gateway_id'             => $this->_gatewayInfo->gateway_id,
        'gateway_transaction_id' => $info['TRANSACTIONID'],
        'gateway_fee'            => isset($info['FEEAMT']) ? $info['FEEAMT'] : 0.00,
        'amt'                    => (int)-$info['AMT'],
        'currency'               => $info['CURRENCYCODE']
      ));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
    }

    // Check payment status
    if ($paymentStatus == 'okay') {

      // Payment success
      $order->onPaymentSuccess();

      // send notification
      if ($order->didStatusChange()) {
      }

      return $order->status;
    }
    else if ($paymentStatus == 'pending') {

      // Payment pending
      $order->onPaymentPending();

      // send notification
      if ($order->didStatusChange()) {
      }

      return $order->status;
    }
    else if ($paymentStatus == 'failed') {

      // Payment failed
      $order->onPaymentFailure();

      throw new Store_Model_Exception('Your payment could not be ' .
        'completed. Please ensure there are sufficient available funds ' .
        'in your account.');
    }
    else {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
  }

  /**
   * Process ipn of money request transaction
   *
   * @param Store_Model_Order   $order
   * @param Experts_Payment_Ipn $ipn
   *
   * @throws Experts_Payment_Plugin_Exception
   * @return Store_Plugin_Gateway_PayPal
   */
  public function onRequestTransactionIpn(
    Store_Model_Order $order,
    Experts_Payment_Ipn $ipn)
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Experts_Payment_Plugin_Exception('Gateways do not match');
    }

    // Check that item_type match
    if ($order->item_type != 'store_request') {
      throw new Experts_Payment_Plugin_Exception("Wrong item_type has been provided. Method requires 'store_request'");
    }

    // Check if the order has item
    if (null == ($request = $order->getItem())) {
      throw new Experts_Payment_Plugin_Exception("Provided store_request doesn't exist");
    }

    // Check if the request has page
    if (null == ($page = $request->getOwner())) {
      throw new Experts_Payment_Plugin_Exception("Provided store doesn't exist");
    }
    // Check if the page owner
    if (null == ($owner = $page->getOwner())) {
      throw new Experts_Payment_Plugin_Exception("Store owner doesn't exist or has been deleted");
    }

    // Get related info
    $user = $order->getUser();

    $translate = Zend_Registry::get('Zend_Translate');
    // Get IPN data
    $rawData = $ipn->getRawData();

    $transactionDetails = array(
      array(
        'label' => $translate->_('Gateway Title'),
        'value' => $this->_gatewayInfo->title,
      ),
      array(
        'label' => $translate->_('Payment Type'),
        'value' => $rawData['txn_type']
      ),
      array(
        'label' => $translate->_('Sale ID'),
        'value' => $rawData['txn_id']
      ),
      array(
        'label' => $translate->_('Invoice ID'),
        'value' => $rawData['invoice']
      ),
      array(
        'label' => $translate->_('Invoice Amount'),
        'value' => $rawData['mc_gross']
      ),
      array(
        'label' => $translate->_('Currency'),
        'value' => $rawData['mc_currency']
      )
    );
    $transactionDetails = Engine_Api::_()->store()->params_string($transactionDetails);

    // Chargeback --------------------------------------------------------------
    if (!empty($rawData['case_type']) && $rawData['case_type'] == 'chargeback') {
      $order->onPaymentFailure(); // or should we use pending?
    }

    // Transaction Type --------------------------------------------------------
    else if (!empty($rawData['txn_type'])) {
      if ($rawData['txn_type'] == 'express_checkout') {
        // Only allowed for one-time
        switch ($rawData['payment_status']) {

          case 'Created': // Not sure about this one
            $order->onPaymentSuccess();
            break;
          case 'Pending':
            $order->onPaymentPending();
            break;
          case 'Completed':
          case 'Processed':
          case 'Canceled_Reversal': // Not sure about this one
            $order->onPaymentSuccess();
            // send notification
            if ($order->didStatusChange()) {
            }
            break;

          case 'Denied':
          case 'Failed':
          case 'Voided':
          case 'Reversed':
            $order->onPaymentFailure();
            // send notification
            if ($order->didStatusChange()) {
            }
            break;

          case 'Refunded':
          case 'Expired': // Not sure about this one
            // Payment Refunded
            $order->onRefund();
            // send notification
            if ($order->didStatusChange()) {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_transaction_refunded', array(
                'order_details'       => $order->getDetails(),
                'transaction_details' => $transactionDetails,
                'order_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
                  Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
              ));
            }
            break;

          default:
            throw new Experts_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
              'payment status %1$s', $rawData['payment_status']));
            break;
        }
      }
      else {
        throw new Experts_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
          'type %1$s', $rawData['txn_type']));
      }
    }

    // Payment Status ----------------------------------------------------------
    else if (!empty($rawData['payment_status'])) {
      switch ($rawData['payment_status']) {

        case 'Created': // Not sure about this one
          $order->onPaymentSuccess();
          // send notification
          if ($order->didStatusChange()) {
          }
          break;
        case 'Pending':
          $order->onPaymentPending();
          // send notification
          if ($order->didStatusChange()) {
          }
          break;
        case 'Completed':
        case 'Processed':
        case 'Canceled_Reversal': // Not sure about this one
          $order->onPaymentSuccess();
          // send notification
          if ($order->didStatusChange()) {
          }
          break;

        case 'Denied':
        case 'Failed':
        case 'Voided':
        case 'Reversed':
          $order->onPaymentFailure();
          // send notification
          if ($order->didStatusChange()) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_transaction_overdue', array(
              'order_details'       => $order->getDetails(),
              'transaction_details' => $transactionDetails,
              'order_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
          }
          break;

        case 'Refunded':
        case 'Expired': // Not sure about this one
          if ($order->didStatusChange()) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_transaction_refunded', array(
              'order_details'       => $order->getDetails(),
              'transaction_details' => $transactionDetails,
              'order_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
          }
          break;

        default:
          throw new Experts_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
            'payment status %1$s', $rawData['payment_status']));
          break;
      }
    }

    // Unknown -----------------------------------------------------------------
    else {
      throw new Experts_Payment_Plugin_Exception(sprintf('Unknown IPN ' .
        'data structure'));
    }

    return $this;
  }

  // IPN
  /**
   * @param Experts_Payment_Ipn $ipn
   *
   * @return Experts_Payment_Plugin_Abstract|Store_Plugin_Gateway_PayPal
   * @throws Engine_Payment_Plugin_Exception
   */
  public function onIpn(Experts_Payment_Ipn $ipn)
  {
    $rawData = $ipn->getRawData();

    /**
     * @var $ordersTable       Store_Model_DbTable_Orders
     * @var $transactionsTable Store_Model_DbTable_Transactions
     */
    $ordersTable       = Engine_Api::_()->getDbtable('orders', 'store');
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'store');


    // Find transactions -------------------------------------------------------
    $transactionId = null;
    $transaction   = null;

    // Fetch by txn_id
    if (!empty($rawData['txn_id'])) {
      $transaction = $transactionsTable->fetchRow(array(
        'gateway_id = ?'             => $this->_gatewayInfo->gateway_id,
        'gateway_transaction_id = ?' => $rawData['txn_id'],
      ));
    }

    // Get transaction id
    if ($transaction) {
      $transactionId = $transaction->gateway_transaction_id;
    } else if (!empty($rawData['txn_id'])) {
      $transactionId = $rawData['txn_id'];
    }

    // Fetch order -------------------------------------------------------------
    $order = null;

    // Transaction IPN - get order by invoice
    if (!$order && !empty($rawData['invoice'])) {
      $order = $ordersTable->getOrderByUkey($rawData['invoice']);
    }

    // Transaction IPN - get order by txn_id
    if (!$order && $transactionId) {
      $order = $ordersTable->fetchRow(array(
        'gateway_id = ?'             => $this->_gatewayInfo->gateway_id,
        'gateway_transaction_id = ?' => $transactionId,
      ));
    }

    // Process generic IPN data ------------------------------------------------

    // Build transaction info
    if (!empty($rawData['txn_id'])) {
      $transactionData = array(
        'gateway_id' => $this->_gatewayInfo->gateway_id,
      );
      // Get timestamp
      if (!empty($rawData['payment_date'])) {
        $transactionData['timestamp'] = date('Y-m-d H:i:s', strtotime($rawData['payment_date']));
      } else {
        $transactionData['timestamp'] = new Zend_Db_Expr('NOW()');
      }
      // Get amount
      if (!empty($rawData['mc_gross'])) {
        $transactionData['amt'] = $rawData['mc_gross'];
      }

      if ($order && $order->item_type == 'store_request') {
        $transactionData['amt'] = -$transactionData['amt'];
      }
      // Get currency
      if (!empty($rawData['mc_currency'])) {
        $transactionData['currency'] = $rawData['mc_currency'];
      }
      // Get order/user
      if ($order) {
        $transactionData['user_id']  = $order->user_id;
        $transactionData['order_id'] = $order->order_id;
      }
      // Get transactions
      if ($transactionId) {
        $transactionData['gateway_transaction_id'] = $transactionId;
      }
      // Get payment_status
      switch ($rawData['payment_status']) {
        case 'Canceled_Reversal': // he@todo make sure this works

        case 'Completed':
        case 'Created':
        case 'Processed':
          $transactionData['type']  = 'store_cart';
          $transactionData['state'] = 'okay';
          break;

        case 'Denied':
        case 'Expired':
        case 'Failed':
        case 'Voided':
          $transactionData['type']  = 'store_cart';
          $transactionData['state'] = 'failed';
          break;

        case 'Pending':
          $transactionData['type']  = 'store_cart';
          $transactionData['state'] = 'pending';
          break;

        case 'Refunded':
          $transactionData['type']  = 'refund';
          $transactionData['state'] = 'refunded';
          break;
        case 'Reversed':
          $transactionData['type']  = 'reversal';
          $transactionData['state'] = 'reversed';
          break;

        default:
          $transactionData = 'unknown';
          break;
      }

      // Insert or update transactions
      if (!$transaction) {
        $transactionsTable->insert($transactionData);
      }
      // Update transaction
      else {
        unset($transactionData['timestamp']);
        $transaction->setFromArray($transactionData);
        $transaction->save();
      }
    }

    // Process specific IPN data -----------------------------------------------
    if ($order) {
      // Subscription IPN
      if ($order->item_type == 'store_cart') {
        $this->onCartTransactionIpn($order, $ipn);
        return $this;
      }elseif ($order->item_type == 'store_request') {
        $this->onRequestTransactionIpn($order, $ipn);
        return $this;
      }
      // Unknown IPN
      else {
        throw new Engine_Payment_Plugin_Exception('Unknown order type for IPN');
      }
    }
    // Missing order
    else {
      throw new Engine_Payment_Plugin_Exception('Unknown or unsupported IPN ' .
        'type, or missing transaction or order ID');
    }
  }

  public function processGatewayForm(array $values)
  {
    return $values;
  }

  /**
   * Get the service API
   *
   * @return Experts_Service_PayPal|Zend_Service_Abstract
   */
  public function getService()
  {
    return $this->getGateway()->getService();
  }

  /**
   * Get the admin form for editing the gateway info
   *
   * @param array $options
   *
   * @return Store_Form_Admin_Gateway_PayPal|Engine_Form
   */
  public function getAdminGatewayForm($options = array())
  {
    return new Store_Form_Admin_Gateway_PayPal($options);
  }

  public function processAdminGatewayForm(array $values)
  {
    return $values;
  }

  /**
   * Create an ipn object from specified parameters
   *
   * @param array $params
   *
   * @return Experts_Payment_Ipn
   */
  public function createIpn(array $params)
  {
    $ipn = new Experts_Payment_Ipn($params);
    $ipn->process($this->getGateway());
    return $ipn;
  }

  /**
   * Cancel a subscription (i.e. disable the recurring payment profile)
   *
   * @param $transactionId
   *
   * @return Experts_Payment_Plugin_Abstract
   */
  public function cancelOrder($transactionId)
  {
    // TODO: Implement cancelOrder() method.
  }

  /**
   * Generate href to a page detailing the order
   *
   * @param $orderId
   *
   * @return string
   */
  public function getOrderDetailLink($orderId)
  {
    // TODO: Implement getOrderDetailLink() method.
  }

  /**
   * Generate href to a page detailing the transaction
   *
   * @param string $transactionId
   *
   * @return string
   */
  public function getTransactionDetailLink($transactionId)
  {
    // @todo make sure this is correct
    if( $this->getGateway()->getTestMode() ) {
      // Note: it doesn't work in test mode
      return 'https://www.sandbox.paypal.com/vst/?id=' . $transactionId;
    } else {
      return 'https://www.paypal.com/vst/?id=' . $transactionId;
    }
  }

  /**
   * Get raw data about an order or recurring payment profile
   *
   * @param string $orderId
   *
   * @return array
   */
  public function getOrderDetails($orderId)
  {
    // TODO: Implement getOrderDetails() method.
  }

  /**
   * Get raw data about a transaction
   *
   * @param $transactionId
   *
   * @return array
   */
  public function getTransactionDetails($transactionId)
  {
    // TODO: Implement getTransactionDetails() method.
  }

  /**
   * @return Store_Form_Gateway_PayPal
   */
  public function getGatewayForm()
  {
    return new Store_Form_Gateway_PayPal();
  }
}