<?php
class Ynbusinesspages_Form_Ynmusic_Search extends Engine_Form {
    public function init()  {
        $translate = Zend_Registry::get("Zend_Translate");
        $this->setAttribs(array(
            'id' => 'filter_form',
            'class' => 'global_form_box',
            ))
            ->setMethod('GET')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page' => null)));

        //Page Id
        $this->addElement('Hidden','page');
    
        //Search Text
        $this->addElement('Text', 'search', array(
            'label' => 'Search Social Music',
            'alt' => $translate->translate('Search Social Music'),
        ));
    
        //Order
        $this->addElement('Select', 'browse_by', array(
            'label' => 'Browse By',
            'multiOptions' => array(
				'recently_created' => 'Recently Created',
				'most_liked' => 'Most Liked',
				'most_viewed' => 'Most Viewed',
				'most_played' => 'Most Played',
				'a_z' => 'A - Z',
				'z_a' => 'Z - A'
			),
			'value' => 'recently_created'
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