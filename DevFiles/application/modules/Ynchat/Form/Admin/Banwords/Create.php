<?php
class Ynchat_Form_Admin_Banwords_Create extends Engine_Form {

    public function init() {
        $this->setTitle('Add Ban Word')
        ->setAttrib('class', 'global_form_popup');
     //   $this->setDescription('YNCHAT_BANWORDS_ADD_DESCRIPTION');
        
        $this->addElement('Text', 'find_value', array(
            'label' => 'Word',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'replacement', array(
            'label' => 'Replacement',
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Text', 'reason', array(
            'label' => 'Reason',
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'label' => 'Add',
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
            'onclick' => 'javascript:parent.Smoothbox.close()',
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
             ),
        ));
    }
}