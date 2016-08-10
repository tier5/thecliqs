<?php
class Ynbusinesspages_Form_Contact_Edit extends Engine_Form
{
	protected $_business;
	protected $_receivers;
	
	public function getBusiness()
	{
		return $this -> _business;
	}

	public function setBusiness($business)
	{
		$this -> _business = $business;
	}
	
	public function getReceivers()
	{
		return $this -> _receivers;
	}

	public function setReceivers($receivers)
	{
		$this -> _receivers = $receivers;
	}
	
	public function init()
	{
		$view = new Zend_View();
		$this->setTitle('Edit Contact Form');
		$this->addElement('textarea', 'form_description', array(
	        'label' => 'Contact Description',
	        'allowEmpty' => true,
	      	'required' => false,
	        'filters' => array(
	        'StripTags',
				new Engine_Filter_Censor(),
			),
			'validators' => array(
		        array('StringLength', false, array(1, 300)),
			),
			'value' => $view -> translate("If you want to ask us a question directly, please submit your message with the following form. How can we help you?")
		));
		
		$this->addElement('text', 'full_name', array(
	        'label' => 'Full Name',
		));
		$this -> full_name -> setAttrib('disabled', 'true');
		
		$this->addElement('text', 'email', array(
	        'label' => 'Email',
		));
		$this -> email -> setAttrib('disabled', 'true');
		
		$this->addElement('text', 'subject', array(
	        'label' => 'Subject',
		));
		$this -> subject -> setAttrib('disabled', 'true');
		
		$this->addElement('text', 'message', array(
	        'label' => 'Message',
		));
		$this -> message -> setAttrib('disabled', 'true');
		
		$fieldMetaTbl= new Ynbusinesspages_Model_DbTable_Meta();
	  	$customFields = $fieldMetaTbl -> getFields($this -> _business);
	  	if (count($customFields))
	  	{
	  		foreach ($customFields as $field)
	  		{
	  			$delete_href = $view -> url(array('action' => 'delete-question', 'id' => $this ->_business -> getIdentity(), 'field_id' => $field -> field_id), 'ynbusinesspages_contact', true);
	  			$edit_href = $view -> url(array('action' => 'edit-question', 'id' => $this ->_business -> getIdentity(), 'field_id' => $field -> field_id), 'ynbusinesspages_contact', true);
				$this -> addElement('dummy', "field_{$field->field_id}", array(
						'decorators' => array( array(
							'ViewScript',
							array(
								'viewScript' => '_contact_field.tpl',
								'field' =>  $field,
								'class' => 'form element',
								'edit_href' => $edit_href,
								'delete_href' => $delete_href,
							)
						)), 
				));  
	  		}
	  	}
		
		$view = Zend_Registry::get("Zend_View");
		$this->addElement('Dummy', 'add_new_question', array(
			'label' => '',
			'content' => $view->htmlLink(	
				array('route' => 'ynbusinesspages_contact', 'action' => 'add-question', 'id' => $this->_business->getIdentity()),//href
				$view->translate("Add more fields"),//content
				array('class' => 'smoothbox'))
		));
		
		$iReceiver = 0;
		//Manage Receiver
		$this ->addElement('heading', 'manage_receiver', array(
			'label' => 'Manage Receiver',
		));
		
		if(!empty($this -> _receivers))
		{
			foreach ($this -> _receivers as  $receiver) {
				$iReceiver++;
				
				//Department
				$delete_href = $view -> url(array('action' => 'delete-receiver', 'id' => $this ->_business -> getIdentity(), 'rec_id' => $receiver -> receiver_id), 'ynbusinesspages_contact', true);
				
				$this->addElement('Text', 'department_'.$receiver -> receiver_id, array(
				  'label' => 'Department',
				  'class' => 'btn_form_inline',
				  'allowEmpty' => false,
				  'required' => true,
				  'validators' => array(
				    array('NotEmpty', true),
				  ),
				  'value' => $receiver -> department,
				  'filters' => array(
				    'StripTags',
				    new Engine_Filter_Censor(),
				  ),
				'description' => '<a type="button" class="smoothbox fa fa-minus" href="'.$delete_href.'"></a>',
			    ));
				$department_name = 'department_'.$receiver -> receiver_id;
			 	$this -> $department_name -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
				
				
				//Email
			    $this->addElement('Text', 'email_'.$receiver -> receiver_id, array(
			        'label' => 'Email',
			        'allowEmpty' => false,
			     	'required' => true,
			     	'validators' => array(
				        array('NotEmpty', true),
				        array('StringLength', false, array(1, 64)),
					),
					'value' => $receiver -> email,
				    'filters' => array(
				        'StripTags',
				        new Engine_Filter_Censor(),
				    ),
			    ));
			}
		}
		
		$this -> addElement('hidden', 'number_receiver', array(
			'value' => '0',
		));
		
		$this -> number_receiver -> setValue($iReceiver);
		
		$this->addElement('Dummy', 'add_new_receiver', array(
			'label' => '',
			'content' => $view->htmlLink(	
				array('route' => 'ynbusinesspages_contact', 'action' => 'add-receiver', 'id' => $this->_business->getIdentity()),//href
				$view->translate("Add more receivers"),//content
				array('class' => 'smoothbox'))
		));
		
		// Buttons
		$this->addElement('Button', 'submit', array(
		      'value' => 'submit',
		      'label' => 'Save Changes',
		      'onclick' => 'removeSubmit()',
		      'type' => 'submit',
		      'ignore' => true,
		      'decorators' => array(
		        'ViewHelper',
			),
		));
	}
}
