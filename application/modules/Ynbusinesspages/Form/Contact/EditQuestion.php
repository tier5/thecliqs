<?php
class Ynbusinesspages_Form_Contact_EditQuestion extends Engine_Form
{
  public function init()
  {
	$user = Engine_Api::_()->user()->getViewer();
    $this->setTitle('Edit Field');
	
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
      'label' => 'Save changes',
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