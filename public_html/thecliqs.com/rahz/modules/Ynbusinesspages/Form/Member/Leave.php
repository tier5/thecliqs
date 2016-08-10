<?php
class Ynbusinesspages_Form_Member_Leave extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Leave Business')
      ->setDescription('Are you sure you want to leave this business?')
      ->setMethod('POST')
      ->setAction($_SERVER['REQUEST_URI'])
      ;

    $this->addElement('Button', 'submit', array(
      'label' => 'Leave Business',
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