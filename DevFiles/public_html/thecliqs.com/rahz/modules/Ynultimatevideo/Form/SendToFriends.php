<?php

class Ynultimatevideo_Form_SendToFriends extends Engine_Form
{
	public $invalid_emails = array();

	public $emails_sent = 0;

	public function init() {
		// Init settings object
		$translate = Zend_Registry::get('Zend_Translate');

		// Init form
		$this -> setTitle('Send Message To Friends')
			->setAttrib('class', 'global_form_popup')
			-> setDescription('YNULTIMATEVIDEO_EMAIL_TO_FRIENDS_DESCRIPTION') -> setLegend('');

		// Init recipients
		$this -> addElement('Textarea', 'recipients', array(
			'label' => 'Recipients',
			'description' => '(Comma-separated list, or one-email-per-line)',
			'style' => 'width:450px',
			'required' => true,
			'allowEmpty' => false,
			'validators' => array(new Engine_Validate_Callback( array(
					$this,
					'validateEmails'
				)), ),
		));
		$this -> recipients -> getValidator('Engine_Validate_Callback') -> setMessage('Please enter only valid email addresses.');
		$this -> recipients -> getDecorator('Description') -> setOptions(array('placement' => 'APPEND'));

		// Init custom message
		$this -> addElement('Textarea', 'message', array(
			'label' => 'Custom Message',
			'style' => 'width:450px',
			'required' => false,
			'allowEmpty' => true,
			'filters' => array(new Engine_Filter_Censor(), )
		));
		$this -> message -> getDecorator('Description') -> setOptions(array('placement' => 'APPEND'));

		$this -> addElement('Button', 'submit', array(
			'label' => 'Send Emails',
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));
		$buttons[] = 'submit';
		$onclick = 'parent.Smoothbox.close();';
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'href' => '',
			'onclick' => $onclick,
			'decorators' => array('ViewHelper')
		));
		$buttons[] = 'cancel';

		$this -> addDisplayGroup($buttons, 'buttons');
		$button_group = $this -> getDisplayGroup('buttons');
	}

	public function validateEmails($value) {
		// Not string?
		if (!is_string($value) || empty($value))
		{
			return false;
		}

		// Validate emails
		$validate = new Zend_Validate_EmailAddress();

		$emails = array_unique(array_filter(array_map('trim', preg_split("/[\s,]+/", $value))));

		if (empty($emails))
		{
			return false;
		}

		foreach ($emails as $email)
		{
			if (!$validate -> isValid($email))
			{
				return false;
			}
		}

		return true;
	}

}
