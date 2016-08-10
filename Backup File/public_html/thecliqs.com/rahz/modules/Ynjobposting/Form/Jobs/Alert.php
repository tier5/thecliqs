<?php
class Ynjobposting_Form_Jobs_Alert extends Engine_Form
{
	
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
    $this->setDescription('Register for Job Alert to receive suitable jobs via email based on your criteria.');
	$this
      ->setAttribs(array( 'id' => 'filter_form_job_alert',
                          'class' => 'global_form_box',
                           'method' => 'GET'
                    ));
	//Industry
	 $this->addElement('Select', 'industry_id_alert', array(
	  'required'  => true,
      'allowEmpty'=> false,
      'label' => '*Industry',
      'multiOptions' => array(
	  	'all' => 'All',
	  ),
    ));
	
	// select level
    $this -> addElement('Select', 'level_alert', array(
        'label' => 'Job Level',
        'multiOptions' => array(
        	'all' => 'All',
        )
    ));
    $tableLevel = Engine_Api::_() -> getDbTable('joblevels', 'ynjobposting');
	$this -> level_alert -> addMultiOptions($tableLevel -> getJobLevelArray());
	
    // select type
    $this -> addElement('Select', 'type_alert', array(
        'label' => 'Job Type',
        'multiOptions' => array(
        	'all' => 'All',
        )
    ));
	$tableType = Engine_Api::_() -> getDbTable('jobtypes', 'ynjobposting');
	$this -> type_alert -> addMultiOptions($tableType -> getJobTypeArray());
	
	//Adress map
	$this -> addElement('Dummy', 'location_map', array(
		'decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_location_search_alert.tpl',
				'class' => 'form element',
				'label' => 'Location',
				'alert' => 1,
			)
		)), 
	));
	
	$this -> addElement('Text', 'within_alert', array(
			'label' => 'Radius (mile)',
			'placeholder' => $view->translate('Radius (mile)..'),
			'maxlength' => '60',
	));
		
	$this -> addElement('hidden', 'location_address_alert', array(
		'value' => '0',
		'order' => '97'
	));

	$this -> addElement('hidden', 'lat_alert', array(
		'value' => '0',
		'order' => '98'
	));
	
	$this -> addElement('hidden', 'long_alert', array(
		'value' => '0',
		'order' => '99'
	));
	
	   // salary
        $this->addElement('Float', 'salary_alert', array(
            'label' => 'Minimum Salary',
            'value' => 0,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
	  
	  $this->addElement('Select', 'salary_currency_alert', array(
            'label' => 'Currency',
            'value' => 'USD',
	  ));
			
	 $this->addElement('Text', 'alertemail', array(
        'label' => 'Email Address',
        'allowEmpty' => false,
     	'required' => true,
     	'validators' => array(
	        array('NotEmpty', true),
	        array('StringLength', false, array(1, 64)),
		),
    ));
	
    // Buttons
    $this->addElement('Button', 'submit_button', array(
	      'value' => 'submit_button',
	      'label' => 'Get Job Now !',
	      'type' => 'button',
	      'onClick' => 'checkValidate()',
	      'ignore' => true,
    ));
	
  }
}
