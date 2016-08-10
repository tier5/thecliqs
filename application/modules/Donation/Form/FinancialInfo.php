<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       16.08.12
 * @time       17:43
 */
class Donation_Form_FinancialInfo extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Financial Information For Donation')
      ->setDescription('DONATION_FinInfo');

//    $this->addElement('Checkbox', 'paypal', array(
//      'label' => 'I Have a PayPal account',
//      'value' => true,
//      "onClick" => "PayPalChecked()",
//    ));

    $this->addElement('Text', 'pemail', array(
      'label' => 'PayPal Email Address',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
      'filters' => array(
        'StringTrim'
      ),
      // fancy stuff
      'inputType' => 'email',
      'autofocus' => 'autofocus',
    ));
//    $this->addElement('Checkbox', '2checkout', array(
//      'label' => 'I Have a 2CheckOut account',
//      'value' => true,
//      "onClick" => "CheckOutChecked()",
//    ));
//
//    $this->addElement('Text', '2email', array(
//      'label' => '2CheckOut Email Address',
//      'filters' => array(
//        new Engine_Filter_Censor(),
//      ),
//    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
