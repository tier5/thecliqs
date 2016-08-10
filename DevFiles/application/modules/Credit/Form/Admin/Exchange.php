<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Exchange.php 18.07.12 14:50 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Form_Admin_Exchange extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Exchange Settings');

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $currency = $settings->getSetting('payment.currency', 'USD');
    $view = Zend_Registry::get('Zend_View');
    $description = $view->translate('How much is %s in credits?', $view->locale()->toCurrency(1, $currency));

    // Elements
    $this->addElement('Text', 'credit_default_price', array(
      'label' => 'Credits',
      'description' => $description,
      'value' => (int)$settings->getSetting('credit.default.price', 100)
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}
