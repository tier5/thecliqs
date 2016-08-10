<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: VideoController.php 19.09.11 16:57 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_VideoController extends Store_Controller_Action_User
{
  public function init()
  {
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
      !( $page->getStorePrivacy() || $product->isOwner($viewer))
//      !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid()
    ) {
      $this->_redirectCustom($page->getHref());
    }

    $this->view->hasVideo = $product->hasVideo();
    $api = Engine_Api::_()->getApi('page', 'store');
    $this->view->navigation = $api->getNavigation($page);
  }

  public function editAction()
  {
    $product = $this->view->product;
    $viewer = $this->view->viewer;

    if (!$this->view->hasVideo) {
      $this->redirect('create');
    }

    $this->view->video = $video = $product->getVideo();

    // Make form
    $this->view->form = $form = new Store_Form_Admin_Video_Edit();
    $form->populate($video->toArray());

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $table = $video->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      // Process

      /**
       * @var $api Store_Api_Core
       * */

      $values = $form->getValues();
      if ($video->url != $values['url']) {
        $api = Engine_Api::_()->getApi('core', 'store');
        $api->deleteVideo($video);
        $video = $table->createRow();
      }

      $video->setFromArray($values['']);
      $video->product_id = (int)$this->_getParam('product_id');
      $video->owner_id = $viewer->getIdentity();
      $video->save();

      $api->createThumbnail($video);
      $db->commit();
      $this->redirect('edit');
    }

    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function createAction()
  {
    $viewer = $this->view->viewer;
    $product = $this->view->product;

    if ($this->view->hasVideo) {
      $this->redirect('edit');
    }

    $this->view->video = $product->getVideo();

    // Create form
    $this->view->form = $form = new Store_Form_Admin_Video_Upload();

    $form->getDecorator('description')->setOption('escape', false);
    if ($this->_getParam('type', false)) $form->getElement('type')->setValue($this->_getParam('type'));

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->_getAllParams())) {
      $values = $form->getValues('url');
      return;
    }

    // Process
    $values = $form->getValues();

    $table = Engine_Api::_()->getDbtable('videos', 'store');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      // Create video
      $video = $table->createRow();

      $video->setFromArray($values);
      $video->product_id = (int)$this->_getParam('product_id');
      $video->owner_id = $viewer->getIdentity();
      $video->status = 1;
      $video->save();

      Engine_Api::_()->getApi('core', 'store')->createThumbnail($video);

      $db->commit();
      $this->redirect('edit');
    }

    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function deleteAction()
  {
    $product = $this->view->product;
    $this->view->video = $video = $product->getVideo();

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $this->view->form = new Store_Form_Admin_Video_Delete();

    if (!$video) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $video->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      Engine_Api::_()->getApi('core', 'store')->deleteVideo($video);
      $db->commit();
    }

    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been deleted.');
    $this->_forward('success', 'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()
        ->getRouter()
        ->assemble(
        array(
          'controller' => 'video',
          'action' => 'create',
          'product_id' => $product->getIdentity()
        ),
        'store_extended', true
      ),
      'messages' => Array($this->view->message)
    ));
  }

  public function validationAction()
  {
    $video_type = $this->_getParam('type');
    $code = $this->_getParam('code');
    $ajax = $this->_getParam('ajax', false);
    $valid = false;

    // check which API should be used
    if ($video_type == "youtube") {
      $valid = $this->checkYouTube($code);
    }
    if ($video_type == "vimeo") {
      $valid = $this->checkVimeo($code);
    }

    $this->view->code = $code;
    $this->view->ajax = $ajax;
    $this->view->valid = $valid;
  }

  // YouTube Functions
  public function checkYouTube($code)
  {
    if (!$data = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/" . $code)) return false;
    if ($data == "Video not found") return false;
    return true;
  }

  // Vimeo Functions
  public function checkVimeo($code)
  {
    //http://www.vimeo.com/api/docs/simple-api
    //http://vimeo.com/api/v2/video
    $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
    $id = count($data->video->id);
    if ($id == 0) return false;
    return true;
  }

  private function redirect($action)
  {
    $this->_redirectCustom(
      $this->view->url(
        array(
          'controller' => 'video',
          'action' => $action,
          'product_id' => $this->view->product->getIdentity()
        ),
        'store_extended', true
      )
    );
  }
}