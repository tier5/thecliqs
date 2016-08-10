<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminAudiosController.php 08.09.11 17:54 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminAudiosController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    /**
     * @var $product Store_Model_Product
     */

    if ($this->getRequest()->getQuery('ul', false)) {
      $this->_forward('upload', null, null, array('format' => 'json'));
    }

    if ($this->getRequest()->getQuery('rm', false)) {
      $this->_forward('remove', null, null, array('format' => 'json'));
    }

    $this->view->product = $product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id'));

    if (!Engine_Api::_()->core()->hasSubject('store_product')) {
      Engine_Api::_()->core()->setSubject($product);
    }

    if (!$product->isOwner($viewer)) return;

    $this->view->menu = $this->_getParam('action');
  }

  public function editAudioAction()
  {
    $this->view->storage = Engine_Api::_()->storage();
    $this->view->audios = $audios = Engine_Api::_()->getDbTable('audios', 'store')->getAudios($this->view->product->getIdentity());

    if (!count($audios)) {
      $this->_redirectCustom(
        $this->view->url(
          array(
            'module' => 'store',
            'controller' => 'audios',
            'action' => 'create-audio',
            'product_id' => $this->view->product->getIdentity()
          ),
          'admin_default', true
        )
      );
    }
  }

  public function createAudioAction()
  {
    $product = $this->view->product;
    $this->view->audios = Engine_Api::_()->getDbTable('audios', 'store')->getAudios($this->view->product->getIdentity());
    $this->view->form = $form = new Store_Form_Admin_Audios_Create();

    if (!$this->getRequest()->isPost()) {
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Api::_()->getDbTable('audios', 'store')->getAdapter();
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
          'module' => 'store',
          'controller' => 'audios',
          'action' => 'edit-audio',
          'product_id' => $product->getIdentity()
        ),
        'admin_default', true
      )
    );
  }

  public function uploadAction()
  {
    // only members can upload audio
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Max file size limit exceeded or session expired.');
      return;
    }

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
    $db = Engine_Api::_()->getDbtable('audios', 'store')->getAdapter();
    $db->beginTransaction();

    try {
      $file = Engine_Api::_()->getApi('core', 'store')->createAudio($_FILES['Filedata']);
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

  public function deleteAction()
  {
    $audio_id = (int)$this->_getParam('audio_id');
    $this->_helper->layout->setLayout('default-simple');
    $this->view->form = new Store_Form_Admin_Audios_Delete();
    $audio = Engine_Api::_()->getDbTable('audios', 'store')->findRow($audio_id);

    if (!$audio) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Audio doesn't exists or not authorized to delete");
      return 0;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return 0;
    }

    $db = $audio->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      Engine_Api::_()->getApi('core', 'store')->deleteAudio($audio);
      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()
        ->getRouter()
        ->assemble(
        array(
          'module' => 'store',
          'controller' => 'audios',
          'action' => 'edit-audio',
          'product_id' => $this->view->product->getIdentity()
        ),
        'admin_default', true
      ),
      'messages' => Array(Zend_Registry::get('Zend_Translate')->_('Audio has been deleted.'))
    ));
  }

  public function removeAction()
  {
    $file_id = $this->_getParam('file_id', 0);
    Engine_Api::_()->getApi('core', 'store')->deleteFile($file_id);
  }
}