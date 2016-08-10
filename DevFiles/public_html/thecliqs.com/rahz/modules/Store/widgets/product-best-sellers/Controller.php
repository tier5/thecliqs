<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-09-28 17:07:11 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_Widget_ProductBestSellersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		/**
		 * @var $table Store_Model_DbTable_Products
		 */
    $table = Engine_Api::_()->getDbtable('products', 'store');
    $this->view->products = $products = $table->getBestSellers(4);

    // Do not render if nothing to show
    if( count($products) <= 0 ) {
      return $this->setNoRender();
    }
  }
}