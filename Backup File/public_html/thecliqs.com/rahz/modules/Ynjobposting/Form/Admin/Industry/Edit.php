<?php
class Ynjobposting_Form_Admin_Industry_Edit extends Ynjobposting_Form_Admin_Industry_Create
{
	public function init()
	{
		parent::init();
		$this->setTitle('Edit Industry');
		$this->submit->setLabel('Save Changes');
	}
}
