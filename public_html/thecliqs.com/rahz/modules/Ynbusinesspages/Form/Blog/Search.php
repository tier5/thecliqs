<?php
class Ynbusinesspages_Form_Blog_Search extends Engine_Form {
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
            'label' => 'Search Blogs',
            'alt' => $translate->translate('Search blogs'),
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