<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminProductLocationsController.php 4/6/12 1:56 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminProductLocationsController extends Store_Controller_Action_Admin
{
  public function init()
  {
    $product_id = $this->_getParam('product_id');
    /**
     * @var $product Store_Model_Product
     */
    if (!$product_id || (null == ($product = Engine_Api::_()->getItem('store_product', $product_id)))) {
      return $this->_helper->redirector->gotoRoute(array(
          'module'     => 'store',
          'controller' => 'products',
          'action'     => 'index'),
        'admin_default', true);
    }

    $this->view->product = $product;
  }

  public function indexAction()
  {
    $this->view->menu = 'product-locations';

    $this->view->parent_id = $parent_id = $this->_getParam('parent_id', 0);
    /**
     * @var $locationApi Store_Api_Location
     * @var $product     Store_Model_Product
     * @var $table       Store_Model_DbTable_Locations
     * @var $parent      Store_Model_Location
     * @var $locationApi Store_Api_Location
     */
    $locationApi = Engine_Api::_()->getApi('location', 'store');
    $product     = $this->view->product;
    $table       = Engine_Api::_()->getDbTable('locations', 'store');

    $select = $table->select()->where('location_id = ?', $parent_id);
    $parent = $table->fetchRow($select);

    $paginator = $locationApi->getPaginator(0, $this->_getParam('page', 1), $parent_id, 'product', $product->getIdentity());
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->paginator = $paginator;
    $this->view->parent    = $parent;
  }

  public function addAction()
  {
    $this->view->parent_id = $parent_id  = (int)$this->_getParam('parent_id', 0);
    $product_id = (int)$this->_getParam('product_id', 0);

    /**
     * @var $product Store_Model_Product
     */
    if (null == ($product = Engine_Api::_()->getItem('store_product', $product_id))) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'messages'       => Zend_Registry::get('Zend_Translate')->_('STORE_No product found'),
      ));
    }

    /**
     * @var $locationApi Store_Api_Location
     * @var $lTable      Store_Model_DbTable_Locations
     */
    $locationApi = Engine_Api::_()->getApi('location', 'store');
    $lTable      = Engine_Api::_()->getDbTable('locations', 'store');

    $paginator = $locationApi->getPaginator(0, $this->_getParam('page', 1), $parent_id, 'product-add', $product->getIdentity());

    $this->view->product   = $product;
    $this->view->parent    = $parent = $lTable->fetchRow(array('location_id = ?' => $parent_id));
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
        $ids[]  = $loc->location_id;
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
    $ids     = array_unique($ids);

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
          'product_id'    => $product->getIdentity(),
          'location_id'   => $location->location_id,
          'shipping_amt'  => $location->shipping_amt,
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
      'messages'      => Zend_Registry::get('Zend_Translate')->_('STORE_Selected locations have been added successfully'),
    ));
  }

  public function editAction()
  {
    $location_id = $this->_getParam('location_id');
    $product_id  = (int)$this->_getParam('product_id', 0);

    /**
     * @var $product Store_Model_Product
     */
    if (null == ($product = Engine_Api::_()->getItem('store_product', $product_id))) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'messages'       => Zend_Registry::get('Zend_Translate')->_('STORE_No product found'),
      ));
    }

    /**
     * @var $psTable  Store_Model_DbTable_Productships
     * @var $location Store_Model_Location
     */
    $psTable = Engine_Api::_()->getDbTable('productships', 'store');
    if (null == ($location = $psTable->getLocation($location_id, $product_id))) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 30,
        'messages'       => Zend_Registry::get('Zend_Translate')->_('STORE_No location found'),
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
        'shipping_amt'  => $shipping_amt,
        'shipping_days' => $shipping_days,
      ), array(
        'location_id = ?' => $location->location_id,
        'product_id = ?'  => $location->product_id,
      ));

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }


    $this->view->status = true;
    $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'  => 10,
    ));
  }

  public function removeAction()
  {
    $location_id = $this->_getParam('location_id');
    $product_id  = $this->_getParam('product_id');

    /**
     * @var $product Store_Model_Product
     */

    if (
      null == ($product = Engine_Api::_()->getItem('store_product', $product_id)) ||
      !$product->isLocationSupported($location_id)
    ) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 30,
        'messages'       => Zend_Registry::get('Zend_Translate')->_('STORE_No location found'),
      ));
    }

    /**
     * @var $psTable  Store_Model_DbTable_Productships
     * @var $location Store_Model_Location
     */
    $psTable = Engine_Api::_()->getDbTable('productships', 'store');
    if (null == ($location = $psTable->getLocation($location_id, $product_id))) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 30,
        'messages'       => Zend_Registry::get('Zend_Translate')->_('STORE_No location found'),
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
    $db     = $psTable->getAdapter();
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
      'parentRefresh'  => 10,
    ));
  }
}
