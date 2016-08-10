<?php
class Ynjobposting_AdminTransactionsController extends Core_Controller_Action_Admin {
    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynjobposting_admin_main', array(), 'ynjobposting_admin_main_transactions');
        
        $table = Engine_Api::_()->getDbTable('transactions', 'ynjobposting');
        $tableName = $table -> info('name');
        
        $jobTbl = Engine_Api::_() -> getItemTable('ynjobposting_job');
        $jobTblName = $jobTbl -> info('name');
        
        $companyTbl = Engine_Api::_() -> getItemTable('ynjobposting_company');
        $companyTblName = $companyTbl -> info('name');
        
        $select = $table -> select();
        $select -> setIntegrityCheck(false);
        $select -> from("$tableName as transaction", "transaction.*");
        $select
            -> joinLeft("$jobTblName as job","job.job_id = transaction.item_id", "")
            -> joinLeft("$companyTblName as company","company.company_id = transaction.item_id", "");
    
        $methods = array();
        
        $this->view->form = $form = new Ynjobposting_Form_Admin_Transactions_Filter();
        
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
        
        $select->order($values['order'].' '.$values['direction']);
        
        $transactions = $table->fetchAll($select);
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($transactions);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }
}