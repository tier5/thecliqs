<?php

class Yncontest_Form_Admin_Category_Edit extends Yncontest_Form_Admin_Category_Create{
	public function init(){
		parent::init();
		$this->setTitle("Edit Category");
	}
}
