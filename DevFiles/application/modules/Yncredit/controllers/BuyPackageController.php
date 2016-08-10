<?php
class Yncredit_BuyPackageController extends Core_Controller_Action_Standard
{
  public function init()
  {
		if (!$this->_helper->requireUser()->isValid()) 
		{
	  		return $this->_redirector();
		}
  }

  public function indexAction()
  {
    $package_id = $this->_getParam('package', 0);
    $viewer = Engine_Api::_()->user()->getViewer();
    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    if (!$gatewayTable->getEnabledGatewayCount() || !$package_id) 
    {
      return $this->_redirector();
    }

	// check package is exist
    //Packages
    $packagesTable = Engine_Api::_()->getDbTable('packages', 'yncredit');
	$package = $packagesTable->fetchRow(array('package_id = ?' => $package_id));
    if (!$package) 
    {
      return $this->_redirector();
    }
    $ordersTable = Engine_Api::_()->getDbTable('orders', 'yncredit');
    if ($row = $ordersTable->getLastPendingOrder())
    {
      $row->delete();
    }

    $db = $ordersTable->getAdapter();
    $db->beginTransaction();

    try {
      $ordersTable->insert(array(
        'user_id' => $viewer->getIdentity(),
        'creation_date' => new Zend_Db_Expr('NOW()'),
        'package_id' => $package -> getIdentity(),
        'credit' => $package -> credit,
        'price' => $package -> price
      ));

      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    // Gateways
    $gatewaySelect = $gatewayTable->select()
      ->where('enabled = ?', 1);
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
    $this->view->package = $package;
  }

  public function updateOrderAction()
  {
    $gateway_id = $this->_getParam('gateway_id', 0);
    if (!$gateway_id) 
    {
      return $this->_redirector();
    }

    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    $gatewaySelect = $gatewayTable->select()
      ->where('gateway_id = ?', $gateway_id)
      ->where('enabled = ?', 1);
    $gateway = $gatewayTable->fetchRow($gatewaySelect);
    if (!$gateway) {
      return $this->_redirector();
    }

    $ordersTable = Engine_Api::_()->getDbTable('orders', 'yncredit');
    $order = $ordersTable->getLastPendingOrder();
    if (!$order) 
    {
      return $this->_redirector();
    }
    $order->gateway_id = $gateway->getIdentity();
    $order->save();
	
    $this->view->status = true;
	if(!in_array($gateway -> title, array('2Checkout', 'PayPal')))
	{
		$this->_forward('success' ,'utility', 'core', array(
	      'parentRedirect' => Zend_Controller_Front::getInstance()
	        ->getRouter()
	        ->assemble(
	          array(
	            'action' => 'process-advanced',
	            'order_id' => $order->getIdentity(),
	            'm' => 'yncredit',
	            'cancel_route' => 'yncredit_general',
	            'return_route' => 'yncredit_transaction',
	          ),
	          'ynpayment_paypackage', true
	        ),
	      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Please wait...'))
	    ));
	}
	else {
		$this->_forward('success' ,'utility', 'core', array(
	      'parentRedirect' => Zend_Controller_Front::getInstance()
	        ->getRouter()
	        ->assemble(
	          array(
	            'action' => 'process',
	            'order_id' => $order->getIdentity(),
	          ),
	          'yncredit_transaction', true
	        ),
	      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Please wait...'))
	    ));
	}
  }

  protected function _redirector()
  {
    $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()
        ->getRouter()
        ->assemble(
          array(),
          'yncredit_general', true
        ),
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Error!'))
    ));
  }
}
