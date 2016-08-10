<?php
class Ynjobposting_Form_Submission_Question extends Engine_Form
{
  public function init()
  {
	$user = Engine_Api::_()->user()->getViewer();
    $this->setTitle('Add Question');
    $type = array(
    	'text' => 'Single line text',
    	'textarea' => 'Text area',
		'checkbox' => 'Checkbox',
		'radio' => 'Radio button',
    );
    $this->addElement('Select', 'type', array(
            'label' => 'Type',
            'multiOptions' => $type,
            'value' => key($type),
    		'onchange' => "showOptions(this);"
    ));
    
    $this->addElement('text', 'label', array(
      'label' => 'Label',
      'required' => true,
      'maxlength' => 63,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63')),
      ),
    ));

    $this->addElement('textarea', 'options', array(
      'label' => 'Values',
      'style' => 'display:none;',
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