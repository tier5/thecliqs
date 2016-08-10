<?php

class Ynresume_Form_Resume_Photo extends Engine_Form
{
  
  public function init()
  {
    $this
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('name', 'EditPhoto');

    $this->addElement('Image', 'current', array(
      'label' => 'Current Photo',
      'ignore' => true,
      'decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_formEditImage.tpl',
				'class' => 'form element',
				'testing' => 'testing'
			)
		)), 
    ));
	
    Engine_Form::addDefaultDecorators($this->current);
    
    $this->addElement('File', 'Filedata', array(
      'label' => 'Choose New Photo',
      'destination' => APPLICATION_PATH.'/public/temporary/',
      'multiFile' => 1,
      'validators' => array(
        array('Count', false, 1),
        // array('Size', false, 612000),
        array('Extension', false, 'jpg,jpeg,png,gif'),
      ),
    ));

    $this->addElement('Hidden', 'coordinates', array(
      'filters' => array(
        'HtmlEntities',
      )
    ));

    $this->addElement('Button', 'done', array(
      'label' => 'Save Photo',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addElement('Cancel', 'remove', array(
      'label' => 'remove photo',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'action' => 'remove-photo',
      )),
      'onclick' => null,
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array('done', 'remove'), 'buttons');
    
  }
}