<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Add.php 14.06.12 10:55 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Form_Admin_Payments_Add extends Engine_Form
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
        'id' => 'payments_form',
        'class' => 'global_form_box',
      ));

    $this->setTitle('Add Price for Credits')
      ->setDescription('Add Credits with its cost');

    $this->addElement('Text', 'credit', array(
      'label' => 'Credits',
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      )
    ));

    $this->addElement('Text', 'price', array(
      'label' => 'Price',
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
      'label' => 'Add',
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
