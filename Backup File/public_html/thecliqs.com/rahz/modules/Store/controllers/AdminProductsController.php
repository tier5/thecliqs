<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminProductsController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminProductsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->menu = $this->_getParam('action');

    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
      $this->_forward('uploadphotos', null, null, array('format' => 'json'));
    }

    if (isset($_GET['rm'])) {
      $this->_forward('removephoto', null, null, array('format' => 'json'));
    }
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('store_admin_main');
  }

  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_products');

    $page = $this->_getParam('page', 1);
    $page_id = $this->_getParam('page_id', 0);
    $this->view->viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('products', 'store');
    $prefix = $table->getTablePrefix();

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $table->info('name')))
      ->joinLeft(array('v' => $prefix . 'store_product_fields_values'), "v.item_id = p.product_id")
      ->joinLeft(array('o' => $prefix . 'store_product_fields_options'), "o.option_id = v.value AND o.field_id = 1", array("category" => "o.label"))
      ->group('p.product_id');

    if (Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page')) {
      $this->view->storeEnabled = true;
      $select->joinLeft(array('s' => $prefix . 'page_pages'), 's.page_id = p.page_id', array());
    }

    $values = array();
    $this->view->filterForm = $filterForm = new Store_Form_Admin_Products_Filter();
    if ($filterForm->isValid($this->_getAllParams())) {
      $values = $filterForm->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'p.product_id',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'p.product_id') . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC'));

    if (!empty($page_id)) {
      $select->where('p.page_id = ?', $page_id);
    }
    if (!empty($values['title'])) {
      $select->where('p.title LIKE ?', '%' . $values['title'] . '%');
    }
    if (!empty($values['category']) && $values['category'] != -1) {
      $select
        ->where('v.field_id = 1 AND ' . 'v.value = ?', $values['category']);
    }
    elseif (isset($values['category']) && $values['category'] == -1) {
      $select
        ->where('o.label IS NULL');
    }
    if (isset($values['featured']) && $values['featured'] != -1) {
      $select->where('p.featured = ?', $values['featured']);
    }
    if (isset($values['sponsored']) && $values['sponsored'] != -1) {
      $select->where('p.sponsored = ?', $values['sponsored']);
    }

    $valuesCopy = array_filter($values);
    // Reset enabled bit
    if (isset($values['enabled']) && $values['enabled'] == 0) {
      $valuesCopy['enabled'] = 0;
    }
    $this->view->formValues = $valuesCopy;

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);

  }

  public function createAction()
  {
    $this->view->hasShippingLocations = Engine_Api::_()->getDbTable('locationships', 'store')->hasShippingLocations();
    $this->view->form = $form = new Store_Form_Admin_Products_Create();
    $form->getDecorator('description')->setOption('escape', false);
    $viewer = Engine_Api::_()->user()->getViewer();

    // If not post or form not valid, return
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $table = Engine_Api::_()->getDbtable('products', 'store');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      // Create product
      $values = array_merge($form->getValues(), array(
        'owner_type' => $viewer->getType(),
        'owner_id' => $viewer->getIdentity(),
      ));
      $values['params'] = $values['additional_params'];

      // Convert times
      if ($values['discount_expiry_date'] != '0000-00-00') {
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($oldTz);
        $discount_expiry_date = strtotime($values['discount_expiry_date']);
        $values['discount_expiry_date'] = date('Y-m-d H:i:s', $discount_expiry_date);
      } else {
        unset($values['discount_expiry_date']);
      }

      /**
       * @var $product Store_Model_Product
       */

      $product = $table->createRow();
      $product->setFromArray($values);

      if ($product->save()) {
        $product->createAlbum($values);
        if (!$product->isDigital()) {
          $product->createLocations();
        }

        // Auth
        $auth = Engine_Api::_()->authorization()->context;

        $auth->setAllowed($product, 'everyone', 'comment', 1);
        $auth->setAllowed($product, 'everyone', 'order', 1);
        $auth->setAllowed($product, 'everyone', 'view', 1);

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $product, 'store_product_new');

        if ($action) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $product);
        }
      }

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $tags = array_filter(array_map("trim", $tags));
      $product->tags()->addTagMaps($viewer, $tags);

      // Add fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($product);
      $customfieldform->saveValues();
      $customfieldform->removeElement('submit');

      // Commit
      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    if ($product->isDigital()) {
      $this->_redirect(
        array(
          'controller' => 'digital',
          'action' => 'edit-file',
          'product_id' => $product->getIdentity()
        )
      );
    }

    $this->_redirect(array(
      'controller' => 'product-locations',
      'product_id' => $product->getIdentity()
    ));
  }

  public function copyAction()
  {
    /**
     * @var $product        Store_Model_Product
     * @var $copied_product Store_Model_Product
     */
    $this->view->hasShippingLocations = Engine_Api::_()->getDbTable('locationships', 'store')->hasShippingLocations();
    $viewer = Engine_Api::_()->user()->getViewer();
    $copied_product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0));

    if ($copied_product === null || $copied_product->getStore() || !$copied_product->isOwner($viewer)) {
      $this->_redirect();
    }

    $this->view->form = $form = new Store_Form_Admin_Products_Copy(array('item' => $copied_product));
    $form->getDecorator('description')->setOption('escape', false);

    // Populate form
    $form->populate(array_merge($copied_product->toArray(), array('additional_params' => $copied_product->params)));
    $tagStr = '';
    foreach ($copied_product->tags()->getTagMaps() as $tagMap) {
      $tag = $tagMap->getTag();
      if (!isset($tag->text)) continue;
      if ('' !== $tagStr) $tagStr .= ', ';
      $tagStr .= $tag->text;
    }
    $form->populate(array(
      'tags' => $tagStr,
    ));
    $this->view->tagNamePrepared = $tagStr;

    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

    foreach ($roles as $role) {
      if ($form->auth_comment) {
        if ($auth->isAllowed($copied_product, $role, 'comment')) {
          $form->auth_comment->setValue($role);
        }
      }
    }

    // If not post or form not valid, return
    if (!$this->getRequest()->isPost()) {
      return;
    }

    $values = $this->getRequest()->getPost();
    $values['type'] = $copied_product->type;

    if (!$form->isValid($values)) {
      return;
    }

    $table = Engine_Api::_()->getDbtable('products', 'store');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      // Create product
      $values = array_merge($form->getValues(), array(
        'owner_type' => $viewer->getType(),
        'owner_id' => $viewer->getIdentity(),
      ));
      $values['params'] = $values['additional_params'];

      // Convert times
      if ($values['discount_expiry_date'] != '0000-00-00') {
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($oldTz);
        $discount_expiry_date = strtotime($values['discount_expiry_date']);
        $values['discount_expiry_date'] = date('Y-m-d H:i:s', $discount_expiry_date);
      } else {
        unset($values['discount_expiry_date']);
      }

      $product = $table->createRow();
      $product->setFromArray($values);

      if ($product->save()) {
        $product->createAlbum($values);
        if (!$product->isDigital()) {
          $product->createLocations();
        }

        if (empty($values['auth_comment'])) {
          $values['auth_comment'] = 'everyone';
        }

        $commentMax = array_search($values['auth_comment'], $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($product, $role, 'comment', ($i <= $commentMax));
        }

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $product, 'store_product_new');

        if ($action) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $product);
        }
      }


      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $tags = array_filter(array_map("trim", $tags));
      $product->tags()->addTagMaps($viewer, $tags);

      // Add fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($product);
      $customfieldform->saveValues();
      $customfieldform->removeElement('submit');

      // Commit
      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    if ($product->isDigital()) {
      $this->_redirect(
        array(
          'controller' => 'digital',
          'action' => 'edit-file',
          'product_id' => $product->getIdentity()
        )
      );
    }

    $this->_redirect(array(
      'controller' => 'product-locations',
      'product_id' => $product->getIdentity()
    ));
  }

  public function sponsorAction()
  {
    /**
     * @var $product Store_Model_Product
     */
    $value = $this->_getParam('value', null);
    $product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0));

    if ($product != null && $value !== null && !_ENGINE_ADMIN_NEUTER) {
      $product->sponsoredStatus($value);
    }
    $this->_redirect();
  }

  public function featureAction()
  {
    /**
     * @var $product Store_Model_Product
     */
    $value = $this->_getParam('value', null);
    $product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0));

    if ($product != null && $value !== null && !_ENGINE_ADMIN_NEUTER) {
      $product->featuredStatus($value);
    }
    $this->_redirect();
  }

  public function editAction()
  {
    /**
     * @var $product Store_Model_Product
     */
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->product = $product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id'));

    if (!Engine_Api::_()->core()->hasSubject('store_product')) {
      Engine_Api::_()->core()->setSubject($product);
    }

    if ($product === null || $product->getStore() || !$product->isOwner($viewer)) {
      $this->_redirect();
    }

    // Prepare form
    $this->view->form = $form = new Store_Form_Admin_Products_Edit(array('item' => $product));
    $form->getDecorator('description')->setOption('escape', false);

    $form->removeElement('file');

    // Populate form
    $form->populate(array_merge($product->toArray(), array('additional_params' => $product->params)));
    $tagStr = '';
    foreach ($product->tags()->getTagMaps() as $tagMap) {
      $tag = $tagMap->getTag();
      if (!isset($tag->text)) continue;
      if ('' !== $tagStr) $tagStr .= ', ';
      $tagStr .= $tag->text;
    }
    $form->populate(array(
      'tags' => $tagStr,
    ));
    $this->view->tagNamePrepared = $tagStr;

    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

    foreach ($roles as $role) {
      if ($form->auth_comment) {
        if ($auth->isAllowed($product, $role, 'comment')) {
          $form->auth_comment->setValue($role);
        }
      }
    }

    // Check post/form
    if (!$this->getRequest()->isPost()) {
      return;
    }

    $values = $this->getRequest()->getPost();
    $values['type'] = $product->type;

    if (!$form->isValid($values)) {
      return;
    }

    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();
      $values['params'] = $values['additional_params'];

      // Convert times
      if ($values['discount_expiry_date'] != '0000-00-00') {
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($oldTz);
        $discount_expiry_date = strtotime($values['discount_expiry_date']);
        $values['discount_expiry_date'] = date('Y-m-d H:i:s', $discount_expiry_date);
      } else {
        unset($values['discount_expiry_date']);
      }

      $product->setFromArray($values);
      $product->modified_date = date('Y-m-d H:i:s');

      $product->save();

      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($product);
      $customfieldform->saveValues();
      $customfieldform->removeElement('submit');

      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = 'everyone';
      }

      $commentMax = array_search($values['auth_comment'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($product, $role, 'comment', ($i <= $commentMax));
      }

      // handle tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $product->tags()->setTagMaps($viewer, $tags);

      // insert new activity if blog is just getting published
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($product);
      if (count($action->toArray()) <= 0) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $product, 'store_product_new');
        // make sure action exists before attaching the blog to the activity
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $product);
        }
      }

      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($product) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
      $mess = 'All changes have been successfully saved';
      $form->addNotice($mess);
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function editphotosAction()
  {
    /**
     * @var $view    User_Model_User
     * @var $product Store_Model_Product
     */

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->product = $product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id'));
    if (!Engine_Api::_()->core()->hasSubject('store_product')) {
      Engine_Api::_()->core()->setSubject($product);
    }
    if (!$product->isOwner($viewer)) return 0;

    // Prepare data
    $this->view->paginator = $paginator = $product->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(5);

    // Make form
    $this->view->form = $form = new Store_Form__Admin_Products_Photos();

    foreach ($paginator as $photo) {
      $subform = new Store_Form_Admin_Products_EditPhoto(array('elementsBelongTo' => $photo->getGuid()));
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->main->addMultiOption($photo->getIdentity(), $photo->getIdentity());
    }

    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $table = $product->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      /**
       * @var $photo Store_Model_Photo
       * @var $file  Storage_Model_File
       */
      $values = $form->getValues();
      if (
        !empty($values['main']) &&
        (null != ($photo = Engine_Api::_()->getItem('store_photo', $values['main']))) &&
        (null != ($file = Engine_Api::_()->getItem('storage_file', $photo->file_id)))
      ) {
        $product->photo_id = $photo->getIdentity();
        $product->save();
      }

      // Process
      foreach ($paginator as $photo) {
        $subform = $form->getSubForm($photo->getGuid());
        $values = $subform->getValues();

        $values = $values[$photo->getGuid()];
        unset($values['photo_id']);
        if (isset($values['delete']) && $values['delete'] == '1') {
          if ($photo->photo_id == $product->photo_id) {
            $product->photo_id = 0;
            $product->save();
          }
          $photo->delete();
        }
        else {
          $photo->setFromArray($values);
          $photo->save();
        }
      }

      $db->commit();

      $this->_redirect(
        array(
          'action' => 'editphotos',
          'product_id' => $product->getIdentity()
        )
      );
    }

    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function addphotosAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->product = $product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id'));

    if (!Engine_Api::_()->core()->hasSubject('store_product')) {
      Engine_Api::_()->core()->setSubject($product);
    }

    $this->view->form = $form = new Store_Form_Admin_Products_Upload();
    $form->getDecorator('description')->setOption('escape', false);

    if (!$product->isOwner($viewer)) return 0;

    if (!$this->getRequest()->isPost()) {
      return 0;
    }
    $ids = explode(' ', $this->_getParam('fancyuploadfileids'));

    $table = Engine_Api::_()->getItemTable('store_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();
    if (count($ids > 0)) {
      try {
        // Do other stuff
        $count = 0;
        foreach ($ids as $photo_id) {
          if ($photo_id == '') continue;
          $photo = Engine_Api::_()->getItem("store_photo", $photo_id);
          if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity()) continue;

          $photo->collection_id = $product->product_id;
          $photo->save();

          if ($product->photo_id == 0) {
            $product->photo_id = $photo->photo_id;
            $product->save();
          }

          $count++;
        }

        $db->commit();
        $this->_redirect(
          array(
            'action' => 'editphotos',
            'product_id' => $product->getIdentity()
          )
        );
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function deleteAction()
  {
    $product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0));

    if ($product != null && !_ENGINE_ADMIN_NEUTER) {
      $product->delete();
    }

    $this->_redirect();
  }

  public function multiModifyAction()
  {
    $ids = $this->_getParam('modify');
    if (!empty($ids) && !_ENGINE_ADMIN_NEUTER) {
      $productsTable = Engine_Api::_()->getDbTable('products', 'store');
      $productsTable->deleteProducts($ids);
    }
    $this->_redirect();
  }

  public function uploadphotosAction()
  {
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();

    if (empty($values['Filename'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');

      return;
    }

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'store')->getAdapter();
    $db->beginTransaction();

    try {
      /**
       * @var $viewer     User_Model_User
       * @var $photoTable Store_Model_DbTable_Photos
       * @var $photo      Store_Model_Photo
       */
      $viewer = Engine_Api::_()->user()->getViewer();
      $photoTable = Engine_Api::_()->getDbtable('photos', 'store');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
        'user_id' => $viewer->getIdentity()
      ));
      $photo->save();

      $photo->setPhoto($_FILES['Filedata']);

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo->photo_id;

      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      throw $e;
      return;
    }
  }

  public function removephotoAction()
  {
    $photo_id = (int)$this->_getParam('photo_id');
    if ($photo_id && !_ENGINE_ADMIN_NEUTER) {
      $photo = Engine_Api::_()->getItem('store_photo', $photo_id);
      $db = $photo->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        $storage = Engine_Api::_()->getItemTable('storage_file');
        $select = $storage->select()
          ->where('parent_file_id = ?', $photo->file_id);

        if (($file = $storage->fetchRow($select)) !== null) {
          $file->delete();
        }
        Engine_Api::_()->getApi('core', 'store')->deleteFile($photo->file_id);
        $photo->delete();
        $db->commit();
      }
      catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  protected function _redirect($params = array())
  {
    $params = array_merge(array(
      'module' => 'store',
      'controller' => 'products'
    ), $params);

    $this->_redirectCustom($this->view->url($params, 'admin_default', true));
  }
}