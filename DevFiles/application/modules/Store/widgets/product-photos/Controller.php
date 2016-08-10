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

class Store_Widget_ProductPhotosController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if( !Engine_Api::_()->core()->hasSubject('store_product') ) return $this->setNoRender();

		/**
		 * @var $product Store_Model_Product
		 * @var $paginator Zend_Paginator
		 */
    $this->view->product = $product = Engine_Api::_()->core()->getSubject('store_product');
    $this->view->paginator = $paginator = $product->getCollectiblesPaginator();

    $paginator->setItemCountPerPage(100);
		if ($paginator->getTotalItemCount() <= 0) {
      return $this->setNoRender();
    }
  }
}