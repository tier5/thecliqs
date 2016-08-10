<?php
class Ynsocialads_Form_Admin_Module_Edit extends Ynsocialads_Form_Admin_Module_Create
{
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Module');
	$this->setDescription("");
    $this->submit->setLabel('Edit');
  }
}