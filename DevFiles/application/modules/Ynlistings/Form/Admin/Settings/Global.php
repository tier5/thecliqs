<?php
class Ynlistings_Form_Admin_Settings_Global extends Engine_Form {
    public function init() {
        $this
        ->setTitle('Global Settings')
        
        ->setDescription('YNLISTINGS_GLOBAL_SETTINGS_DESCRIPTION');
        
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $this->addElement('Integer', 'max_listings', array(
            'label' => 'Maximum listings can be imported each time',
            'value' => $settings->getSetting('ynlistings_max_listings', 100),
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        $this->addElement('Integer', 'new_days', array(
            'label' => 'Mark a listing as "New Listing" within ? day(s) after getting approval.',
            'value' => $settings->getSetting('ynlistings_new_days', 3),
            'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
        ));
        
        $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}