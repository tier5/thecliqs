<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: 2Checkout.php 9041 2011-06-30 04:37:55Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Store_Plugin_Gateway_2Checkout extends Experts_Payment_Plugin_Abstract
{
  // General

  /**
   * Constructor
   *
   * @param Zend_Db_Table_Row_Abstract $gatewayInfo
   */
  public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo)
  {
    $this->_gatewayInfo = $gatewayInfo;
  }

  /**
   * Get the service API
   *
   * @return Experts_Service_2Checkout|Zend_Service_Abstract
   */
  public function getService()
  {
    return $this->getGateway()->getService();
  }

  /**
   * Get the gateway object
   *
   * @throws Engine_Exception
   * @return Experts_Payment_Gateway|Experts_Payment_Gateway_2Checkout
   */
  public function getGateway()
  {
    if (null === $this->_gateway) {
      $class = 'Experts_Payment_Gateway_2Checkout';
      Engine_Loader::loadClass($class);
      $gateway = new $class(array(
        'config'   => (array)$this->_gatewayInfo->config,
        'testMode' => $this->_gatewayInfo->test_mode,
        'currency' => Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD'),
      ));
      if (!($gateway instanceof Experts_Payment_Gateway)) {
        throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
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

    // Prepare request items
    $orderItems   = $order->getItems();
    $requestItems = array();
    /**
     * @var $storeApi  Store_Api_Core
     * @var $orderItem Store_Model_Orderitem
     */
    $storeApi = Engine_Api::_()->store();
    foreach ($orderItems as $key => $orderItem) {
      $description = '';
      $product = Engine_Api::_()->getItem('store_product', $orderItem->item_id);
      if ($product) {
        $description = Zend_Registry::get('Zend_View')->string()->truncate($product->getDescription(), 100) . "\n";
      }
      $requestItems = array_merge($requestItems, array(
        'li_' . $key . '_type'               => 'product',
        'li_' . $key . '_name'               => $orderItem->name,
        'li_' . $key . '_quantity'           => $orderItem->qty,
        'li_' . $key . '_price'              => $orderItem->item_amt,
        'li_' . $key . '_tangible'           => 'N',
        'li_' . $key . '_product_id'         => $orderItem->item_id,
        'li_' . $key . '_product_description'=> $description . $storeApi->params_string($orderItem->params),
      ));
    }

    // Prepare payment request
    $key++;
    $paymentRequest = array_merge($requestItems, array(
        'li_' . $key . '_type'               => 'tax',
        'li_' . $key . '_name'               => 'Tax Amount',
        'li_' . $key . '_quantity'           => 1,
        'li_' . $key . '_price'              => $order->tax_amt,
        'li_' . $key . '_tangible'           => 'N',
        'li_' . $key . '_product_id'         => $order->getIdentity(),
      )
    );

    //Billing and Shipping Details
    if (isset($order->shipping_details) &&
      isset($order->shipping_details['location_id_1']) &&
      null != ($country = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_1']))
    ) {

      //Billing Details
      $billingRequest = array(
        'first_name'     => $order->shipping_details['first_name'],
        'middle_name'    => $order->shipping_details['middle_name'],
        'last_name'      => $order->shipping_details['last_name'],
        'street_address' => $order->shipping_details['address_line_1'],
        'city'           => $order->shipping_details['city'],
        'zip'            => $order->shipping_details['zip'],
        'country'        => $country->location,
        'email'          => $order->shipping_details['email'],
        'phone'          => $order->shipping_details['phone'],
        'phone_extension'=> $order->shipping_details['phone_extension'],
      );
      $paymentRequest = array_merge($paymentRequest, $billingRequest);

      //Shipping Details
      $key++;
      //hehe@todo rename type to shipping when single page payment integrates shipping details
      $shippingItem   = array(
        'li_' . $key . '_type'               => 'product',
        'li_' . $key . '_name'               => 'Shipping Amount',
        'li_' . $key . '_quantity'           => 1,
        'li_' . $key . '_price'              => $order->shipping_amt,
        'li_' . $key . '_tangible'           => 'N',
        'li_' . $key . '_product_id'         => $order->getIdentity(),
      );
      $paymentRequest = array_merge($shippingItem, $paymentRequest);

      $shippingDetails = array(
        'ship_name'                     => $order->shipping_details['first_name'] . ' ' . $order->shipping_details['last_name'],
        'ship_street_address'           => $order->shipping_details['address_line_1'],
        'ship_city'                     => $order->shipping_details['city'],
        'ship_zip'                      => $order->shipping_details['zip'],
        'ship_country'                  => $country->location,
      );
      if (isset($order->shipping_details['address_line_2'])) {
        $billingRequest['street_address2']       = $order->shipping_details['address_line_2'];
        $shippingDetails['ship_street_address2'] = $order->shipping_details['address_line_2'];
      }
      if (isset($order->shipping_details['location_id_2']) &&
        null != ($state = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_2']))
      ) {
        $billingRequest['state']       = $state->location;
        $shippingDetails['ship_state'] = $state->location;
      }

      $paymentRequest = array_merge($paymentRequest, $billingRequest);
    }

    $params['fixed']        = true;
    $params['skip_landing'] = true;

    $params = array_merge($params, $paymentRequest);

    // Create transaction
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

    // Let's log it
    $this->getGateway()->getLog()->log('Return: '
      . print_r($params, true), Zend_Log::INFO);

    // Check for processed
    if (empty($params['credit_card_processed'])) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure order ids match
    if ($params['merchant_order_id'] != $order->ukey) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure vendor ids match
    if ($params['sid'] != $this->getGateway()->getVendorIdentity()) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    /**
     * Ensure product ids match
     *
     * @var $orderItem Store_Model_Orderitem
     * */
    $orderItems    = $order->getItems();
    $checkShipping = false;
    foreach ($orderItems as $key=> $orderItem) {
      if ($params['li_' . $key . '_type'] != 'product' ||
        $params['li_' . $key . '_product_id'] != $orderItem->item_id ||
        $params['li_' . $key . '_price'] != round($orderItem->item_amt, 2)
      ) {
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Store_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
      }

      if (!$orderItem->isItemDigital()) {
        $checkShipping = true;
      }
    }

    //Ensure taxing details
    $key++;
    if ($params['li_' . $key . '_type'] != 'tax' ||
      $params['li_' . $key . '_product_id'] != $order->getIdentity() ||
      $params['li_' . $key . '_price'] != round($order->tax_amt, 2)
    ) {
      throw new Store_Model_Exception('STORE_TRANSACTION_TAX_ERROR');
    }

    //Ensure shipping details
    if ($checkShipping) {
      $key++;
      if ($params['li_' . $key . '_name'] != 'Shipping Amount' ||
        $params['li_' . $key . '_product_id'] != $order->getIdentity() ||
        $params['li_' . $key . '_price'] != round($order->shipping_amt, 2)
      ) {
        throw new Store_Model_Exception('STORE_TRANSACTION_SHIPPING_DETAILS_ERROR');
      }
    }

    // Validate return
    try {
      $this->getGateway()->validateReturn($params);
    } catch (Exception $e) {
      if (!$this->getGateway()->getTestMode()) {
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Store_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
      } else {
        echo $e; // For test mode
      }
    }

    $order->gateway_order_id = $params['order_number'];
    $order->save();

    // Transaction is inserted on IPN since it doesn't send the amount back

    $user = $order->getUser();

    //hehe@todo Get benefit?
  //$giveBenefit = Engine_Api::_()->getDbtable('transactions', 'payment')
  //->getBenefitStatus($user);
    $giveBenefit = false;

    // Enable now
    if ($giveBenefit) {

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
    }
    else {

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
    }
  }

  /**
   * Process ipn of subscription transaction
   *
   * @param Store_Model_Order   $order
   * @param Experts_Payment_Ipn $ipn
   *
   * @throws Experts_Payment_Plugin_Exception
   * @return Store_Plugin_Gateway_2Checkout
   */
  public function onCartTransactionIpn(
    Store_Model_Order $order,
    Experts_Payment_Ipn $ipn)
  {
    // Check that gateways match
    if ($order->gateway_id != $this->_gatewayInfo->gateway_id) {
      throw new Experts_Payment_Plugin_Exception('Gateways do not match');
    }

    // Get related info
    $user = $order->getUser();

    $translate = Zend_Registry::get('Zend_Translate');
    // Get IPN data
    $rawData            = $ipn->getRawData();
    $transactionDetails = array(
      array(
        'label' => $translate->_('Gateway Title'),
        'value' => $this->_gatewayInfo->title,
      ),
      array(
        'label' => $translate->_('Gateway Message'),
        'value' => $rawData['message_description'],
      ),
      array(
        'label' => $translate->_('Payment Type'),
        'value' => $rawData['payment_type']
      ),
      array(
        'label' => $translate->_('Sale ID'),
        'value' => $rawData['sale_id']
      ),
      array(
        'label' => $translate->_('Invoice ID'),
        'value' => $rawData['invoice_id']
      ),
      array(
        'label' => $translate->_('Invoice Amount'),
        'value' => $rawData['invoice_list_amount']
      ),
      array(
        'label' => $translate->_('Currency'),
        'value' => $rawData['list_currency']
      )
    );
    $transactionDetails = Engine_Api::_()->store()->params_string($transactionDetails);

    // switch message_type

    switch ($rawData['message_type']) {
      case 'ORDER_CREATED':
      case 'FRAUD_STATUS_CHANGED':
      case 'INVOICE_STATUS_CHANGED':
        // Check invoice and fraud status
        if (strtolower($rawData['invoice_status']) == 'declined' ||
          strtolower($rawData['fraud_status']) == 'fail'
        ) {
          // Payment failure
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
        } else if (strtolower($rawData['fraud_status']) == 'wait') {

          // Payment pending
          $order->onPaymentPending();
          // send notification
          if ($order->didStatusChange()) {
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_transaction_pending', array(
              'order_details'       => $order->getDetails(),
              'transaction_details' => $transactionDetails,
              'order_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
                Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
            ));
          }
        } else {
          // Payment Success
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
        }
        break;

      case 'REFUND_ISSUED':
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
          'type %1$s', $rawData['message_type']));
        break;
    }


    return $this;
  }

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
      'li_0_type'               => 'product',
      'li_0_name'               => $view->translate('STORE_Money Request'),
      'li_0_quantity'           => 1,
      'li_0_price'              => $order->total_amt,
      'li_0_tangible'           => 'N',
      'li_0_product_id'         => $order->item_id,
    );

    $params['fixed']        = true;
    $params['skip_landing'] = true;

    $params = array_merge($params, $paymentRequest);

    // Create transaction
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


    // Let's log it
    $this->getGateway()->getLog()->log('Return: '
      . print_r($params, true), Zend_Log::INFO);

    // Check for processed
    if (empty($params['credit_card_processed'])) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure order ids match
    if ($params['merchant_order_id'] != $order->ukey) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }
    // Ensure vendor ids match
    if ($params['sid'] != $this->getGateway()->getVendorIdentity()) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    //Ensure product ids match
    if ($params['li_0_type'] != 'product' ||
      $params['li_0_product_id'] != $order->item_id ||
      $params['li_0_price'] != $order->total_amt
    ) {
      // This is a sanity error and cannot produce information a user could use
      // to correct the problem.
      throw new Store_Model_Exception('There was an error processing your ' .
        'transaction. Please try again later.');
    }

    // Validate return
    try {
      $this->getGateway()->validateReturn($params);
    } catch (Exception $e) {
      if (!$this->getGateway()->getTestMode()) {
        // This is a sanity error and cannot produce information a user could use
        // to correct the problem.
        throw new Store_Model_Exception('There was an error processing your ' .
          'transaction. Please try again later.');
      } else {
        echo $e; // For test mode
      }
    }

    $order->gateway_order_id = $params['order_number'];
    $order->save();

    // Transaction is inserted on IPN since it doesn't send the amount back

    $user = $order->getUser();

    //hehe@todo info about benefit

    // Get benefit setting
    $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'payment')
      ->getBenefitStatus($user);

    // Enable now
    if ($giveBenefit) {

      // Payment success
      $order->onPaymentSuccess();

      // send notification
      if ($order->didStatusChange()) {
      }

      return $order->status;
    }
    else {

      // Payment pending
      $order->onPaymentPending();

      // send notification
      if ($order->didStatusChange()) {
      }

      return $order->status;
    }
  }

  /**
   * Process ipn of subscription transaction
   *
   * @param Store_Model_Order   $order
   * @param Experts_Payment_Ipn $ipn
   *
   * @throws Experts_Payment_Plugin_Exception
   * @return Store_Plugin_Gateway_2Checkout
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
    $rawData            = $ipn->getRawData();
    $transactionDetails = array(
      array(
        'label' => $translate->_('Gateway Title'),
        'value' => $this->_gatewayInfo->title,
      ),
      array(
        'label' => $translate->_('Gateway Message'),
        'value' => $rawData['message_description'],
      ),
      array(
        'label' => $translate->_('Payment Type'),
        'value' => $rawData['payment_type']
      ),
      array(
        'label' => $translate->_('Sale ID'),
        'value' => $rawData['sale_id']
      ),
      array(
        'label' => $translate->_('Invoice ID'),
        'value' => $rawData['invoice_id']
      ),
      array(
        'label' => $translate->_('Invoice Amount'),
        'value' => $rawData['invoice_list_amount']
      ),
      array(
        'label' => $translate->_('Currency'),
        'value' => $rawData['list_currency']
      )
    );
    $transactionDetails = Engine_Api::_()->store()->params_string($transactionDetails);

    // switch message_type

    switch ($rawData['message_type']) {
      case 'ORDER_CREATED':
      case 'FRAUD_STATUS_CHANGED':
      case 'INVOICE_STATUS_CHANGED':
        // Check invoice and fraud status
        if (strtolower($rawData['invoice_status']) == 'declined' ||
          strtolower($rawData['fraud_status']) == 'fail'
        ) {
          // Payment failure
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
        } else if (strtolower($rawData['fraud_status']) == 'wait') {

          // Payment pending
          $order->onPaymentPending();
          // send notification
          if ($order->didStatusChange()) {
          }
        } else {
          // Payment Success
          $order->onPaymentSuccess();
          // send notification
          if ($order->didStatusChange()) {
          }
        }
        break;

      case 'REFUND_ISSUED':
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
          'type %1$s', $rawData['message_type']));
        break;
    }


    return $this;
  }

  /**
   * Process an IPN
   *
   * @param Experts_Payment_Ipn $ipn
   *
   * @throws Experts_Payment_Plugin_Exception
   * @return Experts_Payment_Plugin_Abstract
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

    // Fetch by invoice_id
    if (!empty($rawData['invoice_id'])) {
      $transaction = $transactionsTable->fetchRow(array(
        'gateway_id = ?'             => $this->_gatewayInfo->gateway_id,
        'gateway_transaction_id = ?' => $rawData['invoice_id'],
      ));
    }

    if ($transaction && !empty($transaction->gateway_transaction_id)) {
      $transactionId = $transaction->gateway_transaction_id;
    } else {
      $transactionId = @$rawData['invoice_id'];
    }


    // Fetch order -------------------------------------------------------------
    $order = null;

    // Get order by vendor_order_id
    if (!$order && !empty($rawData['vendor_order_id'])) {
      $order = $ordersTable->getOrderByUkey($rawData['vendor_order_id']);
    }

    // Get order by invoice_id
    if (!$order && $transactionId) {
      $order = $ordersTable->fetchRow(array(
        'gateway_id = ?'             => $this->_gatewayInfo->gateway_id,
        'gateway_transaction_id = ?' => $transactionId,
      ));
    }

    // Get order by sale_id
    if (!$order && !empty($rawData['sale_id'])) {
      $order = $ordersTable->fetchRow(array(
        'gateway_id = ?'       => $this->_gatewayInfo->gateway_id,
        'gateway_order_id = ?' => $rawData['sale_id'],
      ));
    }

    // Get order by order_id through transaction
    if (!$order && $transaction && !empty($transaction->order_id)) {
      $order = $ordersTable->find($transaction->order_id)->current();
    }

    // Update order with order/transaction id if necessary
    $orderUpdated = false;
    if (!empty($rawData['invoice_id']) && empty($order->gateway_transaction_id)) {
      $orderUpdated                  = true;
      $order->gateway_transaction_id = $rawData['invoice_id'];
    }
    if (!empty($rawData['sale_id']) && empty($order->gateway_order_id)) {
      $orderUpdated            = true;
      $order->gateway_order_id = $rawData['sale_id'];
    }
    if ($orderUpdated) {
      $order->save();
    }

    // Process generic IPN data ------------------------------------------------

    // Build transaction info
    if (!empty($rawData['invoice_id'])) {
      $transactionData = array(
        'item_id'    => $order->item_id,
        'item_type'  => $order->item_type,
        'gateway_id' => $this->_gatewayInfo->gateway_id,
      );
      // Get timestamp
      if (!empty($rawData['payment_date'])) {
        $transactionData['timestamp'] = date('Y-m-d H:i:s', strtotime($rawData['timestamp']));
      } else {
        $transactionData['timestamp'] = new Zend_Db_Expr('NOW()');
      }
      // Get amount
      if (!empty($rawData['invoice_list_amount'])) {
        $transactionData['amt'] = $rawData['invoice_list_amount'];
      } else if ($transaction) {
        $transactionData['amt'] = $transaction->amount;
      }

      if ($order && $order->item_type == 'store_request') {
        $transactionData['amt'] = -$transactionData['amt'];
      }
      // Get currency
      if (!empty($rawData['list_currency'])) {
        $transactionData['currency'] = $rawData['list_currency'];
      } else if ($transaction) {
        $transactionData['currency'] = $transaction->currency;
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
      if (!empty($rawData['sale_id'])) {
        $transactionData['gateway_order_id'] = $rawData['sale_id'];
      }
      // Get payment_state

      if (!empty($rawData['invoice_status'])) {
        if ($rawData['invoice_status'] == 'declined') {
          $transactionData['type']  = 'payment';
          $transactionData['state'] = 'failed';
        } else if ($rawData['fraud_status'] == 'fail') {
          $transactionData['type']  = 'payment';
          $transactionData['state'] = 'failed-fraud';
        } else if ($rawData['fraud_status'] == 'wait') {
          $transactionData['type']  = 'payment';
          $transactionData['state'] = 'pending-fraud';
        } else {
          $transactionData['type']  = 'payment';
          $transactionData['state'] = 'okay';
        }
      }
      if ($transaction &&
        ($transaction->type == 'refund' || $transaction->state == 'refunded')
      ) {
        $transactionData['type']  = $transaction->type;
        $transactionData['state'] = $transaction->state;
      }
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


    // Process specific IPN data -----------------------------------------------
    if ($order) {
      $ipnProcessed = false;
      // Subscription IPN
      if ($order->item_type == 'store_cart') {
        $this->onCartTransactionIpn($order, $ipn);
        $ipnProcessed = true;
      }
      elseif ($order->item_type == 'store_request') {
        $this->onRequestTransactionIpn($order, $ipn);
        $ipnProcessed = true;
      }

// Custom IPN
      else {
        // Custom item-type IPN
        if (Engine_Api::_()->hasItemType($order->item_type) &&
          !empty($order->item_id)
        ) {
          $orderSourceObject = Engine_Api::_()->getItem($order->item_type, $order->item_id);
          if (method_exists($orderSourceObject, 'onPaymentIpn')) {
            $ipnProcessed = (bool)$orderSourceObject->onPaymentIpn($order, $ipn);
          } else {
            throw new Experts_Payment_Plugin_Exception(sprintf('Custom order ' .
              'item "%s" does not implement onPaymentIpn() method', $order->item_type));
          }
        }
        // Hook IPN
        else {
          $eventName = 'onPaymentIpn_' . Engine_Api::inflect($order->item_type);
          $response  = Engine_Hooks_Dispatcher::getInstance()->callEvent($eventName, array(
            'order' => $order,
            'ipn'   => $ipn,
          ));
          if (false != $response->getResponse()) {
            // Okay
            $ipnProcessed = true;
          }
        }
      }
// Unknown IPN - could not be processed
      if (!$ipnProcessed) {
        throw new Experts_Payment_Plugin_Exception('Unknown order type for IPN');
      }
    }
// Missing order
    else {
      throw new Experts_Payment_Plugin_Exception('Unknown or unsupported IPN ' .
        'type, or missing transaction or order ID');
    }

    return $this;
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

  public function detectIpn(array $params)
  {
    $expectedCommonParams = array(
      'message_type', 'message_description', 'timestamp', 'md5_hash',
      'message_id', 'key_count', 'vendor_id',
    );

    foreach ($expectedCommonParams as $key) {
      if (!isset($params[$key])) {
        return false;
      }
    }

    return true;
  }


  // SE Specific

  /**
   * Generate href to a page detailing the order
   *
   * @param $orderId
   *
   * @internal param string $transactionId
   * @return string
   */
  public function getOrderDetailLink($orderId)
  {
    return 'https://www.2checkout.com/va/sales/detail?sale_id=' . $orderId;
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
    return 'https://www.2checkout.com/va/sales/get_list_sale_paged?invoice_id=' . $transactionId;
  }

  /**
   * Get raw data about an order profile
   *
   * @param string $orderId
   *
   * @return array
   */
  public function getOrderDetails($orderId)
  {
    return $this->getService()->detailSale($orderId);
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
    return $this->getService()->detailInvoice($transactionId);
  }

  // Forms

  /**
   * Get the admin form for editing the gateway info
   *
   * @param array $options
   *
   * @return Engine_Form
   */
  public function getAdminGatewayForm(array $options = array())
  {
    return new Store_Form_Admin_Gateway_2Checkout($options);
  }

  public function processAdminGatewayForm(array $values)
  {
    // Should we get the vendor_id and secret word?
    $info                = $this->getService()->detailCompanyInfo();
    $values['vendor_id'] = $info['vendor_id'];
    $values['secret']    = $info['secret_word'];
    return $values;
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
   * Get the admin form for editing the gateway info
   *
   * @return Engine_Form
   */
  public function getGatewayForm()
  {
    // TODO: Implement getGatewayForm() method.
  }
}