<?php
class Mp3music_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_settings');
  }
  public function indexAction()
  {
    $this->view->form = new Mp3music_Form_Admin_Global();
    if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
      $db = Engine_Api::_()->getDbTable('albums', 'mp3music')->getAdapter();
      $db->beginTransaction();
      try {
        $this->view->form->saveValues();
        $db->commit();
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    }
    $public_level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
    $public_viewable = Engine_Api::_()->getApi('core', 'authorization')->getPermission($public_level_id, 'mp3music_album', 'view');
    $this->view->form->getElement('mp3music_public')->setValue( $public_viewable );
  }
}