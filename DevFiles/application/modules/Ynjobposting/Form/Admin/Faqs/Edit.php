<?php
class Ynjobposting_Form_Admin_Faqs_Edit extends Ynjobposting_Form_Admin_Faqs_Create
{
  public function init()
  {
     parent::init();
    $this->setTitle('Edit FAQ');
    $this->setDescription('YNJOBPOSTING_FAQS_EDIT_DESCRIPTION');
    $this->submit->setLabel('Edit FAQ');
  }
}