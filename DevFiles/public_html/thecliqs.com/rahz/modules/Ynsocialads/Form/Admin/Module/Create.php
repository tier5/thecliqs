<?php
class Ynsocialads_Form_Admin_Module_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Create Module')
      ->setAttrib('class', 'global_form_popup')
	  ->setDescription('YNSOCIALADS_ADD_MODULE_DESCRIPTION')
      ;
	
    $this->addElement('Text', 'module_name', array(
      'label' => 'Module Name',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
	
	$this->addElement('Text', 'module_title', array(
      'label' => 'Module Title',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
	
    $this->addElement('Text', 'table_item', array(
      'label' => 'Database Table Item',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
    
	 $this->addElement('Text', 'owner_field', array(
      'label' => 'Content Owner Field in Table',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
	
	 $this->addElement('Text', 'title_field', array(
      'label' => 'Content Title Field in Table',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
	
	 $this->addElement('Text', 'title_field', array(
      'label' => 'Content Title Field in Table',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
	
	$this->addElement('Text', 'body_field', array(
      'label' => 'Content Body/Description Field in Table',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Create New Module',
      'type' => 'submit',
      'ignore' => true,
      'onclick' => 'removeSubmit()',
      'decorators' => array('ViewHelper')
    ));

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
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}

