<?php
class Ynbusinesspages_Form_Business_Contact extends Engine_Form {
    protected $_business;
    
    public function getBusiness() {
        return $this -> _business;
    }
    
    public function setBusiness($business) {
        $this -> _business = $business;
    } 
    
    public function init() {
            
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this -> setAttrib('id', 'contact_business_form');
        $this -> setAttrib('method', 'post');
        $this->loadDefaultDecorators();
    
        $business = $this ->_business;
        $contactForm = $business->getContactForm();
        
		//Full name
		$this->addElement('Text', 'name', array(
		  'label' => '*Full Name',
		  'allowEmpty' => false,
		  'required' => true,
		  'validators' => array(
		    array('NotEmpty', true),
		  ),
		  'filters' => array(
		    'StripTags',
		    new Engine_Filter_Censor(),
		  ),
		));
		
		//Email
	    $this->addElement('Text', 'email', array(
	        'label' => '*Email',
	        'allowEmpty' => false,
	     	'required' => true,
	     	'validators' => array(
		        array('NotEmpty', true),
		        array('StringLength', false, array(1, 64)),
			),
		    'filters' => array(
		        'StripTags',
		        new Engine_Filter_Censor(),
		    ),
	    ));
		
		//Department
		$tableReceiver = Engine_Api::_() -> getDbTable('receivers', 'ynbusinesspages');
		$receivers = $tableReceiver -> getReceivers($this ->_business -> getIdentity());
		if(count($receivers) > 0)
		{
			$arr_department = array();
			foreach ($receivers as $receiver ) 
			{
				$arr_department[$receiver -> email] = $receiver -> department;
			}
		
			 $this->addElement('Select', 'department', array(
			  'required'  => true,
		      'allowEmpty'=> false,
		      'label' => '*Department',
		    ));
			
			$this -> department -> setMultiOptions($arr_department);
		}
		//Subject
		$this->addElement('Text', 'subject', array(
		  'label' => '*Subject',
		  'allowEmpty' => false,
		  'required' => true,
		  'validators' => array(
		    array('NotEmpty', true),
		  ),
		  'filters' => array(
		    'StripTags',
		    new Engine_Filter_Censor(),
		  ),
		));
		
		//Message
		$this->addElement('Textarea', 'message', array(
		  'label' => '*Message',
		  'allowEmpty' => false,
		  'required' => true,
		  'validators' => array(
		    array('NotEmpty', true),
		    array('StringLength', false, array(1, 500)),
		  ),
		  'filters' => array(
		    'StripTags',
		    new Engine_Filter_Censor(),
		  ),
		));
		
        if (!$contactForm) return;
        $this -> setTitle('Contact Form');
        $this -> setDescription($contactForm->form_description);

        $questionFields = $business->getContactQuestionFields();
        $fieldOptionTbl = new Ynbusinesspages_Model_DbTable_Options();
        foreach ($questionFields as $questionField) {
            if (!$questionField->enabled) continue;
			$isRequired = $questionField->required;
			$iconRequired = '';
			if($isRequired)
			{
				$iconRequired = '*';
			}
            switch ($questionField->type) {
                case 'text':
                    $this->addElement('Text', 'field_'.$questionField->field_id, array(
                        'label' => $iconRequired.$questionField->label,
                        'required' => $questionField->required,
                        'maxlength' => 500,
                        'filters' => array(
                            'StripTags',
                            new Engine_Filter_Censor(),
                        ),
                        'value' => ($questionField->field_id == 1) ? $viewer->getTitle() : '',
                    ));
                    
                    break;
                case 'textarea':
                    $this->addElement('Textarea', 'field_'.$questionField->field_id, array(
                        'label' => $iconRequired.$questionField->label,
                        'required' => $questionField->required,
                        'filters' => array(
                            'StripTags',
                        ),
                    ));
                    break;
                case 'checkbox':
                    $options = $fieldOptionTbl->getOptions($questionField->field_id);
                    $this->addElement('MultiCheckbox', 'field_'.$questionField->field_id, array(
                        'label' => $iconRequired.$questionField->label,
                        'required' => $questionField->required,
                        'multiOptions' => $options,
                    ));
                    break;
                case 'radio':
                    $options = $fieldOptionTbl->getOptions($questionField->field_id);
                    $this->addElement('Radio', 'field_'.$questionField->field_id, array(
                        'label' => $iconRequired.$questionField->label,
                        'required' => $questionField->required,
                        'multiOptions' => $options,
                    ));
                    break;
            }
        }

        // Submit
        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Send',
            'value' => '1',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));

        $this->addDisplayGroup(array(
                'submit_btn',
            ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
    }
}