<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: GetPrice.php 7244 2011-09-01 01:49:53Z mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Core
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_View_Helper_GetPrice extends Zend_View_Helper_Abstract
{
  public function getPrice(Core_Model_Item_Abstract $item )
  {
    $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $api = Engine_Api::_()->store();

    $html = '';
    if ( isset($item->price_type) && $item->price_type == 'discount') {
      $html .= '<span class="store-list-price">' . $this->view->locale()->toCurrency((double)$item->list_price, $currency) . '</span>&nbsp;';
    }

    if ($item->getPrice() == 0) {
      $priceStr = $this->view->translate('Free');
    } else {
      $priceStr = $this->view->locale()->toCurrency((double)$item->getPrice(), $currency);
    }

    $html .= '<span class="store-price">' . $priceStr . '</span>';

    if ($item->getPrice() && $item->isStoreCredit()) {
      $html .= '/<span class="store_credit_icon">';
      $priceStr = $api->getCredits((double)$item->getPrice());
      $html .= '<span class="store-credit-price">' . $priceStr . '</span></span>';
    }

		return $html;
  }
}