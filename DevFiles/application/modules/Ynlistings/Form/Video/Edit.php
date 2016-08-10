<?php

class Ynlistings_Form_Video_Edit extends Engine_Form
{
  protected $_isArray = true;

  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements');

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
      'decorators' => array(
	'ViewHelper',
        array('HtmlTag', array('tag' => 'div')),
        array('Label', array('tag' => 'div', 'placement' => 'PREPEND')),
      ),
    ));
      $this->addElement('Text', 'title', array(
      'label' => 'Video Title',
      'allowEmpty' => false,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'div', 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      ),
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
    )));
    $this->addElement('Text', 'description', array(
      'label' => 'Video Description',
      'style' => 'width:400px',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div')),
        array('Label', array('tag' => 'div', 'placement' => 'PREPEND')),
      ),
    ));

    $this->addElement('Checkbox', 'delete', array(
      'label' => "Delete Video",
      'decorators' => array(
        'ViewHelper',
        array('Label', array('placement' => 'APPEND')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'photo-delete-wrapper')),
      ),
    ));


    $this->addElement('Hidden', 'photo_id', array(
      'validators' => array(
        'Int',
      )
    ));
  }
}