<?php
class Ynbusinesspages_Form_Admin_Settings_Global extends Engine_Form {
    public function init() {
        $this
        ->setTitle('Global Settings')
        
        ->setDescription('YNBUSINESSPAGES_SETTINGS_GLOBAL_DESCRIPTION');
        
        $translate = Zend_Registry::get('Zend_Translate');
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
		$this->addElement('Radio', 'ynbusinesspages_compare_allpages', array(
            'label' => 'Show Compare Bar on all pages?',
            'description' => 'Compare Bar will show on all pages or just on pages of Business Module',
            'multiOptions' => array(
				1 => 'Show on all pages',
				0 =>'Just show on pages of Business Module'
			),
            'value' => $settings->getSetting('ynbusinesspages_compare_allpages', 0),
        ));
		
		$this->addElement('Radio', 'ynbusinesspages_time_format', array(
            'label' => 'Time Format',
            'description' => 'By changing the time format, the Operating Hours format when Create New Business will be affected',
            'multiOptions' => array(
				0 => '12-hour clock',
				1 => '24-hour clock'
			),
            'value' => $settings->getSetting('ynbusinesspages_time_format', 0),
        ));
		
		$this->addElement('Radio', 'ynbusinesspages_radius_unit', array(
            'label' => 'Unit for Distance',
            'description' => 'By changing this setting, this will take effects on Business Search and Location display in Business Profile',
            'multiOptions' => array(
				1 => 'Kilometer',
				0 =>'Mile'
			),
            'value' => $settings->getSetting('ynbusinesspages_radius_unit', 0),
        ));
		
        $this->addElement('Integer', 'ynbusinesspages_feature_fee', array(
            'label' => 'Fee to Feature Business',
            'description' => 'Fee to Feature Business (for 1 day)',
            'value' => $settings->getSetting('ynbusinesspages_feature_fee', 10),
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        $this->addElement('Integer', 'ynbusinesspages_claiming_expire', array(
            'label' => 'Claiming Expire',
            'description' => 'The claimed business will be expired and its status back to unclaimed if the claimer not make payment after claiming. (days)',
            'value' => $settings->getSetting('ynbusinesspages_claiming_expire', 3),
            'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
        ));
		
		$this->addElement('Integer', 'ynbusinesspages_max_comparison', array(
            'label' => 'Maximum businesses for comparison',
            'value' => $settings->getSetting('ynbusinesspages_max_comparison', 5),
            'validators' => array(
                new Engine_Validate_AtLeast(2),
            ),
        ));
		
		 $this->addElement('Text', 'ynbusinesspages_pathname',array(
		      'label'=>'Replace URL text',
		      'description' => 'Please fill in the text that you want to appear in URL instead of "business-page"',
		      'filters' => array(
		        'StringTrim'
		      ),
		     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('ynbusinesspages_pathname', 'business-page'),
    	)); 
		
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}