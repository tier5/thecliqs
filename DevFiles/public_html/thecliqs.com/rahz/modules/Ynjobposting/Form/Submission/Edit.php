<?php
class Ynjobposting_Form_Submission_Edit extends Engine_Form
{
	protected $_company;

	public function getCompany()
	{
		return $this -> _company;
	}

	public function setCompany($company)
	{
		$this -> _company = $company;
	}

	public function init()
	{
		$this->setTitle('Edit Submission Form');
		$this->addElement('Text', 'form_title', array(
		      'label' => 'Form Title',
		      'allowEmpty' => false,
		      'required' => true,
		      'validators' => array(
					array('NotEmpty', true),
					array('StringLength', false, array(1, 64)),
			  ),
		      'filters' => array(
		        'StripTags',
				new Engine_Filter_Censor(),
			  ),
		));

		$this->addElement('Textarea', 'form_description', array(
	        'label' => 'Form Description',
	        'allowEmpty' => true,
	      	'required' => false,
	        'filters' => array(
	        'StripTags',
				new Engine_Filter_Censor(),
			),
		));
		
		$this->addElement('Checkbox', 'show_company_logo', array(
		      'label' => "Show Company Logo",
		));
		
		$this->addElement('Checkbox', 'show_job_title', array(
		      'label' => "Show Job Title",
		));
		
		$this->addElement('Checkbox', 'allow_video', array(
		      'label' => "Allow to apply for jobs by using video resume",
		));
		
		$view = Zend_Registry::get("Zend_View");
		$this->addElement('Dummy', 'add_new_question', array(
			'label' => '',
			'order' => 998,
			'content' => $view->htmlLink(	
				array('route' => 'ynjobposting_extended', 'controller' => 'submission', 'action' => 'add-question', 'id' => $this->_company->getIdentity()),//href
				$view->translate("Add more question"),//content
				array('class' => 'smoothbox'))
		));
		
		// Buttons
		$this->addElement('Button', 'submit', array(
		      'value' => 'submit',
		      'label' => 'Updates',
		      'onclick' => 'removeSubmit()',
		      'type' => 'submit',
		      'ignore' => true,
		  	  'order' => 999,
		      'decorators' => array(
		        'ViewHelper',
			),
		));
	}
}
