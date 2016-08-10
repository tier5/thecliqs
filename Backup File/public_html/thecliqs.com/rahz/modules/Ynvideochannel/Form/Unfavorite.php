<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Form_Unfavorite extends Engine_Form {
    public function init() {
            $this->setTitle('Unfavorite Video')
                ->setDescription('Are you sure you want to remove this video from your Favorite Videos list?')
                ->setAttrib('class', 'global_form_popup')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod('POST');

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Unfavorite',
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
    }

}