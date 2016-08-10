<?php
class Ynresume_Form_Admin_Settings_Global extends Engine_Form {
    public function init() {
        $this
            ->setTitle('Global Settings')
            ->setDescription('YNRESUME_SETTINGS_GLOBAL_DESCRIPTION');
        
        $translate = Zend_Registry::get('Zend_Translate');
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $this->addElement('Integer', 'ynresume_fee_feature', array(
            'label' => 'Fee to feature resume',
            'description' => 'USD for 1 day', 
            'required' =>true,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => $settings->getSetting('ynresume_fee_feature', 0),
        ));
        
        $this->addElement('Integer', 'ynresume_fee_service', array(
            'label' => 'Fee to use "Who Viewed Me" service',
            'description' => 'USD for 1 day',
            'required' =>true,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => $settings->getSetting('ynresume_fee_service', 0),
        ));
		
		$this->addElement('Text', 'ynresume_addthis_pubid', array(
            'label' => 'AddThis - Profile ID',
            'required' =>true,
            'value' => $settings->getSetting('ynresume_addthis_pubid', 'younet'),
        ));
        
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}