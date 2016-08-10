<?php
class Yncontest_AdminTransactionController extends Core_Controller_Action_Admin 
{
	
	public function init(){
		parent::init();
		Zend_Registry::set('admin_active_menu', 'yncontest_admin_main_transactions');
	}

	public function indexAction(){
		
		$view = Zend_Registry::get('Zend_View');
		
		$headScript = new Zend_View_Helper_HeadScript();
		$headLink = new Zend_View_Helper_HeadLink();
		
		$headScript -> appendFile($view->layout()->staticBaseUrl.'application/modules/Yncontest/externals/scripts/jquery-1.8.3.js');
		$headScript -> appendFile($view->layout()->staticBaseUrl.'application/modules/Yncontest/externals/scripts/jquery-ui.js');
		$headLink -> appendStylesheet($view->layout()->staticBaseUrl.'application/modules/Yncontest/externals/styles/jquery-ui.css');
		
		$page = $this->_getParam('page',1);
		$this->view->form = $form = new Yncontest_Form_Admin_Transaction_Search();
		$values = array();  
    	if ($form->isValid($this->_getAllParams())) {
    		$values = $form->getValues();
    	}
  		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.page', 10);
    	$viewer = Engine_Api::_()->user()->getViewer();
    	
    	$this->view->paginator = $paginator = Yncontest_Api_Transaction::getInstance()->getTransactionsPaginator($values);		 
    	$this->view->paginator->setItemCountPerPage($items_per_page);
    	if(isset($page)) $this->view->paginator->setCurrentPageNumber($page);
		$this->view->viewer = $viewer;
    	$this->view->formValues = $values; 
  	
	}
}