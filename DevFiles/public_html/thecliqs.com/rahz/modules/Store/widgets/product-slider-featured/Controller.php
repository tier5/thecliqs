<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-08-31 16:05 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Widget_ProductSliderFeaturedController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $ipp = $this->_getParam('itemCountPerPage', 6);
		/**
		 * @var $table Store_Model_DbTable_Products
		 */
    $table = Engine_Api::_()->getDbTable('products', 'store');
    $params = array('approved' => 1, 'featured' => 1, 'p' => 1, 'ipp' => $ipp, 'quantity' => true);
    $this->view->products = $products = $table->getPaginator($params);

    if (!$products->getTotalItemCount()){
      return $this->setNoRender();
    }
  }
}