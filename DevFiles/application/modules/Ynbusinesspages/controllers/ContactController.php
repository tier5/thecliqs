<?php
class Ynbusinesspages_ContactController extends Core_Controller_Action_Standard
{
	public function init() {
		
		if (0 !== ($business_id = (int)$this -> _getParam('id')) && null !== ($business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id)))
		{
			Engine_Api::_() -> core() -> setSubject($business);
		}
		$this -> _helper -> requireSubject('ynbusinesspages_business');
	}
	
	public function editAction()
	{
		$this->_helper->content->setEnabled();
		
		$id = $this -> _getParam('id', 0);
		if (empty($id) || $id == 0){
			return $this->_helper->requireSubject()->forward();
		}
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $id);
		if (is_null($business))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
	  	$package = $business -> getPackage();
		if(!$package -> getIdentity() || !$package -> allow_owner_add_contactform)
		{
			return $this -> _helper -> requireAuth() -> forward();
		} 
		
		if(!$business -> isEditable())
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		//getBusiness
	  	$this -> view -> business = $business;
		//getReceiver
		$receiverTable = Engine_Api::_() -> getDbTable('receivers', 'ynbusinesspages');
		$receivers = $receiverTable -> getReceivers($business -> getIdentity());
		
	  	$this -> view -> form = $form = new Ynbusinesspages_Form_Contact_Edit(array(
	  		'business' => $business,
	  		'receivers' => $receivers,
	  	));
	  	$contactTbl = Engine_Api::_()->getItemTable('ynbusinesspages_contact');
	  	$this -> view -> contact = $contact = $contactTbl -> getContactByBusiness($business);
	  	if (!is_null($contact))
	  	{
	  		$form->populate($contact->toArray());
	  	}
	  	
		if( !$this->getRequest()->isPost() ) {
	      	return;
	    }
	    
	    if( !$form->isValid($this->getRequest()->getPost()) ) {
	      	return;
	    }
        $viewer = Engine_Api::_()->user()->getViewer();
        $values = $form->getValues();
        $values['business_id'] = $business -> getIdentity();
        $values['user_id'] = $viewer -> getIdentity();
        
		$fieldMetaTbl= new Ynbusinesspages_Model_DbTable_Meta();
	  	$customFields = $fieldMetaTbl -> getFields($business);
	  	$allFieldIds = array();
	  	if (count($customFields))
	  	{
	  		foreach ($customFields as $field)
	  		{
	  			$allFieldIds[] = (string)$field -> field_id;
			}
		}
		
        $enabledArr = (isset($_POST['enable_fields'])) ? $_POST['enable_fields'] : array();
	    $disabledArr = array_diff($allFieldIds, $enabledArr);
        $fieldMetaTbl->setEnabled($enabledArr, 1);
        $fieldMetaTbl->setEnabled($disabledArr, 0);
		
        $requiredArr = (isset($_POST['require_fields'])) ? $_POST['require_fields'] : array();
        $unrequiredArr = array_diff($allFieldIds, $requiredArr);
        $fieldMetaTbl->setRequired($requiredArr, 1);
        $fieldMetaTbl->setRequired($unrequiredArr, 0);
        
		foreach($receivers as $receiver)
		{
			//department
			$receiver -> department = $values['department_'.$receiver -> receiver_id];
			//email
			$email = $values['email_'.$receiver -> receiver_id];
			//check email
			$regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
			if (!preg_match($regexp, $email)) {
				$form -> addError('Please enter valid email!');
				return;
			}
			$receiver -> email = $email;
			$receiver -> save();
		}
		
        if (is_null($contact))
        {
        	$contact = $contactTbl -> createRow();
        }
        $contact -> setFromArray($values);
        $contact -> save(); 
        
        return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'action' => 'edit',
                    'id' => $business -> getIdentity()
                ), 'ynbusinesspages_contact', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
        ));
	}
	
	public function deleteReceiverAction()
	{
		$id = $this -> _getParam('id', 0);
		$rec_id = $this -> _getParam('rec_id', 0);
		if (empty($id) || $id == 0){
			return $this->_helper->requireSubject()->forward();
		}
		if (empty($rec_id) || $rec_id == 0){
			return $this->_helper->requireSubject()->forward();
		}
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $id);
		
		if (is_null($business))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		$package = $business -> getPackage();
		if(!$package -> getIdentity() || !$package -> allow_owner_add_contactform)
		{
			return $this -> _helper -> requireAuth() -> forward();
		} 
		
		if(!$business -> isEditable())
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$this -> view -> business = $business;
	  	$this -> view -> form = $form = new Ynbusinesspages_Form_Contact_DeleteReceiver();
		if( !$this->getRequest()->isPost() ) {
			return;
	    }
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
	    }
		
		$tableReceiver = Engine_Api::_() -> getDbTable('receivers', 'ynbusinesspages');
		$row = $tableReceiver -> fetchRow($tableReceiver -> select() -> where('receiver_id = ?', $rec_id) -> limit(1));
		$row -> delete();
		
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
	    	'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this->view->translate("Please wait..."))
		));
	}
	
	public function addReceiverAction()
	{
		$id = $this -> _getParam('id', 0);
		if (empty($id) || $id == 0){
			return $this->_helper->requireSubject()->forward();
		}
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $id);
		if (is_null($business))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		$package = $business -> getPackage();
		if(!$package -> getIdentity() || !$package -> allow_owner_add_contactform)
		{
			return $this -> _helper -> requireAuth() -> forward();
		} 
		
		if(!$business -> isEditable())
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		$this -> view -> business = $business;
	  	$this -> view -> form = $form = new Ynbusinesspages_Form_Contact_Receiver();
		if( !$this->getRequest()->isPost() ) {
			return;
	    }
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
	    }
		$values = $form->getValues();
	    $reciverTable = Engine_Api::_() -> getDbTable('receivers', 'ynbusinesspages');
	    
	    $db = $reciverTable->getAdapter();
    	$db->beginTransaction();
	    try 
	    {
		    $reciver = $reciverTable -> createRow();
		    $reciver -> setFromArray($values);
		    $reciver -> business_id = $business -> getIdentity();
		    $reciver -> save();
		    
		    $db->commit();	
	    } 
	    catch (Exception $e) 
	    {
	    	$db->rollback();
      		throw $e;
	    }
	    $this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
	    	'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this->view->translate("Please wait..."))
		));
	}
	
	public function editQuestionAction()
	{
		$id = $this -> _getParam('id', 0);
		$field_id = $this -> _getParam('field_id', 0);
		if (empty($id) || $id == 0){
			return $this->_helper->requireSubject()->forward();
		}
		if (empty($field_id) || $field_id == 0){
			return $this->_helper->requireSubject()->forward();
		}
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $id);
		
		if (is_null($business))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		$package = $business -> getPackage();
		if(!$package -> getIdentity() || !$package -> allow_owner_add_contactform)
		{
			return $this -> _helper -> requireAuth() -> forward();
		} 
		
		if(!$business -> isEditable())
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$tableMetas = Engine_Api::_() -> getDbTable('meta', 'ynbusinesspages');
		$this -> view -> field = $field = $tableMetas -> getField($field_id);
		
		$tableOptions = Engine_Api::_() -> getDbTable('options', 'ynbusinesspages');
		$this -> view -> options = $options = $tableOptions -> getOptions($field_id);
		
		$this -> view -> business = $business;
	  	$this -> view -> form = $form = new Ynbusinesspages_Form_Contact_EditQuestion();
		
		$form -> populate($field -> toArray());
		
		if( !$this->getRequest()->isPost() ) {
			return;
	    }
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
	    }
		
		$values = $form->getValues();
	    $fieldMetaTbl= new Ynbusinesspages_Model_DbTable_Meta();
	    
	    $db = $fieldMetaTbl->getAdapter();
    	$db->beginTransaction();
	    try 
	    {
		    $field -> setFromArray($values);
		    $field -> business_id = $business -> getIdentity();
		    $field -> save();
		 	
			$tableOptions = Engine_Api::_() -> getDbTable('options', 'ynbusinesspages');
			$tableOptions -> deleteItem($field_id);
			   
			if ( $field -> type == 'checkbox' || $field -> type == 'radio' )
		    {
			    $options = (array) $this->_getParam('optionsArray');
			    $options = array_filter(array_map('trim', $options));
			    $options = array_slice($options, 0, 99);
			    $this->view->options = $options;
			    if( empty($options) || !is_array($options)) {
			      return $form->addError('You must provide at least one option value.');
			    }
			    foreach( $options as $index => $option ) {
			      if( strlen($option) > 80 ) {
			        $options[$index] = Engine_String::substr($option, 0, 80);
			      }
			    }
		    	// Create options
			    $censor = new Engine_Filter_Censor();
			    $html = new Engine_Filter_Html(array('AllowedTags'=> array('a')));
			    $fieldOptionTbl = new Ynbusinesspages_Model_DbTable_Options();
			    foreach( $options as $option ) {
			        $option = $censor->filter($html->filter($option));
			        $fieldOptionTbl->insert(array(
			          'field_id' => $field_id,
			          'label' => $option,
			        ));
			    }
		    }
			
		    $db->commit();	
	    } 
	    catch (Exception $e) 
	    {
	    	$db->rollback();
      		throw $e;
	    }
		
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
	    	'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this->view->translate("Please wait..."))
		));
	}
	
	public function deleteQuestionAction()
	{
		$id = $this -> _getParam('id', 0);
		$field_id = $this -> _getParam('field_id', 0);
		if (empty($id) || $id == 0){
			return $this->_helper->requireSubject()->forward();
		}
		if (empty($field_id) || $field_id == 0){
			return $this->_helper->requireSubject()->forward();
		}
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $id);
		
		if (is_null($business))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
		$package = $business -> getPackage();
		if(!$package -> getIdentity() || !$package -> allow_owner_add_contactform)
		{
			return $this -> _helper -> requireAuth() -> forward();
		} 
		
		if(!$business -> isEditable())
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$this -> view -> business = $business;
	  	$this -> view -> form = $form = new Ynbusinesspages_Form_Contact_DeleteQuestion();
		if( !$this->getRequest()->isPost() ) {
			return;
	    }
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
	    }
		
		$tableMetas = Engine_Api::_() -> getDbTable('meta', 'ynbusinesspages');
		$tableMetas -> deleteField($field_id);
		
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
	    	'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this->view->translate("Please wait..."))
		));
	}
	
	public function addQuestionAction()
	{
		$id = $this -> _getParam('id', 0);
		if (empty($id) || $id == 0){
			return $this->_helper->requireSubject()->forward();
		}
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $id);
		if (is_null($business))
		{
			return $this->_helper->requireSubject()->forward();
		}
		$package = $business -> getPackage();
		if(!$package -> getIdentity() || !$package -> allow_owner_add_contactform)
		{
			return $this -> _helper -> requireAuth() -> forward();
		} 
		
		if(!$business -> isEditable())
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
	  	$this -> view -> business = $business;
	  	$this -> view -> form = $form = new Ynbusinesspages_Form_Contact_Question();
		if( !$this->getRequest()->isPost() ) {
			return;
	    }
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
	    }
	    $values = $form->getValues();
	    $fieldMetaTbl= new Ynbusinesspages_Model_DbTable_Meta();
	    
	    $db = $fieldMetaTbl->getAdapter();
    	$db->beginTransaction();
	    try 
	    {
		    $fieldMetaRow = $fieldMetaTbl -> createRow();
		    $fieldMetaRow -> setFromArray($values);
		    $fieldMetaRow -> business_id = $business -> getIdentity();
		    $fieldMetaRow -> save();
		    
			if ( $values['type'] == 'checkbox' || $values['type'] == 'radio' )
		    {
			    $options = (array) $this->_getParam('optionsArray');
			    $options = array_filter(array_map('trim', $options));
			    $options = array_slice($options, 0, 99);
			    $this->view->options = $options;
			    if( empty($options) || !is_array($options)) {
			      return $form->addError('You must provide at least one option value.');
			    }
			    foreach( $options as $index => $option ) {
			      if( strlen($option) > 80 ) {
			        $options[$index] = Engine_String::substr($option, 0, 80);
			      }
			    }
		    	// Create options
			    $censor = new Engine_Filter_Censor();
			    $html = new Engine_Filter_Html(array('AllowedTags'=> array('a')));
			    $fieldOptionTbl = new Ynbusinesspages_Model_DbTable_Options();
			    foreach( $options as $option ) {
			        $option = $censor->filter($html->filter($option));
			        $fieldOptionTbl->insert(array(
			          'field_id' => $fieldMetaRow->field_id,
			          'label' => $option,
			        ));
			    }
		    }
			
		    $db->commit();	
	    } 
	    catch (Exception $e) 
	    {
	    	$db->rollback();
      		throw $e;
	    }
	    $this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
	    	'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this->view->translate("Please wait..."))
		));
	}
}

