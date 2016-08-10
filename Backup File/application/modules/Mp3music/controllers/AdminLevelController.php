<?php
class Mp3music_AdminLevelController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_level');
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
    $this->view->form = $form = new Mp3music_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('mp3music_album', $id, array_keys($form->getValues())));  
    // get max allow
    if($id != 5)   
    {
    $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $msselect = $mtable->select()
                ->where("type = 'mp3music_album'")
                ->where("level_id = ?",$id)
                ->where("name = 'max_songs'");
    $mfsselect = $mtable->select()
                ->where("type = 'mp3music_album'")
                ->where("level_id = ?",$id)
                ->where("name = 'max_filesize'");
    $mstselect = $mtable->select()
                ->where("type = 'mp3music_album'")
                ->where("level_id = ?",$id)
                ->where("name = 'max_storage'");
    $maselect = $mtable->select()
                ->where("type = 'mp3music_album'")
                ->where("level_id = ?",$id)
                ->where("name = 'max_albums'");
    $mallow_s = $mtable->fetchRow($msselect);
    $mallow_fs = $mtable->fetchRow($mfsselect);
    $mallow_st = $mtable->fetchRow($mstselect);
   
    $mallow_a = $mtable->fetchRow($maselect);
    if (!empty($mallow_s))
        $max_s = $mallow_s['value'];
    
    if (!empty($mallow_fs))
        $max_fs = $mallow_fs['value'];
        
    if (!empty($mallow_st))
        $max_st = $mallow_st['value'];
        
   
        
    if (!empty($mallow_a))
        $max_a = $mallow_a['value'];

    $max_s_get = $form->max_songs->getValue();
    $max_fs_get = $form->max_filesize->getValue();    
    $max_st_get = $form->max_storage->getValue();    
   
    $max_a_get = $form->max_albums->getValue();    
     
    if ($max_s_get < 1)
    $form->max_songs->setValue($max_s);
    if ($max_fs_get < 1)
    $form->max_filesize->setValue($max_fs);
    if ($max_st_get < 1)
    $form->max_storage->setValue($max_st);
   
    if ($max_a_get < 1)
    $form->max_albums->setValue($max_a);
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
      // Set permissions
       if($values['view'] == 0)
       {
            $values['edit'] = 0;
            $values['delete'] = 0;
       }
       $permissionsTable->setAllowed('mp3music_album', $id, $values);
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
