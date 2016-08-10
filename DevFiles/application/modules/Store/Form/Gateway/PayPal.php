<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Paypal.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_Form_Gateway_PayPal extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();

    $this->setTitle('Payment Gateway: PayPal');
    $description = $this->getTranslator()->translate('STORE_FORM_PAGE_GATEWAY_PAYPAL_DESCRIPTION');
    $this->setDescription($description);

    // Elements
    $this->addElement('Text', 'email', array(
      'label' => 'Login Email Address',
			'required' => true,
      'filters' => array(
        new Zend_Filter_StringTrim(),
      ),
    ));
  }
}