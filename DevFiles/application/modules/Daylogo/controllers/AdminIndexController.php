<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexController.php 2012-08-16 16:18 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Daylogo_AdminIndexController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('daylogo_admin_main', array(), 'daylogo_admin_main_index');
  }

  public function indexAction()
  {
    $daylogo = Engine_Api::_()->getDbTable('content', 'core')->select()
      ->where('name = ?', 'daylogo.day-logo')
      ->query()
      ->fetch();
    $params = Zend_Json::decode($daylogo['params']);
    Engine_Api::_()->getDbTable('logos', 'daylogo')->getDaylogo($params);

    $page = $this->_getParam('page', 1);

    $this->view->paginator = Engine_Api::_()->daylogo()->getLogoPaginator();
    $ipp = Engine_Api::_()->getApi('settings', 'core')->getSetting('daylogo.logosperpage');
    $this->view->paginator->setItemCountPerPage($ipp);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  public function createAction()
  {
    $this->view->photo_id = $photo_id = $this->_getParam('logo_fileid');
    $this->view->result = false;
    $logo_id = (int)$this->_getParam('id');
    $this->view->message = $this->view->translate(($logo_id) ? 'DAYLOGO_EDIT_ERROR' : 'DAYLOGO_CREATE_ERROR');
    $this->view->html = $this->view->render('message.tpl');

    $this->view->form = $form = new Daylogo_Form_Admin_Create();
    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    /**
     * @var Daylogo_Model_DbTable_Logos
     */
    $logoTable = $this->getTable();

    $logo = null;

    if ($logo_id) {
      $logo = $logoTable->findRow($logo_id);
      if (!$logo) {
        return;
      }
    }
    if (!$photo_id and $logo_id == 0) {
      $this->view->message = $this->view->translate('DAYLOGO_LOGOERROR');
      $this->view->html = $this->view->render('message.tpl');
      return;
    }
    if (!$photo_id and $logo_id > 0) {
      $storage = Engine_Api::_()->storage();
      $file = $storage->get($logo->photo_id);
      if (!is_object($file) or is_object($file) and !file_exists($file->storage_path)) {
        $this->view->message = $this->view->translate('DAYLOGO_LOGOERROR');
        $this->view->html = $this->view->render('message.tpl');
        return;
      }
    }

    $values = $form->getValues();
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    if ($start > $end) {
      $this->view->message = $this->view->translate('DAYLOGO_DATEERROR');
      $this->view->html = $this->view->render('message.tpl');
      return;
    }

    $values['start_date'] = date('Y-m-d H:i:s', $start);
    $values['end_date'] = date('Y-m-d H:i:s', $end);

    $db = $logoTable->getAdapter();
    $db->beginTransaction();
    try {
      if (!$logo) {
        if ($logoTable->checkStartTime($start) > 0) {
          $this->view->message = $this->view->translate('DAYLOGO_STARTDATE_ERROR');
          $this->view->html = $this->view->render('message.tpl');
          return;
        }
        $logo = $logoTable->createRow();
        $logo->setFromArray($values);
        $logo->creation_date = $logo->modified_date = date('Y-m-d H:i:s');
      } else {
        $logo->setFromArray($values);
        $logo->modified_date = date('Y-m-d H:i:s');
      }
      $logo->save();

      if ($photo_id) {
        if ($logo->photo_id) {
          Engine_Api::_()->getApi('core', 'daylogo')->deletePhoto($logo->photo_id);
        }
        if ($photo = Engine_Api::_()->storage()->get($photo_id)) {
          $logo->setLogo($photo->getIdentity());
        }
      }

      $db->commit();

      $this->view->result = true;
      $this->view->message = $this->view->translate(($logo_id) ? 'DAYLOGO_EDIT_SUCCESS' : 'DAYLOGO_CREATE_SUCCESS');
      $this->view->html = $this->view->render('message.tpl');
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function uploadPhotoAction()
  {
    $error_msg = $this->view->translate('DAYLOGO_UNKNOWN_ERROR');

    if (!$this->getRequest()->isPost() || !$this->getRequest()->getParam('Filename')) {
      $this->view->status = false;
      $this->view->error = $error_msg;
      return;
    }
    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = $error_msg;
    }

    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $logo = Engine_Api::_()->getApi('core', 'daylogo')->uploadPhoto($_FILES['Filedata']);

      $this->view->status = true;
      $this->view->photo_id = $logo->getIdentity();
      $this->view->photo = $logo->toArray();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $error_msg;
      throw $e;
    }

  }

  public function removePhotoAction()
  {
    $error_msg = $this->view->translate('DAYLOGO_UNKNOWN_ERROR');

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $error_msg;
    }

    $photo_id = $this->_getParam('photo_id');
    if (!$photo_id) {
      return;
    }

    $db = $this->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      Engine_Api::_()->getApi('core', 'daylogo')->deletePhoto($photo_id);
      $this->view->status = true;
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $error_msg;
      throw $e;
    }

  }

  protected function getTable()
  {
    return Engine_Api::_()->getDbTable('logos', 'daylogo');
  }

}