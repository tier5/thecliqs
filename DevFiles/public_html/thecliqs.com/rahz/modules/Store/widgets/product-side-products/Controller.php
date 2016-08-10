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

class Store_Widget_ProductSideProductsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		/**
		 * @var $table Store_Model_DbTable_Products
		 * @var $select Zend_Db_Table_Select
		 * @var $products Engine_Db_Table_Rowset
		 */
    $table = Engine_Api::_()->getDbTable('products', 'store');
    $params = array('approved' => 1, 'sponsored' => 1, 'p' => 1, 'quantity' => true);
		$select = $table->getSelect($params);
		$select->order('RAND()')->limit(2);
    $this->view->products = $products = $table->fetchAll($select);

		if ($products->count() <= 0 ) {
      return $this->setNoRender();
		}
  }
}