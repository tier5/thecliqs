<?php
class Yncontest_AdminLevelController extends Core_Controller_Action_Admin
{
	public function init()
  {
  	parent::init();
  	Zend_Registry::set('admin_active_menu', 'yncontest_admin_main_level');
  }
  public function indexAction()
  {
    //$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
     // ->getNavigation('yncontest_admin_main', array(), 'yncontest_admin_main_level');

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
    $form = new Yncontest_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);
    $this->view->level_id = $id;
    // Populate data
    $valueArray = array('publishC_fee','featureC_fee','premiumC_fee','endingsoonC_fee');
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
	$select = $permissionsTable->select()->from($permissionsTable->info('name'));
    $select->where('level_id = ?', $id);
    $select->where('name IN (?)', $valueArray);
    $result = $permissionsTable->fetchAll($select)->toArray();
	$mainArray = $form->getValues();
	$perms = $permissionsTable->getAllowed('contest', $id, array_keys($mainArray));
	$perms['commententries'] = $permissionsTable->getAllowed('yncontest_entry', $id, 'comment');
	$form->populate($perms);
	foreach($result AS $key=>$value){

		if($value['name'] == 'publishC_fee'){
			$val = $form->publishC_fee->getValue();
			if($val < 1)
				$form->publishC_fee->setValue($value['value']);
		}
		
		if($value['name'] == 'featureC_fee'){
			$val = $form->featureC_fee->getValue();
			if($val < 1)
				$form->featureC_fee->setValue($value['value']);
		}
		
		if($value['name'] == 'premiumC_fee'){
			$val = $form->premiumC_fee->getValue();
			if($val < 1)
				$form->premiumC_fee->setValue($value['value']);
		}

		if($value['name'] == 'endingsoonC_fee'){
			$val = $form->endingsoonC_fee->getValue();
			if($val < 1)
				$form->endingsoonC_fee->setValue($value['value']);
		}
	}
	
	
    $this->view->form = $form;
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
    $privacyCommentEntry = $values['commententries'];
    unset($values['commententries']); 

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      // Set permissions
      $permissionsTable->setAllowed('contest', $id, $values);
      $permissionsTable->setAllowed('yncontest_entry', $id, 'comment', $privacyCommentEntry); 
      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
	
	if ($mainArray['publishC_fee'] == 3 || $mainArray['publishC_fee'] == 5) {
    	$permissionsTable->update(array(
	        	'params' => $mainArray['publishC_fee'],
	      		), array(
	        'name = ?' => 'publishC_fee',
	      		'level_id = ?' => $id, 
	      	 ));
    }
	
    $form->addNotice($this->view->translate('Your changes have been saved.'));
  }

}