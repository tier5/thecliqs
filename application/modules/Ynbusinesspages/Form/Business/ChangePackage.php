<?php
class Ynbusinesspages_Form_Business_ChangePackage extends Engine_Form
{
  public function init()
  {
    //Set form attributes
    $this->setTitle('Change Package')
      ->setDescription('YNBUSINESSPAGES_DASHBOARD_PACKAGE_WARNING')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
      ;
	 // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Change Package',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}