<?php
class Ynmusic_AlbumsController extends Core_Controller_Action_Standard {
	public function indexAction() {
		$this->_helper->content->setEnabled()->setNoRender();
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynmusic_album', null, 'view') -> isValid())
            return;
		
	}
	
	public function viewAction() {
		$this->_helper->content->setEnabled();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = null;
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			$id = $this -> _getParam('id');
			if (null !== $id) {
				$subject = Engine_Api::_() -> getItem('ynmusic_album', $id);
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
		$this -> _helper -> requireSubject('ynmusic_album');
	}
	
	public function manageAction() {
		$this->_helper->content->setEnabled();
		
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		
		$viewer = Engine_Api::_() -> user() -> getViewer();		
		$table = Engine_Api::_()->getDbTable('albums', 'ynmusic');
		
		$params = $this ->_getAllParams();
		$params['user_id'] = $viewer -> getIdentity();
		
		$page = $this ->_getParam('page', 1);
		
		unset($params['module']);
		unset($params['controller']);
		unset($params['index']);
		unset($params['rewrite']);
		
		$this->view->formValues = $params;
        $this->view->paginator = $table -> getAlbumsPaginator($params);
        $this->view->paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_albumsPerPage', 8));
        $this->view->paginator->setCurrentPageNumber($page);
	}
	
	public function sortAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $album = Engine_Api::_() -> getItem('ynmusic_album', $this ->_getParam('album_id'));
		
        $order = explode(',', $this -> getRequest() -> getParam('order'));
		$songs = $album -> getSongs();
		foreach ($order as $i => $item) {
			$song_id = substr($item, strrpos($item, '_') + 1);
			foreach ($songs as $song) {
				if ($song -> song_id == $song_id) {
					$song -> order = $i;
					$song -> save();
				}
			}
		}
    }
	
	public function editAction() {
		$this->_helper->content->setEnabled();
		// only members can upload music
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		
		//$this->_helper->content->setEnabled();
				
		//get viewer & album
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$album = Engine_Api::_() -> getItem('ynmusic_album', $this ->_getParam('album_id'));
		if(!$album) {
			return $this->_helper->requireSubject()->forward();
		}
		
		if(!$album->isEditable()) {
			return $this->_helper->requireAuth()->forward();
		}
		
		$this -> view -> form = $form = new Ynmusic_Form_Album_Edit(array('album' => $album));
		
		if ($album->released_date) {
			$timezone = Engine_Api::_()->getApi('settings', 'core')
	        ->getSetting('core_locale_timezone', 'GMT');
	        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
	            $timezone = $viewer->timezone;
	        }
			$released_date = strtotime($album->released_date);
	      	$oldTz = date_default_timezone_get();
	      	date_default_timezone_set($timezone);
	      	$timeArray['released_date'] = date('Y-m-d H:i:s', $released_date);
	     	date_default_timezone_set($oldTz);
		}
		
		$populateArray = $album->toArray();
		if (!empty($timeArray))
			$populateArray = array_merge($populateArray, $timeArray);
		$form -> populate($populateArray);
		
