<?php
class Ynsocialads_Form_Ads_Edit extends Engine_Form {
    public function init() {
        $this
          ->setTitle('Edit Ads')
          ->setAttrib('class', 'global_form_popup')
          ;
    	
        $this->addElement('Text', 'name', array(
          'label' => 'Ads Name',
          'required' => true,
          'allowEmpty' => false,
          'filters' => array(
            new Engine_Filter_Censor(),
            'StripTags'
          ),
        ));
       	
        // Buttons
        $this->addElement('Button', 'submit', array(
          'label' => 'Edit Ads',
          'type' => 'submit',
          'ignore' => true,
          'onclick' => 'removeSubmit()',
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

