<?php
class Mp3music_AdminManageplaylistController extends Core_Controller_Action_Admin
{
  protected $_paginate_params = array();
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_manageplaylist');
    $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.songsPerPage', 10);
    $this->_paginate_params['sort']   = $this->getRequest()->getParam('sort', 'recent');
    $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
  }
  public function indexAction()
  {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $playlist = Engine_Api::_()->getItem('mp3music_playlist', $value);
          $songs = $playlist->getPSongs();
          foreach($songs as $song)
          {
             $song->delete();
          }
          $playlist->delete();
        }
      }
    }
    $params = array_merge($this->_paginate_params, array(
        'user' => $this->view->viewer_id,'admin'=>"admin"
    )); 
     $this->view->form = $form = new Mp3music_Form_Admin_Search();   
    $values = array();  
    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
    }  
    if(!empty($values['title']))  
    {
         $params['title'] =  $values['title'];
    }  
    if(!empty($values['owner']))  
    {
         $params['owner'] =  $values['owner'];
    }   
    $params['page'] = $this->_getParam('page', 1);   
    $this->view->formValues = $values;                                                                                            
    $obj = new Mp3music_Api_Core();
    $this->view->paginator = $obj->getPaginator($params);
    $this->view->params    = $params;
  }
}