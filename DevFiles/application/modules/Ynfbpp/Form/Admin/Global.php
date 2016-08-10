<?php

class Ynfbpp_Form_Admin_Global extends Engine_Form
{
    public function init()
    {

        $this -> setTitle('Global Settings') -> setDescription('These settings affect all members in your community.');
        
        $this->addElement('Radio','ynfbpp_showstatus',array(
            'label'=>'Show User Status',
            'description'=>'Show user status under profile link',
            'multiOptions'=>array('1'=>'Yes','0'=>'No'),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.showstatus', 1),
        ));
        
        $this->addElement('Radio','ynfbpp_showfriendcount',array(
            'label'=>'Show Total Friends of user',
            'description'=>'Show total friend of user when popup open',
            'multiOptions'=>array('1'=>'Yes','0'=>'No'),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.showfriendcount', 0),
        ));
        
        $this->addElement('Radio','ynfbpp_shownetwork',array(
            'label'=>'Show user networks',
            'description'=>'Show network of user when popup open',
            'multiOptions'=>array('1'=>'Yes','0'=>'No'),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.shownetwork', 0),
        ));
        
        $this->addElement('Radio','ynfbpp_showmutual',array(
            'label'=>'Show mutual friends',
            'description'=>'Show mutual friends',
            'multiOptions'=>array('1'=>'Yes','0'=>'No'),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.showmutual', 1),
        ));
        
        
        $this -> addElement('Text', 'ynfbpp_fieldlimit', array(
            'label' => 'Number of profile fields to display',
            'description' => 'How many profile question entries will be shown when popup open? (Enter a number between 1 and 10).',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.fieldlimit', 4),
        ));

        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
