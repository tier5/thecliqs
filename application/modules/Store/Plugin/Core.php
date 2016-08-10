<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    $md = $request->getParam('md', null);

    if ($md != 'store') {
      return;
    }

    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();

    if ($module != 'payment' ||
      $module != 'store' ||
      $controller != 'ipn' || strtolower($action) != 'paypal'
    ) {
      return;
    }

    //Redirect to store module
    if (Engine_Api::_()->store()->isActiveTransaction()) {
      $request->setModuleName($md);
    }
  }

  public function onRenderLayoutDefault($event)
  {
    /**
     * @var $viewer     User_Model_User
     */

    $viewer = Engine_Api::_()->user()->getViewer();
    $adapter = Engine_Api::_()->authorization()->getAdapter('levels');
    $isAllowed = ( $adapter->getAllowed('store', $viewer, 'use') && $adapter->getAllowed('store_product', $viewer, 'order') ) ? true:false;

    if (!$isAllowed) return;

    /**
     * @var $cartTb         Store_Model_DbTable_Carts
     * @var $cart           Store_Model_Cart
     * @var $settings       Core_Model_DbTable_Settings
     */
    $fc = Zend_Controller_Front::getInstance();
    $request = $fc->getRequest();
    $module = $request->getModuleName();
    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $show_cart = $settings->getSetting('store.show.cart', 2);
    $show_mini_cart = $settings->getSetting('store.show.mini.cart', 2);

    $cartTb = Engine_Api::_()->getDbTable('carts', 'store');
    $cart = $cartTb->getCart($viewer->getIdentity());
    $table = Engine_Api::_()->getDbTable('cartitems', 'store');
    $items = $table->fetchAll($table
        ->select()
        ->where('cart_id = ?', $cart->getIdentity())
        ->order('cartitem_id DESC')
    );

    $totalCount = $items->count();
    $view = $event->getPayload();

    if ($show_cart == 2 || $show_cart == 1 && $module == 'store') {
      $currency = $settings->getSetting('payment.currency', 'USD');
      $totalPrice = $cart->getPrice();

      $params = array(
        'show_cart' => 1,
        'viewer' => $viewer,
        'items' => $items,
        'totalCount' => $totalCount,
        'totalPrice' => $totalPrice,
        'currency' => $currency,
      );
    } else {
      $params = array(
        'show_cart' => 0,
      );
    }

    $script = $view->partial('cart/_boxJs.tpl', 'store', array('params' => $params));
    $view->headScript()->appendScript($script);

    if ($show_mini_cart == 2 || $show_mini_cart == 1 && $module == 'store') {
      $params = array(
        'totalCount' => $totalCount
      );

      $script = $view->partial('cart/_miniJs.tpl', 'store', array('params' => $params));
      $view->headScript()->appendScript($script);
    }
  }
}