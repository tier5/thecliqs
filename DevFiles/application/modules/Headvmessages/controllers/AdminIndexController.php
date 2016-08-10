<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Bolot
 * Date: 03.05.13
 * Time: 12:52
 * To change this template use File | Settings | File Templates.
 */
class Headvmessages_AdminIndexController extends Core_Controller_Action_Admin
{
  public function init()
  {

  }

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('headvmessages_admin_main', array(), 'headvmessages_admin_main_index');

    $this->view->form = $form = new Headvmessages_Form_Admin_Settings();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    $params = $this->getRequest()->getParams();

    if (!$form->isValid($params)) {
      return;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $settings->__set('headvmessages.enabled', (int)((boolean)$form->getValue('enabled_adv_messages')));
    $settings->__set('headvmessages.enter.send.enabled', (int)((boolean)$form->getValue('enabled_enter')));

    $form->addNotice('HEADVMESSAGES_Settings have been successfully saved');
  }

  public function levelsAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('headvmessages_admin_main', array(), 'headvmessages_admin_main_levels');

    $this->view->form = $form = new Headvmessages_Form_Admin_Levels();
    $level_id = $this->_getParam('id', 1);

    $form->level_id->setValue($level_id);
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    if (!$this->getRequest()->isPost()) {
      if (null !== $level_id) {
        $form->populate($permissionsTable->getAllowed('headvmessages', $level_id, array_keys($form->getValues())));
        return;
      }
      return;
    }

    // Check validitiy
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process

    $values = $form->getValues();
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $permissionsTable->setAllowed('headvmessages', $level_id, $values);
      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('HEADVMESSAGES_Settings have been successfully saved');
  }
}
