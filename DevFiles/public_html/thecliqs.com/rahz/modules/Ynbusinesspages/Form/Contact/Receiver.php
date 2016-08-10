<?php
class Ynbusinesspages_Form_Contact_Receiver extends Engine_Form {
    
    public function init() {
            
        $view = Zend_Registry::get('Zend_View');
        $viewer = Engine_Api::_()->user()->getViewer();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this -> setAttrib('method', 'post');
        $this->loadDefaultDecorators();
    	$this->setTitle('Add Receiver');
        
		//Department
		$this->addElement('Text', 'department', array(
		  'label' => 'Department',
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
	        'label' => 'Email',
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
		
        // Submit
        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Add',
            'value' => '1',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));
		
		$this->addElement('Cancel', 'cancel', array(
	      'prependText' => ' or ',
	      'label' => 'cancel',
	      'link' => true,
	      'href' => '',
	      'onclick' => 'parent.Smoothbox.close();',
	      'decorators' => array(
	        'ViewHelper'
	      ),
	    ));
		
        $this->addDisplayGroup(array(
                'submit_btn',
                'cancel',
            ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
    }
}