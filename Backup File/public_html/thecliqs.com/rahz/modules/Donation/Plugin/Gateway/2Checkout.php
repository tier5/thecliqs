<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 27.07.12
 * Time: 9:58
 * To change this template use File | Settings | File Templates.
 */
class Donation_Plugin_Gateway_2Checkout extends Engine_Payment_Plugin_Abstract
{
  protected $_gatewayInfo;

  protected $_gateway;

  /**
   * Constructor
   * @param \Zend_Db_Table_Row_Abstract $gatewayInfo
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
  public function getService()
  {
    return $this->getGateway()->getService();
  }

  /**
   * Get the gateway object
   *
   * @throws Engine_Exception
   * @return Engine_Payment_Gateway
   */
  public function getGateway()
  {
    if( null === $this->_gateway ) {
      $class = 'Engine_Payment_Gateway_2Checkout';
      Engine_Loader::loadClass($class);
      $gateway = new $class(array(
        'config' => (array) $this->_gatewayInfo->config,
        'testMode' =>  $this->_gatewayInfo->test_mode,
        'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'),
      ));
      if( !($gateway instanceof Engine_Payment_Gateway) ) {
        throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
      }
      $this->_gateway = $gateway;
    }

    return $this->_gateway;
  }

  /**
   * Create a transaction object from specified parameters
   *
   * @param array $params
   * @return Engine_Payment_Transaction
   */
  public function createTransaction(array $params)
  {
    $transaction = new Engine_Payment_Transaction($params);
    $transaction->process($this->getGateway());
    return $transaction;
  }

  /**
   * Create an ipn object from specified parameters
   *
   * @param array $params
   * @return Engine_Payment_Ipn
   */
  public function createIpn(array $params)
  {
    $ipn = new Engine_Payment_Ipn($params);
    $ipn->process($this->getGateway());
    return $ipn;
  }

  /**
   * Create a transaction for a subscription
   *
   * @param User_Model_User $user
   * @param Zend_Db_Table_Row_Abstract $subscription
   * @param Payment_Model_Package $package
   * @param array $params
   * @return Engine_Payment_Gateway_Transaction
   */
  public function createSubscriptionTransaction(User_Model_User $user,
                                                Zend_Db_Table_Row_Abstract $subscription,
                                                Payment_Model_Package $package,
                                                array $params = array())
  {
    // TODO: Implement createSubscriptionTransaction() method.
  }

  /**
   * Process return of subscription transaction
   *
   * @param Payment_Model_Order $order
   * @param array $params
   */
  public function onSubscriptionTransactionReturn(
    Payment_Model_Order $order, array $params = array())
  {
    // TODO: Implement onSubscriptionTransactionReturn() method.
  }

  /**
   * Process ipn of subscription transaction
   *
   * @param Payment_Model_Order $order
   * @param Engine_Payment_Ipn $ipn
   */
  public function onSubscriptionTransactionIpn(
    Payment_Model_Order $order,
    Engine_Payment_Ipn $ipn)
  {
    // TODO: Implement onSubscriptionTransactionIpn() method.
  }

  /**
   * Cancel a subscription (i.e. disable the recurring payment profile)
   *
   * @params $transactionId
   * @return Engine_Payment_Plugin_Abstract
   */
  public function cancelSubscription($transactionId)
  {
    // TODO: Implement cancelSubscription() method.
  }

  /**
   * Generate href to a page detailing the order
   *
   * @param string $transactionId
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
   * @return string
   */
  public function getTransactionDetailLink($transactionId)
  {
    // TODO: Implement getTransactionDetailLink() method.
  }

  /**
   * Get raw data about an order or recurring payment profile
   *
   * @param string $orderId
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
   * @return array
   */
  public function getTransactionDetails($transactionId)
  {
    // TODO: Implement getTransactionDetails() method.
  }

  /**
   * Process an IPN
   *
   * @param Engine_Payment_Ipn $ipn
   * @return Engine_Payment_Plugin_Abstract
   */
  public function onIpn(Engine_Payment_Ipn $ipn)
  {
    // TODO: Implement onIpn() method.
  }

  /**
   * Get the admin form for editing the gateway info
   *
   * @return Engine_Form
   */
  public function getAdminGatewayForm()
  {
    return new Donation_Form_Admin_Gateway_2Checkout();
  }

  public function processAdminGatewayForm(array $values)
  {
    // Should we get the vendor_id and secret word?
    $info = $this->getService()->detailCompanyInfo();
    $values['vendor_id'] = $info['vendor_id'];
    $values['secret'] = $info['secret_word'];
    return $values;
  }
}
