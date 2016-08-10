<?php
class Ynresume_Form_Admin_Industry_Create extends Engine_Form
{

  public function init()
  {
    $this->setMethod('post');
   $this->setTitle('Add Industry');
   $this->addElement('Hidden','id');
   
   $this->addElement('Text','title',array(
      'label'     => 'Industry Name',
      'required'  => true,
      'allowEmpty'=> false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}