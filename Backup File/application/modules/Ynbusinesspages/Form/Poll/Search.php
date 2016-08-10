<?php
class Ynbusinesspages_Form_Poll_Search extends Engine_Form {
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
            'label' => 'Search Polls',
            'alt' => $translate->translate('Search polls'),
        ));
    
        //Closed
        $this->addElement('Select', 'closed', array(
            'label' => 'Status',
            'multiOptions' => array(
                '' => $translate->translate('All Polls'),
                '0' => $translate->translate('Only Open Polls'),
                '1' => $translate->translate('Only Closed Polls'),
            ),
        ));

        //Order
        $this->addElement('Select', 'order', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'recent' => $translate->translate('Most Recent'),
                'popular' => $translate->translate('Most Popular') ,
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