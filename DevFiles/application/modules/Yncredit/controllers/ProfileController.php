<?php

class Yncredit_ProfileController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
  	if( !$this->_helper->requireAuth()->setAuthParams('yncredit', null, 'use_credit')->isValid() ) return;
  	if( !$this->_helper->requireUser->isValid() ) return;
	$viewer = Engine_Api::_() -> user() -> getViewer();
	$this -> view -> form = $form = new Yncredit_Form_SearchTransactions();
	$params = $this -> _getAllParams();
	
	unset($params['module']);
	unset($params['controller']);
	unset($params['action']);
	unset($params['rewrite']);
	unset($params['button']);
	
	$form -> populate($params);
	
	if(!isset($params['time']))
	{
		$params['time'] = 'today';
	}
	
	$this -> view -> formValues = $params;
	$params['user_id'] = $viewer -> getIdentity();
	$params['type'] = 'user';
	$params['level_id'] = $viewer -> level_id;
	$this -> view -> transactions = $transactions = Engine_Api::_() -> getDbTable('logs', 'yncredit') -> getTranactionsPaginator($params);
	$transactions -> setCurrentPageNumber($this -> _getParam('page'), 1);
	$transactions -> setItemCountPerPage(15);
	 // Landing page mode
	$this->_helper->content->setEnabled ();
  }
}
