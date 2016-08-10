<?php
class Ynbusinesspages_Form_Role_Create extends Engine_Form
{
	protected $_businessId;
	
	public function getBusinessId()
	{
		return $this -> _businessId;
	}
	
	public function setBusinessId($businessId)
	{
		$this -> _businessId = $businessId;
	} 
	
	public function init()
  	{
  		// Init form
    	$this->setTitle('Add New Role');
    	
    	// Init name
	    $this->addElement('Text', 'name', array(
	      'label' => 'Role Title',
	      'maxlength' => '100',
	      'allowEmpty' => false,
	      'required' => true,
	      'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	        new Engine_Filter_StringLength(array('max' => '100')),
	      )
	    ));
	    
	    //Clone Role Element
	    $listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
		$options = $listTbl -> getListAssocForClone($this->_businessId);
		$this->addElement('Select', 'clone_list_id', array(
	      	'label' => 'Copy Values from',
		 	'multiOptions' => $options,
	    ));
	    
	    // Buttons
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Save',
	      'type' => 'submit',
	      'ignore' => true,
	      'decorators' => array('ViewHelper')
	    ));
	
	    $this->addElement('Cancel', 'cancel', array(
	      'label' => 'cancel',
	      'link' => true,
	      'prependText' => ' or ',
	      'href' => '',
	      'onclick' => 'parent.Smoothbox.close();',
	      'decorators' => array(
	        'ViewHelper'
	      )
	    ));
	    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
	    $button_group = $this->getDisplayGroup('buttons');
  	}
}
