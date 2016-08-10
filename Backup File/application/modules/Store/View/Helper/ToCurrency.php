<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: CartButton.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_View_Helper_ToCurrency extends Zend_View_Helper_Abstract
{
  public function toCurrency( $value, $currency = null )
  {
    if ($currency == 'CRD') {
      return '<span class="store_credit_icon"><span class="store-credit-price">'.(int)$value.'</span></span>';
    }

    if ( !$currency ) {
      $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    }

		return $this->view->locale()->toCurrency($value, $currency);
  }
}