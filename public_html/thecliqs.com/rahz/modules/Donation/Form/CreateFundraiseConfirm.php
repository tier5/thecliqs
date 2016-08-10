<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       14.08.12
 * @time       10:23
 */
class Donation_Form_CreateFundraiseConfirm extends Engine_Form
{
  protected $_id;

  public function setId($id)
  {
    $this->_id = $id;
    return $this;
  }

  public function getId()
  {
    return $this->_id;
  }

  public function init()
  {
    $donation = Engine_Api::_()->getItem('donation', $this->getId());
    $translator = Zend_Registry::get('Zend_View');
    $title = $translator->translate('DONATION_Raise_money %1$s, %2$s', $donation->getTitle(), $donation->type);
    $description = $translator->translate('DONATION_Raise_money_description %1$s, %2$s', $donation->type , $donation->type);
    $this->setTitle($title)
      ->setDescription($description)
      ->setAttrib('name', 'donations_create');

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Fundraising Page',
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
      'href' => '',
      'onclick' => 'parent.Smoothbox.close()',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
      ),
    ));
  }

}
