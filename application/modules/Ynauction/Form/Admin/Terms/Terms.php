<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Ynauction_Form_Admin_Terms_Terms extends Engine_Form {

 public function init()
  {
    $this->setTitle('Update your terms of service')
    //  ->setDescription('Please compose your new announcement below.');
      ->setAttrib('id', 'ynauction_terms');

    // Add title
    $this->addElement('Text', 'static_title', array(
      'label' => 'Title',
      'required' => true,
      'allowEmpty' => false,
    ));

    $this->addElement('TinyMce', 'static_content', array(
    //  'label' => 'Body',
      'required' => true,
      'allowEmpty' => false,
    ));

        // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Update',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

//    $this->addElement('Cancel', 'cancel', array(
//      'label' => 'cancel',
//      'ignore' => true,
//      'link' => true,
//      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'announcement', 'controller' => 'manage', 'action' => 'index'), 'admin_default', true),
//      'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
//      'decorators' => array(
//        'ViewHelper',
//      ),
//    ));

    $this->addDisplayGroup(array('submit'), 'buttons');
  }

}

?>
