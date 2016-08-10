<?php
class Ynmusic_Form_Admin_Artist_Edit extends Ynmusic_Form_Admin_Artist_Create
{
	public function init()
	{
		parent::init();
		$this->setTitle('Edit Artist');
		$this->submit_btn->setLabel('Save Changes');
	}
}