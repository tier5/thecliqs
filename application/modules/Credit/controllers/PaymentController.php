<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PaymentController.php 20.01.12 15:49 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_PaymentController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // If no user, redirect to home?
		if (!$this->_helper->requireUser()->isValid()) {
      $this->_redirector();
		}
  }

  public function gatewayAction()
  {
    //$this->_helper->layout->setLayout('default-simple');
    $payment_id = $this->_getParam('choose', 0);
    $viewer = Engine_Api::_()->user()->getViewer();

    /**
     * @var $gatewayTable Payment_Model_DbTable_Gateways
     * @var $paymentsTable Credit_Model_DbTable_Payments
     * @var $table Credit_Model_DbTable_Orders
     */
    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    if (!$gatewayTable->getEnabledGatewayCount() || !$payment_id) {
      $this->_redirector();
    }

    //Payments
    $paymentsTable = Engine_Api::_()->getDbTable('payments', 'credit');
    if (null == ($product = $paymentsTable->fetchRow(array('payment_id = ?' => $payment_id)))) {
      $this->_redirector();
    }

    $table = Engine_Api::_()->getItemTable('credit_order');

    if ($row = $table->getLastPendingOrder()) {
      $row->delete();
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $table->insert(array(
        'user_id' => $viewer->getIdentity(),
        'creation_date' => new Zend_Db_Expr('NOW()'),
        'payment_id' => $product->payment_id,
        'credit' => $product->credit,
        'price' => $product->price
      ));

      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    // Gateways
    $gatewaySelect = $gatewayTable->select()
      ->where('enabled = ?', 1)
    ;
    $gateways = $gatewayTable->fetchAll($gatewaySelect);

    $gatewayPlugins = array();
    foreach ($gateways as $gateway) {
      $gatewayPlugins[] = array(
        'gateway' => $gateway,
        'plugin' => $gateway->getGateway()
      );
    }
    $this->view->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $this->view->gateways = $gatewayPlugins;
    $this->view->product = $product;
  }

  public function creditOrderAction()
  {
    /**
     * @var $table Credit_Model_DbTable_Orders
     */
    $gateway_id = $this->_getParam('gateway_id', 0);
    if (!$gateway_id) {
      $this->_redirector();
    }

    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    $gatewaySelect = $gatewayTable->select()
      ->where('gateway_id = ?', $gateway_id)
      ->where('enabled = ?', 1)
    ;
    $gateway = $gatewayTable->fetchRow($gatewaySelect);
    if (!$gateway) {
      $this->_redirector();
    }
    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $table = Engine_Api::_()->getItemTable('credit_order');
    $order = $table->getLastPendingOrder();
    if ($order == null) {
      $this->_redirector();
    }

    $order->gateway_id = $gateway->getIdentity();
    $order->save();

    $this->view->status = true;
    $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()
        ->getRouter()
        ->assemble(
          array(
            'action' => 'process',
            'co_id' => $order->getIdentity(),
          ),
          'credit_transaction', true
        ),
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Please wait...'))
    ));
  }

  protected function _redirector()
  {
    $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()
        ->getRouter()
        ->assemble(
          array(),
          'credit_general', true
        ),
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Error'))
    ));
  }
}
