<?php

class Yncontest_MyRuleController extends Core_Controller_Action_Standard
{
	protected $_myRule;
	public function getMyRule(){
		return $this->$_myRule;
	}
	
	public function setMyRule($rule){
		$this->$_myRule =  $rule;
		return $this;
	}
	
	public function init(){
		
		if( !$this->_helper->requireUser()->isValid() ) 
		return;
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest_id = $this->_getParam('contest', null);
		
		if($contest_id == null)
			$this->_forward('requireauth', 'error', 'core');
		$contest = Engine_Api::_() -> getItem('yncontest_contest', $contest_id);
		if($contest == null || !$contest->checkIsOwner())
			$this->_forward('requireauth', 'error', 'core');;
	}
	
    public function indexAction()
    {
    }
	
	
	public function editRuleAction(){
		
		$rule_id = $this->_getParam('rule', null);	
		$rule = Engine_Api::_()->getDbTable('rules','yncontest')->find($rule_id)->current();		
	
		$this->view->form = $form = new Yncontest_Form_Rule_Edit();
		
		$form->setAttrib("class", "global_form_popup");
			
		$arr_option['option'] = array();
		if($rule->viewentries == 1)
			$arr_option['option'][] = 'view';
		if($rule->submitentries == 1)
			$arr_option['option'][] = 'submit';
		if($rule->voteentries == 1)
			$arr_option['option'][] = 'vote';
		$form->populate($arr_option);
		// If not post or form not valid, return
		if(!$this -> getRequest() -> isPost()) {
			return ;
		}
	
		$post = $this -> getRequest() -> getPost();
	
		if(!$form -> isValid($post))
			return ;
	
		// Process				
		$values = array_merge($form -> getValues());	
		
		$arr = array('contestId'=>$this->_getParam('contest', null), 'ruleId'=> $rule_id);
		
		$preRule =  Engine_Api::_()->getDbTable('rules','yncontest')->getPreRule($arr);
		
		$contest_id = $this->_getParam('contest', null);	
		$contest = Engine_Api::_()->getItem('contest', $contest_id);
		if($values['start_date'] < $contest->start_date){
			$form -> getElement('start_date') -> addError('Start Date must be greater than or equal to Start Date of Contest.');
			return;
		}
		if($values['end_date'] > $contest->end_date){
			$form -> getElement('end_date') -> addError('End Date must be less than or equal to End Date of Contest.');
				return;
		}
		if($values['end_date']< $values['start_date']){
				$form->addError('Start Date must be less than End Date.');
				return;
			}	
		
		
		if(count($preRule)>0 && $preRule->end_date>$values['start_date']){
			$form->addError('Start Date must be greater than or equal to End Date of previous rule.');
			return;
		}
		
		$nextRule =  Engine_Api::_()->getDbTable('rules','yncontest')->getNextRule($arr);
		
		if(count($nextRule)>0 && $nextRule->start_date<$values['end_date']){
			$form->addError('End Date can not after Start date of next rule ['.$nextRule->start_date.'].');
			return;
		}
		
		$rule -> setFromArray($values);
		$rule -> modified_date = date('Y-m-d H:i:s');
	
		if(!in_array('view',$values['option'] ))
			$rule -> viewentries = 0;
		else 
			$rule -> viewentries = 1;
		if(!in_array('submit',$values['option'] ))
			$rule -> submitentries = 0;
		else
			$rule -> submitentries = 1;		
		if(!in_array('vote',$values['option'] ))
			$rule -> voteentries = 0;
		else 
			$rule -> voteentries = 1;
		$rule -> save();
	
		$form->reset();
		
		// Refresh parent page
		$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh'=> 10,
				'messages' => array('')
		));
	
	}
	public function deleteRuleAction(){		
		$rule_id = $this->_getParam('rule', null);	
		$form = $this->view->form = new Yncontest_Form_Rule_Delete();
		
		$post = $this -> getRequest() -> getPost();
		
		if(!$this -> getRequest() -> isPost()) {
			return ;
		}
	
		$post = $this -> getRequest() -> getPost();
	
		if(!$form -> isValid($post))
			return ;
		
		$rule = Engine_Api::_()->getDbTable('rules','yncontest')->find($rule_id)->current();
		
		$rule->delete();
		
		
		// Refresh parent page
		$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh'=> 10,
				'messages' => array('')
		));
	}
	
	
	public function manageRuleAction(){
			
	
		Zend_Registry::set('active_menu', 'yncontest_main_create_contest');
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest_id = $this->_getParam('contest', null);	
		
		$this->view->form = $form = new Yncontest_Form_Rule_Create();
	
		$manageRuleId = $this->_getParam("rule", null);
		if($manageRuleId){
			$manaRule = Engine_Api::_()->getItem('yncontest_managerules', $manageRuleId);
			
			$array = array();
			
			$array = $manaRule->toArray();
			$form->populate($array);	
			
		}
		
		
		$values['contest_id'] = $contest_id;
		
		$page = $this -> _getParam('page', 1);
		$values['limit'] = 10;
		$this -> view -> paginator = Engine_Api::_() -> getApi('rule','yncontest') -> getRulesPaginator($values);
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
		$table = new Yncontest_Model_DbTable_Rules;
		$db = $table -> getAdapter();
		$db -> beginTransaction();
	
		try {
			$contest = Engine_Api::_()->getItem('contest', $contest_id);
			// Create rule
			$values = array_merge($form -> getValues(), array('user_id' => $contest->user_id,'contest_id'=>$contest_id ));
				
			
			//check start_date & end_date
			
			if($values['start_date'] < $contest->start_date){
				$form -> getElement('start_date') -> addError('Start Date must be greater than or equal to Start Date of Contest.');
				return;
				}
			if($values['end_date'] > $contest->end_date){
				$form -> getElement('end_date') -> addError('End Date must be less than or equal to End Date of Contest.');
				return;
			}		
			
			
			if($values['end_date']< $values['start_date']){
				$form->addError('Start Date must be less than End Date.');
				return;
			}
			$pre_rule = $table->getLastRule($contest_id);			
			if(count($pre_rule)>0){
				
				if($pre_rule->end_date >$values['start_date']){
					$form->addError('Start Date must be greater than or equal to End Date of previous rule');
					return;
				}
			}
		
				
			$rule = $table -> createRow();
			$rule -> setFromArray($values);
		
				
			
			if(!in_array('view',$values['option'] ))
				$rule -> viewentries = 0;
			else
				$rule -> viewentries = 1;
			if(!in_array('submit',$values['option'] ))
				$rule -> submitentries = 0;
			else
				$rule -> submitentries = 1;
			if(!in_array('vote',$values['option'] ))
				$rule -> voteentries = 0;
			else
				$rule -> voteentries = 1;
			$rule -> save();
	
			$db -> commit();
	
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		
		$values['contest_id'] = $contest_id;
		
		$page = $this -> _getParam('page', 1);
		$values['limit'] = 10;
		$this -> view -> paginator = Engine_Api::_() -> getApi('rule','yncontest') -> getRulesPaginator($values);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		$this->view->contest = $contest_id;
	
	
	}
	public function manageEditRuleAction(){
	
		Zend_Registry::set('active_menu', 'yncontest_main_create_contest');
	
		$viewer = Engine_Api::_()->user()->getViewer();
		$contest_id = $this->_getParam('contest', null);
	
		$this->view->form = $form = new Yncontest_Form_Rule_Create();
	
	
		$values['contest_id'] = $contest_id;
	
		$page = $this -> _getParam('page', 1);
		$values['limit'] = 10;
		$this -> view -> paginator = Engine_Api::_() -> getApi('rule','yncontest') -> getRulesPaginator($values);
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
		$table = new Yncontest_Model_DbTable_Rules;
		$db = $table -> getAdapter();
		$db -> beginTransaction();
	
		try {
			// Create rule
			$values = array_merge($form -> getValues(), array('user_id' => $viewer -> getIdentity(),'contest_id'=>$contest_id ));
	
			if($values['end_date']<= $values['start_date']){
				$form->addError('End Date can not before Start date');
				return;
			}
			$pre_rule = $table->getLastRule($contest_id);
			if(count($pre_rule)>0){
	
				if($pre_rule->end_date >$values['start_date']){
					$form->addError('Start date should be after End Date of previous rule');
					return;
				}
			}
	
	
			$rule = $table -> createRow();
			$rule -> setFromArray($values);
				
	
			if(!in_array('view',$values['option'] ))
				$rule -> viewentries = 0;
			else
				$rule -> viewentries = 1;
			if(!in_array('submit',$values['option'] ))
				$rule -> submitentries = 0;
			else
				$rule -> submitentries = 1;
			if(!in_array('vote',$values['option'] ))
				$rule -> voteentries = 0;
			else
				$rule -> voteentries = 1;
			$rule -> save();
	
			$db -> commit();
	
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
	
		$values['contest_id'] = $contest_id;
	
		$page = $this -> _getParam('page', 1);
		$values['limit'] = 10;
		$this -> view -> paginator = Engine_Api::_() -> getApi('rule','yncontest') -> getRulesPaginator($values);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		$this->view->contest = $contest_id;
	
	
	}
	
	
}
