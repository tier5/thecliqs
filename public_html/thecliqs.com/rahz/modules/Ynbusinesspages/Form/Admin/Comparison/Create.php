<?php
class Ynbusinesspages_Form_Admin_Comparison_Create extends Engine_Form {
	public function init() {
		$this 
            -> setTitle('Add Header')
            -> setDescription('Add header to separate informations for businesses comparison.')
            -> setAttrib('class', 'global_form_popup');
          
		$this -> addElement('Text', 'title', array(
            'label' => 'Header title',
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

		$this -> addElement('Button', 'submit_btn', array(
			'label' => 'Submit',
			'type' => 'submit',
			'order' => 3,
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));
		$onclick = 'parent.Smoothbox.close();';
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'order' => 4,
			'link' => true,
			'prependText' => ' or ',
			'onclick' => $onclick,
			'decorators' => array('ViewHelper')
		));

		$this -> addDisplayGroup(array(
			'submit_btn',
			'cancel'
		), 'buttons');
	}

}
