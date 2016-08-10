<?php
class Ynbusinesspages_Form_Post_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Edit Post')->setAttrib('id', 'ynbusinesspages_post_edit');

    $this->addElement('textarea', 'body', array(
      'filters' => array(
        new Engine_Filter_Censor(),
      )
    ));

    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'label' => 'Edit Post',
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