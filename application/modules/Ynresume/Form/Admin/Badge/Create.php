<?php
class Ynresume_Form_Admin_Badge_Create extends Engine_Form {

    public function init() {
        $this->setMethod('post');
        $this->setTitle('Create New Badge');
   
        $this->addElement('Text', 'title', array(
            'label' => 'Badge Title',
            'required'  => true,
            'allowEmpty'=> false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        
        $this->addElement('File', 'photo', array(
            'label' => 'Badge Icon',
            'required'  => true,
            'accept' => 'image/*'
        ));
    	
        $this->addElement('Radio', 'condition', array(
            'label' => 'Condition',
            'multiOptions' => array(
                'view' => 'View',
                'endorsements' => 'Endorsements',
                'recommendations' => 'Recommendations',
                'completeness' => 'Resume Completeness',
            ),
            'value' => 'view'
        ));
        
        $this->addElement('Integer', 'count_value', array(
            'label' => 'Value',
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        $completeness = Engine_Api::_()->ynresume()->getAllSectionsAndGroups();
        if (isset($completeness['recommendation'])) unset($completeness['recommendation']);
        
        $this->addElement('MultiCheckbox', 'completeness_value', array(
            'label' => 'Sections this member has to add',
            'multiOptions' => $completeness
        ));
        
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