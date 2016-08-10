<?php
class Ynjobposting_SubmissionController extends Core_Controller_Action_Standard
{
	public function editAction()
	{
		$this->_helper->content->setEnabled();
		$id = $this -> _getParam('id', 0);
		if ($id == 0){
			return $this->_helper->requireSubject()->forward();
		}
		$company = Engine_Api::_() -> getItem('ynjobposting_company', $id);
		if (is_null($company))
		{
			return $this->_helper->requireSubject()->forward();
		}
	  	$this -> view -> company = $company;
	  	$this -> view -> form = $form = new Ynjobposting_Form_Submission_Edit(array(
	  		'company' => $company
	  	));
	  	$submissionTbl = Engine_Api::_()->getItemTable('ynjobposting_submission');
	  	$this -> view -> submission = $submission = $submissionTbl -> getSubmissionByCompany($company);
	  	if (!is_null($submission))
	  	{
	  		$form->populate($submission->toArray());
	  	}
	  	$fieldMetaTbl= new Ynjobposting_Model_DbTable_Meta();
	  	$customFields = $fieldMetaTbl -> getFields($company);
	  	$allFieldIds = array();
	  	if (count($customFields))
	  	{
	  		foreach ($customFields as $field)
	  		{
	  			$allFieldIds[] = (string)$field -> field_id;
				$form -> addElement('dummy', "field_{$field->field_id}", array(
						'decorators' => array( array(
							'ViewScript',
							array(
								'viewScript' => '_submission_field.tpl',
								'field' =>  $field,
								'class' => 'form element',
							)
						)), 
				));  
	  		}
	  	}
	  	
		if( !$this->getRequest()->isPost() ) {
	      	return;
	    }
	    
	    if( !$form->isValid($this->getRequest()->getPost()) ) {
	      	return;
	    }
        $viewer = Engine_Api::_()->user()->getViewer();
        $values = $form->getValues();
        $values['company_id'] = $company -> getIdentity();
        $values['user_id'] = $viewer -> getIdentity();
        
        if (is_null($submission))
        {
        	$submission = $submissionTbl -> createRow();
        }
        
        $submission -> setFromArray($values);
        $submission -> save(); 
        
        $enabledArr = (isset($_POST['enable_fields'])) ? $_POST['enable_fields'] : array();
        $disabledArr = array_diff($allFieldIds, $enabledArr);
        $fieldMetaTbl->setEnabled($enabledArr, 1);
        $fieldMetaTbl->setEnabled($disabledArr, 0);
		
        $requiredArr = (isset($_POST['require_fields'])) ? $_POST['require_fields'] : array();
        $unrequiredArr = array_diff($allFieldIds, $requiredArr);
        $fieldMetaTbl->setRequired($requiredArr, 1);
        $fieldMetaTbl->setRequired($unrequiredArr, 0);
        
        
        return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                    'controller' => 'submission',
                    'action' => 'edit',
                    'id' => $company -> getIdentity()
                ), 'ynjobposting_extended', true),
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
        ));
	}
	
	public function addQuestionAction()
	{
		$id = $this -> _getParam('id', 0);
		if ($id == 0){
			return $this->_helper->requireSubject()->forward();
		}
		$company = Engine_Api::_() -> getItem('ynjobposting_company', $id);
		if (is_null($company))
		{
			return $this->_helper->requireSubject()->forward();
		}
	  	$this -> view -> company = $company;
	  	$this -> view -> form = $form = new Ynjobposting_Form_Submission_Question();
		if( !$this->getRequest()->isPost() ) {
			return;
	    }
		if( !$form->isValid($this->getRequest()->getPost()) ) {
			return;
	    }
	        
	    
	    $values = $form->getValues();
	    $fieldMetaTbl= new Ynjobposting_Model_DbTable_Meta();
	    
	    $db = $fieldMetaTbl->getAdapter();
    	$db->beginTransaction();
	    try 
	    {
		    $fieldMetaRow = $fieldMetaTbl -> createRow();
		    $fieldMetaRow -> setFromArray($values);
		    $fieldMetaRow -> company_id = $company -> getIdentity();
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
			    $fieldOptionTbl = new Ynjobposting_Model_DbTable_Options();
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
			//'parentRedirect' => $redirect_url,
			'format' => 'smoothbox',
			'messages' => array($this->view->translate("Please wait..."))
		));
	}
}

