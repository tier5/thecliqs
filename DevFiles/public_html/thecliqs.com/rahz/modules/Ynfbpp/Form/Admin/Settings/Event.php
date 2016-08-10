<?php

class Ynfbpp_Form_Admin_Settings_Event extends Engine_Form
{
    public function init()
    {

        $this -> setTitle('Event Settings') -> setDescription('These settings affect all members in your community.');

        $this -> addElement('Radio', 'ynfbpp_event_description', array(
            'label' => 'Show Description',
            'description' => 'Show event description',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.event.description', 0),
        ));
        $this -> addElement('Radio', 'ynfbpp_event_owner', array(
            'label' => 'Show Event Owner',
            'description' => 'Show event owner on popup',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.event.owner', 1),
        ));

        $this -> addElement('Radio', 'ynfbpp_event_mutual', array(
            'label' => 'Show Friends',
            'description' => 'Show friends who joined to event',
            'multiOptions' => array(
                '1' => 'Yes',
                '0' => 'No'
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.event.mutual', 1),
        ));

        $this -> addElement('Text', 'ynfbpp_event_mutuallimit', array(
            'label' => 'Number of Friends To Show',
            'description' => 'How many friends will be shown on popup? (Enter a number from 1 to 10).',
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynfbpp.event.mutuallimit', 5),
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
