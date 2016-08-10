<?php

class Ynbusinesspages_Form_Announcement_Delete extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Delete Announcement')
      ->setDescription('Are you sure you want to delete this announcement?');

    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Announcement',
      'type' => 'submit',
    ));

    $this->addElement('Button', 'cancel', array(
      'label' => 'cancel',
      'onclick' => 'window.location.href="'.Zend_Controller_Front::getInstance()->assemble().'";'
    ));
  }
}