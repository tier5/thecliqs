<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminLevelController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminLevelController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->menu       = 'level';
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_settings');
  }

  public function indexAction()
  {
    /**
     * @var $table Authorization_Model_DbTable_Levels
     */
    $table  = Engine_Api::_()->getDbtable('levels', 'authorization');

    if (null !== ($id = $this->_getParam('level_id') ) )
    {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = $table->getDefaultLevel();
    }

    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    $this->view->levelForm = $levelForm = new Store_Form_Admin_Level(array('level' => $level));
    $levelForm->level_id->setValue($id);

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    if (!$this->getRequest()->isPost()) {
      $values = $levelForm->getValues();
      unset($values['use']);
      $levelForm->populate($permissionsTable->getAllowed('store_product', $id, array_keys($values)));
      $levelForm->populate($permissionsTable->getAllowed('store', $id, array('use')));
      return;
    }

    if (!$levelForm->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $levelForm->getValues();
    // Work around bug :(
    $use = $values['use'];
    unset($values['level_id']);
    unset($values['use']);


    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $permissionsTable->setAllowed('store_product', $id, $values);
      $permissionsTable->setAllowed('store', $id, array('use'=> $use));
      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }
}