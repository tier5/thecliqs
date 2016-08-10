<?php
class Viewed_AdminIndexController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		
	}
	
	
	public function settingAction()
	{
		$log=Zend_Registry::get('Zend_Log');
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('whoviewedme_admin_main', array(), 'whoviewedme_admin_main_settings');
		 
		$this->view->form = $form = new Viewed_Form_Admin_Setting();
		
		
		$member_level = $this->_getParam('level_id',"1");
		$memberviewcount_table = Engine_Api::_()->getDbTable('membercounts','viewed');
		$select = $memberviewcount_table->select()
		->where('level_id = ?', $member_level);
		$level_exits = $memberviewcount_table->fetchRow($select);
		if(isset($level_exits) && count($level_exits)>0)
		{
		  $form->populate($level_exits->toArray());
		}
		else {
			$form->getElement('level_id')->setValue($member_level);
		}
		
		// Not post/invalid
		$coresettings_table = Engine_Api::_()->getDbTable('settings','core');
		$coresettings_select = $coresettings_table->select()
		->where("name = 'testmode'");
		$coreexclude = $coresettings_table->select()
		->where("name= 'excludelevels'");
		$coresettings_result = $coresettings_table->fetchRow($coresettings_select);
		$coreexclude_result = $coresettings_table->fetchRow($coreexclude);
		$testMode = $coresettings_result->value;
		if(isset($coresettings_result) && count($coresettings_result)>0 && $testMode == 1)
		{
			$form->getElement('test_mode')->setChecked(true);
		}
		
		if(isset($coreexclude_result) && count($coreexclude_result)>0)
		{
			$excludedLevels = $coreexclude_result->value;
			$excludedLevels = explode(',', $excludedLevels);
			$form->getElement('exclude')->setValue($excludedLevels);
		}
		
		if (!$this->getRequest()->isPost()) {
			
			return;
		}
		
		if (!$form->isValid($this->getRequest()->getPost())) {
			$this->view->formErrors = $form->getMessages(null, true);
			return;
		}
		// Process
		$values = $form->getValues();
		if($values['exclude'] == null)
		{
		   $exclude_Mode = 0;
		  
		}
		else
		{
			$exclude_Mode = implode(',', $values['exclude']);
		}
		$test_Mode = $values['test_mode'];
		$insertdata =array(
				'name'=>'testmode',
				'value'=>$test_Mode
				);
		$updatedata =array(
				'value'=>$test_Mode
				);
		$exclude_insert = array(
				'name'=>'excludelevels',
				'value'=>$exclude_Mode
		);
		$exclude_update = array(
				'value'=>$exclude_Mode
		);
		if(isset($coresettings_result) && count($coresettings_result)>0)
		{
			$coresettings_table->update($updatedata,"name ='testmode'");
		}
		else {
			$coresettings_table->insert($insertdata);
		}
		if(isset($coreexclude_result) && count($coreexclude_result)>0)
		{
			$coresettings_table->update($exclude_update,"name ='excludelevels'");
		}
		else {
			$coresettings_table->insert($exclude_insert);
		}
		
		if(!($values['exclude']== null)){
		    $values['exclude'] = implode(',', $values['exclude']);
		}
		
		// update if exits
		if(isset($level_exits) && count($level_exits) > 0)
		{
			$memberviewcount_table->update($values,'level_id ='.$member_level);
		}
		else 
		{
			//saving data
			$db = $memberviewcount_table->getAdapter();
			$db->beginTransaction();
			try {	
					$setcount = $memberviewcount_table->createRow();
					$setcount->setFromArray($values);
					$setcount->save();
					$db->commit();		
				}
				catch (Exception $e)
				{
					$db->rollBack();
					throw $e;
				}
		}
		
	}
	
	
}
