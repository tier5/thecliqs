<?php
class Yncontest_Form_Vote2 extends Engine_Form
{
  public function init()
  {
  	 	$this->setMethod('post');
  		
  		// Buttons
    $this->addElement('Button', 'submit2', array(
      'label' => 'Vote',     
      'type' => 'submit',      
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
		}
}