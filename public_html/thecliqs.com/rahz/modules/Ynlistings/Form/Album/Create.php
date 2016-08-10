<?php
class Ynlistings_Form_Album_Create extends Engine_Form
{
  public function init()
  {
  	$translate = Zend_Registry::get("Zend_Translate");
    // Init form
    $this
      ->setTitle('Create New Album')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('class', 'global_form')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
     ;
    $this->addElement('Hidden','album_id');
    $this->addElement('Text','title',array(
    'label'=> 'Title',
    'allowEmpty' => false,
    'required' => true,
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
      'label' => 'Create Album',
      'class' => 'btnlisting-create-album',
      'type' => 'submit',
        'decorators' => array(
        'ViewHelper'
      )
    ));

     $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onClick' =>'history.go(-1)',
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}