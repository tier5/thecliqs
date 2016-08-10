<?php

class Ynauction_Form_HelpPage_Admin_Edit extends Ynauction_Form_HelpPage_Admin_Create{
	
	public function init(){
		parent::init();
		$this -> setTitle('Edit Help Page') -> setDescription('');
	}
}
