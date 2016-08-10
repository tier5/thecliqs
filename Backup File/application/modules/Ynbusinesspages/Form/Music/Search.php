<?php
class Ynbusinesspages_Form_Music_Search extends Engine_Form {
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
            'label' => 'Search Albums',
            'alt' => $translate->translate('Search albums'),
        ));
    
        //View
        $this->addElement('Select', 'view', array(
            'label' => 'View',
            'multiOptions' => array(
                '0' => 'All Members Albums',
                '1' => 'Only My Albums',
            ),
        ));

        //Order
        $this->addElement('Select', 'order', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'recent' => 'Most Recent',
                'play' => 'Most Play',
                'comment' => 'Most Commented',
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