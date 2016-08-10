<?php
class Ynlistings_Form_Album_Delete extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Delete Album')
      ->setDescription('Are you sure you want to delete this album?')
      ;

    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Delete Album',
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}