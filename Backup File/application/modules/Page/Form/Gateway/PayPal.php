<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: PayPal.php 8221 2011-01-15 00:24:02Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Page_Form_Gateway_PayPal extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: PayPal');
    $description = $this->getTranslator()->translate('PAGE_FORM_GATEWAY_PAYPAL_DESCRIPTION');
    $description = vsprintf($description, array(
      'https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-api-signature',
      'https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-ipn-notify',
      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
          'module' => 'store',
          'controller' => 'ipn',
          'action' => 'PayPal'
        ), 'default', true),
    ));
    $this->setDescription($description);

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);


    // Elements
    $this->addElement('Text', 'username', array(
      'label' => 'API Username',
			'required' => true,
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));

    $this->addElement('Text', 'password', array(
      'label' => 'API Password',
			'required' => true,
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));

    $this->addElement('Text', 'signature', array(
      'label' => 'API Signature',
			'required' => true,
      'filters' => array(
        new Zend_Filter_StringTrim(),
 		)));
  }
}