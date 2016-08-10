<?php
class Yncontest_Form_Member_Leave extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Leave This Contest')
      ->setDescription('Are you sure you want to leave this contest?');

    //$this->addElement('Hash', 'token');

    $this->addElement('Button', 'submit', array(
      'label' => 'Leave Contest',
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

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))->setMethod('POST');
  }
}