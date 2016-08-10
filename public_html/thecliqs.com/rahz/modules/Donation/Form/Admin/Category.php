<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 21.08.12
 * Time: 11:02
 * To change this template use File | Settings | File Templates.
 */
class Donation_Form_Admin_Category extends Engine_Form
{
  public function init()
  {
    $this->setMethod('post');

    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Category Name')
      ->addValidator('NotEmpty')
      ->addFilter('StringTrim')
      ->setRequired(true)
      ->setAttrib('class', 'text');
    $this->addElement($label);

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Category',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}
