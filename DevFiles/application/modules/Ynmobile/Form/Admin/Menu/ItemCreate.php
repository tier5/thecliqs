<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: ItemCreate.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Form_Admin_Menu_ItemCreate extends Engine_Form
{
	public function init()
	{
		$this -> setTitle('Create Menu Item') -> setAttrib('class', 'global_form_popup');

		$this -> addElement('Text', 'label', array(
			'label' => 'Label',
			'required' => true,
			'allowEmpty' => false,
		));

		$this -> addElement('Text', 'name', array(
			'label' => 'Name',
			'required' => true,
			'allowEmpty' => false,
		));
		/*
		$this -> name -> addValidator('Db_NoRecordExists', TRUE, array(
			'table' => Engine_Db_Table::GetTablePrefix() . 'ynmobile_menuitems',
			'field' => 'name'
		));
		//$this -> name -> getValidator('Db_NoRecordExists') -> setMessage('Someone has already added this name.', 'recordFound');
		*/
		$this -> addElement('Text', 'layout', array(
			'label' => 'Layout',
			'required' => true,
			'allowEmpty' => false,
		));

		$this -> addElement('Text', 'uri', array(
			'label' => 'URL',
			'required' => true,
			'allowEmpty' => false,
			'style' => 'width: 500px',
		));

		$this -> addElement('Text', 'icon', array(
			'label' => 'Icon',
		));

		$this -> addElement('Checkbox', 'enabled', array(
			'label' => 'Enabled?',
			'checkedValue' => '1',
			'uncheckedValue' => '0',
			'value' => '1',
		));

		// Buttons
		$this -> addElement('Button', 'submit', array(
			'label' => 'Create Menu Item',
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));

		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'href' => '',
			'onclick' => 'parent.Smoothbox.close();',
			'decorators' => array('ViewHelper')
		));

		$this -> addDisplayGroup(array(
			'submit',
			'cancel'
		), 'buttons');
	}

}
