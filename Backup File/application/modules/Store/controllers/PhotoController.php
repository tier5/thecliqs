<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: PhotoController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_PhotoController extends Store_Controller_Action_User
{
  public function init()
  {
    if ($this->getRequest()->getQuery('ul', false)) {
      $this->_forward('upload', null, null, array('format' => 'json'));
    }
    if ($this->getRequest()->getQuery('rm', false)) {
      $this->_forward('remove', null, null, array('format' => 'json'));
    }

    /**
     * @var $product Store_Model_Product
     */
    if (null != ( $product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0)))) {
      Engine_Api::_()->core()->setSubject($product);
    }

    //Set Requires
    $this->_helper->requireSubject('store_product')->isValid();

    $this->view->product = $product = Engine_Api::_()->core()->getSubject('store_product');
    $this->view->page = $page = $product->getStore();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //he@todo check admin settings
    if (
      !$page->isAllowStore() ||
      !( $page->getStorePrivacy() || $product->isOwner($viewer))
      //      !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
    ) {
      $this->_redirectCustom($page->getHref());
    }

    $api = Engine_Api::_()->getApi('page', 'store');
    $this->view->navigation = $api->getNavigation($page);
  }

  public function editAction()
  {
    $viewer = $this->view->viewer;
    $product = $this->view->product;


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
      $this->_redirectCustom($this->view->url(array(
          'controller' => 'photo',
          'action' => 'edit',
          'product_id' => $product->getIdentity()),
        'store_extended', true));
    }

    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function addAction()
  {
    /**
     * @var $product Store_Model_Product
     */
    $product = $this->view->product;

    $this->view->form = $form = new Store_Form_Page_Products_Upload();
    $form->getDecorator('description')->setOption('escape', false);

    if (!$this->getRequest()->isPost()) {
      return;
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
        $this->_redirectCustom($this->view->url(array(
            'controller' => 'photo',
            'action' => 'edit',
            'product_id' => $product->getIdentity()),
          'store_extended', true));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
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
    }
    catch (Exception $e) {
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
      }
      catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }
}