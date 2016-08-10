<?php
class Ynbusinesspages_Form_Contact_Question extends Engine_Form
{
  public function init()
  {
  	$translate = Zend_Registry::get('Zend_Translate');
	$user = Engine_Api::_()->user()->getViewer();
    $this->setTitle('Add Field');
    $type = array(
    	'text' => $translate -> translate('Single line text'),
    	'textarea' => $translate -> translate('Text area'),
		'checkbox' => $translate -> translate('Checkbox'),
		'radio' => $translate -> translate('Radio button'),
    );
	
	$this->addElement('text', 'label', array(
      'label' => 'Custom Field Name',
      'required' => true,
      'maxlength' => 63,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      ),
    ));
	
    $this->addElement('Select', 'type', array(
            'label' => 'Type',
            'multiOptions' => $type,
            'value' => key($type),
    		'onchange' => "showOptions(this);"
    ));
    
    $this->addElement('textarea', 'options', array(
      'label' => 'Values',
      'style' => 'display:none;',
    ));
	
	$this->addElement('Checkbox', 'required', array(
	      'label' => 'Required field',
	      'checkedValue' => '1',
	      'uncheckedValue' => '0',
    ));
	
    // Submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Add',
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
      'submit',
      'cancel'
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}