		//Populate Tag
        $tagStr = '';
        foreach ($album->tags()->getTagMaps() as $tagMap)
        {
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
		$this -> view -> genreMappings = $genreMappings = $genreMappingTable -> getGenresByItem($album);
		//populate artist
		$artistMappingTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
		$this -> view -> artistMappings = $artistMappings = $artistMappingTable -> getArtistsByItem($album);
		
		//populate authorization
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		$auth_arr = array('view', 'comment', 'download');
        
        foreach ($auth_arr as $elem) {
            foreach ($roles as $role) {
                if(1 === $auth->isAllowed($album, $role, $elem)) {
                	$authVar = 'auth_'.$elem;
                    if ($form->$authVar)
                        $form->$authVar->setValue($role);
                }
            }
        }
		
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$form -> released_date -> setAllowEmpty(true);
		if ($form -> isValid($this -> getRequest() -> getPost()))
		{
			$values = $form -> getValues();
			$post = $this -> getRequest() -> getPost();
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			try 
			{
				//get genre & artists
				$genre_ids = $this ->_getParam('genre_ids'); 
				$genre_ids = explode(",",$genre_ids);
				$artist_ids = $this ->_getParam('artist_ids'); 
				$artist_ids = explode(",",$artist_ids);
							
				//clear old values
				$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
				$genreMappingsTable -> deleteGenresByItem($album);
				$artistMappingsTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
				$artistMappingsTable -> deleteArtistsByItem($album);
				
				//getTableAlbum
				$albumTable = Engine_Api::_() -> getItemTable('ynmusic_album');
				
				//save value to album if has
				if($album) {
					$album -> title = (!empty($values['title']))? $values['title'] : $this -> view -> translate("Untitled album");
					$album -> description = $values['description'];
					
					if(isset($values['released_date']) && !empty($values['released_date']) && $values['released_date'] != '0000-00-00') {
						//Set viewer time zone
						$timezone = Engine_Api::_()->getApi('settings', 'core')
				        ->getSetting('core_locale_timezone', 'GMT');
				        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
				            $timezone = $viewer->timezone;
				        }
						$oldTz = date_default_timezone_get();
						date_default_timezone_set($timezone);
						$released = strtotime($values['released_date']);
						date_default_timezone_set($oldTz);
						$values['released_date'] = date('Y-m-d H:i:s', $released);
						$album -> released_date = $values['released_date'];
					}
					
					$album -> save();
					
					// Set photo
					if (!empty($values['photo'])) {
						$album -> setPhoto($form -> photo, "photo_id");
					}
				
					// Set cover
					if (!empty($values['cover'])) {
						$album -> setPhoto($form -> cover, "cover_id");
					}
					
					//save mapping artists and genres
					Engine_Api::_() -> ynmusic() -> saveMappingsForItem($album, $artist_ids, $genre_ids);
					
					//add tags
					$tags = preg_split('/[,]+/', $values['tags']);
					$album -> tags() -> addTagMaps($viewer, $tags);
					
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
					if (empty($values['auth_view']))
					{
						$values['auth_view'] = 'everyone';
					}
			
					if (empty($values['auth_comment']))
					{
						$values['auth_comment'] = 'everyone';
					}

					if (empty($values['auth_download']))
					{
						$values['auth_download'] = 'everyone';
					}
					$viewMax = array_search($values['auth_view'], $roles);
					$commentMax = array_search($values['auth_comment'], $roles);
					$downloadMax = array_search($values['auth_download'], $roles);
			
					foreach ($roles as $i => $role)
					{
						$auth -> setAllowed($album, $role, 'view', ($i <= $viewMax));
						$auth -> setAllowed($album, $role, 'comment', ($i <= $commentMax));
						$auth -> setAllowed($album, $role, 'download', ($i <= $downloadMax));
					}
					
					if (!empty($post['order'])) {
						$order = explode(',', $post['order']);
						foreach ($order as $i => $item) {
							if (!is_numeric($item)) continue;
							$song = Engine_Api::_()->getItem('ynmusic_song', $item);
							$song->order = $i;
							$song->save();
						}
					}

					//remove songs
					if (!empty($post['deleted'])) {
					$deleted = explode(',', $post['deleted']);
					foreach ($deleted as $id) {
						if (!is_numeric($id)) continue;
						$song = Engine_Api::_()->getItem('ynmusic_song', $id);
						if ($song && $song->isDeletable()) {
							$song->delete();
						}
					}
 				}
					
				}		
				$db -> commit();
				return $this -> _forward('success', 'utility', 'core', array(
					'parentRedirect' => $album -> getHref(),
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
				));
			}
				catch (Exception $e) {
			      $db->rollBack();
			      throw $e;
		    }
		}
	}
	
	public function deleteAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form  = new Ynmusic_Form_Album_Delete();
		$album = Engine_Api::_() -> getItem('ynmusic_album', $this -> getRequest() -> getParam('id'));
		
		if (!$album) {
			return $this->_helper->requireSubject()->forward();
		}
		
		if(!$album->isDeletable()) {
			return $this->_helper->requireAuth()->forward();
		}
		
		// Check post
	    if(!$this->getRequest()->isPost()) {
            return;
        }
		
		$db = Engine_Api::_() -> getDbTable('albums', 'ynmusic') -> getAdapter();
		$db -> beginTransaction();
		try	{
			$album -> delete();
			$db -> commit();
		}
		catch (Exception $e) {
			$db -> rollback();
			throw $e;
		}
		
		$redirect = $this->_getParam('redirect', false);
		
		if (!$redirect) {
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Album deleted.')),
				'format' => 'smoothbox',
	            'smoothboxClose' => true,
				'parentRefresh' => true,
			));
		}
		$this->view->success = true;
	}
	
	public function downloadAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$album = Engine_Api::_() -> getItem('ynmusic_album', $this -> getRequest() -> getParam('id'));
		if (!$album || !$album->isDownloadable() || !$album->isViewable() || !$album->getCountAvailableDownloadSongs()) {
		//	die($this->view->translate('This album is empty or you don\'t have permission to download.'));
			$this->view->notAuth = true;
			return;
		}
		if ($this->_getParam('auth', false)) {
			Engine_Api::_() -> getApi('createzipfile', 'ynmusic') -> downloadAlbum($album);
		}
		$this->view->notAuth = false;
	}
}	
?>