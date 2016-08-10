<?php
/**
 * SocialEngine
 *
 * @category   Experts
 * @package    Store_Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Abstract.php 8292 2011-01-25 00:21:31Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Experts
 * @package    Store_Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
abstract class Experts_Payment_Plugin_Abstract
{
  // General
  protected $_gatewayInfo;

  protected $_gateway;

  /**
   * Constructor
   * @param Zend_Db_Table_Row_Abstract $gatewayInfo
   */
  public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo)
  {
    $this->_gatewayInfo = $gatewayInfo;
  }

  /**
   * Get the service API
   *
   * @return Zend_Service_Abstract
   */
  abstract public function getService();

  /**
   * Get the gateway object
   *
   * @return Experts_Payment_Gateway
   */
  abstract public function getGateway();


  // Actions

  /**
   * Create a transaction object from specified parameters
   *
   * @param array
   * @return Experts_Payment_Transaction
   */
  public function createTransaction(array $params)
  {
    $transaction = new Experts_Payment_Transaction($params);
    $transaction->process($this->getGateway());
    return $transaction;
  }

  /**
   * Create an ipn object from specified parameters
   *
   * @param array $params
   * @return Experts_Payment_Ipn
   */
  abstract public function createIpn(array $params);


  // SE Specific

  /**
   * Process ipn of subscription transaction
   *
   * @param Store_Model_Order   $order
   * @param Experts_Payment_Ipn $ipn
   */
  abstract public function onCartTransactionIpn(
    Store_Model_Order $order,
    Experts_Payment_Ipn $ipn);

  /**
   * Create a transaction for a order
   *
   * @param Store_Model_Order $order
   * @param array $params
   * @return Experts_Payment_Transaction
   */
  abstract public function createCartTransaction(
    Store_Model_Order $order,
    array $params = array());

  /**
   * Process return of order transaction
   *
   * @param Store_Model_Order $order
   * @param array $params
   * @return
   */
  abstract public function onCartTransactionReturn(
    Store_Model_Order $order,
    array $params = array());

  /**
   * Create a transaction for a order
   *
   * @param Store_Model_Order $order
   * @param array $params
   * @return Experts_Payment_Transaction
   */
  abstract public function createRequestTransaction(
    Store_Model_Order $order,
    array $params = array());

  /**
   * Process return of order transaction
   *
   * @param Store_Model_Order $order
   * @param array $params
   * @return
   */
  abstract public function onRequestTransactionReturn(
    Store_Model_Order $order,
    array $params = array());

  /**
   * Process ipn of money request transaction
   *
   * @param Store_Model_Order   $order
   * @param Experts_Payment_Ipn $ipn
   */
  abstract public function onRequestTransactionIpn(
    Store_Model_Order $order,
    Experts_Payment_Ipn $ipn);

  /**
   * Cancel a subscription (i.e. disable the recurring payment profile)
   *
   * @param $transactionId
   * @return Experts_Payment_Plugin_Abstract
   */
  abstract public function cancelOrder($transactionId);


  // Informational

  /**
   * Generate href to a page detailing the order
   *
   * @param $orderId
   * @return string
   */
  abstract public function getOrderDetailLink($orderId);

  /**
   * Generate href to a page detailing the transaction
   *
   * @param string $transactionId
   * @return string
   */
  abstract public function getTransactionDetailLink($transactionId);

  /**
   * Get raw data about an order or recurring payment profile
   *
   * @param string $orderId
   * @return array
   */
  abstract public function getOrderDetails($orderId);

  /**
   * Get raw data about a transaction
   *
   * @param $transactionId
   * @return array
   */
  abstract public function getTransactionDetails($transactionId);


  // IPN

  /**
   * Process an IPN
   *
   * @param Experts_Payment_Ipn $ipn
   * @return Experts_Payment_Plugin_Abstract
   */
  abstract public function onIpn(Experts_Payment_Ipn $ipn);

  /**
   * Process a return
   *
   * @param Store_Model_Order $order
   * @param array $params
   * @throws Engine_Payment_Plugin_Exception
   * @return Experts_Payment_Plugin_Abstract
   */
  public function onReturn(Store_Model_Order $order, array $params = array())
  {
    if ($order->source_type == 'payment_subscription') {
      $this->onSubscriptionTransactionReturn($order, $params);
    } else {
      throw new Engine_Payment_Plugin_Exception('Unknown order type');
    }
    return $this;
  }


  // Forms

  /**
   * Get the admin form for editing the gateway info
   *
   * @return Engine_Form
   */
  abstract public function getAdminGatewayForm();

  abstract public function processAdminGatewayForm(array $values);

  /**
   * Get the admin form for editing the gateway info
   *
   * @return Engine_Form
   */
  abstract public function getGatewayForm();
}