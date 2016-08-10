<?php
class Ynchat_Form_Admin_Settings_Global extends Engine_Form {
    public function init() {
        $this
        ->setTitle('Global Settings')
        
        ->setDescription('YNCHAT_GLOBAL_SETTINGS_DESCRIPTION');
        
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $this->addElement('Radio', 'ynchat_chatbox_userip', array(
            'label' => 'Use IP for WebSocket Server',
            'description' => 'Use the IP address for request to WebSocket Server.',
            'multiOptions' => array(
                1 => 'Yes. Let use IP address.',
                0 => 'No. Let use Domain name.',
            ),
            'value' => $settings->getSetting('ynchat_chatbox_userip', 0),
        ));
        
        $this->addElement('Text', 'ynchat_chatbox_ipaddress', array(
            'label' => 'IP address of WebSocket Server',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
            'value' => $settings->getSetting('ynchat_chatbox_ipaddress', ''),
        ));
        
        $this->addElement('Radio', 'ynchat_chatbox_position', array(
            'label' => 'Chat Box Position',
            'description' => 'Setting position for the chat box.',
            'multiOptions' => array(
                2 => 'Left.',
                1 => 'Right.',
            ),
            'value' => $settings->getSetting('ynchat_chatbox_position', 1),
        ));
        
        $this->addElement('Integer', 'ynchat_num_show_friends', array(
            'label' => 'Number of contacts visible on friend list',
            'description' => 'Setting number of friends show on friends list.',
            'value' => $settings->getSetting('ynchat_num_show_friends', 1000),
            'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
        ));
        
        $this->addElement('Integer', 'ynchat_num_search_friends', array(
            'label' => 'Number of results display when search friends',
            'description' => 'Setting number of results display when search friends.',
            'value' => $settings->getSetting('ynchat_num_search_friends', 10),
            'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
        ));
        
        $this->addElement('Integer', 'ynchat_num_old_message', array(
            'label' => 'Number of old message visible on chat box',
            'description' => 'Setting number of old messages show on chat box.',
            'value' => $settings->getSetting('ynchat_num_old_message', 10),
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        $this->addElement('Integer', 'ynchat_websocket_server_port', array(
            'label' => 'Websocket server port',
            'description' => 'Config port for the websocket server. Please restart the websocket server after changing.',
            'value' => $settings->getSetting('ynchat_websocket_server_port', 9009),
            'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
        ));
        
        $this->addElement('Integer', 'ynchat_websocket_stunnel_port', array(
            'label' => 'Stunnel port',
            'description' => 'Config port for the stunnel.',
            'value' => $settings->getSetting('ynchat_websocket_stunnel_port', 9010),
            'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
        ));
        
        $this->addElement('Text', 'ynchat_embedly_api_key', array(
            'label' => 'Embedly API key',
            'value' => $settings->getSetting('ynchat_embedly_api_key', '90659766ff4e43a9b9eeabfe9768d75c'),
            'required' => true,
        ));
        
        $this->addElement('Integer', 'ynchat_photo_maxsize', array(
            'label' => 'Max size of photo for uploading',
            'value' => $settings->getSetting('ynchat_photo_maxsize', 1024),
            'required' => true,
            'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
        ));
        
        $this->addElement('Integer', 'ynchat_file_maxsize', array(
            'label' => 'Max size of file for uploading',
            'value' => $settings->getSetting('ynchat_file_maxsize', 1024),
            'required' => true,
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