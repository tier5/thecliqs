<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Form_Remove extends Engine_Form {

    private $_remove_title;
    private $_remove_description;

    public function __construct($options = null) {
        if (array_key_exists('remove_title', $options)) {
            $this->_title = $options['remove_title'];
            unset($options['remove_title']);
        }
        if (array_key_exists('remove_description', $options)) {
            $this->_description = $options['remove_description'];
            unset($options['remove_description']);
        }
        
        parent::__construct($options);
    }

    public function init() {
        $this->setTitle($this->_title)
                ->setDescription($this->_description)
                ->setAttrib('class', 'global_form_popup')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod('POST');

        //$this->addElement('Hash', 'token');
        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Remove',
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