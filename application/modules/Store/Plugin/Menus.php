<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Plugin_Menus
{

  public function onMenuInitialize_CoreMiniCart($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $adapter = Engine_Api::_()->authorization()->getAdapter('levels');
    $isAllowed = ( $adapter->getAllowed('store', $viewer, 'use') && $adapter->getAllowed('store_product', $viewer, 'order') ) ? true:false;

    if (!$isAllowed) return false;

    /**
     * @var $settings       Core_Model_DbTable_Settings
     */
    $fc             = Zend_Controller_Front::getInstance();
    $request        = $fc->getRequest();
    $module         = $request->getModuleName();
    $settings       = Engine_Api::_()->getDbTable('settings', 'core');
    $show_mini_cart = $settings->getSetting('store.show.mini.cart', 2);

    if( !($show_mini_cart == 2 || $show_mini_cart == 1 && $module == 'store') ){
      return false;
    }

    /**
     * @var $view  Zend_View
     * @var $table Store_Model_DbTable_Carts
     * @var $cart  Store_Model_Cart
     */
    $view  = Zend_Registry::get('Zend_View');
    $table = Engine_Api::_()->getDbTable('carts', 'store');
    $cart = $table->getCart($viewer->getIdentity());

    $label  = $view->translate('%s cart', $view->locale()->toNumber((int)$cart->getItemCount()));
    $params = array(
      'label'  => $label,
      "uri"    => "javascript:void(0);",
    );

    return $params;
  }

  public function onMenuInitialize_CoreMainStore($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('store', $viewer, 'use')) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_StoreMainHome($row)
  {
    return true;
  }

  public function onMenuInitialize_StoreMainStores($row)
  {
    if (!Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page')) return false;

    return true;
  }

  public function onMenuInitialize_StoreMainPanel(Engine_Db_Table_Row  $row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_StoreMainTransactions($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      return false;
    }
    return true;
  }

  public function onMenuInitialize_StoreMainCart($row)
  {
    $viewer                   = Engine_Api::_()->user()->getViewer();
    $this->view->isAllowOrder = $isAllowOrder = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('store_product', $viewer, 'order');

    if (!$isAllowOrder) {
      return false;
    }

    /**
     * @var $cart Store_Model_Cart
     */
    if (null == ($cart = Engine_Api::_()->getDbtable('carts', 'store')->getCart($viewer->getIdentity())) || !$cart->hasItem()) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_UserSettingsStore($row)
  {
    //he@todo Check for created store
    $user = Engine_Api::_()->user()->getViewer();

    // Check if they are an admin or moderator (don't require subscriptions from them)
    $level = Engine_Api::_()->getItem('authorization_level', $user->level_id);
    if ($level->type == 'admin') {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_StoreProductProfileOrder($row)
  {
    $viewer  = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if ($subject->authorization()->isAllowed($viewer, 'order')) {
      return array(
      );
    }
  }

  public function onMenuInitialize_StoreProductProfileEdit($row)
  {
    /**
     * @var $subject Store_Model_Product
     */
    $viewer  = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if ($viewer->isAdmin() && $subject->isOwner($viewer) && ($subject->page_id == 0)) {
      return array(
        'label'  => 'STORE_Edit Product',
        'icon'   => 'application/modules/User/externals/images/edit.png',
        'route'  => 'admin_default',
        'params' => array(
          'module'     => 'store',
          'controller' => 'products',
          'action'     => 'edit',
          'product_id' => ($viewer->getGuid(false) == $subject->getGuid(false) ? null : $subject->getIdentity()),
        )
      );
    } else if ($subject->isOwner($viewer) || ($subject->getStore() && $subject->getStore()->isOwner($viewer))) {
      $view = Zend_Registry::get('Zend_View');
      if (null != ($page = Engine_Api::_()->getItem('page', $subject->page_id))) {
        $url = $view->url(array('action'     => 'edit',
                                'page_id'    => $page->getIdentity(),
                                'product_id' => $subject->getIdentity()), 'store_products', true);
        return array(
          'label' => 'STORE_Edit Product',
          'icon'  => 'application/modules/User/externals/images/edit.png',
          'uri'   => $url,
        );
      }
    }

    return false;
  }

  public function onMenuInitialize_StoreProductProfileStore($row)
  {
    $view       = Zend_Registry::get('Zend_View');
    $subject    = Engine_Api::_()->core()->getSubject();
    $navigation = array();
    if ($subject->page_id != 0) {
      if (null != ($page = Engine_Api::_()->getItem('page', $subject->page_id))) {
        $navigation[] = array(
          'label' => 'STORE_Back to Store',
          'icon'  => 'application/modules/Like/externals/images/icons/store_product.png',
          'uri'   => $page->getHref(),
        );
      }
    }

    $url = $view->url(array('action' => 'products'), 'store_general', true);

    $navigation[] = array(
      'label' => 'STORE_Back to Products',
      'icon'  => 'application/modules/Core/externals/images/back.png',
      'uri'   => $url,
    );

    return $navigation;
  }

  public function onMenuInitialize_StoreProductProfileShare($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    return array(
      'label'  => 'Share Product',
      'icon'   => 'application/modules/Store/externals/images/icons/share.png',
      'class'  => 'smoothbox',
      'route'  => 'default',
      'params' => array(
        'module'     => 'activity',
        'controller' => 'index',
        'action'     => 'share',
        'type'       => $subject->getType(),
        'id'         => $subject->getIdentity(),
        'format'     => 'smoothbox'
      )
    );
  }

  public function onMenuInitialize_StoreProductProfileDelete($row)
  {
    $viewer  = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if ($subject->isOwner($viewer) || ($subject->getStore() && $subject->getStore()->isOwner($viewer))) {
      return array(
        'label'  => 'Delete',
        'icon'   => 'application/modules/Core/externals/images/delete.png',
        'route'  => 'default',
        'params' => array(
          'module'     => 'store',
          'controller' => 'product',
          'action'     => 'delete',
          'product_id' => $subject->getIdentity(),
          'format'     => 'smoothbox',
        ),
        'class'  => 'smoothbox',
      );
    }

    return false;
  }

  public function onMenuInitialize_StorePageAll($row)
  {
    return array(
      'label'   => 'STORE_Browse Products',
      'onClick' => 'javascript:store_page.list();',
      'route'   => 'store_page'
    );
  }


  public function onMenuInitialize_StorePageMine($row)
  {
    $subject       = Engine_Api::_()->core()->getSubject();
    $viewer        = Engine_Api::_()->user()->getViewer();
    $isAllowedPost = $subject->authorization()->isAllowed($viewer, 'posting');

    if ($isAllowedPost) {
      return array(
        'label'   => 'STORE_My Products',
        'onClick' => 'javascript:store_page.my_products();',
        'route'   => 'store_page'
      );
    }

    return false;
  }

  public function onMenuInitialize_StoreAdminMainStores($row)
  {
    return (boolean)(Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page'));
  }

  public function onMenuInitialize_StoreAdminMainCredit($row)
  {
    return (boolean)(Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('credit'));
  }

  public function onMenuInitialize_StoreAdminMainRequests($row)
  {
    return (boolean)(Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page'));
  }

  public function onMenuInitialize_StorePageCreate($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();

    if ($subject->authorization()->isAllowed(Engine_Api::_()->user()->getViewer(), 'posting')) {
      return array(
        'label'   => 'STORE_Add New Product',
        'onClick' => 'store_page.create();',
        'route'   => 'store_page',
      );
    }

    return false;
  }
}