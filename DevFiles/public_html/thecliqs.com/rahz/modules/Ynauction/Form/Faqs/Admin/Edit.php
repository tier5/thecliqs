<?php

class Ynauction_Form_Faqs_Admin_Edit extends Ynauction_Form_Faqs_Admin_Create{
	
	public function init(){
		parent::init();
		$this -> setTitle('Edit FAQ') -> setDescription('');
	}
}
