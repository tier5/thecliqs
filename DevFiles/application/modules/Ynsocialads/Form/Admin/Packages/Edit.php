<?php
class Ynsocialads_Form_Admin_Packages_Edit extends Ynsocialads_Form_Admin_Packages_Create
{
  public function init()
  {
  	 parent::init();
    $this->setTitle('Edit Package');
	$this->setDescription("");
    $this->submit->setLabel('Edit Package');
  }
}

