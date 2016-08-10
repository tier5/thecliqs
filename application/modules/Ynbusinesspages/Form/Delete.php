<?php
class Ynbusinesspages_Form_Job_Delete extends Engine_Form {
    public function init() {
        $this->setTitle('Delete Job')
            ->setDescription('Are you sure you want to delete this job?')
            ->setAttrib('class', 'global_form_popup')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('POST');
        ;

        // Buttons
        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Delete Job',
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
        
        $this->addDisplayGroup(array('submit_btn', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    } 
}