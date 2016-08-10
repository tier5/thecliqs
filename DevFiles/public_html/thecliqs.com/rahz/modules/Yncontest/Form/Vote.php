<?php
class Yncontest_Form_Vote extends Engine_Form
{
  public function init()
  {
  	 	$this->setMethod('post');
  		
  		// Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Vote',     
      'type' => 'submit',      
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
		}
}