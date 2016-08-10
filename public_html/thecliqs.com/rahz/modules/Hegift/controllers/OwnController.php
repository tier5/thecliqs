<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: OwnController.php 27.02.12 17:35 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_OwnController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hegift_main', array(), 'hegift_main_own');

    if( $gift_type = $this->getRequest()->getQuery('gift_type', false) ) {
      return $this->_forward('upload', null, null, array('format' => 'json', 'type' => $gift_type));
    }

    $this->view->action = $this->_getParam('action');
  }

  public function indexAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->type = $this->_getParam('type', '');
    $this->view->photo_price = $settings->getSetting('hegift.photo.credits', 50);
    $this->view->audio_price = $settings->getSetting('hegift.audio.credits', 80);
    $this->view->video_price = $settings->getSetting('hegift.video.credits', 100);
  }

  public function createAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Gifts
     * @var $gift Hegift_Model_Gift
     */

    $this->view->type = $type = $this->_getParam('type', '');
    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;

    if (empty($type) || ($type == 'video' && !$ffmpeg_path)) {
      $this->redirector(array('type' => $type));
    }

    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $this->view->form = $form = new Hegift_Form_Create(array('type' => $type));
    $form->getDecorator('Description')->setOption('escape', false);

    if (!$this->getRequest()->isPost()) {
      return 0;
    }

    $values = $this->getRequest()->getPost();

    if (!$form->isValid($values)) {
      $form->populate($values);
      return 0;
    }

    if (empty($values['fancyupload'.$type])) {
      $form->populate($values);
      $form->addError('You have not upload a(an) '.$type);
      return 0;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $db = $table->getAdapter();
    $db->beginTransaction();

    $gift = $table->createRow();

    try {
      $gift->setFromArray($values);
      $gift->type = ($type == 'video') ? 3 : (($type == 'audio') ? 2 : 1);
      $gift->creation_date = new Zend_Db_Expr('NOW()');
      $gift->owner_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $gift->credits = $settings->getSetting('hegift.'.$type.'.credits', ($type == 'video') ? 100 : (($type == 'audio') ? 80 : 60));
      $gift->amount = 0;
      $gift->save();

      if ($type == 'photo') {
        $gift->setPhoto((int)$values['fancyuploadphoto']);
      } elseif ($type == 'audio') {
        $gift->setAudio((int)$values['fancyuploadaudio']);
        if ($form->photo->getValue()) {
          $gift->setPhoto($form->photo);
        }
      } elseif ($type == 'video') {
        $gift->setVideo((int)$values['fancyuploadvideo']);
        if ($form->photo->getValue()) {
          $gift->setPhoto($form->photo);
        }
      }

     $db->commit();
      $form->addNotice('Gift successfully created');
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirector(array('action' => 'select-send', 'gift_id' => $gift->getIdentity()));
  }

  public function uploadAction()
  {
    $type = $this->_getParam('type', '');
    if (!$type) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();

    if( empty($values['Filename']) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');

      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    if( !$this->_helper->requireUser()->checkRequire() ) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Max file size limit exceeded or session expired.');
      return;
    }

    $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
    if( in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions) )
    {
      $this->view->status = false;
      $this->view->error  = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    /**
     * @var $api Hegift_Api_Core
     */
    try {
      $api = Engine_Api::_()->hegift();

      if ($type == 'photo') {
        $photo_id = $api->setPhoto($_FILES['Filedata']);
        $this->view->status = true;
        $this->view->name = $_FILES['Filedata']['name'];
        $this->view->photo_id = $photo_id;

      } elseif ($type == 'audio') {
        $file = $api->setAudio($_FILES['Filedata']);
        $this->view->status   = true;
        $this->view->file     = $file;
        $this->view->file_id  = $file->getIdentity();

      } elseif ($type == 'video') {
        $video = $api->setVideo($_FILES['Filedata']);
        $this->view->status   = true;
        $this->view->file_id  = $video->file_id;
      }

      return;

    } catch (Exception $e) {
      throw $e;
    }
  }

  public function videoAction()
  {
    /**
     * @var $gift Hegift_Model_Gift
     */

    $this->_helper->layout->setLayout('default-simple');
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));

    if (!$this->checkGift($gift)) {
      $this->view->message = 'Invalid data or gift doesn\'t exist';
      return ;
    }

    if ($gift->status != 1) {
      $this->view->message = 'Video is not encoded yet, encoding will begin only after sending. If you have sent gift, please wait...';
      return ;
    }

    $file_id = $gift->file_id;

    if( !empty($file_id) ) {
      $storage_file = Engine_Api::_()->getItem('storage_file', $file_id);
      if( $storage_file ) {
        $this->view->video_location = $storage_file->map();
      }
    }
  }

  public function audioAction()
  {
    $this->_helper->layout->setLayout('default-simple');
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));
    $file_id = $gift->file_id;

    if (!$this->checkGift($gift)) {
      $this->view->message = 'Invalid data or gift doesn\'t exist';
      return ;
    }

    if( !empty($file_id) ) {
      $storage_file = Engine_Api::_()->getItem('storage_file', $file_id);
      if( $storage_file ) {
        $this->view->audio_location = $storage_file->map();
      }
    }
  }

  public function photoAction()
  {
    $this->_helper->layout->setLayout('default-simple');
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));

    if (!$this->checkGift($gift)) {
      $this->view->message = 'Invalid data or gift doesn\'t exist';
      return ;
    }
  }

  public function selectSendAction()
  {
    /**
     * @var $gift Hegift_Model_Gift
     */

    $gift_id = $this->_getParam('gift_id', 0);
    $this->view->gift = $gift = Engine_Api::_()->getItem('gift', $gift_id);
    $this->view->type = $gift->getTypeName();
    if (!$gift || $gift->isSent()) {
      return $this->redirector();
    }
  }

  public function removeAction()
  {
    $file_id = $this->_getParam('file_id', 0);
    if ($file_id && (null != ($file = Engine_Api::_()->getItem('storage_file', $file_id)))) {
      $file->remove();
    }
  }

  protected function redirector($params = array(), $route = 'hegift_own')
  {
    $this->_redirectCustom($this->view->url($params, $route, true));
  }

  protected function checkGift($gift)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$gift || $viewer->getIdentity() != $gift->owner_id) {
      return false;
    }
    return true;
  }
}
