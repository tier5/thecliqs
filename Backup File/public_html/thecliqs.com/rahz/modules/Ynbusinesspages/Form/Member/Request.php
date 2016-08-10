<?php 
class Ynbusinesspages_Form_Member_Request extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Request Business Membership')
      ->setDescription('Would you like to request membership in this Business?')
      ->setMethod('POST')
      ->setAction($_SERVER['REQUEST_URI'])
      ;

    $this->addElement('Button', 'submit', array(
      'label' => 'Send Request',
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