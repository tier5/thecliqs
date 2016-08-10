<?php
class Ynresume_Form_Admin_Degree_Add extends Engine_Form
{
  protected $_field;

  public function init()
  {
    $this->setMethod('post');

    $label = new Zend_Form_Element_Text('label');
    $label->setLabel('Degree Name')
      ->addValidator('NotEmpty')
      ->setRequired(true)
      ->setAttrib('class', 'text');


    $id = new Zend_Form_Element_Hidden('id');


    $this->addElements(array(
      //$type,
      $label,
      $id
    ));
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Degree',
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

   // $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

  public function setField($degree)
  {
    $this->_field = $degree;

    // Set up elements
    //$this->removeElement('type');
    $this->label->setValue($degree->name);
    $this->id->setValue($degree->degree_id);
    $this->submit->setLabel('Edit');

    // @todo add the rest of the parameters
  }
}