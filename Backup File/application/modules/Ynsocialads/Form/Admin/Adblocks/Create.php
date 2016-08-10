<?php
class Ynsocialads_Form_Admin_Adblocks_Create extends Engine_Form
{
  public function init(){
             
    $this
      ->setTitle('Create Ads Block')
      ;
    $this->setAttribs(array(
            'method'=>'POST',
            'id'=>'create_adblock',
            'onsubmit' => 'submitFilter(1)'
        ));
      
    $this->setDescription('YNSOCIALADS_FORM_ADMIN_ADBLOCKS_CREATE_DESCRIPTION');
    
    $this->addElement('Select', 'page_id', array(
      'label' => 'Site Page',
      'description' => 'YNSOCIALADS_FORM_ADMIN_ADBLOCKS_CREATE_SITEPAGE_DESCRIPTION',
      'value' => 3,
      'onchange' => 'submitFilter(0)',
    ));
	
	$this->addElement('Image', 'page_layout', array(
      'label' => 'Page Layout'
    ));
   	
	$this->addElement('Select', 'placement', array(
      'label' => 'Ad Block Placement',
      'onchange' => 'changePlacement(this)',
    ));
	
    $this->addElement('Image', 'widget_preview', array(
      'label' => 'Widget Preview'
    ));
	
    $this->addElement('Text', 'title', array(
		'label' => 'Ad Block Name',
		'description' => 'YNSOCIALADS_FORM_ADMIN_ADBLOCKS_CREATE_NAME_DESCRIPTION',
		'required' => true,
        'allowEmpty' => false,
        'filters' => array(
            'StripTags'
        )
    ));
    
    $this->addElement('Integer', 'ads_limit', array(
		'label' => 'Ads Limit',
		'description' => 'YNSOCIALADS_FORM_ADMIN_ADBLOCKS_CREATE_ADSLIMIT_DESCRIPTION',
		'required' => true,
		'validators' => array(
		new Engine_Validate_AtLeast(0),
		),
    ));
    
    $this->addElement('Radio', 'ajax', array(
      'label' => 'Ajax Based Display',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => 0
    ));
    
    $this->addElement('Hidden', 'enable', array(
      'value' => 1
    ));
		
    // Buttons
    $this->addElement('Button', 'submitBtn', array(
      'label' => 'Save Change',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}

