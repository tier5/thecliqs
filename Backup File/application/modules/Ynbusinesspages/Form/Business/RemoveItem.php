<?php
class Ynbusinesspages_Form_Business_RemoveItem extends Engine_Form {
    protected $_label;
    
    public function getLabel() {
        return $this -> _label;
    }
    
    public function setLabel($label) {
        $this -> _label = $label;
    } 
    
    public function init() {
        $this->setTitle('Delete '.$this->getLabel())
            ->setDescription('Are you sure you want to delete this '.$this->getLabel().' ?')
            ->setAttrib('class', 'global_form_popup')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('POST');
        ;

        // Buttons
        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Delete '.$this->getLabel(),
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
        
        $this->addDisplayGroup(array('submit_btn', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    } 
}