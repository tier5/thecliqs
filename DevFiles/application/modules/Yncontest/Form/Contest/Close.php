<?php
class Yncontest_Form_Contest_Close extends Engine_Form
{
  public function init()
  {
  	
  	//Set Form Informations
  	$this -> setAttribs(array('class' => 'global_form_popup','method' => 'post'))
  	-> setTitle('Close Contest?')
  	-> setDescription('Are you sure that you want to close this contest?');
  	
  	//VAT Id
  	//$this->addElement('Hidden','rule_id');
  	
  	// Buttons
  	$this->addElement('Button', 'submit', array(
  			'label' => 'Close',
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
  	
  }
}

