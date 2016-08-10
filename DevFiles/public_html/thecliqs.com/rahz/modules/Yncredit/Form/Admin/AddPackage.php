<?php
class Yncredit_Form_Admin_AddPackage extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'packages_form',
        'class' => 'global_form_box',
      ));

    $this->setTitle('Add Price for Credits')
      ->setDescription('Add Credits with its cost');

	 $this->addElement('Text', 'price', array(
      'label' => 'Price',
      'required' => true,
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast(0)
      )
    ));
	
	$this->addElement('Text', 'credit', array(
      'label' => 'Credits',
      'required' => true,
      'validators' => array(
        array('Int', true),
        new Engine_Validate_AtLeast(0)
      )
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Add Package',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}