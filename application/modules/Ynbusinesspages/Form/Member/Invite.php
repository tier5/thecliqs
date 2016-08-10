<?php

class Ynbusinesspages_Form_Member_Invite extends Engine_Form
{

	public function init()
	{
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> setTitle('Invite Members') -> setDescription('Choose the people you want to invite to this business.') -> setAttrib('id', 'ynbusinesspages_form_invite');
		$onfocus = "$(this).store('over', this.value);this.value = '';";
		$onblur = "this.value = $(this).retrieve('over');";
		$this->addElement('Text', 'search', array(
			'label' => '',
			'id'=>'friends_search',
			'value'=>'Search friends',
			'onfocus'=> $onfocus,
			'onblur' => $onblur,
			));

		$this -> addElement('Checkbox', 'all', array(
			'id' => 'selectall',
			'label' => 'Choose All Friends',
			'ignore' => true
		));

		$this -> addElement('dummy', 'friendlist', array(
			'label'     => '',
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_friend_list_container.tpl',
				)
			)),
		));
		// invite friend via email
		$this -> addElement('Textarea', 'recipients', array(
			'label' => 'Invite people via email',
			'description' => 'Comma-separated list, or one-email-per-line.',
			'required' => false,
			'allowEmpty' => true,
			'validators' => array(new Engine_Validate_Callback( array(
					$this,
					'validateEmails'
				)), ),
		));
		$this -> recipients -> getValidator('Engine_Validate_Callback') -> setMessage('Please enter only valid email addresses.');
		$this -> recipients -> getDecorator('Description') -> setOptions(array('placement' => 'APPEND'));

		// Init custom message
		if ($settings -> getSetting('invite.allowCustomMessage', 1) > 0)
		{
			$this -> addElement('Textarea', 'message', array(
				'label' => 'Add a person message',
				'required' => false,
				'allowEmpty' => true,
				'filters' => array(new Engine_Filter_Censor(), )
			));
			$this -> message -> getDecorator('Description') -> setOptions(array('placement' => 'APPEND'));
		}

		// Init captcha
		if ($settings -> core_spam_invite)
		{
			$this -> addElement('captcha', 'captcha', Engine_Api::_() -> core() -> getCaptchaOptions());
		}

		$this -> addElement('Button', 'submit', array(
			'label' => 'Send Invites',
			'type' => 'submit',
			'id' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper', ),
		));
		$onclick = 'parent.Smoothbox.close();';
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$onclick = '';
		}
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'onclick' => $onclick,
			'decorators' => array('ViewHelper', ),
		));

		$this -> addDisplayGroup(array(
			'submit',
			'cancel'
		), 'buttons');
	}

	public function isValid($data)
	{
		$result = parent::isValid($data);

		return $result;
	}

	public function validateEmails($value)
	{
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
