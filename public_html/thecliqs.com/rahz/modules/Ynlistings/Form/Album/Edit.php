<?php
class Ynlistings_Form_Album_Edit extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Edit Album')
      ->setAttrib('id', 'form-upload')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
     ;
    $this->addElement('Hidden','album_id');
    $this->addElement('Text','title',array(
    'label'=> 'Title',
    'allowEmpty' => false,
    'required' => true,
    'style' => 'margin: 5px 0 10px;',
    'validators' => array(
       array('NotEmpty', true),
       array('StringLength', false, array(1, 64)),
      ),
    'filters' => array(
       'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
     $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'style' => 'margin: 5px 0 10px;',
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_StringLength(array('max' => 10000)),
      ),
    ));

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Edit Album',
      'type' => 'submit',
        'decorators' => array(
        'ViewHelper'
      )
    ));

     $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onClick' =>'parent.Smoothbox.close();',
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}