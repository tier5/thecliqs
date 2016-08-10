<?php
class Ynbusinesspages_Form_Admin_Package_Create extends Engine_Form
{
  protected $_package;
	
  public function getPackage()
  {
     return $this -> _package;
  }
  public function setPackage($package)
  {
     $this -> _package = $package;
  } 
  
  public function filterRound($value)
  {
    if( empty($value) ) {
		return '0';
    }
    return round($value, 2);
  }
  
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
    	'description' => 'YNBUSINESSPAGES_ADMIN_PACKAGE_LEVEL',
        'label' => 'Member Levels',
        'multiOptions' => $multiOptions,
        'value' => array_keys($multiOptions),
        'required' => true,
        'allowEmpty' => false,
    ));
	
	$this->addElement('Multiselect', 'category_id', array(
    	'description' => 'Select the Categories to which this Package should be available.',
        'label' => 'Categories',
        'required' => true,
        'allowEmpty' => false,
    ));
	
	$this->addElement('Float', 'price', array(
      'label' => 'Price',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'YNBUSINESSPAGES_ADMIN_PACKAGE_PRICE'
    ));
	
	$this->price -> addFilter('Callback', array(array($this, 'filterRound')));
	
	$this->addElement('Float', 'valid_amount', array(
      'label' => 'Valid Period',
      'description' => 'How long until this package expires, in days. Left empty or set 0 is never expire package.',
      'required' => false,
      'allowEmpty' => true,
      'validators' => array(
            new Engine_Validate_AtLeast(0),
        ),
    ));
	
	$this->addElement('Checkbox', 'all_module_support', array(
	  'description' => 'Modules Supported',	
      'label' => 'Select All',
    ));
	
	$this->addElement ( 
	    'multiCheckbox', 'modules', array(
	));
	
	$this->addElement('Checkbox', 'allow_select_all', array(
	  'description' => 'Features Supported',	
      'label' => 'Select All',
    ));
	
	$this->addElement('Checkbox', 'allow_owner_manage_page', array(
      'label' => 'Allow business owner to manage pages',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
      'class' => 'feature_support',
    ));
	
	$this->addElement('Checkbox', 'allow_user_join_business', array(
      'label' => 'Allow users to join Business',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
      'class' => 'feature_support',
    ));
	
	$this->addElement('Checkbox', 'allow_user_share_business', array(
      'label' => 'Allow users to share Business',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
      'class' => 'feature_support',
    ));
	
	$this->addElement('Checkbox', 'allow_user_invite_friend', array(
      'label' => 'Allow users to invite friends to Business',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
      'class' => 'feature_support',
    ));
	
	$this->addElement('Checkbox', 'allow_owner_add_contactform', array(
      'label' => 'Allow business owner to add contact form',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
      'class' => 'feature_support',
    ));
	
	$this->addElement('Checkbox', 'allow_owner_add_customfield', array(
      'label' => 'Allow business owner to add more custom fields to his Business',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
      'class' => 'feature_support',
    ));
	
	$this->addElement('Checkbox', 'allow_bussiness_multiple_admin', array(
      'label' => 'Allow business to have multiple admins',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
      'class' => 'feature_support',
    ));
	
	if(!empty($this -> _package))
	{
		$this -> addElement('dummy', 'themes', array(
				'label'     => 'Select Themes',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_themes.tpl',
						'package' =>  $this -> _package,
						'class' => 'form element',
					)
				)), 
		));  
	}
	else
	{
		$this -> addElement('dummy', 'themes', array(
				'label'     => 'Select Themes',
		        'required'  => true,
		        'allowEmpty'=> false,
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_themes.tpl',
						'class' => 'form element',
					)
				)), 
		));  
	}
	
	$this->addElement('Integer', 'max_cover', array(
			'required'  => true,
	        'allowEmpty'=> false,
            'label' => 'Maximum cover photos can be displayed',
            'description' => '5 is maximum',
            'value' => 3,
            'validators' => array(
                array('Between',true,array(1,5)),
            ),
    ));
		
	$this->addElement('Checkbox', 'show', array(
      'label' => 'Show?',
      'description' => 'Show/Hide',
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
