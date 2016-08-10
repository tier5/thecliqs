<?php
class Ynsocialads_AdminMoneyRequestsController extends Core_Controller_Action_Admin {

	public function init() {
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
     ->getNavigation('ynsocialads_admin_main', array(), 'ynsocialads_admin_main_money_requests');
	}

	public function indexAction() {
		$this -> view -> form = $form = new Ynsocialads_Form_Admin_Moneyrequest_Search();
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		$this -> view -> formValues = $params;
		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = Engine_Api::_() -> getItemTable('ynsocialads_moneyrequest') -> getMoneyRequestsPaginator($params);
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);
	}
	
	public function approveAction() {
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$id = $this -> _getParam('id');
		$response_message = $this -> _getParam('response_message');
		$money_req = Engine_Api::_() -> getItem('ynsocialads_moneyrequest', $id);
		$money_req -> response_message = $response_message;
		$money_req -> response_date = $date = date('Y-m-d H:i:s');
		$money_req -> save();
		
		$user = Engine_Api::_() -> user() -> getUser($money_req->user_id);
		
		//Add notification
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$notifyApi -> addNotification($user, $viewer, $money_req, 'ynsocialads_admin_money_approve');
		
	}
	
	public function rejectAction() {
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$id = $this -> _getParam('id');
		$response_message = $this -> _getParam('response_message');
		$money_req = Engine_Api::_() -> getItem('ynsocialads_moneyrequest', $id);
		$money_req -> status = 'rejected';
		$money_req -> response_message = $response_message;
		$money_req -> response_date = $date = date('Y-m-d H:i:s');
		$money_req -> save();
		
		//refund money -> remain
		$refund_money = $money_req -> amount;
		$virtualTable = Engine_Api::_()->getItemTable('ynsocialads_virtual');
		$select = $virtualTable -> select() -> where('user_id = ?', $money_req -> user_id) -> limit(1);
        $virtual_money = $virtualTable -> fetchRow($select);
		$virtual_money->remain = $virtual_money->remain + $refund_money;
		$virtual_money-> save();
		
		//Add notification
		$user = Engine_Api::_() -> user() -> getUser($money_req->user_id);
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$notifyApi -> addNotification($user, $viewer, $money_req, 'ynsocialads_admin_money_reject');
		
		return $this -> _helper -> redirector -> gotoRoute(
		 			array(
					  'module' => 'ynsocialads',
					  'controller' => 'money-requests',
			          'action' => 'index',
			        ), 'admin_default', true);
	}
	
	public function viewDetailAction() {
		
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$money_req = Engine_Api::_() -> getItem('ynsocialads_moneyrequest', $id);
		$this -> view -> money_req = $money_req;

		// Output
		$this -> renderScript('admin-money-requests/view-detail.tpl');
	}

	public function requestPaymentAction() {
		
		$id = $this -> _getParam('id');
		$status = $this -> _getParam('status');
		$money_req = Engine_Api::_() -> getItem('ynsocialads_moneyrequest', $id);
		
		$gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		$activeGateway = $gatewayTable -> fetchRow(array('enabled = ?' => 1, 'title = ?' => 'PayPal'));
		if (!$activeGateway) {
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'gateway'));
		}
		$test_mode = $activeGateway -> test_mode;
		$paymentForm = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		if ($test_mode) {
			$paymentForm = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		} else {
			$paymentForm = "https://www.paypal.com/cgi-bin/webscr";
		}
		
		$this-> view -> ipnNotificationUrl = $ipnNotificationUrl = 
						'http://' . $_SERVER['HTTP_HOST'].$this->view->baseUrl().
						"/application/modules/Ynsocialads/externals/scripts/paypal-callback.php?". 
						"&money_req=".$id;
		$this-> view -> returnUrl  = $returnUrl = 
					'http://' . $_SERVER['HTTP_HOST'] 
					. Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
					  'module' => 'ynsocialads',
					  'controller' => 'money-requests',
			          'action' => 'index',
			        ), 'admin_default', true);
		$this-> view -> cancelUrl  = $cancelUrl = 
					'http://' . $_SERVER['HTTP_HOST'] 
					. Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
					  'module' => 'ynsocialads',
					  'controller' => 'money-requests',
			          'action' => 'index',
			        ), 'admin_default', true);
					
	
		
		$this -> view -> money_req = $money_req;
		$this -> view -> status = $status;
		$this -> view -> paymentForm = $paymentForm;
		
		if ($status == "approved") {
			$this -> renderScript('admin-money-requests/approve-payment.tpl');
		} else {
			$this -> renderScript('admin-money-requests/reject-payment.tpl');
		}
	}
	
}
