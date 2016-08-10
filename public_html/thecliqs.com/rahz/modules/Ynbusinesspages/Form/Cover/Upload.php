<?php
class Ynbusinesspages_Form_Cover_Upload extends Engine_Form
{
	public function init()
	{
		// Init form
		$this 
		-> setTitle('Add New Photos') 
		-> setAttrib('id', 'form-upload') 
		-> setAttrib('class', 'global_form event_form_upload') 
		-> setAttrib('name', 'albums_create') 
		-> setAttrib('enctype', 'multipart/form-data') 
		-> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array()));
		
		// Init file
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$this -> setDescription('Choose photos on your mobile to add to this album. (2MB maximum)');
			$this -> addElement('File', 'photos', array(
				'label' => 'Photos',
				'multiple' => 'multiple',
				'isArray' => true,
				'attrs' => array(
					'accept' => "image/*"
				)
			));
			$this -> addElement('Cancel', 'add_more', array(
				'label' => 'Add more',
				'link' => true,
				'onclick' => 'addMoreFile()',
			));
		}
		else
		{
			$this -> addElement('Dummy', 'html5_upload', array('decorators' => array( array(
						'ViewScript',
						array(
							'viewScript' => '_Html5Upload.tpl',
							'class' => 'form element',
							'isCover' => true,
						)
					)), ));
			$this -> addElement('Hidden', 'business_id', array('order' => 1));
			$this -> addElement('Hidden', 'html5uploadfileids', array(
				'value' => '',
				'order' => 2
			));
		}

		// Init submit
		$this -> addElement('Button', 'submit', array(
			'label' => 'Save Photos',
			'type' => 'submit',
		));
	}

}
