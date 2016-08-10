<?php
class Ynjobposting_Form_Jobs_Search extends Engine_Form
{
	
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
	$url = $view -> url(array('controller' => 'jobs', 'action' => 'listing'), 'ynjobposting_extended', true);
	$this
      ->setAttribs(array( 'id' => 'filter_form',
                          'class' => 'global_form_box',
                           'method' => 'GET',
                           'action' => $url
                    ));
					
	$this -> addElement('Text', 'job_title', array(
			'label' => 'Keyword',
			'placeholder' => $view->translate('Search Jobs..'),
	));
	
	$this -> addElement('Text', 'company_name', array(
			'label' => 'Company',
	));
	
	$arr_industry_id = array('all' => 'All');
	//Industry
	 $this->addElement('Select', 'industry_id', array(
      'label' => 'Industry',
      'multiOptions' => $arr_industry_id,
    ));
	
	// select level
    $this -> addElement('Select', 'level', array(
        'label' => 'Job Level',
        'multiOptions' => array(
        	'all' => 'All',
        )
    ));
 	$tableLevel = Engine_Api::_() -> getDbTable('joblevels', 'ynjobposting');
	$this -> level -> addMultiOptions($tableLevel -> getJobLevelArray());
	
	
    // select type
    $this -> addElement('Select', 'type', array(
        'label' => 'Job Type',
        'multiOptions' => array(
        	'all' => 'All',
        )
    ));
	$tableType = Engine_Api::_() -> getDbTable('jobtypes', 'ynjobposting');
	$this -> type -> addMultiOptions($tableType -> getJobTypeArray());
	
    // salary
    $this->addElement('Float', 'salary_from', array(
        'label' => 'Minimum Salary',
        'value' => 0,
        'validators' => array(
            new Engine_Validate_AtLeast(0),
        ),
    ));
      
    $this->addElement('Select', 'salary_currency', array(
        'value' => 'USD',
    ));
    
    // Expired time
    $expired = new Ynjobposting_Form_YnCalendarSimple('expire_before');
    $expired -> setLabel("Expire before");
    $expired -> setAllowEmpty(true);
    $this -> addElement($expired);
          
	//Adress map
	$this -> addElement('Dummy', 'location_map', array(
		'decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_location_search.tpl',
				'label' => 'Location',
				'alert' => 1,
			)
		)), 
	));
	
	$this -> addElement('Text', 'within', array(
			'label' => 'Radius (mile)',
			'placeholder' => $view->translate('Radius (mile)..'),
			'maxlength' => '60',
			'value' => 50,
	));
		
	$this -> addElement('hidden', 'location_address', array(
		'value' => '0',
		'order' => '97'
	));

	$this -> addElement('hidden', 'lat', array(
		'value' => '0',
		'order' => '98'
	));
	
	$this -> addElement('hidden', 'long', array(
		'value' => '0',
		'order' => '99'
	));
	
	$arr_status = array(
		'all' => 'All',
		'published' => 'Published',
		'expired' => 'Expired',
	);
	
	//Industry
	 $this->addElement('Select', 'status', array(
        'label' => 'Job Status',
        'multiOptions' => $arr_status,
    ));
	
    //Industry
     $this->addElement('Select', 'order', array(
        'label' => 'Browse by',
        'multiOptions' => array (
            'job.title' => 'A - Z',
            'newest' => 'Newest',
            'oldest' => 'Oldest'
        )
    ));
    
    // Buttons
    $this->addElement('Button', 'submit_button', array(
	      'value' => 'submit_button',
	      'label' => 'Search',
	      'type' => 'submit',
	      'ignore' => true,
    ));
	
  }
}
