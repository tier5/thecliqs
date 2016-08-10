<?php

class Yncontest_Form_Photo_Edit extends Engine_Form
{
  protected $_isArray = true;

  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements');

    $this->addElement('Text', 'label', array(
      'label' => 'Title',
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
      'decorators' => array(
	'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class'=>'yncontest_editphotos_title_input')),
        array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'yncontest_editphotos_title')),
      ),
    ));
      $this->addElement('Text', 'title', array(
      'label' => 'Image Title',
      'allowEmpty' => false,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class'=>'yncontest_editphotos_caption_label')),
        array('HtmlTag', array('tag' => 'div', 'class' => 'yncontest_editphotos_caption_input')),
      ),
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
    )));
    $this->addElement('Text', 'description', array(
      'label' => 'Image Description',
      
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class'=>'yncontest_editphotos_caption_input')),
        array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class'=>'yncontest_editphotos_caption_label')),
      ),
    ));

    $this->addElement('Checkbox', 'delete', array(
      'label' => "Delete Photo",
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