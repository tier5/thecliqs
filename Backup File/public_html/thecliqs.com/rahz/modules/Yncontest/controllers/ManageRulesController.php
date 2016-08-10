<?php
class Yncontest_ManageRulesController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
  	
  }
  
  public function createAction(){
  	if( !$this->_helper->requireUser()->isValid() ) return;
    //if( !$this->_helper->requireAuth()->setAuthParams('rule', null, 'create')->isValid()) return;
	$viewer = Engine_Api::_()->user()->getViewer();
	
  	$this -> view -> form = $form = new Yncontest_Form_ManageRule_Create();
	if($this->getRequest()->isPost()){//&& $form -> isValid($this->getRequest()->isPost())
		$table = Engine_Api::_() -> getDbTable('managerules', 'yncontest');
		
		$values = $this->getRequest()->getPost();
		$values['user_id'] = $viewer->getIdentity();

		$rule = $table->createRow();
      	$rule->setFromArray($values);
		$rule->save();		
		
		if(is_object($rule)){
				$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
			}else{
				$form->addError('An error occurs');
			}
	}
	
  }
  
  public function deleteAction(){
  	$form = $this->view->form = new Yncontest_Form_ManageRule_Delete();		
	$viewer = Engine_Api::_()->user()->getViewer();
	$rule = Engine_Api::_()->getItem('yncontest_managerules', $this->_getParam('rule_id'));
					
	// Save values
	if( $this->getRequest()->isPost() )
	{			
		$rule->delete();
		$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh'=> 10,
				'messages' => array('Rule is deleted.')
		));
	}
	
	//Output
    $this->renderScript('manage-rules/delete.tpl');  	
  }
  
  public function editAction(){
  	if( !$this->_helper->requireUser()->isValid() ) return;
    //if( !$this->_helper->requireAuth()->setAuthParams('rule', null, 'create')->isValid()) return;
	$viewer = Engine_Api::_()->user()->getViewer();
	
  	$this -> view -> form = $form = new Yncontest_Form_ManageRule_Create();
	$rule = Engine_Api::_()->getItem('yncontest_managerules', $this->_getParam('rule_id'));
	// Populate form
    $form->populate($rule->toArray());
	
	if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
		
	// Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {	
		$values = $this->getRequest()->getPost();
		$values['user_id'] = $viewer->getIdentity();
	
	  	$rule->setFromArray($values);
		$rule->save();
		$db->commit();
	}catch(Exception $e){
		$db->rollBack();
      throw $e;
	}
	
	if(is_object($rule)){
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}else{
			$form->addError('An error occurs');
		}
	
  }
  
}