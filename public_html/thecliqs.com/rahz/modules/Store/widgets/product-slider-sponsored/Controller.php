<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Store_Widget_ProductSliderSponsoredController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $ipp = $this->_getParam('itemCountPerPage', 6);

		/**
		 * @var $table Store_Model_DbTable_Products
		 */
    $table = Engine_Api::_()->getDbTable('products', 'store');
    $params = array('approved' => 1, 'sponsored' => 1, 'p' => 1, 'ipp' => $ipp, 'quantity' => true);
    $this->view->products = $products = $table->getPaginator($params);

    if (!$products->getTotalItemCount()) {
      return $this->setNoRender();
    }
  }
}