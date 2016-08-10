<?php
class Ynbusinesspages_Form_Contest_Search extends Engine_Form {
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
            'label' => 'Search Contests',
            'alt' => $translate->translate('Search Contests'),
        ));
    
        $this->addElement('Select', 'browseby', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'all'=>'',
                'feature' => 'Featured Contests',
                'premium' => 'Premium Contests',
                'endingsoon' => 'Ending Soon',
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