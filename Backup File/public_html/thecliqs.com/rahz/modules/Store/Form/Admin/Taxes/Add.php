<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Add.php 11.04.12 15:45 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Taxes_Add extends Engine_Form
{
  public function init()
  {
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
    ;

    $this
      ->setAttribs(array(
        'id' => 'tax_form',
        'class' => 'global_form_box',
      ));

    $this->setTitle('Add Tax')
      ->setDescription('Add title and percent of Tax');

    $this->addElement('Text', 'title', array(
      'label' => 'Name',
      'required' => true,
      'allowEmpty' => false,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      )
    ));

    $this->addElement('Text', 'percent', array(
      'label' => 'Percent(Ex: 10.00 or 0.03)',
      'required' => true,
      'allowEmpty' => false,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      ),
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast(0)
      )
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'STORE_+Add',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
        array('HtmlTag2', array('tag' => 'div'))
      )
    ));
  }
}
