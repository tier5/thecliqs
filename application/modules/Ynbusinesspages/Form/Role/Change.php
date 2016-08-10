<?php
class Ynbusinesspages_Form_Role_Change extends Engine_Form
{
	protected $_business;
	protected $_user;
	public function getBusiness()
	{
		return $this -> _business;
	}
	public function setBusiness($business)
	{
		$this -> _business = $business;
	} 
	public function getUser()
	{
		return $this -> _user;
	}
	public function setUser($user)
	{
		$this -> _user = $user;
	} 
	public function init()
	{
		$this->setTitle('Roles')
		->setAttrib('class', 'global_form_popup')
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
		->setMethod('POST');
		;
		
		$listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
		
		$options = $listTbl->getListAssocByBusiness($this->_business, false);
		$this->addElement('Radio', 'role_id', array(
	      	'label' => 'Select to change role for the member',
	      	'multiOptions' => $options,
		));

		$list = $listTbl -> getListByUser($this -> _user, $this -> _business);
		if (!is_null($list))
		{
			$this -> getElement('role_id') -> setValue($list -> getIdentity());
		}
		
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