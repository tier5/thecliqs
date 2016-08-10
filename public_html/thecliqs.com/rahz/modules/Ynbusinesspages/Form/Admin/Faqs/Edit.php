<?php
class Ynbusinesspages_Form_Admin_Faqs_Edit extends Ynbusinesspages_Form_Admin_Faqs_Create
{
  public function init()
  {
     parent::init();
    $this->setTitle('Edit FAQ');
    $this->setDescription('YNBUSINESSPAGES_FAQS_EDIT_DESCRIPTION');
    $this->submit_btn->setLabel('Edit FAQ');
  }
}