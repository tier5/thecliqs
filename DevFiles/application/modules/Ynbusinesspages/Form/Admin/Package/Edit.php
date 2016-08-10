<?php
class Ynbusinesspages_Form_Admin_Package_Edit extends Ynbusinesspages_Form_Admin_Package_Create
{
	public function init()
	{
		parent::init();
		$this->setTitle('Edit Package');
		$this->submit->setLabel('Save Changes');
	}
}