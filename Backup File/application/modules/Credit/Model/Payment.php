<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Payment.php 24.01.12 14:34 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Model_Payment extends Core_Model_Item_Abstract
{
  protected $_type = 'credit_payment';

  public function getPrice()
  {
    return $this->price;
  }

  public function getCredit()
  {
    return $this->credit;
  }

  public function getPaymentParams()
  {
    $params = array();

    $view = Zend_Registry::get('Zend_View');

    // General
    $params['name'] = $view->translate('Buying %s credits', $this->getCredit());
    $params['price'] = $this->price;
    $params['description'] = $view->translate('Buying Credit from %s', $view->layout()->siteinfo['title']);
    $params['vendor_product_id'] = $this->getGatewayIdentity();
    $params['tangible'] = false;
    $params['recurring'] = false;

    return $params;
  }

  public function getGatewayIdentity()
  {
    return 'tj' . $this->getIdentity() * 2 . 'ea' . $this->getIdentity() * 3 . 'ay';
  }
}
