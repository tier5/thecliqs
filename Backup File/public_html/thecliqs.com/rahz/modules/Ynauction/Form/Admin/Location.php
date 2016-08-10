<?php
class Ynauction_Form_Admin_Location extends Engine_Form
{
  protected $_field;

  public function init()
  {
     $this
      ->setTitle('Location');
    $this->setMethod('post');
    $this->addElement('Hidden','id');
     //Location Name - Required
    $this->addElement('Text','label',array(
      'label'     => 'Location Name',
      'required'  => true,
      'allowEmpty'=> false,
    ));
  
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Location',
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
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      )));
    $button_group = $this->getDisplayGroup('buttons');
  }

  public function setField($location)
  {
    $this->_field = $location;

    // Set up elements
    //$this->removeElement('type');
    $this->label->setValue($location->title);
    $this->id->setValue($location->location_id);
    $this->submit->setLabel('Edit Location');

    // @todo add the rest of the parameters
  }
}