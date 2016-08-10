<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-07-22 16:05 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Widget_ProductSponsoredCarouselController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		/**
		 * @var $productTbl Store_Model_DbTAble_Products
		 */
    $productTbl = Engine_Api::_()->getDbTable('products', 'store');
    $select = $productTbl->getSelect(array('sponsored' => 1, 'quantity' => true));
		$select
        ->order('RAND()');

    $paginator = $this->view->paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(999);

    $this->view->total = $total = $paginator->getTotalItemCount();

    if (!$total || !count($paginator)) {
      return $this->setNoRender();
    }
  }
}