<?php
class Ynbusinesspages_Form_Classified_Search extends Engine_Form {
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
            'label' => 'Search Classifieds',
            'alt' => $translate->translate('Search classifieds'),
        ));
    
        //Order
        $this->addElement('Select', 'order', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'recent' => 'Most Recent',
                'view' => 'Most Viewed',
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