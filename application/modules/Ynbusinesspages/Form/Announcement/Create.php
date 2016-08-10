<?php
class Ynbusinesspages_Form_Announcement_Create extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Post New Announcement')
      ->setDescription('Please compose your new announcement below.')
      ->setAttrib('id', 'ynbusinesspages_announcements_create');

    // Add title
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'required' => true,
      'allowEmpty' => false,
    ));

    $this->addElement('Textarea', 'body', array(
      'label' => 'Body',
      'required' => true,
      'allowEmpty' => false,
    ));

        // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Post Announcement',
      'type' => 'submit',
      'onclick' => 'removeSubmit()',
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