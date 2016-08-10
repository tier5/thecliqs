<?php
class Yncredit_Form_SendCredits extends Engine_Form
{
	public function init()
	{
		parent::init();
		$this->setAttribs(array(
			'class' => 'global_form',
			));
		$action = "";
		$this -> setAction($action);
		
		$this -> addElement('Text', 'to', array(
		'autocomplete' => 'off',
		'label' => 'Your friend name'));
		Engine_Form::addDefaultDecorators($this -> to);
		// Init to Values
		$this -> addElement('Hidden', 'toValues', array(
			'label' => 'Your friend name',
			'required' => true,
			'allowEmpty' => false,
			'order' => 1,
			'validators' => array('NotEmpty'),
			'filters' => array('HtmlEntities'),
		));
		Engine_Form::addDefaultDecorators($this -> toValues);

	    // Credit
	    $this->addElement('Text', 'credit', array(
	      'label' => 'Credit',
	      'allowEmpty' => false,
	      'required' => true,
	      'onkeypress' => "return onlyNumbers(event);",
	      'validators' => array(
	       	array('Int', true),
        	new Engine_Validate_AtLeast(0)
	      ),
	    ));

		$this->addElement('Button', 'send_credit', array(
	      'label' => 'Send Credits',
	      'type' => 'submit',
	      'onclick' => 'return sendCredit()',
	      'ignore' => true
	    ));
	}
}