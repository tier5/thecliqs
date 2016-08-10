<?php

class Ynfbpp_Form_Admin_Settings_Global extends Engine_Form
{
    public function init()
    {

        $this -> setTitle('Global Settings') -> setDescription('These settings affect all members in your community.');

        $this -> addElement('Text', 'ynfbpp_time_open', array(
            'label' => 'Opening Delay Time',
            'description' => 'Timeout before popup open in milisecond (100-1000)',
            'required'=>true,
            'validators'=>array('Int',array('Between',false,array(100,1000))),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.time.open', 300),
        ));
        $this -> addElement('Text', 'ynfbpp_time_close', array(
            'label' => 'Closing Delay Time',
            'description' => 'Timeout before popup close in milisecond (100-1000)',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.time.close', 300),
            'required'=>true,
            'validators'=>array('Int',array('Between',false,array(100,1000))),
        ));
        
        $this->addElement('Text','ynfbpp_ignore_classes',array(
            'label'=>'Ignore CSS class',
            'description'=>'Disable popup when mouse within element has following CSS class, separate by comma.',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.ignore.classes', ''),
        ));
        
         $this->addElement('Text','ynfbpp_max_height',array(
            'label'=>'Max height of popup',
            'description'=>'Max height of popup content (pixel)',
            'required'=>true,
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.max.height', 150),
            'validators'=>array('Int')
        ));
        
        
        $this->addElement('Radio','ynfbpp_enable_thumb',array(
            'label'=>'Enable Thumbnails',
            'description'=>'Enable popup when mouseover on thumbnail',
            'multiOptions'=>array(
                1=>'Yes',
                0=>'No'
            ),
            'required'=>true,
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.enable.thumb', 1),
        ));
        
        $this->addElement('Radio','ynfbpp_enabled_admin',array(
            'label'=>'Enable Back End',
            'description'=>'Enable profile popup at admin control panel',
            'multiOptions'=>array(
                '1'=>'Yes',
                'O'=>'No'
            ),
            'required'=>true,
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.enabled.admin', 0),
        ));
        
       

        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
