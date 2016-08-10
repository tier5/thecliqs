<?php

class Suggest_Form_Photo extends Engine_Form
{
  public function init()
  {
    $this
      ->setMethod('post')
      ->setAttrib('class', 'suggest_profile_photo_form')
      ->setAttrib('id', 'form-upload')
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('HtmlTag', array('tag' => 'ul'))
      ->addDecorator('FormErrors', array('placement' => 'PREPEND', 'escape' => false))
      ->addDecorator('FormMessages', array('placement' => 'PREPEND', 'escape' => false))
      ->addDecorator('Form');

    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
      ->addDecorator('FormFancyUpload')
      ->addDecorator('viewScript', array(
        'viewScript' => '_FancyUpload.tpl',
        'placement'  => '',
      ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    $this->addElement('Hidden', 'format', array(
      'value' => 'smoothbox'
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Suggest This Photo',
      'type' => 'submit',
      'order' => 100006,
      'ignore' => true,
      'decorators' => array('ViewHelper'),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'ignore' => true,
      'order' => 100007,
      'decorators' => array('ViewHelper'),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'order' => 100008,
    ));
  }
}