<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Form_EditFile extends Engine_Form
{

	public function init()
	{
		// Init form
		$this -> setAttrib('class', '');
		$fileId = Zend_Controller_Front::getInstance() -> getRequest() -> getParam('file_id');
		$file = Engine_Api::_() -> getItem('ynfilesharing_file', $fileId);
		$this -> addElement('Hidden', 'file_id', array('value' => $file -> file_id, ));

		$fileName = Ynfilesharing_Plugin_Utilities::findFileName($file -> name);
		// Init name
		$this -> addElement('Text', 'name', array(
			'label' => 'File name',
			'required' => true,
			'style' => "width:260px; margin-bottom: 15px; margin-top: 10px;",
			'value' => $fileName,
			'description' => ''
		));
		$this -> name -> getDecorator('Description') -> setOption('placement', 'append');

		// Init submit
		$this -> addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_formButtonCancel.tpl',
						'class' => 'form element'
					)
				)),
		));
	}
}
