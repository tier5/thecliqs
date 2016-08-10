<?php
class Ynbusinesspages_Form_Member_Join extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Join Business')
      ->setDescription('Would you like to join this business?')
      ->setMethod('POST')
      ->setAction($_SERVER['REQUEST_URI'])
      ;

    $this->addElement('Button', 'submit', array(
      'label' => 'Join Business',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons');
  }
}