<?php
class Ynresume_Form_EditPrivacy extends Engine_Form {
    public function init() {
		$view = Zend_Registry::get('Zend_View');
		$this->setTitle('Edit Privacy');
        $this->setMethod('POST');

        // Privacy
        $availableLabels = array(
            'everyone' => 'Everyone',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );
        
        $id = Engine_Api::_()->user()->getViewer() -> level_id;
        $sections = Engine_Api::_()->ynresume()->getAllSections();
        $auth_arr = array_keys($sections);
        if (isset($auth_arr['photo'])) unset($auth_arr['photo']);
        foreach ($auth_arr as $auth_item) {
            $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynresume_resume', $id, 'auth_'.$auth_item);
            $options = array_intersect_key($availableLabels, array_flip($options));
            if( !empty($options) && count($options) >= 1 ) {
                // Make a hidden field
                if(count($options) == 1) {
                    $this->addElement('hidden', $auth_item, array('value' => key($options)));
                // Make select box
                } else {
                    $this->addElement('Select', $auth_item, array(
                        'label' => Engine_Api::_()->ynresume()->getSectionLabel($auth_item),
                        'multiOptions' => $options,
                        'value' => key($options),
                    ));
                    $this->$auth_item->getDecorator('Description')->setOption('placement', 'append');
                }
            }
        }        

        $this->addElement('Button', 'submit_btn', array(
            'type' => 'submit',
            'label' => 'Submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addElement('Cancel', 'cancel', array(
            'link' => true,
            'label' => 'Cancel',
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addDisplayGroup(array('submit_btn', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
             ),
        ));
    }
}