<?php
class Ynmusic_Form_Playlist_Delete extends Engine_Form {

    public function init() {
        $this->setTitle('Delete Playlist')
            ->setDescription('Are you sure you want to delete this playlist?')
            ->setAttrib('class', 'global_form_popup')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('POST');
        ;

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Delete',
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