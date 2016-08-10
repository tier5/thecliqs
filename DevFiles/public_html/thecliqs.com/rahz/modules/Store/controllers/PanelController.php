<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: PanelController.php 4/17/12 3:15 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_PanelController extends Store_Controller_Action_User
{
  public function init()
  {
    $this->view->navigation = $this->getNavigation();
  }

  public function indexAction()
  {
    if (
      !Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page') ||
      !$this->_helper->requireAuth()->setAuthParams('page', null, 'create')->isValid()
    ) {
      $this->_helper->redirector->gotoRoute(array('action' => 'purchases'));
    }

    /**
     * @var $viewer User_Model_User
     * @var $api    Store_Api_Page
     */
    $this->view->viewer    = $viewer = Engine_Api::_()->user()->getViewer();
    $api                   = Engine_Api::_()->getApi('page', 'store');
    $this->view->paginator = $paginator = $api->getMyStores($viewer);

    $ipp = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.browse_count', 10);

    $paginator->setItemCountPerPage($ipp);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    /**
     * @var $productsTbl Store_Model_DbTable_Products
     */

    $productsTbl = Engine_Api::_()->getDbTable('products', 'store');

    // Preload info
    $products = array();
    foreach ($paginator as $store) {
      $count = $productsTbl->getProducts(array('count'    => 1,
                                               'page_id'  => $store->page_id,
                                               'quantity' => true));
      $products[$store->page_id] = ($count) ? $count : 0;
    }

    $this->view->products = $products;
  }

  public function purchasesAction()
  {
    /**
     * @var $viewer   User_Model_User
     * @var $table    Store_Model_DbTable_Orders
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $table  = Engine_Api::_()->getDbTable('orders', 'store');

    $select = $table
      ->select()
      ->where('status != ?', 'initial')
      ->where('user_id = ?', $viewer->getIdentity())
      ->limit(30);

    $this->view->filterForm = $filterForm = new Store_Form_Panel_PurchaseFilter();
    $filterForm
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->setAttribs(array(
      'id'    => 'search_form',
      'class' => 'store_filter_form inner',
    ))
      ->setAction($this->view->url(array('action'=>'purchases'), 'store_panel', true))
    ;

    if ($filterForm->isValid($this->_getAllParams())) {
      $values = $filterForm->getValues();
    } else {
      $values = array();
    }

    foreach ($values as $key => $value) {
      if (null == $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order'           => 'order_id',
      'direction' => 'DESC',
    ), $values);

    $this->view->filterValues = $values;

    $select->order($values['order'] . ' ' . $values['direction']);

    if (!empty($values['status'])) {
      $select
        ->where('status = ?', $values['status']);
    }

    if (!empty($values['ukey'])) {
      $select
        ->where('ukey LIKE ?', '%' . $values['ukey'] . '%');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //Preload Gateways
    $gateways = array();
    foreach ($paginator as $order) {
      if (!isset($gateways[$order->gateway_id])) {
        $gateways[$order->gateway_id] = Engine_Api::_()->getItem('store_gateway', $order->gateway_id);
      }
    }

    $this->view->gateways = $gateways;
    $this->view->api = Engine_Api::_()->store();
  }

  public function purchaseAction()
  {
    $order_id = $this->_getParam('order_id', 0);

    /**
     * @var  $viewer   User_Model_User
     * @var  $table    Store_Model_DbTable_Orderitems
     * @@var $ordersTb Store_Model_DbTable_Orders
     * @@var $order    Store_Model_Order
     *
     */
    $ordersTb = Engine_Api::_()->getDbTable('orders', 'store');

    if (is_integer($order_id) && $order_id) {
      $order = $ordersTb->findRow($order_id);
    } elseif (is_string($order_id) && strlen($order_id) >= 10) {
      $order = $ordersTb->getOrderByUkey($order_id);
    }

    if (isset($order) && $order->status != 'initial') {
      Engine_Api::_()->core()->setSubject($order);
    }

    // Set up require
    if (!$this->_helper->requireSubject('store_order')->isValid()) {
      return null;
    }

    //Order items
    $order  = Engine_Api::_()->core()->getSubject('store_order');
    $table  = Engine_Api::_()->getDbTable('orderitems', 'store');
    $select = $table->select()
      ->where('status != ?', 'initial')
      ->where('order_id = ?', $order->getIdentity())
      ->order('orderitem_id DESC');

    $this->view->items = $table->fetchAll($select);

    //Shipping Details
    if (isset($order->shipping_details) &&
      isset($order->shipping_details['location_id_1']) &&
      null != ($country = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_1']))
    ) {
      $this->view->country = $country;

      if (isset($order->shipping_details['location_id_2']) &&
        null != ($state = Engine_Api::_()->getDbTable('locations', 'store')->findRow($order->shipping_details['location_id_2']))
      ) {
        $this->view->state = $state;
      }
    }

    $this->view->downloadCount = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('store.download.count', 10);
    $this->view->api           = Engine_Api::_()->store();
    $this->view->order         = $order;
    $this->view->gateway       = Engine_Api::_()->getItem('store_gateway', (int)$order->gateway_id);
  }

  public function addressAction()
  {
    if ($this->_getParam('format') == 'json' && $this->_getParam('just_locations')) {
      $parent_id = $this->_getParam('parent_id', 0);

      try {
        $element = new Engine_Form_Element_Select('state', array(
          'Label'        => 'STORE_State/Region',
          'required'     => true,
          'decorators'   => array(
            'ViewHelper',
          )
        ));

        /**
         * @var $table    Store_Model_DbTable_Locations
         * @var $location Store_Model_Location
         */
        $table = Engine_Api::_()->getDbTable('locations', 'store');
        if (null == ($location = $table->findRow($parent_id))) {
          $this->view->status = 0;
          return;
        }

        $select = $table->select()
          ->from($table, array('location_id', 'location'))
          ->where('parent_id =?', $location->getIdentity())
          ->order('location ASC');

        foreach ($table->fetchAll($select) as $loc) {
          $element->addMultiOption($loc->location_id, $loc->location);
        }

        $this->view->html   = $element->render();
        $this->view->status = 1;
        return;
      } catch (Exception $e) {
        $this->view->status = 0;
        return;
      }
    }

    $this->view->form = $form = new Store_Form_Panel_Address();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $details = (array)$this->getRequest()->getPost();

    /**
     * @var $viewer       User_Model_User
     * @var $detailsTable Store_Model_DbTable_Details
     */
    $viewer       = Engine_Api::_()->user()->getViewer();
    $detailsTable = Engine_Api::_()->getDbTable('details', 'store');

    try {
      $detailsTable->setDetails($viewer, $details);
      $form->addNotice('The details you have entered have been successfully saved.');
    } catch (Exception $e) {
      $form->addErrorMessage('An unexpected error has occurred! Please, make sure you have filled all the required fields correctly.');
    }
  }

  public function wishListAction()
  {
    /**
     * @var $select    Zend_Db_Table_Select
     * @var $table     Store_Model_DbTable_Products
     * @var $viewer    User_Model_User
     * @var $paginator Zend_Paginator
     */

    $viewer = Engine_Api::_()->user()->getViewer();

    $table  = Engine_Api::_()->getDbTable('products', 'store');
    $prefix = $table->getTablePrefix();

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'))
      ->joinLeft(array('w'=> $prefix . 'store_wishes'), "w.product_id = " . $prefix . "store_products.product_id")
      ->joinLeft(array('v'=> $prefix . 'store_product_fields_values'), "v.item_id = " . $prefix . "store_products.product_id")
      ->joinLeft(array('o'=> $prefix . 'store_product_fields_options'), "o.option_id = v.value AND o.field_id = 1", array("category" => "o.label"))
      ->where('w.user_id = ?', $viewer->getIdentity())
      ->group($prefix . 'store_products.product_id');

    $select = $table->setStoreIntegrity($select);

    $select
      ->where($prefix.'store_products.quantity <> 0 OR ' . $prefix.'store_products.type = ?', 'digital')
      ->where('w.user_id = ?', $viewer->getIdentity());

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  public function getNavigation()
  {
    $menu = $this->_getParam('action', 'index');

    $navigation    = new Zend_Navigation();
    $isPageEnabled = (
      Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page')
        //$this->_helper->requireAuth()->setAuthParams('page', null, 'create')->isValid()
    );

    $navigation->addPages(array(
      array(
        'label'  => "My Purchases",
        'route'  => 'store_panel',
        'action' => 'purchases',
        'icon'   => 'application/modules/Store/externals/images/history.png',
        'class' => (in_array($menu, array('purchase', 'purchases')))?'active':'',
      ),
      array(
        'label'  => "My Wishlist",
        'route'  => 'store_panel',
        'action' => 'wish-list',
        'icon'   => 'application/modules/Store/externals/images/heart.png',
        'class'  => ($menu == 'wish-list')?'active':'',
      ),
      array(
        'label'  => "Shipping Details",
        'route'  => 'store_panel',
        'action' => 'address',
        'icon'   => 'application/modules/Store/externals/images/ship.png',
        'class' => ($menu == 'address')?'active':'',
      )
    ));

    if ($isPageEnabled) {
      $navigation->addPages(array(
        array(
          'label' => "Manage Stores",
          'route' => 'store_panel',
          'icon'  => 'application/modules/Store/externals/images/edit_store.png',
          'class' => ($menu == 'index') ? 'active' : ''
        ),
        array(
          'label'  => "Create New Store",
          'route'  => 'page_create',
          'target' => '_blank',
          'icon'   => 'application/modules/Store/externals/images/new_product.png'
        ))
      );
    }

    return $navigation;
  }
}
