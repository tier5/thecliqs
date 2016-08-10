<?php
class Ynmusic_PlaylistsController extends Core_Controller_Action_Standard {
	
	public function indexAction() {
		$this->_helper->content->setEnabled()->setNoRender();
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynmusic_playlist', null, 'view') -> isValid())
            return;
	}
	
	public function getPlaylistFormAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> view -> item = $item = Engine_Api::_() -> core() -> getSubject();
	}
	
	public function manageAction() {
		$this->_helper->content->setEnabled();
		
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		
		$viewer = Engine_Api::_() -> user() -> getViewer();		
		$table = Engine_Api::_()->getDbTable('playlists', 'ynmusic');
		
		$params = $this ->_getAllParams();
		$params['user_id'] = $viewer -> getIdentity();
		$this->view->formValues = $params;
        $this->view->paginator = $table -> getPaginator($params);
        $this->view->paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_playlistsPerPage', 8));
        $this->view->paginator->setCurrentPageNumber($page);
	}
	
	public function addToPlaylistAction()
	{
		// Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		
		//get viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		//get params
		$params = $this ->_getAllParams();
		$item = Engine_Api::_() -> core() -> getSubject();
		$playlist_id = $params['playlist_id'];
		$checked = $params['checked'];
		$message = '';
		$status = true;
		$playlist = Engine_Api::_()->getItem('ynmusic_playlist', $playlist_id);
		
		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		try 
		{
			$playlistSongTable = Engine_Api::_() -> getDbTable('playlistSongs', 'ynmusic');
			//get songids of playlist
			$songIds = $playlistSongTable -> getSongIds($playlist_id);
			$type = $item -> getType();
			if($type == "ynmusic_song") {
				//check exist before insert
				if($checked == "true"){
					//if checked means add
					if(!in_array($item -> getIdentity(), $songIds)) {
						if ($playlist->canAddSongs()) {
							if (!$playlist->photo_id && !$playlist->cover_id) {
								if ($item->photo_id) {
									$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($item->photo_id);
									$playlist->setPhoto($photo, 'photo_id');
								}
								if ($item->cover_id) {
									$cover = Engine_Api::_()->getItemTable('storage_file')->getFile($item->cover_id);
									$playlist->setPhoto($cover, 'cover_id');
								}
								$playlist->save();
							}
							$mapRow = $playlistSongTable -> createRow();
							$mapRow -> playlist_id = $playlist_id;
							$mapRow -> song_id = $item -> getIdentity();
							$mapRow -> save();
							$message .= $this->view->translate('This song has been added to playlist %s successfully. ', $playlist->getTitle());
						}
						else {
							$status = false;
							$message .= $this->view->translate('The number of playlist songs reached limit. You can not add this song to playlist %s. ', $playlist->getTitle());
						}
					}
					else {
						$status = false;
						$message .= $this->view->translate('This song already has been in playlist %s. ', $playlist->getTitle());
					} 
				} else if($checked == "false"){
					//if checked means remove
					$mapRow = $playlistSongTable -> getMapRow($playlist_id, $item -> getIdentity());
					if($mapRow){
						$mapRow -> delete();
						$message .= $this->view->translate('This song has been removed from playlist %s successfully. ', $playlist->getTitle());
					}
				}	
				
			} else if($type == "ynmusic_album") {
				//get songs of album
				$songs = $item -> getAvailableSongs();
				
				$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
		        $max = $permissionsTable->getAllowed('ynmusic_playlist', $viewer->level_id, 'max_songs');
		        if ($max == null) {
		            $row = $permissionsTable->fetchRow($permissionsTable->select()
		                ->where('level_id = ?', $viewer->level_id)
		                ->where('type = ?', 'ynmusic_playlist')
		                ->where('name = ?', 'max_songs'));
		            if ($row) {
		                $max = $row->value;
		            }
		        }
				
				$numOfExists = $playlist->getCountSongs();
				$remain = ($max) ? ($max - $numOfExists) : 0;
				$count = 0;
				foreach($songs as $song) {
					$song_id = $song -> getIdentity();
					//check exist before insert
					if($checked == "true"){
						if ($max && $remain <= 0) {
							$message .= $this->view->translate('The number of playlist songs reached limit. ');
							break;
						}
						//if checked means add
						if(!in_array($song_id, $songIds)) {
							if (!$playlist->photo_id && !$playlist->cover_id) {
								$song = Engine_Api::_()->getItem('ynmusic_song', $song_id);
								if ($song->photo_id) {
									$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($song->photo_id);
									$playlist->setPhoto($photo, 'photo_id');
								}
								if ($song->cover_id) {
									$cover = Engine_Api::_()->getItemTable('storage_file')->getFile($song->cover_id);
									$playlist->setPhoto($cover, 'cover_id');
								}
								$playlist->save();
							}
							$mapRow = $playlistSongTable -> createRow();
							$mapRow -> playlist_id = $playlist_id;
							$mapRow -> song_id = $song_id;
							$mapRow -> save();
							
							$remain--;
							$count++;
						}
						
					} else if($checked == "false"){
						//if checked means remove
						$mapRow = $playlistSongTable -> getMapRow($playlist_id, $song_id);
						if($mapRow){
							$mapRow -> delete();
							$count++;
						}	
					}
				}
				
				if($checked == "true"){
					$message .= $this->view->translate('%s song(s) has been added to playlist %s.', $count, $playlist->getTitle());
				}
				else {
					$message .= $this->view->translate('%s song(s) has been removed from playlist %s.', $count, $playlist->getTitle());
				}
			}

			$db -> commit();
			$data = Zend_Json::encode(
				array(
					'status' => $status,
					'message' => $message
				)
			);
			echo $data;
    		return true;
		} 
		catch (Exception $e) {
	      $db->rollBack();
	      throw $e;
	    }
	}
	public function createPlaylistAction()
	{
		// Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynmusic_playlist', null, 'create') -> isValid())
            return;
		
		//get viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		//get params
		$params = $this ->_getAllParams();
		$params['user_id'] = $viewer -> getIdentity();
		$params['title'] = strip_tags($params['title']);
		$song_ids = $this ->_getParam('song_ids');
	    $songIds = explode(",",$song_ids);
		$db = Engine_Db_Table::getDefaultAdapter();
		$db->beginTransaction();
		try 
		{
			//create playlists
			$playlistTable = Engine_Api::_() -> getItemTable('ynmusic_playlist');
			$playlist = $playlistTable -> createRow();
			$playlist = $playlist -> setFromArray($params);
			$playlist -> save();
			
			//add activity
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$action = $activityApi->addActivity($playlist -> getOwner(), $playlist, 'ynmusic_playlist_create');
			if($action) {
				$activityApi->attachActivity($action, $playlist);
			}
			
			//set auth
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'registered',
				'everyone',
			);
			
			if (empty($params['auth_view']))
			{
				$params['auth_view'] = 'everyone';
			}
	
			if (empty($params['auth_comment']))
			{
				$params['auth_comment'] = 'everyone';
			}
			$viewMax = array_search($params['auth_view'], $roles);
			$commentMax = array_search($params['auth_comment'], $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($playlist, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($playlist, $role, 'comment', ($i <= $commentMax));
			}
			
			if (Engine_Api::_() -> hasModuleBootstrap("yncredit")) {
	        	$user = $playlist -> getOwner();
				if($user -> getIdentity())
	            	Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $user -> getTitle(), 'ynmusic_playlist', $user);
			}
			//save songs to playlist
			$songTable = Engine_Api::_()->getDbTable('songs', 'ynmusic');
			$playlistSongTable = Engine_Api::_() -> getDbTable('playlistSongs', 'ynmusic');
			foreach($songIds as $song_id){
				if (!$playlist->photo_id && !$playlist->cover_id) {
					$song = Engine_Api::_()->getItem('ynmusic_song', $song_id);
					if ($song->photo_id) {
						$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($song->photo_id);
						$playlist->setPhoto($photo, 'photo_id');
					}
					if ($song->cover_id) {
						$cover = Engine_Api::_()->getItemTable('storage_file')->getFile($song->cover_id);
						$playlist->setPhoto($cover, 'cover_id');
					}
					$playlist->save();
				}
				$mapRow = $playlistSongTable -> createRow();
				$mapRow -> playlist_id = $playlist -> getIdentity();
				$mapRow -> song_id = $song_id;
				$mapRow -> save();
			}
			$db -> commit();
			echo Zend_Json::encode(array('json' => 'true'));
    		return true;
		} 
		catch (Exception $e) {
	      $db->rollBack();
	      throw $e;
	    }
	}

	public function viewAction() {
		$this->_helper->content->setEnabled();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = null;
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			$id = $this -> _getParam('id');
			if (null !== $id) {
				$subject = Engine_Api::_() -> getItem('ynmusic_playlist', $id);
				if ($subject && $subject -> getIdentity()) {
					Engine_Api::_() -> core() -> setSubject($subject);
				} else {
					return $this -> _helper -> requireSubject() -> forward();
				}
				// Check authorization to view album.
				if (!$subject->isViewable()) {
				    return $this -> _helper -> requireAuth() -> forward();
				}
				
				if (!$subject->isOwner($viewer)) {
					$subject->view_count++;
					$subject->save();
				}
			}
		}
		$this -> _helper -> requireSubject('ynmusic_playlist');
	}
	
	public function deleteAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form  = new Ynmusic_Form_Playlist_Delete();
		$playlist = Engine_Api::_() -> getItem('ynmusic_playlist', $this -> getRequest() -> getParam('id'));
		
		if (!$playlist) {
			return $this->_helper->requireSubject()->forward();
		}
		
		if(!$playlist->isDeletable()) {
			return $this->_helper->requireAuth()->forward();
		}
		
		// Check post
	    if(!$this->getRequest()->isPost()) {
            return;
        }
		
		$db = Engine_Api::_() -> getDbTable('playlists', 'ynmusic') -> getAdapter();
		$db -> beginTransaction();
		try	{
			$playlist -> delete();
			$db -> commit();
		}
		catch (Exception $e) {
			$db -> rollback();
			throw $e;
		}
		
		$redirect = $this->_getParam('redirect', false);
		
		if (!$redirect) {
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Playlist deleted.')),
				'format' => 'smoothbox',
	            'smoothboxClose' => true,
				'parentRefresh' => true,
			));
		}
		
		$this->view->success = true;
	}

	public function editAction() {
		$this->_helper->content->setEnabled();
		// only members can upload music
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
				
		//get viewer & album
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$playlist = Engine_Api::_() -> getItem('ynmusic_playlist', $this ->_getParam('id'));
		if(!$playlist) {
			return $this->_helper->requireSubject()->forward();
		}
		
		if(!$playlist->isEditable()) {
			return $this->_helper->requireAuth()->forward();
		}
		
		$this -> view -> form = $form = new Ynmusic_Form_Playlist_Edit(array('playlist' => $playlist));
		
		$populateArray = $playlist->toArray();
		$form -> populate($populateArray);
		
		//Populate Tag
        $tagStr = '';
        foreach ($playlist->tags()->getTagMaps() as $tagMap) {
            $tag = $tagMap -> getTag();
            if (!isset($tag -> text))
                continue;
            if ('' !== $tagStr)
                $tagStr .= ', ';
            $tagStr .= $tag -> text;
        }
        $form -> populate(array('tags' => $tagStr, ));
		
		//populate genre
		$genreMappingTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
		$this -> view -> genreMappings = $genreMappings = $genreMappingTable -> getGenresByItem($playlist);
		
		//populate authorization
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		$auth_arr = array('view', 'comment');
        
        foreach ($auth_arr as $elem) {
            foreach ($roles as $role) {
                if(1 === $auth->isAllowed($playlist, $role, $elem)) {
                    if ($form->$elem)
                        $form->$elem->setValue($role);
                }
            }
        }
		
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if ($form -> isValid($this -> getRequest() -> getPost())) {
			$post = $this -> getRequest() -> getPost();
			$values = $form -> getValues();
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			try {
				//get genre & artists
				$genre_ids = $this ->_getParam('genre_ids'); 
				$genre_ids = explode(",",$genre_ids);
				//clear old values
				$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
				$genreMappingsTable -> deleteGenresByItem($playlist);
				
				//getTableAlbum
				$albumTable = Engine_Api::_() -> getItemTable('ynmusic_playlist');
				
				$playlist -> title = $values['title'];
				$playlist -> description = $values['description'];
				$playlist -> save();
					
				// Set photo
				if (!empty($values['photo'])) {
					$playlist -> setPhoto($form -> photo, "photo_id");
				}
				
				// Set cover
				if (!empty($values['cover'])) {
					$playlist -> setPhoto($form -> cover, "cover_id");
				}
					
				//save mapping artists and genres
				Engine_Api::_() -> ynmusic() -> saveMappingsForItem($playlist, array(), $genre_ids);
					
				//add tags
				$tags = preg_split('/[,]+/', $values['tags']);
				$playlist -> tags() -> setTagMaps($viewer, $tags);
					
				// Set auth
				$auth = Engine_Api::_() -> authorization() -> context;
				$roles = array(
					'owner',
					'owner_member',
					'owner_member_member',
					'owner_network',
					'registered',
					'everyone',
				);
				if (empty($values['view'])) {
					$values['view'] = 'everyone';
				}
		
				if (empty($values['comment'])) {
					$values['comment'] = 'everyone';
				}
				$viewMax = array_search($values['view'], $roles);
				$commentMax = array_search($values['comment'], $roles);

				foreach ($roles as $i => $role) {
					$auth -> setAllowed($playlist, $role, 'view', ($i <= $viewMax));
					$auth -> setAllowed($playlist, $role, 'comment', ($i <= $commentMax));
				}
				
				if (!empty($post['order'])) {
					$order = explode(',', $post['order']);
					$playlist->updateSongsOrder($order);
				}
				
				if (!empty($post['deleted'])) {
					$deleted = explode(',', $post['deleted']);
					$playlist->deleteSongs($deleted);
				}
					
				$db -> commit();
				return $this -> _forward('success', 'utility', 'core', array(
					'parentRedirect' => $playlist -> getHref(),
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
				));
			}
			catch (Exception $e) {
		      	$db->rollBack();
		      	throw $e;
		    }
		}
	}

	public function renderPlaylistListAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $item = Engine_Api::_()->core()->getSubject();
        
        if (!$item) {
            return $this->_helper->requireSubject()->forward();
        }
        echo $this->view->partial('_add_exist_playlist.tpl', 'ynmusic', array('item' => $item));
    }
}