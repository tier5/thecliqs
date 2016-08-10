<?php
class Yncontest_Form_Member_Join extends Engine_Form
{
	protected $_terms;
	public function setTerms($terms) {
		$this->_terms = $terms;
	}
  public function init()
  {  	 
	$this->setTitle("Terms and Conditions");
  	
  	$this->addElement('dummy', 'temr',array(  			
  			'content' => '<br>  		
<div class="ynContest_join_term_description">'.$this->_terms.'</div>
<br>',
  			));
  	$this->temr->removeDecorator('label');
	
     $this->addElement('Checkbox', 'terms', array(
        'label' => 'I have read and agree to the terms and conditions.',     
        'required' => true,    
      ));     
	
    $this->addElement('Button', 'submit', array(
      'label' => 'Join',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'onclick' => 'removeSubmit()',
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons');
   
  }
}