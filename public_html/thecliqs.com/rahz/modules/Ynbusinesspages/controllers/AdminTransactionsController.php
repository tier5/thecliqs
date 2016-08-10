<?php
class Ynbusinesspages_AdminTransactionsController extends Core_Controller_Action_Admin {
    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynbusinesspages_admin_main', array(), 'ynbusinesspages_admin_main_transactions');
        
        $table = Engine_Api::_()->getDbTable('transactions', 'ynbusinesspages');
		$select = $table -> select();
        $tableName = $table -> info('name');
        
		$userTbl = Engine_Api::_() -> getDbtable('users', 'user');
		$userTblName = $userTbl -> info('name');
		
        $businessTbl = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
        $businessTblName = $businessTbl -> info('name');
        
        $select = $table -> select() -> from(array('transaction' => $tableName));
        $select -> setIntegrityCheck(false) 
       			-> joinLeft("$businessTblName as business", "business.business_id = transaction.item_id", "");
				
        $methods = array();
        
        $this->view->form = $form = new Ynbusinesspages_Form_Admin_Transactions_Search();
        
        if (Engine_Api::_()->hasModuleBootstrap("yncredit")) {
            $form->gateway_id->addMultiOption(-3, 'Pay with Credit');
            $methods['-3'] = 'Pay with Credit';
        }
        
        $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable->select()->where('enabled = ?', 1);
        $gateways = $gatewayTable->fetchAll($gatewaySelect);
        foreach ($gateways as $gateway) {
            $form->gateway_id->addMultiOption($gateway->gateway_id, 'Pay with '.$gateway->title);
            $methods[''.$gateway->gateway_id] = 'Pay with '.$gateway->title;        
        }
        
        $this->view->methods = $methods;
        $form->populate($this->_getAllParams());
        $values = $form->getValues();
        $this->view->formValues = $values;
        
        if ($values['gateway_id'] != 'all') {
            $select->where('gateway_id = ?', $values['gateway_id']);
        }
        
		if ($values['name'] != '') {
            $select->where('business.name LIKE ?', '%'.$values['name'].'%');
        }
		
		$sysTimezone = date_default_timezone_get();
        if ($values['from_date']) {
            $from_date = new Zend_Date(strtotime($values['from_date']));
			$from_date->setTimezone($sysTimezone);
			$select->where('transaction.creation_date >= ?', $from_date->get('yyyy-MM-dd'));
        }
	    if ($values['to_date']) {
	    	$to_date = new Zend_Date(strtotime($values['to_date']));
			$to_date->setTimezone($sysTimezone);
			$select->where('transaction.creation_date <= ?', $to_date->get('yyyy-MM-dd'));
	    }
       
	    if (isset($values['order'])) {
	        if (empty($values['direction'])) {
	            $values['direction'] = ($values['order'] == 'business.name') ? 'ASC' : 'DESC';
	        }
            $select->order($values['order'].' '.$values['direction']);
		}
		else {
	        if (!empty($values['direction'])) {
	            $select->order('transaction.transaction_id'.' '.$values['direction']);
	        }
	    }
        $transactions = $table->fetchAll($select);
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($transactions);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }
}