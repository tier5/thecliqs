<?php

class Ynfbpp_Form_Admin_Settings_User extends Engine_Form
{
    public function init()
    {

        $this -> setTitle('User Settings') -> setDescription('These settings affect all members in your community.');

        $this -> addElement('Radio', 'ynfbpp_user_status', array(
            'label' => 'Show User Status',
            'description' => 'Show user status under profile link',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.user.status', 1),
        ));
        $this -> addElement('Radio', 'ynfbpp_user_membertype', array(
            'label' => 'Show Member Profile Type',
            'description' => 'Show member profile type on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.user.membertype', 0),
        ));

        $this -> addElement('Radio', 'ynfbpp_user_profile', array(
            'label' => 'Show Profile Fields',
            'description' => 'Show profile fields on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.user.profile', 1),
        ));

        $this -> addElement('Text', 'ynfbpp_user_fieldlimit', array(
            'label' => 'Number of Profile Fields',
            'description' => 'How many profile fields will be shown on popup? (Enter a number from 1 to 10).',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.user.fieldlimit', 3),
            'required'=>true,
            'validators' => array('Int',array('Between',false,array(1,10)))
        ));

        $this -> addElement('Radio', 'ynfbpp_user_mutual', array(
            'label' => 'Show Mutual Friend',
            'description' => 'Show mutual friends',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.user.mutual', 1),
        ));

        $this -> addElement('Text', 'ynfbpp_user_mutuallimit', array(
            'label' => 'Number of Mutual Friend',
            'description' => 'How many multual friends will be shown on popup? (Enter a number from 1 to 10).',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.user.mutuallimit', 5),
            'required'=>true,
            'validators' => array('Int',array('Between',false,array(1,10)))            
        ));

        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
