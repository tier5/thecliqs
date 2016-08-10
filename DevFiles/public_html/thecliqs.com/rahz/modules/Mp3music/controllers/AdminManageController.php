<?php
class Mp3music_AdminManageController extends Core_Controller_Action_Admin
{
  protected $_paginate_params = array();
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mp3music_admin_main', array(), 'mp3music_admin_main_manage');
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
          $album = Engine_Api::_()->getItem('mp3music_album', $value);
          $songs = $album->getSongs();
          foreach($songs as $song)
          {
             $song->deleteUnused();
          }
          $album->delete();
        }
      }
    }
    $params = array_merge($this->_paginate_params, array(
        'user' => $this->view->viewer_id, 'admin'=>"admin"
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
    $obj = new Mp3music_Api_Core();
    $this->view->formValues = $values; 
    $this->view->paginator = $obj->getAlbumPaginator($params);
    $this->view->params    = $params;
  }
	/* ----- Set Featured Album Function ----- */
	public function featureAction() {
		// Get params
		$id = $this->_getParam ( 'album_id' );
		$is_featured = $this->_getParam ( 'status' );

		// Get album need to set featured
		$table = Engine_Api::_ ()->getItemTable ( 'mp3music_album' );
		$select = $table->select ()->where ( "album_id = ?", $id );
		$album = $table->fetchRow ( $select );

		// Set featured/unfeatured
		if ($album) {
			$album->is_featured = $is_featured;
			$album->save ();
		}
	}
 
}