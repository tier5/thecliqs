<?php
class Yncredit_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    // Form information
    $this->setTitle('Global Settings')
         ->setDescription('These settings affect all members in your community.');

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $currency = $settings->getSetting('payment.currency', 'USD');
    $view = Zend_Registry::get('Zend_View');
    $description = $view->translate('How many credits in %s?', $view->locale()->toCurrency(1, $currency));

    // Elements
    $this->addElement('Text', 'yncredit_credit_price', array(
      'label' => 'Credits',
      'description' => $description,
      'value' => (int)$settings->getSetting('yncredit.credit_price', 100)
    ));

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}