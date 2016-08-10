<?php

class Yncontest_Form_Member_Deny extends Engine_Form{
    public function init()
    {
       //Set Form Informations
    $this -> setAttribs(array('class' => 'global_form_popup','method' => 'post'))
          -> setTitle('Deny This Member?')
	  -> setDescription('Do you make sure that this member has been denied?');

       //VAT Id
    $this->addElement('Hidden','member_id');
    
        // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Deny',
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
