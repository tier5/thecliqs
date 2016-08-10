<?php
/**
 * SocialEngine
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: PayPal.php 8221 2011-01-15 00:24:02Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Store_Form_Admin_Gateway_PayPal extends Store_Form_Admin_Gateway_Abstract
{
  protected $_isTestMode = false;

  public function __construct($options = array())
  {
    if (array_key_exists('isTestMode', $options)) {
      $this->_isTestMode = (bool)$options['isTestMode'];
    }

    return parent::__construct($options);
  }

  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: PayPal');
    $description = $this->getTranslator()->translate('PAYMENT_FORM_ADMIN_GATEWAY_PAYPAL_DESCRIPTION');

    if ($this->_isTestMode) {
      $signature = 'https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature';
      $ipn = 'https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-ipn-notify';
    } else {
      $signature = 'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature';
      $ipn = 'https://www.paypal.com/us/cgi-bin/webscr?cmd=_profile-ipn-notify';
    }

    $description = vsprintf($description, array(
      $signature,
      $ipn,
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
        'module' => 'store',
        'controller' => 'ipn',
        'action' => 'PayPal'
      ), 'default', true)
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    // PayPal Email
    $mode = Engine_Api::_()->store()->getPaymentMode();
    $required = ($mode == 'client_store') ? true : false;

    $this->addElement('Text', 'email', array(
      'label' => 'PayPal Email Address',
      'description' => 'Please fill this PayPal Email field, it is required for Client-Store mode.',
			'required' => $required,
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
    $this->email->getDecorator("Description")->setOption("placement", "append");

    // PayPal Api username
    $this->addElement('Text', 'username', array(
      'label' => 'API Username',
      'filters' => array(
        new Zend_Filter_StringTrim()
      )
    ));

    // PayPal Api password
    $this->addElement('Text', 'password', array(
      'label' => 'API Password',
      'filters' => array(
        new Zend_Filter_StringTrim()
      )
    ));

    // PayPal Api signature
    $this->addElement('Text', 'signature', array(
      'label' => 'API Signature',
      'filters' => array(
        new Zend_Filter_StringTrim()
      ),
    ));
  }
}