<?php
class Ynlistings_Form_Post_Report extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Report Post')
      ->setDescription('Report wrong post in a topic to admin')
      ->setMethod('POST')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;
    
    $this->addElement('Textarea', 'body', array(
      'required' => true,
      //'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_Censor(),
      ),
    ));

    // Buttons
    $buttons = array();

    $translate = Zend_Registry::get('Zend_Translate');

    $this->addElement('Button', 'submit', array(
      'label' => 'Report',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));
    $buttons[] = 'submit';

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
    $buttons[] = 'cancel';


    $this->addDisplayGroup($buttons, 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

  }
}