<?php
class Ynjobposting_Form_Admin_Package_Create extends Engine_Form
{
  public function init()
  {
	 
    $id = Engine_Api::_()->user()->getViewer() -> level_id;

    $this->setTitle('Add New Package');
	$this->setAttrib('class', 'global_form_popup');
	
	$this->addElement('Text', 'title', array(
      'label' => 'Package Name',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	
	$this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'cols' => '50',
      'rows' => '4',
      'maxlength' => '100',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
	
	// Element: levels
    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
    $multiOptions = array();
    foreach ($levels as $level) {
        $multiOptions[$level->getIdentity()] = $level->getTitle();
    }
    reset($multiOptions);
    $this->addElement('Multiselect', 'levels', array(
    	'description' => 'YNJOBPOSTING_ADMIN_PACKAGE_LEVEL',
        'label' => 'Member Levels',
        'multiOptions' => $multiOptions,
        'value' => array_keys($multiOptions),
        'required' => true,
        'allowEmpty' => false,
    ));
	
	$this->addElement('Float', 'price', array(
      'label' => 'Price',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'YNJOBPOSTING_ADMIN_PACKAGE_PRICE'
    ));
	
	$this->addElement('Float', 'valid_amount', array(
      'label' => 'Valid Period',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'by days',
      'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
    ));
	 $this->valid_amount->getDecorator("Description")->setOption("placement", "append");
	
	$this->addElement('Checkbox', 'show', array(
      'label' => 'Show?',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
    ));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add',
      'onclick' => 'removeSubmit()',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
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
