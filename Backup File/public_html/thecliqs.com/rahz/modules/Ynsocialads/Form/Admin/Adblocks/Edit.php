<?php
class Ynsocialads_Form_Admin_Adblocks_Edit extends Ynsocialads_Form_Admin_Packages_Create {
    public function init() {
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'method'=>'POST',
        ));
        
        $this
          ->setTitle('Edit Ad Block')
          ->setDescription('You can edit the Ad Block Name and number of ads display in this Ad Block.');
          
        $this->addElement('Text', 'title', array(
            'label' => 'Ad Block Name',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('Integer', 'ads_limit', array(
            'label' => 'Ads Limit',
            'required' => true,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
        ));
        
        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'label' => 'Save Changes',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addElement('Cancel', 'cancel', array(
            'link' => true,
            'label' => 'Cancel',
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
            'onclick' => 'javascript:parent.Smoothbox.close()',
        ));
        
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array());
    }
}

