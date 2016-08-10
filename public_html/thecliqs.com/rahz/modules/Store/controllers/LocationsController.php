<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: LocationsController.php 4/12/12 4:05 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_LocationsController extends Store_Controller_Action_User
{
  public function init()
  {
    /**
     * @var $page Page_Model_Page
     */
    if (null != ($page = Engine_Api::_()->getItem('page', (int)$this->_getParam('page_id', 0)))) {
      Engine_Api::_()->core()->setSubject($page);
    }

    // Set up requires
    $this->_helper->requireSubject('page')->isValid();

    $this->view->page = $page = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //he@todo check admin settings
    if (
      !$page->isAllowStore() ||
      !$page->isOwner($viewer)
//    !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
    ) {
      $this->_redirectCustom($page->getHref());
    }

    /**
     * @var $api Store_Api_Page
     */
    $api = Engine_Api::_()->getApi('page', 'store');
    $this->view->navigation = $api->getNavigation($page, 'settings');
  }

  public function indexAction()
  {
    /**
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->core()->getSubject('page');
    $parent_id = $this->_getParam('parent_id', 0);

    /**
     * @var $table       Store_Model_DbTable_Locations
     * @var $parent      Store_Model_Location
     * @var $locationApi Store_Api_Location
     */
    $table = Engine_Api::_()->getDbTable('locations', 'store');
    $locationApi = Engine_Api::_()->getApi('location', 'store');

    $select = $table->select()->where('location_id = ?', $parent_id);
    $this->view->parent_id = $parent_id;
    $this->view->parent = $parent = $table->fetchRow($select);
    $this->view->paginator = $paginator = $locationApi->getPaginator($page->getIdentity(), $this->_getParam('page', 1), $parent_id, 'supported');
    $this->view->count = $paginator->getTotalItemCount();
  }

  public function addAction()
  {
    /**
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->core()->getSubject('page');
    $parent_id = $this->_getParam('parent_id', 0);

    /**
     * @var $locationApi Store_Api_Location
     * @var $lTable      Store_Model_DbTable_Locations
     */
    $locationApi = Engine_Api::_()->getApi('location', 'store');
    $lTable = Engine_Api::_()->getDbTable('locations', 'store');

    $paginator = $locationApi->getPaginator($page->getIdentity(), $this->_getParam('page', 1), $parent_id, 'supported-add');

    $this->view->parent_id = $parent_id;
    $this->view->parent = $parent = $lTable->fetchRow($lTable->select()->where('location_id = ?', $parent_id));
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
     * @var $lsTable Store_Model_DbTable_Locationships
     */
    $lsTable = Engine_Api::_()->getDbTable('locationships', 'store');
    $ids = array_unique($ids);

    $db = $lsTable->getDefaultAdapter();
    $db->beginTransaction();

    try {

      // Add location's nodes
      foreach ($ids as $location_id) {
        $lsSelect = $lsTable->select()->where('page_id = ?', 0)->where('location_id = ?', $location_id);
        $existSelect = $lsTable->select()->where('page_id = ?', (int)$page->getIdentity())->where('location_id = ?', $location_id);
        if (
          (null == ($location = $lsTable->fetchRow($lsSelect)) && null == ($location = $lTable->findRow($location_id))) ||
          (null != ($lsTable->fetchRow($existSelect)))
        ) continue;

        $lsTable->insert(array(
          'location_id' => $location->location_id,
          'page_id' => $page->getIdentity(),
          'shipping_amt' => $location->shipping_amt,
          'shipping_days' => $location->shipping_days,
          'creation_date' => new Zend_Db_Expr('NOW()')
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
    /**
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->core()->getSubject('page');
    $location_id = $this->_getParam('location_id');

    /**
     * @var $location Store_Model_Locations
     */
    if (null == $location = Engine_Api::_()->getDbTable('locations', 'store')->findRow($location_id)) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'messages' => Zend_Registry::get('Zend_Translate')->_('STORE_No location found'),
      ));
    }

    /**
     * @var $shipTable Store_Model_DbTable_Locationships
     */
    $shipTable = Engine_Api::_()->getDbTable('locationships', 'store');
    $select = $shipTable
      ->select()
      ->where('page_id = ?', $page->getIdentity())
      ->where('location_id = ?', $location->getIdentity());
    $ship = $shipTable->fetchRow($select);

    $this->view->form = $form = new Store_Form_Admin_Locations_Edit(array('location' => $location));
    $form->removeElement('location');
    $form->getElement('shipping_amt')->setValue($ship->shipping_amt);
    $form->getElement('shipping_days')->setValue($ship->shipping_days);

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
        $ship->shipping_amt = null;
      else
        $ship->shipping_amt = (float)$data['shipping_amt'];

      if ((int)$data['shipping_days'] <= 0)
        $ship->shipping_days = 1;
      else
        $ship->shipping_days = (int)$data['shipping_days'];

      $ship->save();

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
    /**
     * @var $page Page_Model_Page
     */
    $page = Engine_Api::_()->core()->getSubject('page');
    $location_id = $this->_getParam('location_id');

    /**
     * @var $table    Store_Model_DbTable_Locations
     * @var $location Store_Model_Location
     */
    $table = Engine_Api::_()->getDbTable('locations', 'store');
    if (null == $location = $table->findRow($location_id)) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
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
     * @var $tableShips Store_Model_DbTable_Locationships
     */
    $tableShips = Engine_Api::_()->getDbTable('locationships', 'store');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $tableShips->delete(array('location_id IN (' . $table->getTreeIds($location_id) . ')',
        'page_id = ?' => $page->getIdentity()));
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
