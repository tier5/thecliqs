<?php
class Ynbusinesspages_Form_Job_Get extends Engine_Form {
    public function init() {
        $this->setTitle('Get Jobs')
            ->setDescription('You can get jobs from your companies in module Job Posting. Please select one company to get its jobs.')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('POST');
        ;

        // Select company
        $this->addElement('Select', 'company_id', array(
            'label' => 'Company',
        ));
        
        //Select jobs
        $this->addElement('Multiselect', 'job_ids', array(
            'label' => 'Job',
            'description' => 'Use CTRL-click to select or deselect jobs',
            'required' => true,
            'allowEmpty' => false,
    ));
        // Buttons
        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Get jobs',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
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
        
        $this->addDisplayGroup(array('submit_btn', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
        )));
    } 
}