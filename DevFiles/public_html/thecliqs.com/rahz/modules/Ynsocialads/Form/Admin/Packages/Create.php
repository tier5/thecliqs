<?php
class Ynsocialads_Form_Admin_Packages_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Create Packages')
      ->setAttrib('class', 'global_form_popup')
	  ->setDescription('YNSOCIALADS_ADD_PACKAGE_DESCRIPTION')
      ;
	
    $this->addElement('Text', 'title', array(
      'label' => 'Package Title',
      'required' => true,
      'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags'
      ),
    ));
	
	$this->addElement('Float', 'price', array(
      'label' => 'Price',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'YNSOCIALADS_FORM_PACKAGE_PRICE',
    ));
   	
	$this->addElement('Float', 'benefit_amount', array(
      'label' => 'Benefit',
      'required' => true,
      'allowEmpty' => false,
    ));
	
	
	$benefit_arr = array(
		'click' => 'Clicks',
		'impression' => 'Impressions',
		'day' => 'Days',
	);
	
	$this->addElement('Select', 'benefit_type', array(
		  'multiOptions' => $benefit_arr,
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
    	'description' => 'YNSOCIALADS_FORM_PACKAGE_LEVEL',
        'label' => 'Member Levels',
        'order' => 4,
        'multiOptions' => $multiOptions,
        'value' => array_keys($multiOptions),
        'required' => true,
        'allowEmpty' => false,
    ));
	
	
	 $this->addElement('Multiselect', 'blocks', array(
        'label' => 'Ad Block Name',
        'description' => 'YNSOCIALADS_FORM_PACKAGE_BLOCK',
        'required' => true,
        'allowEmpty' => false,
    ));
	
	$arr_ad_type = array(
		'banner' => 'Banner',
		'text' => 'Text',
		'feed' => 'Feed'
	);
	$this->addElement('Multiselect', 'allowed_ad_types', array(
		'label' => 'Allowed Ad Types',
		'multiOptions' => $arr_ad_type,
		'required' => true,
        'allowEmpty' => false,
	));
	
	$this->addElement('Multiselect', 'modules', array(
		'label' => 'Allowed Modules'
	));
	
	$this->addElement('Checkbox', 'show', array(
      'label' => 'Show?',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
    ));	
		
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Package',
      'type' => 'submit',
      'ignore' => true,
      'onclick' => 'removeSubmit()',
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}

