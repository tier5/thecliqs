<?php
class Ynresume_Form_Custom_Create extends Engine_Form
{
	protected $_formArgs;
	
	public function getFormArgs()
	{
		return $this -> _formArgs;
	}
	
	public function setFormArgs($formArgs)
	{
		$this -> _formArgs = $formArgs;
	} 
	
	protected $_item;
	
	public function getItem()
	{
		return $this -> _item;
	}
	
	public function setItem($item)
	{
		$this -> _item = $item;
	} 
	
  public function init()
  {
  	
	$this -> setAttrib('id', 'custom-field-group-form-'.$this -> _formArgs['heading']);
	$this -> setAttrib('onsubmit', 'return false;');
	
	$view = Zend_Registry::get('Zend_View');
    $settings = Engine_Api::_()->getApi('settings', 'core');
	$viewer = Engine_Api::_()->user()->getViewer();
	
	//Custom field
	if( !$this->_item ) {
      $customFields = new Ynresume_Form_Custom_Fields($this -> _formArgs);
    } else {
      $customFields = new Ynresume_Form_Custom_Fields(array_merge(array(
        'item' => $this->_item,
      ),$this -> _formArgs));
    }
	
    if( get_class($this) == 'Ynresume_Form_Custom_Create' ) {
      $customFields->setIsCreation(true);
    }
	
    $this->addSubForms(array(
      'fields' => $customFields
    ));
	
	// Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'onclick' => 'submitForm('.$this -> _formArgs['heading'].')',
      'type' => 'button',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
	
    $this->addElement('Button', 'cancel', array(
      'label' => 'Cancel',
      'type' => 'button',
      'href' => 'javascript:void(0);',
      'decorators' => array(
        'ViewHelper'
      )
    ));
	
	
	if( !$this->_item )
	{
		$this -> cancel -> setAttrib('class', 'ynresume-cancel-btn');
		$this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
	}
	else
	{
		$this -> cancel -> setAttrib('class', 'ynresume-group-cancel-btn');
		$this->addElement('Cancel', 'remove', array(
	      'label' => 'Remove this',
	      'link' => true,
	      'onclick' => 'confirmRemove('.$this -> _formArgs['heading'].')',
	      'ignore' => true,
	      'prependText' => ' or ',
	      'decorators' => array(
	        'ViewHelper'
	      )
	    ));
		$this->addDisplayGroup(array('submit', 'cancel', 'remove'), 'buttons');
	}
	
  }
}
