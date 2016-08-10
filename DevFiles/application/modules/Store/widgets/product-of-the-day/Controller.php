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

class Store_Widget_ProductOfTheDayController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    /**
     * @var $product Store_Model_Product
     * @var $productsTable Store_Model_DbTable_Products
     */
    $productsTable = Engine_Api::_()->getItemTable('store_product');
		if (null == ($product = $productsTable->getProductOfTheDay())) {
      return $this->setNoRender();
		}

    $this->view->product = $product;

    $this->view->owner = $owner = $product->getOwner();
    $this->view->photo = $product->photo_id == 0 ? false : true;
	  $this->view->widget_title = $this->getElement()->getTitle();
	  $this->getElement()->setTitle('');
  }
}