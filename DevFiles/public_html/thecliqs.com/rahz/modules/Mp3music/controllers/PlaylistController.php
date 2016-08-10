<?php
class Mp3music_PlaylistController extends Core_Controller_Action_Standard {
    protected $_paginate_params = array();
    public function init() {
        $this->view->viewer_id  = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->navigation = $this->getNavigation();
        $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.songsPerPage', 10);
        $this->_paginate_params['sort']   = $this->getRequest()->getParam('sort', 'recent');
        $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
        $this->_paginate_params['search'] = $this->getRequest()->getParam('search', '');
        $this->_paginate_params['typesearch'] = $this->getRequest()->getParam('typesearch', '');
        $this->_paginate_params['title'] = $this->getRequest()->getParam('title', '');
        $this->_paginate_params['id'] = $this->getRequest()->getParam('id', '');
    }
    public function manageAction() {
    // only members can manage music
        if( !$this->_helper->requireUser()->isValid() ) return;
		$this->_helper->content->setEnabled();
        $params = array_merge($this->_paginate_params, array(
                'user' => $this->view->viewer_id,
        ));
        $obj = new Mp3music_Api_Core();
        $this->view->paginator = $obj->getPaginator($params);
        $this->view->params    = $params;
    }
    public function createAction() {
        // only members can upload music
        if( !$this->_helper->requireUser()->isValid() ) return;
        if( !$this->_helper->requireAuth()->setAuthParams('mp3music_playlist', null, 'create')->isValid())
            return;
        $this->view->form = new Mp3music_Form_CreatePlaylist();
        $this->view->playlist_id = $this->_getParam('playlist_id', '0');

        if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
            $db = Engine_Api::_()->getDbTable('playlists', 'mp3music')->getAdapter();
            $db->beginTransaction();
            try {
                $playlist = $this->view->form->saveValues();
                $db->commit();
                return $this->_redirect('mp3-music/manage_playlist');
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }
    }
    public function editAction() {
        // only members can upload music
        if (!$this->_helper->requireUser()->isValid()) return;
        // catch uploads from FLASH fancy-uploader and redirect to uploadSongAction()
        if ($this->getRequest()->getQuery('ul', false))
            return $this->_forward('edit-add-song', null, null, array('format' => 'json'));
        $playlist_id = $this->getRequest()->getParam('playlist_id');
        $playlist    = $this->view->playlist = Engine_Api::_()->getItem('mp3music_playlist', $playlist_id);
        if (empty($playlist) && $playlist_id > 0) {
            $this->_helper->redirector->gotoUrl(array(), 'mp3music_browse', true);
            return;
        }
        // only user and admins and moderators can create
        if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'edit')->isValid())
            return;
        foreach($this->_navigation->getPages() as $page)
            if ($page->route == 'mp3music_manage_playlist')
                $page->setActive(true);
        $this->view->form = new Mp3music_Form_EditPlaylist();
        $this->view->form->populate($playlist);
        if ($this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost())) {
            $db = Engine_Api::_()->getDbTable('playlists', 'mp3music')->getAdapter();
            $db->beginTransaction();
            try {
                $this->view->form->saveValues();
                $db->commit();
                return $this->_redirect('mp3-music/manage_playlist');
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }
    }
    public function deleteAction() {
        $playlist = Engine_Api::_()->getItem('mp3music_playlist', $this->getRequest()->getParam('playlist_id'));
        if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'delete')->isValid())
            return;
        $this->view->playlist_id = $playlist->getIdentity();
        // This is a smoothbox by default
        if( null === $this->_helper->ajaxContext->getCurrentContext() )
            $this->_helper->layout->setLayout('default-simple');
        else // Otherwise no layout
            $this->_helper->layout->disableLayout(true);
        if (!$this->getRequest()->isPost())
            return;
        $db = Engine_Api::_()->getDbtable('playlists', 'mp3music')->getAdapter();
        $db->beginTransaction();
        try {
            foreach ($playlist->getPSongs() as $song)
                $song->delete();
            $playlist->delete();
            $db->commit();
            $this->view->success = true;
            $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format'=> 'smoothbox',
                  'messages' => array($this->view->translate('Delete playlist successfully.'))
                  ));
        } catch (Exception $e) {
            $db->rollback();
            $this->view->success = false;
            throw $e;
        }
    }
    public function playlistAction() {
        $playlist = Engine_Api::_()->getItem('mp3music_playlist', $this->getRequest()->getParam('playlist_id'));
        if (!empty($playlist)) {
            Engine_Api::_()->core()->setSubject($playlist);
        }
        if (!$this->_helper->requireSubject()->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams($playlist,null, 'view')->isValid())
            return;
         $this->_helper->content
           ->setNoRender()
            ->setEnabled()
            ; 
    }
    public function playlistSortAction() {
        $translate = Zend_Registry::get('Zend_Translate');
        $playlist  = Engine_Api::_()->getItem('mp3music_playlist', $this->getRequest()->getParam('playlist_id'));
        if (!$this->getRequest()->isPost() || !$playlist || $this->view->viewer_id !== $playlist->user_id) {
            $this->view->error = $translate->_('Invalid playlist');
            return;
        }
        if (!$playlist->isEditable()) {
            $this->view->success = false;
            $this->view->error   = $translate->_('Not allowed to edit this playlist');
            return;
        }
        $songs = $playlist->getPSongs();
        $order = explode(',', $this->getRequest()->getParam('order'));
        foreach ($order as $i => $item) {
            $song_id = substr($item, strrpos($item, '_')+1);
            foreach ($songs as $song) {
                if ($song->album_song_id == $song_id) {
                    $song->order = $i;
                    $song->save();
                }
            }
        }
        $this->view->songs    = $playlist->getSongs()->toArray();
    }
    public function removeSongPlaylistAction() {
        $translate = Zend_Registry::get('Zend_Translate');
        if (!$this->getRequest()->isPost()) {
            $this->view->success = false;
            $this->view->error   = $translate->_('isGet');
            exit;
        }
        $songID = Mp3music_Model_PlaylistSong::getSongID($this->getRequest()->getParam('playlist_id'),$this->getRequest()->getParam('song_id'));
        $song     = Engine_Api::_()->getItem('mp3music_playlist_song', $songID);
        if (!$song) {
            $this->view->success = false;
            $this->view->error   = $translate->_('Not a valid song');
            $this->view->post    = $_POST;
            return;
        }
        $db = Engine_Api::_()->getDbTable('playlists', 'mp3music')->getAdapter();
        $db->beginTransaction();
        try {
            $song->delete();
            $db->commit();
            $this->view->success = true;
        } catch (Exception $e) {
            $db->rollback();
            $this->view->success = false;
            $this->view->error   = $translate->_('Unknown database error');
            throw $e;
        }
    }
    public function playlistAppendAction() {
        $translate = Zend_Registry::get('Zend_Translate');
        if (!Engine_Api::_() -> user() -> getViewer() -> getIdentity())
		{
			$callbackUrl = $this -> view -> url(array(), 'user_login', true);
			return $this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRedirect' => $callbackUrl,
				'format' => 'smoothbox',
				'messages' => array($this->view->translate("Please sign in to continue."))
				));
		}
        if( !$this->_helper->requireAuth()->setAuthParams('mp3music_playlist', null, 'create')->isValid())
            return;
        $this->view->form        = new Mp3music_Form_Playlist();
        $this->view->playlist_id = $this->getRequest()->getParam('playlist_id');
        $this->view->song_id     = $this->getRequest()->getParam('song_id');
        if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
            $db = Engine_Api::_()->getDbTable('playlists', 'mp3music')->getAdapter();
            $db->beginTransaction();
            try {
                if($this->view->form->saveValues() == false)
                    return;
                $db->commit();
                $this->view->success     = true;
                $this->view->playlist_id = $this->view->form->playlist->playlist_id;
                $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => false,
                  'format'=> 'smoothbox',
                  'messages' => array($translate->_('Add song successfully.'))
                  ));
            } catch (Exception $e) {
                $db->rollback();
                $this->view->success = false;
            }
        }
    }
    public function setProfilePlaylistAction() {
        if (! $this->getRequest()->isPost() )
            return;
        $playlist = Engine_Api::_()->getItem('mp3music_playlist', $this->getRequest()->getPost('playlist_id', null));
        if (!$playlist || $playlist->user_id != $this->view->viewer_id)
            return;
        $this->view->playlist_id = $playlist->getIdentity();
        $db = Engine_Api::_()->getDbTable('playlists', 'mp3music')->getAdapter();
        $db->beginTransaction();
        try {
            $playlist->setProfile();
            $db->commit();
            $this->view->success = true;
            $this->view->enabled = $playlist->profile;
        } catch (Exception $e) {
            $db->rollback();
            $this->view->success = false;
        }
    }
    /* Utility */
    protected $_navigation;
    public function getNavigation() {
    $tabs   = array();
    $tabs[] = array(
          'label'      => 'Browse Music',
          'route'      => 'mp3music_browse',
          'action'     => 'browse',
          'controller' => 'index',
          'module'     => 'mp3music'
        );
   $tabs[] = array(
          'label'      => 'My Albums',
          'route'      => 'mp3music_manage_album',
          'action'     => 'manage',
          'controller' => 'album',
          'module'     => 'mp3music'
        );
    $tabs[] = array(
          'label'      => 'My Playlists',
          'route'      => 'mp3music_manage_playlist',
          'action'     => 'manage',
          'controller' => 'playlist',
          'module'     => 'mp3music'
        );
    $tabs[] = array(
          'label'      => 'Upload Music',
          'route'      => 'mp3music_create_album',
          'action'     => 'create',
          'controller' => 'album',
          'module'     => 'mp3music'
        );
        if( is_null($this->_navigation) ) {
            $this->_navigation = new Zend_Navigation();
            $this->_navigation->addPages($tabs);
        }
        return $this->_navigation;
    }
}
