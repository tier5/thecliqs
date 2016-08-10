<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminManageController.php 2012-08-16 16:19 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Daylogo_AdminManageController extends Core_Controller_Action_Admin
{

  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('daylogo_admin_main', array(), 'daylogo_admin_main_index');
  }

  public function enableAction()
  {
    $this->view->result = false;
    $this->view->message = $this->view->translate('DAYLOGO_ENABLE_ERROR');
    $this->view->html = $this->view->render('message.tpl');

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $values = $this->getRequest()->getPost();
    $logo_id = $values['logo_id'];
    if (!$logo_id) {
      return;
    }

    $logo = Engine_Api::_()->getItem('logo', $logo_id);

    if (!$logo) {
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $logo->enabled = 1;
      $logo->save();
      $db->commit();

      $this->view->result = true;
      $this->view->message = $this->view->translate('DAYLOGO_ENABLE_SUCCESS');
      $this->view->html = $this->view->render('message.tpl');
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function disableAction()
  {
    $this->view->result = false;
    $this->view->message = $this->view->translate('DAYLOGO_DISABLE_ERROR');
    $this->view->html = $this->view->render('message.tpl');

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $values = $this->getRequest()->getPost();
    $logo_id = $values['logo_id'];
    if (!$logo_id) {
      return;
    }

    $logo = Engine_Api::_()->getItem('logo', $logo_id);
    if (!$logo) {
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $logo->setFromArray(array(
        'enabled' => 0,
        'active' => 0
      ));
      $logo->save();

      $db->commit();

      $this->view->result = true;
      $this->view->message = $this->view->translate('DAYLOGO_DISABLE_SUCCESS');
      $this->view->html = $this->view->render('message.tpl');
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function editAction()
  {
    $this->view->result = false;
    $user = Engine_Api::_()->user()->getViewer();

    if (!$this->_helper->requireUser()->isValid()) return;
    $logo_id = $this->_getParam('logo_id');

    $this->view->form = new Daylogo_Form_Admin_Create();
    if (!$logo_id) {
      return;
    }
    $table = Engine_Api::_()->getDbTable('logos', 'daylogo');
    $logo = $table->findRow($logo_id);
    if (!$logo) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'create'), 'daylogo_admin_index', true);
    }

    // Convert and re-populate times
    $start = strtotime($logo->start_date);
    $end = strtotime($logo->end_date);
    $start = date('Y-m-d H:i:s', $start);
    $end = date('Y-m-d H:i:s', $end);

    $this->view->result = true;
    $this->view->logo_id = $logo_id;
    $logo->start_date = $start;
    $logo->end_date = $end;
    $this->view->logo_info = $logo->toArray();

    $this->view->photo = Engine_Api::_()->storage()->get($logo->photo_id);
    $this->view->photo_html = $this->view->render('edit_photo.tpl');
  }

  public function removeAction()
  {
    $this->view->result = false;
    $this->view->message = $this->view->translate('DAYLOGO_REMOVE_ERROR');
    $this->view->html = $this->view->render('message.tpl');

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $values = $this->getRequest()->getPost();
    $logo_id = $values['logo_id'];

    if (!$logo_id) {
      return;
    }

    $table = Engine_Api::_()->getDbTable('logos', 'daylogo');
    $logo = $table->findRow($logo_id);

    if (!$logo) {
      return;
    }

    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $storage = Engine_Api::_()->storage();
      $photo = $storage->get($logo->photo_id);
      Engine_Api::_()->daylogo()->deletePhoto($photo->getIdentity());
      $logo->delete();
      $db->commit();

      $this->view->result = true;
      $this->view->message = $this->view->translate('DAYLOGO_REMOVE_SUCCESS');
      $this->view->html = $this->view->render('message.tpl');
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  public function previewAction()
  {
    $logo_id = $this->_getParam('logo_id');
    if (!$logo_id) {
      return;
    }

    $table = Engine_Api::_()->getDbTable('logos', 'daylogo');
    $logo = $table->findRow($logo_id);
    $storage = Engine_Api::_()->getDbTable('files', 'storage');
    ;
    $photo_original = $storage->getFile($logo->photo_id, 'thumb.original');
    $photo_normal = $storage->getFile($photo_original->getIdentity(), 'thumb.normal');
    if (!$logo) {
      return;
    }

    $this->view->navigation_mini = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_mini');

    $this->view->navigation_logo = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('day_logo' ? 'day_logo' : 'core_logo');

    $this->view->navigation_main = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_main');

    $this->view->logo = $logo;
    $this->view->photo = $photo_normal;
  }
}