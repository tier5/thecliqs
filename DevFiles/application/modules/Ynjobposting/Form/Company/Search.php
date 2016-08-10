<?php
class Ynjobposting_Form_Company_Search extends Engine_Form
{
  public function init()
  {
	  	$translate = Zend_Registry::get("Zend_Translate");
	     //Set Form Layout And Attributes.
	    $this -> setAttribs(array( 
	    	'id' => 'filter_form',
			'class' => 'global_form_box',
			'method' => 'GET'
		));

	    // Search Text Field.
	    $this->addElement('Text', 'keyword', array(
			'label' => 'Keyword',
	    ));
    
      	// Industry 
      	$industries = Engine_Api::_() -> getItemTable('ynjobposting_industry') -> getIndustriesAssoc();
	    if(count($industries) >= 1 ) {
		      $this->addElement('Select', 'industry_id', array(
		        'label' => 'Industry',
		        'multiOptions' => $industries,
		      ));
	    }
	    
		$location = Zend_Controller_Front::getInstance()->getRequest()->getParam('location', '');
		$this -> addElement('Text', 'location', array(
			'label' => 'Location',
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_location_search.tpl',
					'class' => 'form element',
					'location' => $location
				)
			)), 
		));
		
		$this -> addElement('Text', 'within', array(
			'label' => 'Radius (mile)',
			'placeholder' => $translate->translate('Radius (mile)..'),
			'maxlength' => '60',
			'required' => false,
			'style' => "display: block",
			'validators' => array(
				array(
					'Int',
					true
				),
				new Engine_Validate_AtLeast(0),
			),
			'value' => 50,
		));

		//Search Text Field.
	    $this->addElement('Text', 'size', array(
			'label' => 'Company size',
	    	'validators' => array(
				array(
					'Int',
					true
				),
				new Engine_Validate_AtLeast(0),
			),
	    ));
		
		$this -> addElement('hidden', 'lat', array(
			'value' => '0',
			'order' => '98'
		));
		
		$this -> addElement('hidden', 'long', array(
			'value' => '0',
			'order' => '99'
		));

		$this -> addElement('Button', 'Search', array(
			'label' => 'Search',
			'type' => 'submit',
		));
  }
}