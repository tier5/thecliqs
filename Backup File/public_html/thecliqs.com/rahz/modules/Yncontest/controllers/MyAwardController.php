<?php

class Yncontest_MyAwardController extends Core_Controller_Action_Standard
{
	public function init(){

		$viewer = Engine_Api::_()->user()->getViewer();
		$contest_id = $this->_getParam('contest', null);
		
		if($contest_id == null)
			$this->_forward('requireauth', 'error', 'core');
		$contest = Engine_Api::_() -> getItem('yncontest_contest', $contest_id);
		//if($contest == null || !$contest->checkIsOwner())
		if($contest == null)
			$this->_forward('requireauth', 'error', 'core');;
		if( !$this->_helper->requireUser()->isValid() ) return;
	}

    public function indexAction()
    {
        // Render
        //$this -> _helper -> content -> setNoRender() -> setEnabled();
    }
    public function manageAwardAction(){
    	
    	Zend_Registry::set('active_menu', 'yncontest_main_create_contest');
    	
    	$viewer = Engine_Api::_()->user()->getViewer();
    	
    	$this->view->form = $form = new Yncontest_Form_Award_Create();
    	
    	$contest_id = $this->_getParam('contest', null);
    	$values['contest_id'] = $contest_id;
    	
    	$page = $this -> _getParam('page', 1);
    	$values['limit'] = 10;
    	$this -> view -> paginator = Engine_Api::_() -> getApi('award','yncontest') -> getAwardsPaginator($values);
    	$this -> view -> paginator -> setCurrentPageNumber($page);
    	$this->view->contest = $contest_id;
    	
    	// If not post or form not valid, return
    	if(!$this -> getRequest() -> isPost()) {
    		return ;
    	}
    	
    	$post = $this -> getRequest() -> getPost();
    	
    	if(!$form -> isValid($post))
    		return ;
    	
    	// Process
    	$table = new Yncontest_Model_DbTable_Awards;
    	$db = $table -> getAdapter();
    	$db -> beginTransaction();
    	
    	try {
    		$contest = Engine_Api::_() -> getItem('yncontest_contest', $this->_getParam('contest', null));
    		// Create award
    		$values = array_merge($form -> getValues(), array('user_id' => $contest->user_id,'contest_id'=>$contest_id ));
    		
    		$symbol = Engine_Api::_()->getDbTable('currencies','yncontest')->getCurrencySymbol($values['currency']); 
    		
    		$values['currency'] = $symbol;
    		$now = date('Y-m-d H:i:s');
    		$values['modified_date'] = $now;
    		
    		
    		if($values['value'] > 0)
    			$values['award_type'] = 1; // cash
    		else 
    			$values['award_type'] = 2; // no cash
    		$award = $table -> createRow();
    		$award -> setFromArray($values);
    		
    		$award -> save();
    	
    		$db -> commit();
    	
    	} catch( Exception $e ) {
    		$db -> rollBack();
    		throw $e;
    	}
    	$values['contest_id'] = $contest_id;
    	 
    	
    	$page = $this -> _getParam('page', 1);
    	$values['limit'] = 10;
    	$this -> view -> paginator = Engine_Api::_() -> getApi('award','yncontest') -> getAwardsPaginator($values);
    	$this -> view -> paginator -> setCurrentPageNumber($page);
    	$this->view->contest = $contest_id;
    	
    }
    public function manageEditAwardAction(){
    	 
    	Zend_Registry::set('active_menu', 'yncontest_main_create_contest');
    	 
    	$viewer = Engine_Api::_()->user()->getViewer();
    	 
    	$this->view->form = $form = new Yncontest_Form_Award_Create();
    	 
    	$contest_id = $this->_getParam('contest', null);
    	$values['contest_id'] = $contest_id;
    	 
    	$page = $this -> _getParam('page', 1);
    	$values['limit'] = 10;
    	$this -> view -> paginator = Engine_Api::_() -> getApi('award','yncontest') -> getAwardsPaginator($values);
    	$this -> view -> paginator -> setCurrentPageNumber($page);
    	$this->view->contest = $contest_id;
    	 
    	// If not post or form not valid, return
    	if(!$this -> getRequest() -> isPost()) {
    		return ;
    	}
    	 
    	$post = $this -> getRequest() -> getPost();
    	 
    	if(!$form -> isValid($post))
    		return ;
    	 
    	// Process
    	$table = new Yncontest_Model_DbTable_Awards;
    	$db = $table -> getAdapter();
    	$db -> beginTransaction();
    	 
    	try {
    		// Create award
    		$values = array_merge($form -> getValues(), array('user_id' => $viewer -> getIdentity(),'contest_id'=>$contest_id ));
    
    		$symbol = Engine_Api::_()->getDbTable('currencies','yncontest')->getCurrencySymbol($values['currency']);
    
    		$values['currency'] = $symbol;
    		$now = date('Y-m-d H:i:s');
    		$values['modified_date'] = $now;
    
    
    		if($values['value'] > 0)
    			$values['award_type'] = 1; // cash
    		else
    			$values['award_type'] = 2; // no cash
    		$award = $table -> createRow();
    		$award -> setFromArray($values);
    
    		$award -> save();
    		 
    		$db -> commit();
    		 
    	} catch( Exception $e ) {
    		$db -> rollBack();
    		throw $e;
    	}
    	$values['contest_id'] = $contest_id;
    
    	 
    	$page = $this -> _getParam('page', 1);
    	$values['limit'] = 10;
    	$this -> view -> paginator = Engine_Api::_() -> getApi('award','yncontest') -> getAwardsPaginator($values);
    	$this -> view -> paginator -> setCurrentPageNumber($page);
    	$this->view->contest = $contest_id;
    	 
    }

