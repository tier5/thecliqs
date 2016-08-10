<?php
class Ynjobposting_Form_Admin_Settings_Global extends Engine_Form {
    public function init() {
        $this
        ->setTitle('Global Settings')
        
        ->setDescription('YNJOBPOSTING_SETTINGS_GLOBAL_DESCRIPTION');
        
        $translate = Zend_Registry::get('Zend_Translate');
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $this->addElement('Integer', 'ynjobposting_fee_featurejob', array(
            'label' => 'Fee to Feature Job',
            'description' => 'Fee to Feature Job (for 1 day)',
            'value' => $settings->getSetting('ynjobposting_fee_featurejob', 10),
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        $this->addElement('Integer', 'ynjobposting_fee_sponsorcompany', array(
            'label' => 'Fee to Sponsor Company',
            'description' => 'Fee to Sponsor Company (for 1 day)',
            'value' => $settings->getSetting('ynjobposting_fee_sponsorcompany', 10),
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        $this->addElement('Integer', 'ynjobposting_max_uploadsize', array(
            'label' => 'Maximum Upload Size',
            'description' => 'Maximum upload size for resume (KB)',
            'value' => $settings->getSetting('ynjobposting_max_uploadsize', 500),
            'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
        ));
		
		$this->addElement('Integer', 'ynjobposting_max_alertemail', array(
            'label' => 'Maximum Alert Email',
            'description' => 'Maximum alert emails that one IP can use to get jos (Enter a number between 1 and 5)',
            'value' => $settings->getSetting('ynjobposting_max_alertemail', 1),
            'validators' => array(
                array('Between',true,array(1,5)),
            ),
        ));
		
		$this->addElement('Integer', 'ynjobposting_max_getalertperemail', array(
            'label' => 'Maximum Alerts For One Email',
            'description' => 'Maximum job alerts that one email can use to get jos (Enter a number between 1 and 5)',
            'value' => $settings->getSetting('ynjobposting_max_getalertperemail', 1),
            'validators' => array(
                array('Between',true,array(1,5)),
            ),
        ));
        
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}