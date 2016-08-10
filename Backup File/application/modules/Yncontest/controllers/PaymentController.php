<?php

class Yncontest_PaymentController extends Core_Controller_Action_Standard
{
	
	public function init(){
		
	}
	
    public function indexAction()
    {
    	
    }
	
	protected function _redirector($message = null) {
		if(empty($message))
		{
			$message = Zend_Registry::get('Zend_Translate') -> _('Error!');
		}
		$this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'yncontest_general', true), 'messages' => array($message)));
	}
	
	public function methodAction(){
		//require user
		if( !$this->_helper->requireUser()->isValid() ) return;
			
		
		
		$contest_id = $this->_getParam('contestId', null);
		$this -> view -> contest = $contest = Engine_Api::_() -> getItem('yncontest_contest', $contest_id);
		
		
		$security = $this->_getParam('id', null);
		if($security == null)
		{
			$this->_forward('requireauth', 'error', 'core');
			return;	
		}
		
			
		//require owner
		$viewer = Engine_Api::_()->user()->getViewer();
		$table = Engine_Api::_() -> getDbTable('transactions', 'yncontest');
		$transactions = $table->getTranBySec($security);
				
		if(count($transactions) == 0)
		{
			$this->_forward('requireauth', 'error', 'core');
			return;	
		}
		
		
		$final_price = 0;
		foreach($transactions as $transaction){
			$final_price +=$transaction->amount;
		}
		
			
		$firstTransaction = $transactions[0];
		
		
		
		if($firstTransaction->transaction_status != 'pending')
		{
			$this->_forward('requireauth', 'error', 'core');
			return;	
		}	
		if($firstTransaction->user_buyer != $viewer->getIdentity())
		{
			$this->_forward('requireauth', 'error', 'core');
			return;	
		}
		
		
		//******************IMPLEMENT INTERGRATE ADV-PAYMENT*************************
		
		$settings = Engine_Api::_()->getApi('settings', 'core');
        $viewer = Engine_Api::_() -> user() -> getViewer();
       	
		$this -> view -> total_pay = $total_pay = $final_price;
        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');

        if ((!$gatewayTable -> getEnabledGatewayCount() && !Engine_Api::_() -> hasModuleBootstrap('yncredit'))) {
            $message = $this -> view -> translate('There are no payment gateways.');
            return $this -> _redirector($message);
        }
        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'yncontest');
		
        if ($row = $ordersTable -> getLastPendingOrder()) {
           $row -> delete();
        }
        $db = $ordersTable -> getAdapter();
        $db -> beginTransaction();
        try 
        {
            $ordersTable -> insert(array(
            	'user_id' => $viewer -> getIdentity(), 
	            'creation_date' => new Zend_Db_Expr('NOW()'), 
	            'item_id' => $contest -> getIdentity(),
	            'price' => $total_pay, 
	            'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), 
				'security_code' => $firstTransaction -> security,
			));
            // Commit
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
		
        // Gateways
        $gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
        $gateways = $gatewayTable -> fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) 
        {
            $gatewayPlugins[] = array('gateway' => $gateway, 'plugin' => $gateway -> getGateway());
        }
        $this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD');
        $this -> view -> gateways = $gatewayPlugins;
		
		//******************END IMPLEMENT INTERGRATE ADV-PAYMENT*************************
	}
	
	public function updateOrderAction() 
    {
        $gateway_id = $this -> _getParam('gateway_id', 0);
        if (!$gateway_id) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable -> select() -> where('gateway_id = ?', $gateway_id) -> where('enabled = ?', 1);
        $gateway = $gatewayTable -> fetchRow($gatewaySelect);
        if (!$gateway) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'yncontest');
        $order = $ordersTable -> getLastPendingOrder();
        if (!$order) {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
        $order -> gateway_id = $gateway -> getIdentity();
        $order -> save();

        $this -> view -> status = true;
        if (!in_array($gateway -> title, array('2Checkout', 'PayPal'))) {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process-advanced', 'order_id' => $order -> getIdentity(), 'm' => 'yncontest', 'cancel_route' => 'yncontest_transaction_process', 'return_route' => 'yncontest_transaction_process', ), 'ynpayment_paypackage', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        } else {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process', 'order_id' => $order -> getIdentity(), ), 'yncontest_transaction_process', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        }
    }
	
}
