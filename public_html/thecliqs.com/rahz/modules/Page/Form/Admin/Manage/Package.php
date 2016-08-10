<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 15.05.12
 * Time: 16:41
 * To change this template use File | Settings | File Templates.
 */

class Page_Form_Admin_Manage_Package extends Engine_Form
{

  public function init()
  {
    $this
      ->setTitle('Manage Pages Package')
      ->setAttrib('class', 'admin_manage_form')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST')
    ;


    $this->addElement('Select', 'package_id', array(
      'label' => 'Packages',
      'descriptions' => 'Choose package',
    ));

    $this->addElement('Checkbox', 'is_expired_day', array(
      'description' => 'Set Expired Date',
      'label' => 'You can set expiration date',
    ));

     //End time
    $end = new Engine_Form_Element_CalendarDateTime('expiration');
    $end->setLabel("Expiration Date");
    $this->addElement($end);

    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper'
      )
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