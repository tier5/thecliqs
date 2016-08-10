<?php

class Ynfbpp_Form_Admin_Settings_Group extends Engine_Form
{
    public function init()
    {

        $this -> setTitle('Group Settings') -> setDescription('These settings affect all members in your community.');

        $this -> addElement('Radio', 'ynfbpp_group_description', array(
            'label' => 'Show Description',
            'description' => 'Show group description',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.group.description', 0),
        ));
        $this -> addElement('Radio', 'ynfbpp_group_owner', array(
            'label' => 'Show Group Owner',
            'description' => 'Show group owner on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.group.owner', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_group_mutual', array(
            'label' => 'Show Friends',
            'description' => 'Show friends who joined to group',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.group.mutual', 1),
        ));

        $this -> addElement('Text', 'ynfbpp_group_mutuallimit', array(
            'label' => 'Number of Friends To Show',
            'description' => 'How many friends will be shown on popup? (Enter a number from 1 to 10).',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.group.mutuallimit', 5),
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
