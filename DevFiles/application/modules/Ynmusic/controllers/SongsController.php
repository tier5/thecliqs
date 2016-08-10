<?php
class Ynmusic_SongsController extends Core_Controller_Action_Standard {
	
	public function indexAction() {
		$this->_helper->content->setEnabled()->setNoRender();
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynmusic_song', null, 'view') -> isValid())
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
				$subject = Engine_Api::_() -> getItem('ynmusic_song', $id);
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
	
	public function editSongAction()
    {
  		// Disable layout and viewrenderer
  		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _helper -> layout -> disableLayout();
		$params = $this ->_getAllParams();
		$move = 0;
		$song = Engine_Api::_() -> getItem('ynmusic_song', $params['song_id']);
		if(isset($params['title']) && !empty($params['title'])) {
			$song -> title = strip_tags($params['title']);
		}
		if(isset($params['description']) && !empty($params['description'])) {
			$song -> description = strip_tags($params['description']);
		}
		
		if (!empty($params['photo_id']) && ($params['photo_id'] != $song->photo_id)) {
			$song -> photo_id = $params['photo_id'];
		}
		if (!empty($params['cover_id']) && ($params['cover_id'] != $song->photo_id)) {
			$song -> cover_id = $params['cover_id'];
			$song -> cover_top = 0;
		}
		if(isset($params['tags']) && !empty($params['tags'])) {
			//add tags
			$tags = preg_split('/[,]+/', $params['tags']);
			$song -> tags() -> addTagMaps($viewer, $tags);
		}
		if(isset($params['downloadable'])) {
			$song -> downloadable = $params['downloadable'];
		}
		if(isset($params['album_id']) && $params['album_id'] != "none" && $params['album_id'] != $song -> album_id) {
			$song -> album_id = $params['album_id'];
			$move = 1;
		}
		$song -> save();
		//get genre & artists
		$genre_ids = $params['genre_ids']; 
		$genre_ids = explode(",",$genre_ids);
		
		$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
		$genreMappingsTable -> deleteGenresByItem($song);
		
		$artist_ids = $params['artist_ids']; 
		$artist_ids = explode(",",$artist_ids);
		
		$artistMappingsTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
		$artistMappingsTable -> deleteArtistsByItem($song);
		
		//save mapping artists and genres
		Engine_Api::_() -> ynmusic() -> saveMappingsForItem($song, $artist_ids, $genre_ids);
		
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
		
		if (empty($params['auth_view']))
		{
			$params['auth_view'] = 'everyone';
		}

		if (empty($params['auth_comment']))
		{
			$params['auth_comment'] = 'everyone';
		}

		if (empty($params['auth_download']))
		{
			$params['auth_download'] = 'everyone';
		}
		$viewMax = array_search($params['auth_view'], $roles);
		$commentMax = array_search($params['auth_comment'], $roles);
		$downloadMax = array_search($params['auth_download'], $roles);
		foreach ($roles as $i => $role)
		{
			$auth -> setAllowed($song, $role, 'view', ($i <= $viewMax));
			$auth -> setAllowed($song, $role, 'comment', ($i <= $commentMax));
			$auth -> setAllowed($song, $role, 'download', ($i <= $downloadMax));
		}
		
		echo Zend_Json::encode(array('error_code' => 0, 'move' => $move));
		exit();
    }
	
	public function getFormEditAction() {
		// Disable layout and viewrenderer
		$this -> _helper -> layout -> disableLayout();
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> song = $song = Engine_Api::_() -> getItem('ynmusic_song', $this ->_getParam('song_id'));
		if(!$song) {
			return $this->_helper->requireSubject()->forward();
		}
		
		if(!$song->isEditable()) {
			return $this->_helper->requireAuth()->forward();
		}
		
		$this -> view -> form = $form = new Ynmusic_Form_Song_Edit(array('song' => $song));
		
		$populateArray = array();
		$songArray = $song->toArray();
		foreach($songArray as $key => $value) {
			$populateArray[$key.'_'.$song -> getIdentity()] = $value;
		}
		$form -> populate($populateArray);
		
		//Populate Tag
        $tagStr = '';
        foreach ($song->tags()->getTagMaps() as $tagMap)
        {
            $tag = $tagMap -> getTag();
            if (!isset($tag -> text))
                continue;
            if ('' !== $tagStr)
                $tagStr .= ', ';
            $tagStr .= $tag -> text;
        }
        $form -> populate(array('tags'.'_'.$song -> getIdentity() => $tagStr, ));
		
		//populate genre
		$genreMappingTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
		$this -> view -> genreMappings = $genreMappings = $genreMappingTable -> getGenresByItem($song);
		
		//populate artist
		$artistMappingTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
		$this -> view -> artistMappings = $artistMappings = $artistMappingTable -> getArtistsByItem($song);
		
		//populate authorization
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		$auth_arr = array('view', 'comment', 'download');
        
        foreach ($auth_arr as $elem) {
            foreach ($roles as $role) {
                if(1 === $auth->isAllowed($song, $role, $elem)) {
                	$authVar = 'auth_'.$elem.'_'.$song -> getIdentity();
                    if ($form->$authVar)
                        $form->$authVar->setValue($role);
                }
            }
        }	

	}
	
	public function deleteAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form  = new Ynmusic_Form_Song_Delete();
		$song = Engine_Api::_() -> getItem('ynmusic_song', $this -> getRequest() -> getParam('id'));
		
		if (!$song) {
			return $this->_helper->requireSubject()->forward();
		}
		
		if(!$song->isDeletable()) {
			return $this->_helper->requireAuth()->forward();
		}
		
		// Check post
	    if(!$this->getRequest()->isPost()) {
            return;
        }
		
		$db = Engine_Api::_() -> getDbTable('songs', 'ynmusic') -> getAdapter();
		$db -> beginTransaction();
		try	{
			$song -> delete();
			$db -> commit();
		}
		catch (Exception $e) {
			$db -> rollback();
			throw $e;
		}
		
		$redirect = $this->_getParam('redirect', false);
		
		if (!$redirect) {
			 return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Song deleted.')),
				'format' => 'smoothbox',
	            'smoothboxClose' => true,
				'parentRefresh' => true,
			));
		}
		
		$this->view->success = true;
	}
	
	public function manageAction() {
		$this->_helper->content->setEnabled();
		
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		
		$viewer = Engine_Api::_() -> user() -> getViewer();		
		$table = Engine_Api::_()->getDbTable('songs', 'ynmusic');
		
		$params = $this ->_getAllParams();
		$params['user_id'] = $viewer -> getIdentity();
		$page = $this ->_getParam('page', 1);
		
		unset($params['module']);
		unset($params['controller']);
		unset($params['index']);
		unset($params['rewrite']);
		
		$this->view->formValues = $params;
        $this->view->paginator = $table -> getSongsPaginator($params);
        $this->view->paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_songsPerPage', 10));
        $this->view->paginator->setCurrentPageNumber($page);
	}
	
	public function uploadSongAction()
	{
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$translate = Zend_Registry::get('Zend_Translate');
		// only members can upload music
		$user = Engine_Api::_() -> user() -> getViewer();
		if (!$this -> _helper -> requireUser() -> checkRequire()) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded or session expired.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
		}

		if (!$this -> getRequest() -> isPost()) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
		}
		if(!empty($_FILES['Filedata'])) {
			$_FILES['files']['type'][0] = $_FILES['Filedata']['type'];
			$_FILES['files']['tmp_name'][0] = $_FILES['Filedata']['tmp_name'];
			$_FILES['files']['name'][0] = $_FILES['Filedata']['name'];
			$_FILES['files']['error'][0] = $_FILES['Filedata']['error'];
			$_FILES['files']['size'][0] = $_FILES['Filedata']['size'];
		}
		if (empty($_FILES['files'])) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $error)))));
		}
		$name = $_FILES['files']['name'][0];
		if (!isset($_FILES['files']) || !is_uploaded_file($_FILES['files']['tmp_name'][0])) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload or file too large.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}

		if (!preg_match('/\.(mp3)$/', $name)) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid file type.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}

		$max_fileSizes = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynmusic_song', $user, 'max_filesize');
		$max_storage = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynmusic_song', $user, 'max_storage');
		$sumFileSize = Engine_Api::_()->ynmusic()->getStorage($user);
		if ($_FILES['files']['size'][0] + $sumFileSize > $max_storage * 1024) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Storage space of user is limited!');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}
		if ($_FILES['files']['size'][0] > $max_fileSizes * 1024) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload or file too large.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try
		{
			$temp_file = array(
						'type' => $_FILES['files']['type'][0],
						'tmp_name' => $_FILES['files']['tmp_name'][0],
						'name' => $_FILES['files']['name'][0]
					);
			$file = Engine_Api::_() -> getApi('core', 'ynmusic') -> createSong($temp_file);
			$status = true;
			$song_id = $file -> getIdentity();
			
			$type = $this -> _getParam('type');
			if (in_array($type, array('wall', 'message'))) {
				$songTable = Engine_Api::_() -> getItemTable('ynmusic_song');
				require_once APPLICATION_PATH . '/application/modules/Ynmusic/Libs/getid3/getid3.php';
				$getID3 = new getID3;
				$ThisFileInfo = $getID3->analyze($file->temporary());
				$duration = floor(@$ThisFileInfo['playtime_seconds']);
				$random = rand(1, 10);
				
				$now =  date("Y-m-d H:i:s");
				$song = $songTable -> createRow();
				$song -> user_id = Engine_Api::_()->user()->getViewer() -> getIdentity();
				$song -> album_id = 0;
				
				$song -> creation_date = $now;
				$song -> modified_date = $now;
				
				$song -> file_id = $file->getIdentity();
				$song -> duration = $duration;
				$song -> wave_play = -1*$random;
				$song -> wave_noplay = -1*$random;
				$song -> title = preg_replace('/\.(mp3|m4a|aac|mp4)$/i', '', $file -> name);
				
				if ($type == 'wall') {
					$song -> composer = 1;
				}
				else {
					$song -> composer = 2;
				}
				$song -> save();
				
				$auth = Engine_Api::_() -> authorization() -> context;
				$auth -> setAllowed($song, 'everyone', 'view', true);
				$auth -> setAllowed($song, 'everyone', 'comment', true);
				$auth -> setAllowed($song, 'everyone', 'download', true);
				
				$this -> view -> success = true;
				$this -> view -> song_id = $song -> song_id;
				$this -> view -> song_url = $song -> getFilePath();
				$this -> view -> song_title = $song -> getTitle();
			}
			
			$db -> commit();
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $name, 'song_id' => $song_id)))));
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$status = false;
			$name = $_FILES['files']['name'][0];
			$error = Zend_Registry::get('Zend_Translate') -> _('Upload failed by database query.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}

	}
	
	public function uploadAction()
	{
		// only members can upload music
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!Engine_Api::_()->ynmusic()->canUploadSong()) {
			return $this->_helper->requireAuth()->forward();
		}
		
		$this->_helper->content->setEnabled();
		
		$viewer = Engine_Api::_() -> user() -> getViewer();	
		$this -> view -> form = $form = new Ynmusic_Form_Create();
		$this -> view -> album_id = $this -> _getParam('album_id', '0');
		
		//get albums of viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$albumTable = Engine_Api::_() -> getDbTable('albums', 'ynmusic');
		$albums = $albumTable -> getAblumsByUser($viewer);
		
		//support business pages
		$business_id = $this->_getParam('business_id', 0);
		$group_id = $this->_getParam('subject_id', 0);
		$parent_type = $this->_getParam('parent_type', '');
		if ($business_id && Engine_Api::_()->hasModuleBootstrap('ynbusinesspages')) {
			$ids = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages')->getItemIdsMapping('ynmusic_album', array('business_id'=>$business_id, 'user_id'=>$viewer->getIdentity()));
			foreach ($albums as $album) {
				if ($album->isEditable() && $album->canAddSongs() && in_array($album->getIdentity(), $ids)) $form -> album_id -> addMultiOption($album -> getIdentity(), $album -> getTitle());
			}
		}
		else if (Engine_Api::_()->hasModuleBootstrap('advgroup') && $group_id && $parent_type == 'group') {
			$ids = Engine_Api::_()->getDbTable('mappings', 'advgroup')->getItemIdsMapping('ynmusic_album', array('subject_id'=>$group_id, 'user_id'=>$viewer->getIdentity()));
			foreach ($albums as $album) {
				if ($album->isEditable() && $album->canAddSongs() && in_array($album->getIdentity(), $ids)) $form -> album_id -> addMultiOption($album -> getIdentity(), $album -> getTitle());
			}
		}
		else {
			foreach ($albums as $album) {
				if ($album->isEditable() && $album->canAddSongs()) $form -> album_id -> addMultiOption($album -> getIdentity(), $album -> getTitle());
			}
		}
		
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$form -> released_date -> setAllowEmpty(true);
		if ($this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$values = $form -> getValues();
			
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
			
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			try 
			{
				//get genre & artists
				$genre_ids = $this ->_getParam('genre_ids'); 
				$genre_ids = explode(",",$genre_ids);
				$artist_ids = $this ->_getParam('artist_ids'); 
				$artist_ids = explode(",",$artist_ids);
							
				//getTableAlbum
				$albumTable = Engine_Api::_() -> getItemTable('ynmusic_album');
				
				$album = null;
				if(!in_array($values['album_id'], array('none', 'create'))) {
					$album = Engine_Api::_() -> getItem('ynmusic_album', $values['album_id']);
					$genre_ids = Engine_Api::_()->getDbTable('genremappings', 'ynmusic')->getGenreIdsByItem($album);
					$artist_ids = Engine_Api::_()->getDbTable('artistmappings', 'ynmusic')->getArtistIdsByItem($album);
				}
				
				//if has album or create new
				if($values['album_id'] == "create") {
					$album  = $albumTable -> createRow();
				}		
				
				
				//save value to album if has
				if($album && $values['album_id'] == "create") {
					$album -> title = (!empty($values['title']))? $values['title'] : $this -> view -> translate("Untitled album");
					$album -> description = $values['description'];
					$album -> user_id = $viewer -> getIdentity();
					
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
					
					if (empty($values['album_auth_view']))
					{
						$values['album_auth_view'] = 'everyone';
					}
			
					if (empty($values['album_auth_comment']))
					{
						$values['album_auth_comment'] = 'everyone';
					}

					if (empty($values['album_auth_download']))
					{
						$values['album_auth_download'] = 'everyone';
					}
					$viewMax = array_search($values['album_auth_view'], $roles);
					$commentMax = array_search($values['album_auth_comment'], $roles);
					$downloadMax = array_search($values['album_auth_download'], $roles);
			
					foreach ($roles as $i => $role)
					{
						$auth -> setAllowed($album, $role, 'view', ($i <= $viewMax));
						$auth -> setAllowed($album, $role, 'comment', ($i <= $commentMax));
						$auth -> setAllowed($album, $role, 'download', ($i <= $downloadMax));
					}
					
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
					
					//add activity
					$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
					$action = $activityApi->addActivity($album -> getOwner(), $album, 'ynmusic_album_create');
					if($action) {
						$activityApi->attachActivity($action, $album);
					}
					
					if (Engine_Api::_() -> hasModuleBootstrap("yncredit")) {
			        	$user = $album -> getOwner();
						if($user -> getIdentity())
			            	Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $user -> getTitle(), 'ynmusic_album', $user);
					}
				}		
				
				
				$song_count = 0;
				$song_ids = array();				
				//getTableSongs
				$songTable = Engine_Api::_() -> getItemTable('ynmusic_song');
				
				// get file_id list
				$file_ids = array();
				foreach (explode(' ', $values['html5uploadfileids']) as $file_id)
				{
					$file_id = trim($file_id);
					if (!empty($file_id))
						$file_ids[] = $file_id;
				}
				
				// Attach songs (file_ids) to album
				if (!empty($file_ids))
				{
					foreach ($file_ids as $file_id)
					{
						$file = Engine_Api::_() -> getItem('storage_file', $file_id);
						if ($file) 
						{
							try
							{
								require_once APPLICATION_PATH . '/application/modules/Ynmusic/Libs/getid3/getid3.php';
								$getID3 = new getID3;
								$ThisFileInfo = $getID3->analyze($file->temporary());
								$duration = floor(@$ThisFileInfo['playtime_seconds']);
								$random = rand(1, 10);
								
								$now =  date("Y-m-d H:i:s");
								$song = $songTable -> createRow();
								$song -> user_id = $viewer -> getIdentity();
								if(isset($album) && $album -> getIdentity()) {
									$song -> album_id = $album -> getIdentity();
								} else {
									$song -> album_id = 0;
								}
								
								$song -> creation_date = $now;
								$song -> modified_date = $now;
								
								$song -> file_id = $file->getIdentity();
								$song -> duration = $duration;
								$song -> wave_play = -1*$random;
								$song -> wave_noplay = -1*$random;
								$song -> downloadable = $values['downloadable'];
								$song -> title = preg_replace('/\.(mp3|m4a|aac|mp4)$/i', '', $file -> name);
								$song -> save();
								
								$song_count++;
								$song_ids[] = $song->getIdentity();
								
								//save mapping artists and genres
								Engine_Api::_() -> ynmusic() -> saveMappingsForItem($song, $artist_ids, $genre_ids);
								
								//add tags
								$tags = preg_split('/[,]+/', $values['tags']);
								$song -> tags() -> addTagMaps($viewer, $tags);
								
								// Set auth song if song not belong to any albums
								if($song -> album_id == 0) 
								{
									if (empty($values['song_auth_view']))
									{
										$values['song_auth_view'] = 'everyone';
									}
							
									if (empty($values['song_auth_comment']))
									{
										$values['song_auth_comment'] = 'everyone';
									}

									if (empty($values['song_auth_download']))
									{
										$values['song_auth_download'] = 'everyone';
									}
									$viewMax = array_search($values['song_auth_view'], $roles);
									$commentMax = array_search($values['song_auth_comment'], $roles);
									$downloadMax = array_search($values['song_auth_download'], $roles);
									foreach ($roles as $i => $role)
									{
										$auth -> setAllowed($song, $role, 'view', ($i <= $viewMax));
										$auth -> setAllowed($song, $role, 'comment', ($i <= $commentMax));
										$auth -> setAllowed($song, $role, 'download', ($i <= $downloadMax));
									}
								} 
								else 
								{
									$songAlbum = $song -> getAlbum();
									if($songAlbum) {
										$auth_arr = array('view', 'comment', 'download');
								        foreach ($auth_arr as $elem) {
								            foreach ($roles as $i => $role) {
								                if(1 === $auth->isAllowed($songAlbum, $role, $elem)) {
								                	$authMax = array_search($role, $roles);
							                		$auth -> setAllowed($song, $role, $elem, ($i <= $authMax));
								                }
								            }
								        }
							        }
								}
								
								//set default photo and cover for songs
								if ($song->album_id != 0 && $album) {
									if ($album->photo_id) {
										$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($album->photo_id);
										if ($photo) {
											$song->setPhoto($photo, 'photo_id');
										}
									}
									
									if ($album->cover_id) {
										$photo = Engine_Api::_()->getItemTable('storage_file')->getFile($album->cover_id);
										if ($photo) {
											$song->setPhoto($photo, 'cover_id');
										}
									}
								}
								
								
								if (Engine_Api::_() -> hasModuleBootstrap("yncredit")) {
						        	$user = $song -> getOwner();
									if($user -> getIdentity())
						            	Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $user -> getTitle(), 'ynmusic_song', $user);
								}
							} 
							catch (Exception $e) {
						      throw $e;
						    }
						}
					}
				}
				//save songs form soundcloud
				$songcloud_count = $this ->_getParam('songcloud_count');
				for($id = 1; $id <= $songcloud_count; $id++) {
					$permalink = $this ->_getParam('soundcloud_value'.$id);
					if($permalink) {
						
						$setting = Engine_Api::_()->getApi('settings', 'core');
						$cliendId = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_sound_clientid', "");
						$cliendSecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_sound_clientsecret', "");
						
						try {
							$client = new Services_Soundcloud($cliendId, $cliendSecret);
							$track = json_decode($client->get("tracks/".$permalink));
						} catch (Exception $e) {
					      	break;
					    }
						
						if($track) {
							$random = rand(1, 10);
							$now =  date("Y-m-d H:i:s");
							$song = $songTable -> createRow();
							$song -> user_id = $viewer -> getIdentity();
							$song -> title = $track -> title;
							$song -> permalink = $permalink;
							if(isset($album) && $album -> getIdentity()) {
								$song -> album_id = $album -> getIdentity();
							} else {
								$song -> album_id = 0;
							}
							$song -> creation_date = $now;
							$song -> modified_date = $now;
							$song -> duration = floor($track -> duration/1000);
							$song -> wave_play = -1*$random;
							$song -> wave_noplay = -1*$random;
							$song -> save();
							
							$song_count++;
							$song_ids[] = $song->getIdentity();
							
							//save mapping artists and genres
							Engine_Api::_() -> ynmusic() -> saveMappingsForItem($song, $artist_ids, $genre_ids);
							
							//add tags
							$tags = preg_split('/[,]+/', $values['tags']);
							$song -> tags() -> addTagMaps($viewer, $tags);
							
							
							// Set auth song if song not belong to any albums
							if($song -> album_id == 0) 
							{
								if (empty($values['song_auth_view']))
								{
									$values['song_auth_view'] = 'everyone';
								}
						
								if (empty($values['song_auth_comment']))
								{
									$values['song_auth_comment'] = 'everyone';
								}

								if (empty($values['song_auth_download']))
								{
									$values['song_auth_download'] = 'everyone';
								}
								$viewMax = array_search($values['song_auth_view'], $roles);
								$commentMax = array_search($values['song_auth_comment'], $roles);
								$downloadMax = array_search($values['song_auth_download'], $roles);
								foreach ($roles as $i => $role)
								{
									$auth -> setAllowed($song, $role, 'view', ($i <= $viewMax));
									$auth -> setAllowed($song, $role, 'comment', ($i <= $commentMax));
									$auth -> setAllowed($song, $role, 'download', ($i <= $downloadMax));
								}
							} 
							else 
							{
								$songAlbum = $song -> getAlbum();
								if($songAlbum) {
									$auth_arr = array('view', 'comment', 'download');
							        foreach ($auth_arr as $elem) {
							            foreach ($roles as $i => $role) {
							                if(1 === $auth->isAllowed($songAlbum, $role, $elem)) {
							                	$authMax = array_search($role, $roles);
						                		$auth -> setAllowed($song, $role, $elem, ($i <= $authMax));
							                }
							            }
							        }
						        }
							}
							
							if (Engine_Api::_() -> hasModuleBootstrap("yncredit")) {
					        	$user = $song -> getOwner();
								if($user -> getIdentity())
					            	Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $user -> getTitle(), 'ynmusic_song', $user);
							}
						}
					}
				}

				if(!in_array($values['album_id'], array('none', 'create')) && $song_count) {
					//add activity
					$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
					$action = $activityApi->addActivity($album -> getOwner(), $album, 'ynmusic_album_addsong', null, array('count'=> "".$song_count));
					if($action) {
						$activityApi->attachActivity($action, $album);
					}
				}
				
				if ($values['album_id'] == 'none' && !empty($song_ids)) {
					$table = Engine_Api::_()->getDbTable('alonesongs', 'ynmusic');
					$alonesong = $table->createRow();
					$alonesong->user_id = $viewer -> getIdentity();
					$alonesong->song_ids = $song_ids;
					$alonesong->creation_date = date('Y-m-d H:i:s');
					$alonesong->save();
					
					if (empty($values['song_auth_view'])) {
						$values['song_auth_view'] = 'everyone';
					}
			
					if (empty($values['song_auth_comment'])) {
						$values['song_auth_comment'] = 'everyone';
					}

					if (empty($values['song_auth_download']))
					{
						$values['song_auth_download'] = 'everyone';
					}
					$viewMax = array_search($values['song_auth_view'], $roles);
					$commentMax = array_search($values['song_auth_comment'], $roles);
					$downloadMax = array_search($values['song_auth_download'], $roles);
					foreach ($roles as $i => $role)
					{
						$auth -> setAllowed($song, $role, 'view', ($i <= $viewMax));
						$auth -> setAllowed($song, $role, 'comment', ($i <= $commentMax));
						$auth -> setAllowed($song, $role, 'download', ($i <= $downloadMax));
					}
									
					//add activity
					$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
					$action = $activityApi->addActivity($alonesong -> getOwner(), $alonesong, 'ynmusic_song_addalonesongs', null, array('count'=> "".$song_count));
					if($action) {
						$activityApi->attachActivity($action, $alonesong);
					}
				}
				
				$db -> commit();
				
				if(isset($album) && $album -> getIdentity()){
					return $this -> _forward('success', 'utility', 'core', array(
						'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
							'action' => 'edit',
							'album_id' => $album -> getIdentity()
						), 'ynmusic_album', true),
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
					));
				}
				
				return $this -> _forward('success', 'utility', 'core', array(
					'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
						'action' => 'manage',
					), 'ynmusic_song', true),
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
				));
			}
				catch (Exception $e) {
			      $db->rollBack();
			      throw $e;
		    }
		}
	}

	public function editAction() {
		$this->_helper->content->setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> song = $song = Engine_Api::_() -> getItem('ynmusic_song', $this ->_getParam('song_id'));
		if(!$song) {
			return $this->_helper->requireSubject()->forward();
		}
		
		if(!$song->isEditable()) {
			return $this->_helper->requireAuth()->forward();
		}
		$this -> view -> form = $form = new Ynmusic_Form_Song_EditSong(array('song' => $song));
		
		$form -> populate($song->toArray());
		
		//Populate Tag
        $tagStr = '';
        foreach ($song->tags()->getTagMaps() as $tagMap)
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
		$this -> view -> genreMappings = $genreMappings = $genreMappingTable -> getGenresByItem($song);
		
		//populate artist
		$artistMappingTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
		$this -> view -> artistMappings = $artistMappings = $artistMappingTable -> getArtistsByItem($song);
		
		//populate authorization
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		$auth_arr = array('view', 'comment', 'download');
        
        foreach ($auth_arr as $elem) {
            foreach ($roles as $role) {
                if(1 === $auth->isAllowed($song, $role, $elem)) {
                	$authVar = 'auth_'.$elem;
                    if ($form->$authVar)
                        $form->$authVar->setValue($role);
                }
            }
        }	
		
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if ($form -> isValid($this -> getRequest() -> getPost()))
		{
			$values = $form -> getValues();
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
				$genreMappingsTable -> deleteGenresByItem($song);
				$artistMappingsTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
				$artistMappingsTable -> deleteArtistsByItem($song);
				
				if($song) {
					$song -> setFromArray($values);
					$song -> title = (!empty($values['title']))? $values['title'] : $this -> view -> translate("Untitled song");
					$song -> description = $values['description'];
					$song -> save();
					
					//save mapping artists and genres
					Engine_Api::_() -> ynmusic() -> saveMappingsForItem($song, $artist_ids, $genre_ids);
					
					//add tags
					$tags = preg_split('/[,]+/', $values['tags']);
					$song -> tags() -> addTagMaps($viewer, $tags);
					
					// Set photo
					if (!empty($values['photo'])) {
						$song -> setPhoto($form -> photo, "photo_id");
					}
					
					// Set cover
					if (!empty($values['cover'])) {
						$song -> setPhoto($form -> cover, "cover_id");
					}
				
					// Set auth
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
						$auth -> setAllowed($song, $role, 'view', ($i <= $viewMax));
						$auth -> setAllowed($song, $role, 'comment', ($i <= $commentMax));
						$auth -> setAllowed($song, $role, 'download', ($i <= $downloadMax));
					}
				}		
				$db -> commit();
				return $this -> _forward('success', 'utility', 'core', array(
					'parentRedirect' => $song -> getHref(),
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
				));
			}
				catch (Exception $e) {
			      $db->rollBack();
			      throw $e;
		    }
		}
	}

	public function validateSongCountAction() {
		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		
		
		$values = $this->_getAllParams();
		$status = true;
		$message = false;
		
		$album = null;
		if(!in_array($values['album_id'], array('none', 'create'))) {
			$album = Engine_Api::_() -> getItem('ynmusic_album', $values['album_id']);
		}
		
		//check max businesses user can create
		$viewer = Engine_Api::_()->user()->getViewer();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max = $permissionsTable->getAllowed('ynmusic_album', $viewer->level_id, 'max_songs');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynmusic_album')
                ->where('name = ?', 'max_songs'));
            if ($row) {
                $max = $row->value;
            }
        }
		
		if ($max && $values['album_id'] != 'none') {
			$numOfExists = 0;
			if ($album) $numOfExists = $album->getCountSongs();
			$remain = $max - $numOfExists;
			$file_ids = array();
			foreach (explode(' ', $values['html5uploadfileids']) as $file_id) {
				$file_id = trim($file_id);
				if (!empty($file_id))
					$file_ids[] = $file_id;
			}
			$songcloud_count = $this ->_getParam('songcloud_count');
			$addSongs = intval($songcloud_count) + count($file_ids);
			if ($addSongs > $remain) {
				$status = false;
				$label = ($values['album_id'] != 'create') ? $this->view->translate('new') : $this->view->translate('this');
				$message = $this->view->translate('You can only add %s song(s) to %s album. Please remove some.', $remain, $label);
			}
		}
		
		$data = array(
			'status' => $status,
			'message' => $message
		);
		$data = Zend_Json::encode($data);
		echo $data;
		return;
	}
}
?>