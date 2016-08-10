<?php
class Yncredit_AdminTransactionsController extends Core_Controller_Action_Admin
{
	 public function indexAction()
  	{
  		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation('yncredit_admin_main', array(), 'yncredit_admin_main_transactions');
			
		$this -> view -> headLink() -> appendStylesheet($this -> view -> baseUrl() . '/application/modules/Yncredit/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');
		$this -> view -> form = $form = new Yncredit_Form_Admin_SearchTransactions();
		$typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
	    $typeTblName = $typeTbl->info("name");
		$moduleTbl = Engine_Api::_()->getDbTable("modules", "core");
	    $modules = $moduleTbl->select()->where("enabled = ?", 1)->query()->fetchAll();
	    $enabledModules = array();
	    foreach ($modules as $key => $module)
	    {
	    	$enabledModules[] = $module['name']; 
	    }
	    
	    $select = $typeTbl->select() ->distinct() 
			-> from ($typeTblName, "module")
	    	-> where("$typeTblName.module in (?)", $enabledModules)
	    	-> order("$typeTblName.module ASC");
		$modules = $typeTbl -> fetchAll($select);
		$moduleOptions = array('' => 'All');
		$translate = Zend_Registry::get('Zend_Translate');
		foreach($modules as $module)
		{
			$moduleOptions[$module -> module] = ucfirst($translate->translate('YNCREDIT_MODULE_'. strtoupper($module->module)));
		}
		$form -> modu -> setMultiOptions($moduleOptions);
		
		$form->isValid($this->_getAllParams());
	    
	    $params = $form->getValues();
	    if(empty($params['orderby'])) $params['orderby'] = 'creation_date';
	    if(empty($params['direction'])) $params['direction'] = 'DESC';
		$this -> view -> formValues = $params;
		$this -> view -> transactions = $transactions = Engine_Api::_() -> getDbTable('logs', 'yncredit') -> getTranactionsPaginator($params);
		$transactions -> setCurrentPageNumber($this -> _getParam('page'), 1);
		$transactions -> setItemCountPerPage(15);
	}
}