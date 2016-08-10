<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 16.03.12
 * Time: 16:45
 * To change this template use File | Settings | File Templates.
 */
class Page_Form_Admin_CreateTerm extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Add New Terms')
      ->setMethod('POST')
      ->setDescription('Write the terms below, then click "Save Terms" to save your terms.')
      ->setAttrib('name', 'terms_create');

    /*$this->addElement('Text','label',array(
      'label' => 'Label',
      'required' => true,
      'allowEmpty' => false,
    ));*/
    $this->addElement('TinyMce', 'terms', array(
      'disableLoadDefaultDecorators' => true,
      'label' => 'Terms',
      'required' => true,
      'allowEmpty' => false,
      'decorators' => array(
        'ViewHelper'
      ),
    ));
    $this->addElement('Select','enabled',array(
      'label' => 'Enabled?',
      'multiOptions' => array(
        '0' => 'Disabled',
        '1' => 'Enabled'
      ),
    ));

    //Element: submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Terms',
      'type' => 'submit',
    ));
  }
}
