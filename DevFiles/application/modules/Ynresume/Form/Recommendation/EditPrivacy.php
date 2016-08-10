<?php
class Ynresume_Form_Recommendation_EditPrivacy extends Engine_Form {
    public function init() {
		$view = Zend_Registry::get('Zend_View');
		$this->setTitle('Edit Privacy');
        $this->setAttrib('class', 'global_form_popup');
        $this->setDescription('Display on your profile to:');
        $this->setMethod('POST');

        // Privacy
        $availableLabels = array(
            'everyone' => 'Everyone',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );
        
        $this->addElement('Radio', 'view', array(
            'label' => Engine_Api::_()->ynresume()->getSectionLabel($auth_item),
            'multiOptions' => $availableLabels,
            'value' => key($availableLabels),
        ));
        $this->view->getDecorator('Description')->setOption('placement', 'append');
        
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
            'onclick' => 'parent.Smoothbox.close()',
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