<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: ProductLocationsController.php 4/16/12 2:21 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_ProductLocationsController extends Store_Controller_Action_User
{
  public function init()
  {
    // he@todo this may not work with some of the content stuff in here, double-check
    $product = null;

    if (!Engine_Api::_()->core()->hasSubject('store_product')) {
      if (null != ($product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0)))) ;

      if ($product && $product->getIdentity()) {
        Engine_Api::_()->core()->setSubject($product);
      } else {
        if ($this->_getParam('format') == 'json') {
          $this->view->status = 0;
          $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product doesn\'t exist');
          return;
        }
        $this->_redirectCustom(
          $this->view->url(
            array(
              'action' => 'index'
            ), 'store_general', true
          )
        );
      }
    }

    //Set Requires
    $this->_helper->requireSubject('store_product')->isValid();

    /**
     * @var $page Page_Model_Page
     */
    $this->view->product = $product = Engine_Api::_()->core()->getSubject('store_product');
    $this->view->page = $page = $product->getStore();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //he@todo check admin settings
    if (
      !$page->isAllowStore() ||
      !( $page->getStorePrivacy() || $product->isOwner($viewer))
      // !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
    ) {
      $this->_redirectCustom($page->getHref());
    }

    /**
     * @var $api Store_Api_Page
     */
    $api = Engine_Api::_()->getApi('page', 'store');
    $this->view->navigation = $api->getNavigation($page);
  }

  public function indexAction()
  {
    $this->view->parent_id = $parent_id = $this->_getParam('parent_id', 0);
    /**
     * @var $locationApi Store_Api_Location
     * @var $product     Store_Model_Product
     * @var $table       Store_Model_DbTable_Locations
     * @var $parent      Store_Model_Location
     * @var $locationApi Store_Api_Location
     */
    $locationApi = Engine_Api::_()->getApi('location', 'store');
    $product = Engine_Api::_()->core()->getSubject('store_product');
    $table = Engine_Api::_()->getDbTable('locations', 'store');

    $select = $table->select()->where('location_id = ?', $parent_id);
    $parent = $table->fetchRow($select);

    $paginator = $locationApi->getPaginator($product->page_id, $this->_getParam('page', 1), $parent_id, 'product', $product->getIdentity());
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->paginator = $paginator;
    $this->view->parent = $parent;
  }

  public function addAction()
  {
    $parent_id = (int)$this->_getParam('parent_id', 0);

    /**
     * @var $product Store_Model_Product
     */
    $product = Engine_Api::_()->core()->getSubject('store_product');

    /**
     * @var $locationApi Store_Api_Location
     * @var $lTable      Store_Model_DbTable_Locations
     */
    $locationApi = Engine_Api::_()->getApi('location', 'store');
    $lTable = Engine_Api::_()->getDbTable('locations', 'store');

    $paginator = $locationApi->getPaginator($this->view->page->getIdentity(), $this->_getParam('page', 1), $parent_id, 'product-add', $product->getIdentity());

    $this->view->parent_id = $parent_id;
    $this->view->parent = $parent = $lTable->fetchRow(array('location_id = ?' => $parent_id));
    $paginator->setItemCountPerPage($paginator->getTotalItemCount());
    $this->view->paginator = $paginator;

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $params = $this->getRequest()->getParams();

    if (count($params['locations']) <= 0) {
      return;
    }

    $ids = array();
    foreach ($params['locations'] as $id) {
      // Get parent locations
      $tmp_id = $id;
      while (null != ($loc = $lTable->findRow($tmp_id))) {
        $ids[] = $loc->location_id;
        $tmp_id = $loc->parent_id;
      }

      // Get child locations
      $ids = array_merge($ids, explode(',', $lTable->getTreeIds($id)));
    }

    /**
     * @var $psTable Store_Model_DbTable_Productships
     * @var $lsTable Store_Model_DbTable_Locationships
     */
    $psTable = Engine_Api::_()->getDbTable('productships', 'store');
    $lsTable = Engine_Api::_()->getDbTable('locationships', 'store');
    $ids = array_unique($ids);

    $db = $psTable->getDefaultAdapter();
    $db->beginTransaction();

    try {

      // Add location's nodes
      foreach ($ids as $location_id) {
        $lsSelect = $lsTable->select()->where('page_id = ?', 0)->where('location_id = ?', $location_id);
        if (
          $product->isLocationSupported($location_id) ||
          (null == ($location = $lsTable->fetchRow($lsSelect)) && null == ($location = $lTable->findRow($location_id)))
        ) continue;

        $psTable->insert(array(
          'product_id' => $product->getIdentity(),
          'location_id' => $location->location_id,
          'shipping_amt' => $location->shipping_amt,
          'shipping_days' => $location->shipping_days,
          'creation_date' => new Zend_Db_Expr('NOW()'),
        ));
      }

      $db->commit();

    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
      'parentRefresh' => 10,
      'messages' => Zend_Registry::get('Zend_Translate')->_('STORE_Selected locations have been added successfully'),
    ));
  }

  public function editAction()
  {
    $location_id = $this->_getParam('location_id');

    /**
     * @var $product Store_Model_Product
     */
    $product = Engine_Api::_()->core()->getSubject('store_product');

    /**
     * @var $psTable  Store_Model_DbTable_Productships
     * @var $location Store_Model_Location
     */
    $psTable = Engine_Api::_()->getDbTable('productships', 'store');
    if (null == ($location = $psTable->getLocation($location_id, $product->getIdentity()))) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 30,
        'messages' => Zend_Registry::get('Zend_Translate')->_('STORE_No location found'),
      ));
    }

    $this->view->form = $form = new Store_Form_Admin_Locations_Edit(array('location' => $location));
    $form->removeElement('location');

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $data = $this->getRequest()->getParams();

    if (!$form->isValid($data)) {
      return;
    }

    $db = Engine_Api::_()->getDbTable('products', 'store')->getAdapter();
    $db->beginTransaction();

    try {
      if ((float)$data['shipping_amt'] <= 0)
        $shipping_amt = null;
      else
        $shipping_amt = (float)$data['shipping_amt'];

      if ((int)$data['shipping_days'] <= 0)
        $shipping_days = 1;
      else
        $shipping_days = (int)$data['shipping_days'];

      $psTable->update(array(
        'shipping_amt' => $shipping_amt,
        'shipping_days' => $shipping_days,
      ), array(
        'location_id = ?' => $location->location_id,
        'product_id = ?' => $location->product_id,
      ));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }


    $this->view->status = true;
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
    ));
  }

  public function removeAction()
  {
    $location_id = $this->_getParam('location_id');

    /**
     * @var $product Store_Model_Product
     */
    $product = Engine_Api::_()->core()->getSubject('store_product');

    /**
     * @var $psTable  Store_Model_DbTable_Productships
     * @var $location Store_Model_Location
     */
    $psTable = Engine_Api::_()->getDbTable('productships', 'store');
    if (null == ($location = $psTable->getLocation($location_id, $product->getIdentity()))) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 30,
        'messages' => Zend_Registry::get('Zend_Translate')->_('STORE_No location found'),
      ));
    }

    $this->view->form = $form = new Store_Form_Admin_Locations_Remove(array('location' => $location));

    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getParams())) {
      return;
    }

    /**
     * @var $lTable Store_Model_DbTable_Locations
     */
    $lTable = Engine_Api::_()->getDbTable('locations', 'store');
    $db = $psTable->getAdapter();
    $db->beginTransaction();

    try {
      $psTable->delete(array('location_id IN (' . $lTable->getTreeIds($location_id) . ')',
        'product_id = ?' => $product->getIdentity()));
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }


    $this->view->status = true;
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
    ));
  }
}
