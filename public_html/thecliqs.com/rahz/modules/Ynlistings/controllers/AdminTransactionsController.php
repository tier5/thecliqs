<?php
class Ynlistings_AdminTransactionsController extends Core_Controller_Action_Admin {
    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynlistings_admin_main', array(), 'ynlistings_admin_main_transactions');
        
        $table = Engine_Api::_()->getDbTable('transactions', 'ynlistings');
        $tableName = $table -> info('name');
        
        $listingTbl = Engine_Api::_() -> getItemTable('ynlistings_listing');
        $listingTblName = $listingTbl -> info('name');
        
        $userTbl = Engine_Api::_() -> getDbtable('users', 'user');
        $userTblName = $userTbl -> info('name');
    
        $select = $table -> select() -> from(array('transaction' => $tableName));
        $select -> setIntegrityCheck(false) 
        -> joinLeft("$userTblName as user", "user.user_id = transaction.user_id", "") 
        -> joinLeft("$listingTblName as listing", "listing.listing_id = transaction.listing_id", "");
    
        $methods = array();
        
        $this->view->form = $form = new Ynlistings_Form_Admin_Transactions_Search();
        
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
            $select->where('transaction.gateway_id = ?', $values['gateway_id']);
        }
        
        if ($values['listing'] != '') {
            $select->where('listing.title LIKE ?', '%'.$values['listing'].'%');
        }
        
        if ($values['owner'] != '') {
            $select->where('user.displayname LIKE ?', '%'.$values['owner'].'%');
        }
        if (!$values['date_from']) {
            $now = new Zend_Date();
            $date_to = $now;
            $date_from = new Zend_Date($date_to->getTimestamp());
            $date_from->sub(1, 'dd');             
        }        
        else {
            $date_from = new Zend_Date(strtotime($values['date_from']));
            $date_to = new Zend_Date(strtotime($values['date_to']));
        }
        $sysTimezone = date_default_timezone_get();
        $date_from->setTimezone($sysTimezone);
        $date_to->setTimezone($sysTimezone);
        
        $select
        ->where('transaction.creation_date >= ?', $date_from->get('yyyy-MM-dd'))
        ->where('transaction.creation_date <= ?', $date_to->get('yyyy-MM-dd'));
        $transactions = $table->fetchAll($select);
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
        $form->date_from->setValue($date_from->setTimezone($timezone)->get('MM/dd/yyyy'));
        $form->date_to->setValue($date_to->setTimezone($timezone)->get('MM/dd/yyyy'));
        
        $page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($transactions);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }
}