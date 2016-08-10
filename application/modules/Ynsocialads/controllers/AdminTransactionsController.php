<?php
class Ynsocialads_AdminTransactionsController extends Core_Controller_Action_Admin {
    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynsocialads_admin_main', array(), 'ynsocialads_admin_main_transactions');
        
        $table = Engine_Api::_()->getDbTable('transactions', 'ynsocialads');
        $select = $table->select();
        
        $methods = array(
            '-1' => 'Pay with Virtual Money',
            '-2' => 'Pay Later' 
        );
        
        $this->view->form = $form = new Ynsocialads_Form_Admin_Transactions_Filter();
        
        if (Engine_Api::_()->hasModuleBootstrap("yncredit")) {
            $form->gateway_id->addMultiOption(-3, 'Pay with Credit');
            $methods['-3'] = 'Pay by Credit';
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
            
        if ($values['status'] == 'All') {
            $statusArr = array('initialized', 'expired', 'pending', 'completed', 'canceled');
        }
        else {
            $statusArr = array($values['status']);
        }
        $select = $select->where('status IN (?)', $statusArr);
        
        if ($values['gateway_id'] != 'All') {
            $select = $select->where('gateway_id = ?', $values['gateway_id']);
        }
        $this->view->formValues = $values;      
        
       
        $transactions = $table->fetchAll($select);
        
        $page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($transactions);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }
}