<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Form_Custom_Upload extends Zend_Form
{
	private $_url = NULL;
		
	public function init()
	{
		if ($this->_url == NULL) {
			$this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
				'action' => 'upload', 
				'module' => 'ynprofilestyler',
			    'controller' => 'index'
			)));
		}
		
		$this->setAttribs(array('enctype' => 'multipart/form-data'/*, 'target' => 'wnd'*/, 'class' => 'global_form'));

		$this->addElement('file', 'background_file', array(
			'label'       => 'Background File',
			'description' => 'Choose an image from your computer',
			'accept' => 'image/*',
		));		
		
		$this->getElement('background_file')->addValidator('Extension', false, 'jpg,png,gif,jpeg');
		$this->getElement('background_file')->addValidator('Size', false, '2MB');
		
		$this->getElement('background_file')->removeDecorator('HtmlTag');

		$this->addElement('Button', 'submit', array(
            'label' => 'Upload',
            'type' => 'submit',
        ));      
        
        $this->getElement('submit')->removeDecorator('DtDdWrapper')->removeDecorator('HtmlTag'); 
	}
	
	public function setUrl($url) {
		$this->_url = $url;
	}
}