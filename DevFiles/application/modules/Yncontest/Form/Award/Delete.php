<?php
class Yncontest_Form_Award_Delete extends Engine_Form
{
  public function init()
  {
  	
  	//Set Form Informations
  	$this -> setAttribs(array('class' => 'global_form_popup','method' => 'post'))
  	-> setTitle('Delete Award?')
  	-> setDescription('Are you sure that you want to delete this award? It will not be recoverable after being deleted.');
  	
  	//VAT Id
  	//$this->addElement('Hidden','rule_id');
  	
  	// Buttons
  	$this->addElement('Button', 'submit', array(
  			'label' => 'Delete',
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

