<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Spectacular_Form_Admin_Images_Add extends Engine_Form {

    public function init() {

        $this->setTitle("Add New Image")
                ->setDescription('Upload an image for Landing Page. (Note: The recommended size for the image is: 750px x 470px.)');

        // Init name
        $this->addElement('Text', 'title', array(
            'label' => 'Image Name (for your reference only)',
            'maxlength' => '100',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                //new Engine_Filter_HtmlSpecialChars(),
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '100')),
            )
        ));

        $this->addElement('file', 'photo', array(
            'label' => 'Image',
            'accept' => 'image/*',
            'required' => true,
            'allowEmpty' => false,
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $this->getDisplayGroup('buttons');
    }

}
