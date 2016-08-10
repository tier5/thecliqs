<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DigitalController.php 21.09.11 15:48 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_DigitalController extends Store_Controller_Action_User
{
  public function init()
  {
    if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
      $this->_forward('upload-file', null, null, array('format' => 'json'));
    }

    if (isset($_GET['rm'])) {
      $this->_forward('remove-file', null, null, array('format' => 'json'));
    }

    /**
     * @var $product Store_Model_Product
     */
    if (null != ($product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0)))) {
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
//      !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
      !( $page->getStorePrivacy() || $product->isOwner($viewer))
    ) {
      $this->_redirectCustom($page->getHref());
    }

    $api = Engine_Api::_()->getApi('page', 'store');
    $this->view->navigation = $api->getNavigation($page);

    if (!$product->hasFile() && $this->_getParam('action') == 'edit-file') {
      $this->_redirectCustom(
        $this->view->url(
          array(
            'controller' => 'digital',
            'action' => 'create-file',
            'product_id' => $product->getIdentity()
          ), 'store_extended', true
        )
      );
    } elseif ($product->hasFile() && $this->_getParam('action') == 'create-file') {
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
  }

  public function createFileAction()
  {
    $product = $this->view->product;
    $this->view->form = $form = new Store_Form_Admin_Digital_Create();

    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Api::_()->getDbTable('products', 'store')->getAdapter();
    $db->beginTransaction();
    try {
      $form->saveValues($product);
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }

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

  public function editFileAction()
  {
    $product = $this->view->product;
    $this->view->file = $product->getFile();
  }

  public function deleteFileAction()
  {
    $this->_helper->layout->setLayout('default-simple');
    $this->view->form = new Store_Form_Admin_Digital_Delete();
    $file = $this->view->product->getFile();

    if (!$file) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("File doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if ($file) {
      $table = Engine_Api::_()->getDbTable('products', 'store');
      $db = $table->getAdapter();
      $db->beginTransaction();
      try {
        Engine_Api::_()->getApi('core', 'store')->deleteFile($file->file_id);
        $db->commit();
      }
      catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->view->message = Zend_Registry::get('Zend_Translate')->_('File has been deleted.');
      $this->_forward('success', 'utility', 'core', array(
        'parentRedirect' => Zend_Controller_Front::getInstance()
          ->getRouter()
          ->assemble(
          array(
            'controller' => 'digital',
            'action' => 'create-file',
            'product_id' => $this->view->product->getIdentity()
          ),
          'store_extended', true
        ),
        'messages' => Array($this->view->message)
      ));
    }
  }

  public function removeFileAction()
  {
    $file_id = $this->_getParam('file_id', 0);
    Engine_Api::_()->getApi('core', 'store')->deleteFile($file_id);
  }

  public function uploadFileAction()
  {
    // Check method
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid request method');
      return;
    }

    // Check file
    $values = $this->getRequest()->getPost();
    if (empty($values['Filename']) || empty($_FILES['Filedata'])) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('No file');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('products', 'store')->getAdapter();
    $db->beginTransaction();

    try {
      $file = Engine_Api::_()->getApi('core', 'store')->createFile($_FILES['Filedata']);
      $this->view->status = true;
      $this->view->file = $file;
      $this->view->file_id = $file->getIdentity();
      $this->view->file_url = $file->getHref();
      $db->commit();

    } catch (Storage_Model_Exception $e) {
      $db->rollback();

      $this->view->status = false;
      $this->view->message = $this->view->translate($e->getMessage());

    } catch (Exception $e) {
      $db->rollback();

      $this->view->status = false;
      $this->view->message = $this->view->translate('Upload failed by database query');

      throw $e;
    }
  }
}