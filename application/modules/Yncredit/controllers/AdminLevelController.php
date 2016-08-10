<?php
class Yncredit_AdminLevelController extends Core_Controller_Action_Admin
{
	public function init()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('yncredit_admin_main', array(), 'yncredit_admin_main_level');
		$this->view->menu = $this->_getParam('action', 'index');
	}
	
	public function indexAction()
	{
		// Get level id
	    if( null !== ($id = $this->_getParam('id')) ) {
	      $level = Engine_Api::_()->getItem('authorization_level', $id);
	    } else {
	      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
	    }
	
	    if( !$level instanceof Authorization_Model_Level ) {
	      throw new Engine_Exception('missing level');
	    }
	
	    $id = $level->level_id;
	
	    // Make form
	    $this->view->form = $form = new Yncredit_Form_Admin_Level(array(
	      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
	    ));
	    
	    $form->level_id->setValue($id);
	    // Populate values
	    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
	    $form->populate($permissionsTable->getAllowed('yncredit', $id, array_keys($form->getValues())));
	    
	    
	    // Check post
	    if( !$this->getRequest()->isPost() ) {
	      return;
	    }
	
	    // Check validitiy
	    if( !$form->isValid($this->getRequest()->getPost()) ) {
	      return;
	    }
	
	    // Process
	    $values = $form->getValues();
	
	    $db = $permissionsTable->getAdapter();
	    $db->beginTransaction();
	
	    try
	    {
	      // Set permissions
	      $permissionsTable->setAllowed('yncredit', $id, $values);
	
	      // Commit
	      $db->commit();
	    }
	
	    catch( Exception $e )
	    {
	      $db->rollBack();
	      throw $e;
	    }
	    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
	}
	
	public function creditAction()
	{
		// Get level id
	    if( null !== ($id = $this->_getParam('id')) ) {
	      $level = Engine_Api::_()->getItem('authorization_level', $id);
	    } else {
	      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
	    }
	
	    if( !$level instanceof Authorization_Model_Level ) {
	      throw new Engine_Exception('missing level');
	    }

	    $this->view->levelId = $levelId = $level->getIdentity();
	    $creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
	    $creditTblName = $creditTbl->info("name");
	    
	    $creditAmount = $creditTbl->select()
	    	->from($creditTblName, "count(`credit_id`)")
	    	->where("level_id = ? ", $levelId)
	    	->query()
	    	->fetchColumn(0);
	    if (!$creditAmount)
	    {
	    	Engine_Api::_()->yncredit()->inputCreditData($levelId);
	    }
	    
		$this -> view -> credits = $creditTbl -> getAllActionEnableByLevel($levelId);
		$modules = Engine_Api::_() -> getDbTable('modules', 'yncredit') -> getModulesDisabled($levelId);
		$disableModules = array();
		foreach($modules as $module)
		{
			$disableModules[] = $module['name'];
		}
		$this -> view -> disableModules = $disableModules;
		// Prepare user levels
		$levelOptions = array();
		foreach( Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level ) 
		{
			if($level -> type != "public")
				$levelOptions[$level->level_id] = $level->getTitle();
		}
		$this->view->levelOptions = $levelOptions;
		
	    // Check post
	    if( !$this->getRequest()->isPost() ) 
	    {
	    	return;
	    }
	    
		$values = $this->_getAllParams();
		if (!isset($values['level_id']))
		{
			return;
		}
		
		if (isset($values['submit_set_default']) && $values['submit_set_default'] != '')
		{
			$where = $creditTbl->getAdapter()->quoteInto('level_id = ?', intval($values['level_id']));
			$creditTbl->delete($where);
			Engine_Api::_()->yncredit()->inputCreditData($levelId);
			return $this->_helper->redirector->gotoRoute(array('action' => 'credit', 'id'=> $levelId));
		}
		
    	foreach ($values as $k => $v) 
    	{
    		if (strpos($k, "__") !== false)
    		{
    			$args = explode("__", $k);
    			$fieldName = $args[0];
    			$fieldId = $args[1];
    			$fieldValue = (int)$v;
    			if ($fieldValue < 0) 
    			{
    				$fieldValue *= -1;
    			}
    			if ($fieldId && $fieldName) 
    			{
    				$creditTbl->update(
    						array(
    							$fieldName => $fieldValue
    						), 
    						array(
    							'credit_id = ?' => $fieldId,
    						));
    			}
      		
      		}
	      	
    	}
    	$this->_helper->redirector->gotoRoute(array('action' => 'credit', 'id'=> $levelId));
	}
	/*----- Disable or Enable Module Function -----*/
	  public function disableEnableModuleAction()
	  {
	      //Get params
	      $levle_id = $this->_getParam('level_id', 0); 
	      $status = $this->_getParam('status', 0);
	      $name = $this->_getParam('name', '');
		  
		  $moduleTable = Engine_Api::_() -> getDbTable('modules', 'yncredit');
		  $row = $moduleTable -> getModuleDisabled($name, $levle_id);
		  if(!$status)
		  {
		  	// add to disable modules
		  	if(!$row)
			{
				$row = $moduleTable -> createRow() -> setFromArray(array('name' => $name, 'level_id' => $levle_id));
				$row -> save();
			}
		  }
		  else 
		  {
			  // remove form disable modules
			  if($row)
			  {
			  	$row -> delete();
			  }
		  }
	  }
}