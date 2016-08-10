<?php
class Ynbusinesspages_Form_Announcement_Edit extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Edit Announcement')
      ->setDescription('Please modifiy your announcement below.');

    // Add title
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'required' => true,
      'allowEmpty' => false,
    ));

     $this->addElement('TinyMce', 'body', array(
      'label' => 'Body',
      'required' => true,
      'allowEmpty' => false,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Edit Announcement',
      'onclick' => 'removeSubmit()',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'ignore' => true,
      'link' => true,
      'href' => '',
      'onclick' => 'javascript:history.go(-1);',
      'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}