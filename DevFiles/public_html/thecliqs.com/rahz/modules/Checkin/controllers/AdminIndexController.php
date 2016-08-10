<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexController.php 2011-11-17 11:18:13 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_AdminIndexController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->form = $form = new Checkin_Form_Admin_Global();

    $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');
    $checksSel = $checksTbl->select()
      ->from($checksTbl->info('name'), array(new Zend_Db_Expr('COUNT(*)')))
      ->where('place_id = ?', 0);
    $rowCount = $checksTbl->getAdapter()->fetchOne($checksSel);

    if ($rowCount) {
      $linkHTML = $this->view->htmlLink($this->view->url(array('module' => 'checkin', 'controller' => 'index', 'action' => 'convert'), 'admin_default', true), $this->view->translate('Upgrade'), array('class' => 'smoothbox'));
      $description = sprintf($this->view->translate('CHECKIN_Your database has old formatted %s records. You need to upgrade them. Please click %s'), $rowCount, $linkHTML);
      $form->addNotice($description);
    }

    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
      foreach ($values as $key => $value) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }

      $form->addNotice('Your changes have been saved.');
    }
  }

  public function convertAction()
  {
    Engine_Api::_()->checkin()->convertCheckinData();

    $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');
    $checksSel = $checksTbl->select()
      ->from($checksTbl->info('name'), array(new Zend_Db_Expr('COUNT(*)')))
      ->where('place_id = ?', 0);
    $this->view->rowCount = $rowCount = $checksTbl->getAdapter()->fetchOne($checksSel);
  }
}