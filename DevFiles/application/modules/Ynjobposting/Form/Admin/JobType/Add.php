<?php
class Ynjobposting_Form_Admin_JobType_Add extends Engine_Form
{
  protected $_field;

  public function init()
  {
    $this->setMethod('post');

    $label = new Zend_Form_Element_Text('label');
    $label->setLabel('Type Name')
      ->addValidator('NotEmpty')
      ->setRequired(true)
      ->setAttrib('class', 'text');


    $id = new Zend_Form_Element_Hidden('id');


    $this->addElements(array(
      $label,
      $id
    ));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

  }

  public function setField($type)
  {
    $this->_field = $type;
    $this->label->setValue($type->title);
    $this->id->setValue($type->jobtype_id);
    $this->submit->setLabel('Edit');

  }
}