<?php
class Yncontest_Form_Setting_Create extends Engine_Form
{
  public function init()
  {

    $this
      ->setTitle('Contest Settings')
			->setAttrib('name', 'yncontest_contest_settings');

    $this->addElement('Checkbox', 'comment', array(
    		'label' => 'Allow members to comment on my contest.',
    		'value' => 1,
    		'checked' => true,
    ));

    $this->addElement('Checkbox', 'comment_entries', array(
    		'label' =>'Allow members to comment on entries.',
    		'value' => 1,
    		'checked' => true,
    ));
    
    $this->addElement('Checkbox', 'entries_approve', array(
    		'label' =>'New Entries can approve immediately.',
    		'value' => 1,
    		'checked' => true,
    ));   
    $this->addElement('Checkbox', 'post_send_email', array(
    		'label' =>'Send me an email when there is a posted entry.',
    		'value' => 1,
    		'checked' => true,
    ));   

    
    $this->addElement('Text', 'max_entries', array(
    		'label' => 'Maximum entries which a member can submit.',
    		'allowEmpty' => false,
    		'required' => true,
    		'description'=>'0 means no limit',
    		'value' => 0,
    		'validators' => array(    				
    				array('NotEmpty',true),
    				array('Int',true),
    		),
    		   		
    ));
    $this->max_entries->getDecorator("Description")->setOption("placement", "append");
   
    // Buttons
     $this->addElement('Button', 'submit', array(
      'label' => 'Save & Continue',     
      'type' => 'submit',      
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
         
    $this->addElement('Cancel', 'cancel', array(
    	'label' => 'cancel',
    	'link' => true,
    	'prependText' => ' or ',
    	'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'yncontest_general', true),
    	'onclick' => '',
    	'decorators' => array(
    			'ViewHelper'
    	)
    ));
	$this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}

