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

class Store_Widget_ProductInfoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		if (!Engine_Api::_()->core()->hasSubject()) {
      $product_id = $this->_getParam('product_id', 0);
      if ($product_id) {
        Engine_Api::_()->core()->setSubject(Engine_Api::_()->getItem('store_product', $product_id));
      } else {
        return $this->setNoRender();
      }
    }

		if ( !( ( $this->view->product = $product = Engine_Api::_()->core()->getSubject() )
						instanceof Store_Model_Product) ) {
      return $this->setNoRender();
    }

    /**
     * @var $viewer     User_Model_User
     * @var $cartTb     Store_Model_DbTable_Carts
     * @var $cart       Store_Model_Cart
     */

    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $cartTb = Engine_Api::_()->getItemTable('store_cart');
    $cart   = $cartTb->getCart($viewer->getIdentity());
    $this->view->item_id = ($cart) ? ($item = $cart->getRowByProduct($product->getIdentity())) ? $item->getIdentity() : 0 : 0;
    $this->view->allowOrder = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('store_product', $viewer, 'order');
  }
}