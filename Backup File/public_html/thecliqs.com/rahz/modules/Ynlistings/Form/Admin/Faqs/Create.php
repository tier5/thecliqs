<?php
class Ynlistings_Form_Admin_Faqs_Create extends Engine_Form {

    public function init() {
        $this
          ->setTitle('Create FAQ')
          ->setDescription('YNLISTINGS_FAQS_CREATE_DESCRIPTION');
        
        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Integer', 'order', array(
            'label' => 'Ordering',
            'required' => true,
        ));
        
        $this->addElement('Radio', 'status', array(
            'label' => 'Display this FAQ',
            'multiOptions' => array(
                'hide' => 'No.',
                'show' => 'Yes.'
            ),
            'value' => 'show',
        ));
        
        $this->addElement('TinyMce', 'answer', array(
            'label' => 'Answer',
        ));
        
        $this->addElement('Button', 'submit', array(
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
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
             ),
        ));
    }
}