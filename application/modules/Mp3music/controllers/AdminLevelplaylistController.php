<?php
class Mp3music_AdminLevelplaylistController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_levelplaylist');
  }
  public function indexAction()
  {
    // Get level id
    if( null !== ($id = $this->_getParam('level_id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }
    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }
    $id = $level->level_id;
    // Make form
    $this->view->form = $form = new Mp3music_Form_Admin_Settings_Levelplaylist(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('mp3music_playlist', $id, array_keys($form->getValues())));  
    // get max allow
    if($id != 5)   
    {
    $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
     $msselect = $mtable->select()
                ->where("type = 'mp3music_playlist'")
                ->where("level_id = ?",$id)
                ->where("name = 'max_songs'");
    $mpselect = $mtable->select()
                ->where("type = 'mp3music_playlist'")
                ->where("level_id = ?",$id)
                ->where("name = 'max_playlists'");
     $mallow_s = $mtable->fetchRow($msselect);    
    $mallow_p = $mtable->fetchRow($mpselect);
     if (!empty($mallow_s))
        $max_s = $mallow_s['value'];
    if (!empty($mallow_p))
        $max_p = $mallow_p['value'];
     $max_s_get = $form->max_songs->getValue();          
    $max_p_get = $form->max_playlists->getValue();    
    if ($max_s_get < 1)
    $form->max_songs->setValue($max_s); 
    if ($max_p_get < 1)
    $form->max_playlists->setValue($max_p);
    }
    $this->view->form = $form;
    // Check post
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    // Process
    $values = $form->getValues();
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    try
    {
        if($values['view'] == 0)
       {
            $values['edit'] = 0;
            $values['delete'] = 0;
       }
      // Set permissions
      $permissionsTable->setAllowed('mp3music_playlist', $id, $values);
      // Commit
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
}
