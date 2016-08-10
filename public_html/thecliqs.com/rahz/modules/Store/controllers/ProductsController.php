<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SettingsController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_ProductsController extends Store_Controller_Action_User
{
  protected $params;

  public function init()
  {
    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
      $this->_forward('upload', null, null, array('format' => 'json'));
    }

    if (isset($_GET['rm'])) {
      $this->_forward('remove', null, null, array('format' => 'json'));
    }
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
      !$page->getStorePrivacy()
      //    !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
    ) {
      $this->_redirectCustom($page->getHref());
    }

    $this->params = array(
      'page_id' => $page->getIdentity(),
      'ipp' => 5,
      'p' => $this->_getParam('p', 1),
      'order' => 'DESC');
    /**
     * @var $api Store_Api_Page
     */
    $api = Engine_Api::_()->getApi('page', 'store');
    $this->view->navigation = $api->getNavigation($page, 'products');
    $this->view->storeSettings = 1;
  }

  public function indexAction()
  {
    $this->params['owner'] = true;
    $this->params['order'] = 'DESC';
    $table = $this->getTable();

    $this->view->products = $table->getProducts($this->params);

    if ($this->_getParam('format') == 'json') {
      $this->view->html = $this->view->render('_store_list_edit.tpl');
    }
  }

  public function createAction()
  {
    $page_id = $this->view->page->getIdentity();
    $this->_checkRequiredSettings();
    $this->view->form = $form = new Store_Form_Page_Products_Create();
    $form->getDecorator('description')->setOption('escape', false);

    $viewer = Engine_Api::_()->user()->getViewer();
    $form->populate(array('page_id' => $page_id));

    // If not post or form not valid, return
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $table = $this->getTable();
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

      if (!Engine_Api::_()->store()->isStoreCreditEnabled()) {
        if (isset($values['via_credits'])) {
          unset($values['via_credits']);
        }
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

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $this->view->page, 'page_product_new');

        if ($action) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $product);
        }

        $search_api = Engine_Api::_()->getDbTable('search', 'page');
        $search_api->saveData($product);
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
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    if ($product->isDigital()) {
      $this->_redirectCustom(
        $this->view->url(
          array(
            'controller' => 'digital',
            'action' => 'edit-file',
            'product_id' => $product->getIdentity()
          ), 'store_extended', true
        )
      );
    }
    $this->_redirectCustom(
      $this->view->url(
        array(
          'product_id' => $product->getIdentity()
        ), 'store_product_locations', true
      )
    );
  }

  public function editAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->product = $product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id'));

    if (!$product->isOwner($viewer) && !$this->view->page->isOwner($viewer)) {
      $this->_redirectCustom($this->view->url(array('page_id' => $this->view->page->getIdentity()), 'store_products', true));
    }

    // Prepare form
    $this->view->form = $form = new Store_Form_Page_Products_Edit(array('item' => $product));
    $form->getDecorator('description')->setOption('escape', false);

    $form->removeElement('file');
    // Populate form
    $form->populate(array_merge($product->toArray(), array('additional_params' => $product->params)));
    $tagStr = '';
    foreach ($product->tags()->getTagMaps() as $tagMap) {
      $tag = $tagMap->getTag();
      if (!isset($tag->text)) {
        continue;
      }
      if ('' !== $tagStr) {
        $tagStr .= ', ';
      }
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

      if (!Engine_Api::_()->store()->isStoreCreditEnabled()) {
        if (isset($values['via_credits'])) {
          unset($values['via_credits']);
        }
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
      $actions = $product->getActions();
      if (is_object($actions) && count($actions->toArray()) <= 0) {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $product->getPage(), 'page_product_new');
        // make sure action exists before attaching the blog to the activity
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $product);
        }
      }

      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actions as $action) {
        $actionTable->resetActivityBindings($action);
      }

      $search_api = Engine_Api::_()->getDbTable('search', 'page');
      $search_api->saveData($product);

      $db->commit();

      $mess = 'STORE_All changes have been successfully saved';
      $form->addNotice($mess);
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function copyAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $copied_product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0));
    $page_id = $this->view->page->getIdentity();
    if ($copied_product === null) {
      $this->_redirectCustom($this->view->url(array('page_id' => $page_id), 'store_products', true));
    }

    if (!$copied_product->isOwner($viewer) && !$this->view->page->isOwner($viewer)) {
      $this->_redirectCustom($this->view->url(array('page_id' => $page_id), 'store_products', true));
    }

    $this->_checkRequiredSettings();

    $this->view->form = $form = new Store_Form_Page_Products_Copy(array('item' => $copied_product));
    $form->getDecorator('description')->setOption('escape', false);

    // Populate form
    $form->populate(array_merge($copied_product->toArray(), array('additional_params' => $copied_product->params)));
    $tagStr = '';
    foreach ($copied_product->tags()->getTagMaps() as $tagMap) {
      $tag = $tagMap->getTag();
      if (!isset($tag->text)) {
        continue;
      }
      if ('' !== $tagStr) {
        $tagStr .= ', ';
      }
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

      if (!Engine_Api::_()->store()->isStoreCreditEnabled()) {
        if (isset($values['via_credits'])) {
          unset($values['via_credits']);
        }
      }

      /**
       * @var $product Store_Model_Product
       */
      unset($values['product_id']);

      $product = $table->createRow();
      $product->setFromArray($values);

      if ($product->save()) {
        $product->createAlbum($values);
        if (!$product->isDigital()) {
          $product->createLocations();
        }

        // Auth
        if (empty($values['auth_view'])) {
          $values['auth_view'] = 'everyone';
        }

        if (empty($values['auth_comment'])) {
          $values['auth_comment'] = 'everyone';
        }

        $commentMax = array_search($values['auth_comment'], $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($product, $role, 'comment', ($i <= $commentMax));
        }

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $product->getPage(), 'page_product_new');
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

      $search_api = Engine_Api::_()->getDbTable('search', 'page');
      $search_api->saveData($product);

      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    if ($product->isDigital()) {
      $this->_redirectCustom(
        $this->view->url(
          array(
            'controller' => 'digital',
            'action' => 'edit-file',
            'product_id' => $product->getIdentity()
          ), 'store_extended', true
        )
      );
    }
    $this->_redirectCustom(
      $this->view->url(
        array(
          'product_id' => $product->getIdentity()
        ), 'store_product_locations', true
      )
    );
  }

  public function deleteAction()
  {
    $product_id = $this->_getParam('product_id', 0);
    $product = Engine_Api::_()->getItem('store_product', $product_id);
    $this->_helper->layout->setLayout('default-simple');
    $this->view->form = new Store_Form_Page_Products_Delete();

    if (!$product) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Product doesn't exists or not authorized to delete");
      return;
    }

    if (!$product->isOwner($this->view->viewer) && !$this->view->page->isOwner($this->view->viewer)) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Product doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $product->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $search_api = Engine_Api::_()->getDbTable('search', 'page');
      $search_api->deleteData($product);
      $product->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Product has been deleted.');
    $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()
        ->getRouter()
        ->assemble(
        array(
          'page_id' => $this->view->page->getIdentity()
        ),
        'store_products', true
      ),
      'messages' => Array($this->view->message)
    ));
  }

  /**
   * @return Store_Model_DbTable_Products
   */
  protected function getTable()
  {
    $table = Engine_Api::_()->getDbTable('products', 'store');
    return $table;
  }

  public function uploadAction()
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
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      throw $e;
      return;
    }
  }

  public function removeAction()
  {
    $photo_id = (int)$this->_getParam('photo_id');
    if ($photo_id) {
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
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  protected function _checkRequiredSettings()
  {
    $error = false;
    $page_id = $this->view->page->getIdentity();
    $this->view->gatewaysEnabled = $gatewaysEnabled = (boolean)(Engine_Api::_()->getDbTable('apis', 'store')->getEnabledGatewayCount($page_id) || Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('credit'));
    if (!$gatewaysEnabled) {
      $error = true;
    }
    $this->view->hasShippingLocations = $hasShippingLocations = Engine_Api::_()->getDbTable('locationships', 'store')->hasShippingLocations($page_id);
    if (!$hasShippingLocations) {
      $error = true;
    }
    $this->view->error = $error;
  }
}