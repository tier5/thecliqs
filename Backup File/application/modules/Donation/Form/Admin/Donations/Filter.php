<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Donation_Form_Admin_Donations_Filter
 *
 * @author adik
 */
class Donation_Form_Admin_Donations_Filter extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this->setAttribs(array(
      'id' => 'filter_form',
      'class' => 'global_form_box',
    ));
    // Elevemt : Title
    $title = new Zend_Form_Element_Text('title');
    $title->setLabel('Title')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));
    $this->addElement($title);

    // Elevemt : Owner
    $owner = new Zend_Form_Element_Text('owner');
    $owner->setLabel('Owner')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));
    $this->addElement($owner);


    // Element: target
    $this->addElement('Text', 'target_sum', array(
      'label' => 'DONATION_target_sum',
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast(0)
      ),
    ));

    // Element: target
    $this->addElement('select', 'category_id', array(
      'label' => 'DONATION_category',
    ));

    $type = new Zend_Form_Element_Select('type');
    $type->setLabel('Type')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
      '0' => 'All Donations',
      'charity' => 'Charity',
      'project' => 'Project',
      'fundraise' => 'Fundraising'
    ));
    $this->addElement($type);

    $ipp = new Zend_Form_Element_Select('ipp');
    $ipp->setLabel('Items Per Page')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array('20' => '20',
        '50' => '50',
        '100' => '100',
        '200' => '200',
        '500' => '500',
        '1000' => '1000',
      ))
      ->setValue('2');
    $this->addElement($ipp);

    $this->addElement('Hidden', 'order', array(
      'order' => 10001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 10002,
    ));

    // Element : Submit
    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));
    $this->addElement($submit);

  }

}
