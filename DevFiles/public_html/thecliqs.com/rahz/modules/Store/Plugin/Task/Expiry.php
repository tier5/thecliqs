<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Expiry.php 13.04.12 18:36 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Plugin_Task_Expiry extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
    /**
     * @var $table Store_Model_DbTable_Products
     * @var $product Store_Model_Product
     */
    $table = Engine_Api::_()->getDbTable('products', 'store');
    $products = $table->getProducts(array('price_type' => true, 'p' => 1));

    foreach ($products as $product) {
      if ($product->discount_expiry_date !== null && $product->getExpirationDate() - time() < 0) {
        $product->clearDiscount();
      }
    }

    $this->_setWasIdle();
  }
}
