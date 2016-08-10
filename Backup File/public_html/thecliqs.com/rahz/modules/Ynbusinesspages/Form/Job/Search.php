<?php
class Ynbusinesspages_Form_Job_Search extends Engine_Form {
    public function init()  {
        $translate = Zend_Registry::get("Zend_Translate");
        $this->setAttribs(array(
            'id' => 'filter_form',
            'class' => 'global_form',
            ))
            ->setMethod('GET')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page' => null)));

        //Page Id
        $this->addElement('Hidden','page');
    
        //Search Text
        $this->addElement('Text', 'search', array(
            'label' => 'Search Jobs',
            'alt' => $translate->translate('Search jobs'),
        ));
    
        //Order
        $this->addElement('Select', 'status', array(
            'label' => 'Job Status',
            'multiOptions' => array(
                'all' => 'All',
                'published' => 'Published',
                'expired' => 'Expired',
            ),
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
    }
}