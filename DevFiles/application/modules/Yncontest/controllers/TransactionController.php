<?php
class Yncontest_TransactionController extends Core_Controller_Action_Standard 
{
	public function indexAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
    		return;
		Zend_Registry::set('active_menu','yncontest_main_mycontests');
		Zend_Registry::set('active_mini_menu','transaction');
	    $this -> _helper -> content -> setNoRender() -> setEnabled();
	}
}