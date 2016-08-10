<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/18/2016
 * Time: 9:15 AM
 */
class Ynultimatevideo_Form_Admin_Migration_Category extends Engine_Form
{
    public function init()
    {
        $this->setTitle('Select Category')
            ->setDescription('Select a category which your video will import to')
            ->setAttrib('class', 'global_form_popup')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
            ->setMethod('POST');
        ;

        $this -> addElement('Select', 'category_id', array(
            'label' => 'Category',
            'required' => true,
            'allowEmpty' => false,
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Import Video',
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