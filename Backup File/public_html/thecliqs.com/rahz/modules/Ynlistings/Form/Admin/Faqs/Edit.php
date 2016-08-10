<?php
class Ynlistings_Form_Admin_Faqs_Edit extends Ynlistings_Form_Admin_Faqs_Create
{
  public function init()
  {
     parent::init();
    $this->setTitle('Edit FAQ');
    $this->setDescription('YNLISTINGS_FAQS_EDIT_DESCRIPTION');
    $this->submit->setLabel('Edit FAQ');
  }
}