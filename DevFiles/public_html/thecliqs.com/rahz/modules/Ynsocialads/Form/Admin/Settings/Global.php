<?php

//TODO

//+ define default params

//form for show global settings

class Ynsocialads_Form_Admin_Settings_Global extends Engine_Form {
    public function init() {
        $this
        ->setTitle('YNSOCIALADS_FORM_ADMIN_SETTINGS_GLOBAL_TITLE')
        //TODO Put global settings description here
        ->setDescription('YNSOCIALADS_FORM_ADMIN_SETTINGS_GLOBAL_DESCRIPTION');
        
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $this->addElement('Integer', 'no_ads_shown', array(
            'label' => 'YNSOCIALADS_FORM_ADMIN_SETTINGS_GLOBAL_NOADSSHOWN_LABEL',
            'description' => 'YNSOCIALADS_FORM_ADMIN_SETTINGS_GLOBAL_NOADSSHOWN_DESCRIPTION',
            'value' => $settings->getSetting('ynsocialads_noadsshown', 3),
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        $this->addElement('Integer', 'pos_feed_ads', array(
            'label' => 'YNSOCIALADS_FORM_ADMIN_SETTINGS_GLOBAL_POSFEEDADS_LABEL',
            'description' => 'YNSOCIALADS_FORM_ADMIN_SETTINGS_GLOBAL_POSFEEDADS_DESCRIPTION',
            'value' => $settings->getSetting('ynsocialads_posfeedads', 0),
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        $this->addElement('Integer', 'pay_later_expire_time', array(
            'label' => 'YNSOCIALADS_FORM_ADMIN_SETTINGS_GLOBAL_PAYATEREXPIRETIME_LABEL',
            'description' => 'YNSOCIALADS_FORM_ADMIN_SETTINGS_GLOBAL_PAYATEREXPIRETIME_DESCRIPTION',
            'value' => $settings->getSetting('ynsocialads_paylaterexpiretime', 5),
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}