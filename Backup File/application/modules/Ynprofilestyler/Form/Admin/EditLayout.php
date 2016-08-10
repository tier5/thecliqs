<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Admin_EditLayout extends Engine_Form {
    private $_layout;
    
    public function init() {
        $this->setTitle('Edit Theme');
        $this->addElement('Text', 'title', array(
			'label'      => 'Title',
			'required'   => true,
			'allowEmpty' => false,
			'validators' => array(
				array('StringLength', true, array(1, 50)),
			),
		));
        
        if (is_object($this->_layout)) {
            $this->getElement('title')->setValue($this->_layout->title);
        }

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }
    
    public function setLayout($value) {
        $this->_layout = $value;
    }
}