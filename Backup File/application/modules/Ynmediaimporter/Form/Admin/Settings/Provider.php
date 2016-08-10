<?php

class Ynmediaimporter_Form_Admin_Settings_Provider extends Engine_Form
{
    public function init()
    {

        $this -> setTitle('Providers Settings') -> setDescription('Setup providers for all members of your community.');
        
        $this->loadDefaultDecorators();
        

        $this -> addElement('Radio', 'ynmediaimporter_facebook_enable', array(
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynmediaimporter.facebook.enable', 1),
            'label'=>'Enable Facebook',
            'description'=>'',
            'multiOptions' => array(
                0 => 'No',
                1 => 'Yes'
            )
        ));
        
        
        $this -> addElement('Radio', 'ynmediaimporter_flickr_enable', array(
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynmediaimporter.facebook.enable', 1),
            'label'=>'Enable Flickr',
            'description'=>'YNMEDIAIMPORTER_ADMIN_SETTINGS_FLICKR_DESCRIPTION',
            'multiOptions' => array(
                0 => 'No',
                1 => 'Yes'
            )
        ));
        
        $this->getElement('ynmediaimporter_flickr_enable')->getDecorator('Description')->setOption('escape', false);

        $this -> addElement('Radio', 'ynmediaimporter_picasa_enable', array(
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynmediaimporter.picasa.enable', 1),
            'label'=>'Enable Picasa',
            'description'=>'YNMEDIAIMPORTER_ADMIN_SETTINGS_PICASA_DESCRIPTION',
            'multiOptions' => array(
                0 => 'No',
                1 => 'Yes'
            )
        ));
        
        $this->getElement('ynmediaimporter_picasa_enable')->getDecorator('Description')->setOption('escape', false);
        
        
        $this -> addElement('Radio', 'ynmediaimporter_instagram_enable', array(
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynmediaimporter.instagram.enable', 1),
            'label'=>'Enable Instagram',
            'description'=>'YNMEDIAIMPORTER_ADMIN_SETTINGS_INSTAGRAM_DESCRIPTION',
            'multiOptions' => array(
                0 => 'No',
                1 => 'Yes'
            )
        ));
        
        $this->getElement('ynmediaimporter_instagram_enable')->getDecorator('Description')->setOption('escape', false);

        /*
        $this -> addElement('Radio', 'ynmediaimporter_yfrog_enable', array(
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynmediaimporter.yfrog.enable', 1),
            'label'=>'Enable YFrog',
            'description'=>'YNMEDIAIMPORTER_ADMIN_SETTINGS_YFROG_DESCRIPTION',
            'multiOptions' => array(
                0 => 'No',
                1 => 'Yes'
            )
        ));
        
        $this->getElement('ynmediaimporter_yfrog_enable')->getDecorator('Description')->setOption('escape', false);
         */

        
        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
