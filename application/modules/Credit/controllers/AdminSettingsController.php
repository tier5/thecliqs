<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 03.01.12 12:49 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('credit_admin_main', array(), 'credit_admin_main_settings');
    $this->view->menu = $this->_getParam('action');
  }

  public function indexAction()
  {
    $this->view->form = $form = new Credit_Form_Admin_Settings();

    /**
     * @var $settings Core_Model_DbTable_Settings
     */
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $values            = array();
    $values['sort_by'] = $settings->getSetting('credit.default.sort.mode', 1);

    $form->populate($values);

    // Check method/valid
    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = array_merge($values, $form->getValues());

    $settings->__set('credit.default.sort.mode', $values['sort_by']);

    $form->addNotice('Your changes have been saved.');
  }

  public function levelAction()
  {
    // Get level id
    if (null !== ($id = $this->_getParam('id'))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    $this->view->form = $form = new Credit_Form_Admin_Levels();
    $form->level_id->setValue($id);

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    // Check post
    if (!$this->getRequest()->isPost()) {
      $form->populate($permissionsTable->getAllowed('credit', $id, array_keys($form->getValues())));
      return;
    }

    // Check validitiy
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      $permissionsTable->setAllowed('credit', $id, $values);
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Successfully saved');
  }

  public function exchangeAction()
  {
    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');
    $this->view->form = $form = new Credit_Form_Admin_Exchange();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();
    Engine_Api::_()->getDbTable('settings', 'core')->setSetting('credit.default.price', $values['credit_default_price']);

    $form->addNotice('Successfully saved');
  }


  public function badgesAction()
  {
    $this->view->hebadge = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('hebadge');
  }
}