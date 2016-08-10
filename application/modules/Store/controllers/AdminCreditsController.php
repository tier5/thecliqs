<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: AdminCreditsController.php 01.06.12 16:46 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminCreditsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('credit')) {
      $this->_redirectCustom(
        $this->view->url(
          array(
            'module' => 'store',
            'controller' => 'products',
            'action' => 'index'
          ),
          'admin_default', true
        )
      );
    }

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_credit');
  }

  public function indexAction()
  {
    /**
     * @var $settings Core_Model_DbTable_Settings
     * @var $hecoreModulesTbl Hecore_Model_DbTable_Modules
     */

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $hecoreModulesTbl = Engine_Api::_()->getDbTable('modules', 'hecore');

    $select = $hecoreModulesTbl->select()
      ->where('name = ?', 'credit');

    $credit = $hecoreModulesTbl->fetchRow($select);
    $this->view->error = 0;
    if (version_compare($credit->version, '4.2.2') < 0) {
      $this->view->error = 1;
      $settings->setSetting('store.credit.enabled', 0);
    }

    $this->view->credit_enabled = $credit_enabled = $settings->getSetting('store.credit.enabled', 0);
    $this->view->page_enabled = $page_enabled = $hecoreModulesTbl->isModuleEnabled('page');

    if ($credit_enabled && $page_enabled) {
      $this->view->form = $form = new Store_Form_Admin_Credits_Settings();

      $values = array();
      $values['credits_on_store'] = $settings->getSetting('store.credit.store', 0);

      $form->populate($values);

      if (!$this->getRequest()->isPost()) {
        return ;
      }

      if (!$form->isValid($this->getRequest()->getPost())) {
        return ;
      }

      $values = array_merge($values, $form->getValues());
      $settings->setSetting('store.credit.store', $values['credits_on_store']);
      $form->addNotice('Your changes have been saved.');
    }
  }

  public function enableAction()
  {
    /**
     * @var $settings Core_Model_DbTable_Settings
     */

    $switcher = $this->_getParam('switcher', 0);

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $settings->setSetting('store.credit.enabled', $switcher);
  }
}