    public function deleteAwardAction(){
    
    
    	$award_id = $this->_getParam('award', null);
    
    	if($award_id == null)
    		return;
    	$viewer = Engine_Api::_()->user()->getViewer();
    	if( !$this->_helper->requireUser()->isValid() ) return;
    	//check authoziration
    	if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'createcontests')->isValid()) return;
    
    
    	$form = $this->view->form = new Yncontest_Form_Award_Delete();
    
    	$post = $this -> getRequest() -> getPost();
    
    	if(!$this -> getRequest() -> isPost()) {
    		return ;
    	}
    
    	$post = $this -> getRequest() -> getPost();
    
    	if(!$form -> isValid($post))
    		return ;
    
    	$award = Engine_Api::_()->getDbTable('awards','yncontest')->find($award_id)->current();
    
    	$award->delete();
    
    
    	// Refresh parent page
    	$this->_forward('success', 'utility', 'core', array(
    			'smoothboxClose' => 10,
    			'parentRefresh'=> 10,
    			'messages' => array('')
    	));
    }
	
    public function editAwardAction(){
    	
    	
    	
    	$award_id = $this->_getParam('award', null);
    
    	if($award_id == null)
    		return;
    
    	$viewer = Engine_Api::_()->user()->getViewer();
    	if( !$this->_helper->requireUser()->isValid() ) return;
    
    	//check authoziration
    	if( !$this->_helper->requireAuth()->setAuthParams('contest', $viewer, 'createcontests')->isValid()) return;
    
    	$award = Engine_Api::_()->getDbTable('awards','yncontest')->find($award_id)->current();
    
    	$this->view->form = $form = new Yncontest_Form_Award_Edit();
    	 
    	$form->setAttrib("class", "global_form_popup");
    	
    	// If not post or form not valid, return
    	if(!$this -> getRequest() -> isPost()) {
    		return ;
    	}
    
    	$post = $this -> getRequest() -> getPost();
    
    	if(!$form -> isValid($post))
    		return ;
    
    	// Process
    	$values = array_merge($form -> getValues());
    	
    	$symbol = Engine_Api::_()->getDbTable('currencies','yncontest')->getCurrencySymbol($values['currency']);    	
    	$values['currency'] = $symbol;
    	$award -> setFromArray($values);
    	$award -> modified_date = 	$now = date('Y-m-d H:i:s');
    
    	
    	$award -> save();
    
    	// Refresh parent page
    	$this->_forward('success', 'utility', 'core', array(
    			'smoothboxClose' => 10,
    			'parentRefresh'=> 10,
    			'messages' => array('')
    	));
    
    }
}
