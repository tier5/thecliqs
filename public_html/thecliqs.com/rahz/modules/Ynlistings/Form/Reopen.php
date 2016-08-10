<?php
class YnListings_Form_Reopen extends Engine_Form {

    public function init() {
        $this->setTitle('Re-open Listing')
            ->setDescription('Are you sure you want to re-open this listing?')
            ->setAttrib('class', 'global_form_popup')
            ->setAttrib('id', 'reopen_form')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('POST');
        ;
        
        $this->addElement('Heading', 'error', array(
            'ignore' => true,
        ));
        
        $this->addElement('Text', 'end_date', array(
            'label' => 'New end date',
            'required' => true,
            'class' => 'date_picker',
        ));
        
        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Re-open Listing',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }
}