<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 24.08.12
 * Time: 16:44
 * To change this template use File | Settings | File Templates.
 */
class Donation_AdminLevelController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('donation_admin_main', array(), 'donation_admin_main_level');
  }

  public function indexAction()
  {
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    $this->view->form = $form = new Donation_Form_Admin_Level();
    $form->level_id->setValue($id);

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    if (!$this->getRequest()->isPost()) {

      $form->populate($permissionsTable->getAllowed('donation', $id, array_keys($form->getValues())));
      return ;
    }

    if (!$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    $values = $form->getValues();
    unset($values['level_id']);

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      $permissionsTable->setAllowed('donation', $id, $values);
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('DONATION_Your changes has been saved.');
  }
}
