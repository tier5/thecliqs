<?php
class Mp3music_AlbumController extends Core_Controller_Action_Standard
{
	protected $_paginate_params = array();
	public function init()
	{
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$this -> view -> navigation = $this -> getNavigation();
		$this -> _paginate_params['limit'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.songsPerPage', 10);
		$this -> _paginate_params['sort'] = $this -> getRequest() -> getParam('sort', 'recent');
		$this -> _paginate_params['page'] = $this -> getRequest() -> getParam('page', 1);
		$this -> _paginate_params['search'] = $this -> getRequest() -> getParam('search', '');
		$this -> _paginate_params['typesearch'] = $this -> getRequest() -> getParam('typesearch', '');
		$this -> _paginate_params['title'] = $this -> getRequest() -> getParam('title', '');
		$this -> _paginate_params['id'] = $this -> getRequest() -> getParam('id', '');
	}

	public function manageAction()
	{
		// only members can manage music
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this->_helper->content->setEnabled();
		$params = array_merge($this -> _paginate_params, array('user' => $this -> view -> viewer_id, ));
		$obj = new Mp3music_Api_Core();
		$this -> view -> albumPaginator = $obj -> getAlbumPaginator($params);
		$this -> view -> params = $params;
	}

	public function createAction()
	{
		// only members can upload music
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		// check permission create album
		if (!$this -> _helper -> requireAuth() -> setAuthParams('mp3music_album', null, 'create') -> isValid())
			return;
			
		$this->_helper->content->setEnabled();
			
		$this -> view -> form = new Mp3music_Form_CreateAlbum();
		$this -> view -> album_id = $this -> _getParam('album_id', '0');
		if ((isset($_POST['upload_message']) && $_POST['upload_message'] == 'on') || isset($_POST['title']))
		{
			$this -> view -> upload_message = 1;

		}
		if ($this -> getRequest() -> isPost() && isset($_POST['title']) && $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = Engine_Api::_() -> getDbTable('albums', 'mp3music') -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$album = $this -> view -> form -> saveValues();

				// Send notifications for subscribers
				Engine_Api::_() -> getDbtable('subscriptions', 'mp3music') -> sendNotifications($album);

				$db -> commit();
				return $this -> _redirect('mp3-music/edit_album/album_id/' . $album -> album_id);
			}
			catch (Exception $e)
			{
				$db -> rollback();
				throw $e;
			}
		}
	}

	public function editAction()
	{
		// only members can upload music
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		// only user and admins and moderators can edit
		if (!$this -> _helper -> requireAuth() -> setAuthParams('mp3music_album', null, 'edit') -> isValid())
			return;
		$album_id = $this -> getRequest() -> getParam('album_id');
		$album = $this -> view -> album = Engine_Api::_() -> getItem('mp3music_album', $album_id);
		if (!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'edit') -> isValid())
			return;
		if (empty($album) && $album_id > 0)
		{
			$this -> _helper -> redirector -> gotoUrl(array(), 'mp3music_browse', true);
			return;
		}

		foreach ($this->_navigation->getPages() as $page)
			if ($page -> route == 'mp3music_manage_album')
				$page -> setActive(true);
		$this -> view -> form = new Mp3music_Form_EditAlbum();
		$this -> view -> form -> populate($album);
		if ($this -> getRequest() -> isPost() && $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = Engine_Api::_() -> getDbTable('albums', 'mp3music') -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$this -> view -> form -> saveValues();
				$db -> commit();
				return $this -> _redirect('mp3-music/manage_album');
			}
			catch (Exception $e)
			{
				$db -> rollback();
				throw $e;
			}
		}
	}

	public function editAddSongAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('You must be logged in.');
			return;
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams('mp3music_album', null, 'create') -> isValid())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('You are not allowed to upload songs.');
			return;
		}
		$album_id = $this -> getRequest() -> getParam('album_id', false);
		if (false === $album_id)
		{
			$this -> view -> dump = $this -> getRequest();
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Invalid album');
			return;
		}
		// if the song was uploaded via composer, album_id == -1
		// so we'll need to fetch the true album_id, or create it
		$type = $this -> _getParam('type', 'wall');
		if ($album_id == -1 && $type == 'wall')
		{
			$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
			$obj = new Mp3music_Api_Core();
			$select = $obj -> getAlbumSelect(array('user' => $this -> view -> viewer_id)) -> where('composer = 1') -> limit(1);
			$row = $ab_table -> fetchRow($select);
			if (!empty($row))
			{
				$album_id = $row -> album_id;
			}
			else
			{
				$db = $ab_table -> getAdapter();
				$db -> beginTransaction();
				try
				{
					$row = $ab_table -> createRow();
					$row -> title = $translate -> _('_MUSIC_DEFAULT_ALBUM');
					$row -> user_id = $this -> view -> viewer_id;
					$row -> composer = 1;
					$row -> search = 0;
					$row -> save();
					$album_id = $row -> album_id;

					// Authorizations
					$auth = Engine_Api::_() -> authorization() -> context;
					$auth -> setAllowed($row, 'everyone', 'view', true);
					$auth -> setAllowed($row, 'everyone', 'comment', true);
					$db -> commit();
					// Rebuild privacy

				}
				catch (Exception $e)
				{
					$db -> rollback();
					$this -> view -> success = false;
					$this -> view -> error = $translate -> _('Unable to create default album in database');
					return;
				}
			}
		}
		else
		if ($album_id == -1 && $type == 'message')
		{
			$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
			$db = $ab_table -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$row = $ab_table -> createRow();
				$row -> title = $translate -> _('_MUSIC_MESSAGE_ALBUM');
				$row -> user_id = $this -> view -> viewer_id;
				// composer 2 == it's a message
				$row -> composer = 2;
				$row -> search = 0;
				$row -> save();
				$album_id = $row -> album_id;

				// Authorizations
				$auth = Engine_Api::_() -> authorization() -> context;
				$auth -> setAllowed($row, 'owner', 'view', true);
				$auth -> setAllowed($row, 'owner', 'comment', true);

				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollback();
				$this -> view -> success = false;
				$this -> view -> error = $translate -> _('Unable to create default album in database');
				return;
			}
		}
		// only owner and moderators can edit this album
		$album = Engine_Api::_() -> getItem('mp3music_album', $album_id);
		if (!$album -> isEditable())
		{
			$this -> view -> success = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('You are not allowed to edit this album');
			return;
		}
		// this is already being done in a transaction:
		$this -> uploadSongAction($album);
		// we want to do the assigning-to-album in a transaction, though
		$db = Engine_Api::_() -> getDbTable('albums', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$user = Engine_Api::_() -> user() -> getViewer();
			$max_songs = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('mp3music_album', $user, 'max_songs');
			if ($max_songs == "")
				$max_songs = 5;
			$song_count = count($album -> getSongs());
			if ($song_count < $max_songs && $this -> view -> song)
			{
				$song = $album -> addSong($this -> view -> song, 0, 0, 0);
				if ($song)
				{
					$db -> commit();
					$this -> view -> success = true;
					$this -> view -> song_id = $song -> song_id;
					$this -> view -> song_url = $song -> getFilePath();
					$this -> view -> song_title = $song -> getTitle();
				}
				else
				{
					$db -> rollback();
					$this -> view -> success = false;
					$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Song was not successfully attached');
				}
			}
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Unable to add song to album');
			$this -> view -> exception = $e -> __toString();

		}
	}

	public function deleteAction()
	{
		$album = Engine_Api::_() -> getItem('mp3music_album', $this -> getRequest() -> getParam('album_id'));
		if (!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'delete') -> isValid())
			return;
		$this -> view -> album_id = $album -> getIdentity();
		// This is a smoothbox by default
		if (null === $this -> _helper -> ajaxContext -> getCurrentContext())
			$this -> _helper -> layout -> setLayout('default-simple');
		else// Otherwise no layout
			$this -> _helper -> layout -> disableLayout(true);
		if (!$this -> getRequest() -> isPost())
			return;
		$db = Engine_Api::_() -> getDbtable('albums', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			foreach ($album->getSongs() as $song)
				$song -> deleteUnused();
			$album -> delete();
			$db -> commit();
			$this -> view -> success = true;
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array($this -> view -> translate('Delete album successfully.'))
			));
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}

	public function albumAction()
	{
		$album = Engine_Api::_() -> getItem('mp3music_album', $this -> getRequest() -> getParam('album_id'));
		if(!$album)
		{
			return $this->_helper->requireAuth->forward ();
		}
		$song = Engine_Api::_() -> getItem('mp3music_album_song', $this -> getRequest() -> getParam('song_id'));
		if (empty($song))
		{
			//$song = Engine_Api::_()->getItem('mp3music_album_song', $album->getSongIDFirst());
			Engine_Api::_() -> core() -> setSubject($album);
		}
		if (!empty($song))
		{
			Engine_Api::_() -> core() -> setSubject($song);
		}
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'view') -> isValid())
			return;
		$this -> _helper -> content -> setNoRender() -> setEnabled();

	}

	public function albumSortAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		$album = Engine_Api::_() -> getItem('mp3music_album', $this -> getRequest() -> getParam('album_id'));
		if (!$this -> getRequest() -> isPost() || !$album || $this -> view -> viewer_id !== $album -> user_id)
		{
			$this -> view -> error = $translate -> _('Invalid album');
			return;
		}
		if (!$album -> isEditable())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not allowed to edit this album');
			return;
		}
		$songs = $album -> getSongs();
		$order = explode(',', $this -> getRequest() -> getParam('order'));
		foreach ($order as $i => $item)
		{
			$song_id = substr($item, strrpos($item, '_') + 1);
			foreach ($songs as $song)
			{
				if ($song -> song_id == $song_id)
				{
					$song -> order = $i;
					$song -> save();
				}
			}
		}
		$this -> view -> songs = $album -> getSongs() -> toArray();
	}

	public function editSongAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		// only members can upload music
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> view -> form = new Mp3music_Form_EditSong();
		$this -> view -> song_id = $this -> getRequest() -> getParam('song_id');
		$this -> view -> album_id = $this -> getRequest() -> getParam('album_id');
		if ($this -> getRequest() -> isPost() && $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music') -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$this -> view -> form -> saveValues();
				$db -> commit();
				$this -> view -> success = true;
				return $this -> _redirect('mp3-music/edit_album/album_id/' . $this -> view -> album_id);
			}
			catch (Exception $e)
			{
				$db -> rollback();
				$this -> view -> success = false;
			}
		}
	}

	public function deleteSongAction()
	{
		$song = Engine_Api::_() -> getItem('mp3music_album_song', $this -> getRequest() -> getParam('song_id'));
		$translate = Zend_Registry::get('Zend_Translate');
		// only members can delete song
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> view -> song_id = $song -> getIdentity();
		// This is a smoothbox by default
		if (null === $this -> _helper -> ajaxContext -> getCurrentContext())
			$this -> _helper -> layout -> setLayout('default-simple');
		else// Otherwise no layout
			$this -> _helper -> layout -> disableLayout(true);
		if (!$this -> getRequest() -> isPost())
			return;
		$db = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$song -> deleteUnused();
			$db -> commit();
			$this -> view -> success = true;
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array('Delete song successfully.')
			));
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
		}
	}

	public function removeSongAlbumAction()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('isGet');
			exit ;
		}
		$song = Engine_Api::_() -> getItem('mp3music_album_song', $this -> getRequest() -> getParam('song_id'));
		if (!$song)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid song');
			$this -> view -> post = $_POST;
			return;
		}
		$album = $this -> view -> album = $song -> getParent();
		if (!$album || $this -> view -> viewer_id !== $album -> user_id)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Invalid album');
			return;
		}
		if (!$album -> isEditable())
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('You are not allowed to edit this album');
			return;
		}
		$db = Engine_Api::_() -> getDbTable('albums', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$song -> deleteUnused();
			$db -> commit();
			$this -> view -> success = true;
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Unknown database error');
			throw $e;
		}
	}

	public function songPlayTallyAction()
	{
		$song = Engine_Api::_() -> getItem('mp3music_album_song', $this -> getRequest() -> getParam('song_id'));
		if ($song -> singer_id != 0)
		{
			$singer = Engine_Api::_() -> getItem('mp3music_singer', $song -> singer_id);
			$singer -> play_count++;
			$singer -> save();
		}
		$album = $song ? $song -> getParent() : false;
		if ($this -> getRequest() -> isPost() && $album)
		{
			$db = Engine_Api::_() -> getDbTable('albums', 'mp3music') -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$song -> play_count++;
				$song -> save();
				$album -> play_count++;
				$album -> save();

				$db -> commit();

				$this -> view -> success = true;
				$this -> view -> song = $song -> toArray();
				$this -> view -> play_count = $song -> playCountLanguagified();
			}
			catch (Exception $e)
			{
				$db -> rollback();
				$this -> view -> success = false;
			}

		}
		else
		{
			$this -> view -> success = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('invalid song_id');
		}
	}

	public function songPlayerAjaxAction()
	{
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		//khong su dung view
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$song = Engine_Api::_() -> getItem('mp3music_album_song', $this -> getRequest() -> getParam('song_id'));
		$album = Engine_Api::_() -> getItem('mp3music_album', $song -> album_id);
		echo $this -> view -> partial('_player_ajax.tpl', array(
			'song' => $song,
			'download' => $album -> is_download
		));
		return;
	}

	public function serviceAction()
	{
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		//khong su dung view
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		if ($this -> getRequest() -> getParam('name'))
		{
			$name = $this -> getRequest() -> getParam('name');
		}
		else
		{
			$name = "home";
		}
		if ($this -> getRequest() -> getParam('idalbum'))
		{
			$idalbum = $this -> getRequest() -> getParam('idalbum');
		}
		else
		{
			$idalbum = 0;
		}
		if ($this -> getRequest() -> getParam('idplaylist'))
		{
			$idplaylist = $this -> getRequest() -> getParam('idplaylist');
		}
		else
		{
			$idplaylist = 0;
		}
		if ($this -> getRequest() -> getParam('idsong'))
		{
			$idsong = $this -> getRequest() -> getParam('idsong');
		}
		else
		{
			$idsong = "";
		}
		if ($this -> getRequest() -> getParam('vote'))
		{
			$vote = $this -> getRequest() -> getParam('vote');
		}
		else
		{
			$vote = "";
		}
		if ($this -> getRequest() -> getParam('p'))
		{
			$p = $this -> getRequest() -> getParam('p');
		}
		else
		{
			$p = 1;
		}
		switch($name)
		{
			case "votesong" :
				$this -> service_vote($idsong, $vote);
				break;
			case "playscount" :
				$this -> service_playcount($idsong);
				break;
		}
	}

	public function replaceTitle($str)
	{
		$str = preg_replace("/(&)/", "&amp;", $str);
		$str = preg_replace("/(\")/", "&quot;", $str);
		$str = preg_replace("/(\')/", "&apos; ", $str);
		$str = preg_replace("/(<)/", "&lt;", $str);
		$str = preg_replace("/(>)/", "&gt;", $str);
		return $str;
	}

	public function service_playcount($songid)
	{
		$song = Engine_Api::_() -> getItem('mp3music_album_song', $songid);
		if ($song -> singer_id != 0)
		{
			$singer = Engine_Api::_() -> getItem('mp3music_singer', $song -> singer_id);
			$singer -> play_count++;
			$singer -> save();
		}
		$album = $song -> getParent();
		$db = Engine_Api::_() -> getDbTable('albums', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$song -> play_count++;
			$song -> save();
			$album -> play_count++;
			$album -> save();

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollback();

		}
		echo Zend_Json::encode(array('success'=>1));
	}

	public function selfURL()
	{
		$server_array = explode("/", $_SERVER['PHP_SELF']);
		$server_array_mod = array_pop($server_array);
		if ($server_array[count($server_array) - 1] == "admin")
		{
			$server_array_mod = array_pop($server_array);
		}
		$server_info = implode("/", $server_array);
		return "http://" . $_SERVER['HTTP_HOST'] . $server_info . "/";
	}

	public function service_vote($songid, $vote_no)
	{
		$song = Engine_Api::_() -> getItem('mp3music_album_song', $songid);
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		if ($user_id == 0)
		{
			$xmlstr = "<?xml version='1.0' ?>\n" . "<respone></respone>";
			$xml = new SimpleXMLElement($xmlstr);
			$xml -> addChild("result", "USER_IS_NOT_LOGGED_IN");
			header("Content-type: text/xml");
			echo $xml -> asXML();
			return;
		}

		if ($vote_no < 1 || !$vote_no || $vote_no > 5)
		{
			$xmlstr = "<?xml version='1.0' ?>\n" . "<respone></respone>";
			$xml = new SimpleXMLElement($xmlstr);
			$xml -> addChild("result", "VOTE MUST BE WITHIN 1 - 5");
			header("Content-type: text/xml");
			echo $xml -> asXML();
			return;
		}
		$votes = Mp3music_Model_Rating::checkUservote($songid, $user_id);

		if (count($votes) > 0)
		{
			$votes -> rating = $vote_no;
			$votes -> save();
			$xmlstr = "<?xml version='1.0' ?>\n" . "<respone></respone>";
			$xml = new SimpleXMLElement($xmlstr);
			$xml -> addChild("result", ($votes) ? "success" : "unsucess");
			header("Content-type: text/xml");
			echo $xml -> asXML();
			return;

		}
		else
		{
			$vote = Engine_Api::_() -> getDbtable('ratings', 'mp3music') -> createRow();
			$vote -> item_id = $songid;
			$vote -> user_id = $user_id;
			$vote -> rating = $vote_no;
			$vote -> save();
			$xmlstr = "<?xml version='1.0' ?>\n" . "<respone></respone>";
			$xml = new SimpleXMLElement($xmlstr);
			$xml -> addChild("result", ($vote_no) ? "success" : "unsucess");
			header("Content-type: text/xml");
			echo $xml -> asXML();
			return;
		}
	}

	public function uploadSongAction($album = null)
	{
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$translate = Zend_Registry::get('Zend_Translate');
		// only members can upload music
		$user = Engine_Api::_() -> user() -> getViewer();
		$max_songs = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('mp3music_album', $user, 'max_songs');
		if ($max_songs == "")
			$max_songs = 5;
		if ($album != null)
			$song_count = count($album -> getSongs());
		else
			$song_count = 0;
		if ($song_count >= $max_songs)
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Number song of album is limited!');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
		}
		if (!$this -> _helper -> requireUser() -> checkRequire())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded or session expired.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
		}

		if (!$this -> getRequest() -> isPost())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
		}
		if(!empty($_FILES['Filedata']))
		{
			$_FILES['files']['type'][0] = $_FILES['Filedata']['type'];
			$_FILES['files']['tmp_name'][0] = $_FILES['Filedata']['tmp_name'];
			$_FILES['files']['name'][0] = $_FILES['Filedata']['name'];
			$_FILES['files']['error'][0] = $_FILES['Filedata']['error'];
			$_FILES['files']['size'][0] = $_FILES['Filedata']['size'];
		}
		if (empty($_FILES['files']))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $error)))));
		}
		$name = $_FILES['files']['name'][0];
		if (!isset($_FILES['files']) || !is_uploaded_file($_FILES['files']['tmp_name'][0]))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload or file too large');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}

		if (!preg_match('/\.(mp3)$/', $name))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid file type');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}
		$user = Engine_Api::_() -> user() -> getViewer();
		$max_fileSizes = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('mp3music_album', $user, 'max_filesize');
		$max_storage = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('mp3music_album', $user, 'max_storage');
		$sumFileSize = Mp3music_Model_Album::getStorage($user);
		if ($_FILES['files']['size'][0] + $sumFileSize > $max_storage * 1024)
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Storage space of user is limited!');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}
		if ($_FILES['files']['size'][0] > $max_fileSizes * 1024)
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload or file too large');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}
		$db = Engine_Api::_() -> getDbtable('albums', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$temp_file = array(
						'type' => $_FILES['files']['type'][0],
						'tmp_name' => $_FILES['files']['tmp_name'][0],
						'name' => $_FILES['files']['name'][0]
					);
			$song = Engine_Api::_() -> getApi('core', 'mp3music') -> createSong($temp_file);
			$status = true;
			$this->view->song     = $song;
			$song_id = $song -> getIdentity();
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

	public function iframeHtmlAction()
	{
		$this -> _helper -> layout -> setLayout('default-simple');
		$album = Engine_Api::_() -> getItem('mp3music_album', $this -> getRequest() -> getParam('album_id'));
		$song = Engine_Api::_() -> getItem('mp3music_album_song', $this -> getRequest() -> getParam('song_id'));
		$songs = array();
		if (!empty($song))
		{
			$this -> view -> song = $song;
			$songs = Engine_Api::_() -> mp3music() -> getServiceSongs($album, $song -> getIdentity());
		}
		else
		{
			$songs = Engine_Api::_() -> mp3music() -> getServiceSongs($album);
			$this -> view -> song = Engine_Api::_() -> getItem('mp3music_album_song', $album -> getSongIDFirst());
		}
		$this -> view -> songs = $songs;
		$this -> view -> album = $album;
	}

	public function subscribeAction()
	{
		// Must have a viewer
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		// Get viewer and subject
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$this -> _helper -> requireAuth() -> setAuthParams('mp3music_album', $viewer, 'view') -> isValid())
		{
			return;
		}

		// Get subject
		if (($user_id = $this -> _getParam('user_id')) && ($user = Engine_Api::_() -> getItem('user', $user_id)) instanceof User_Model_User)
		{
			$subject = $user;
			Engine_Api::_() -> core() -> setSubject($subject);
		}
		else
		{
			$subject = null;
		}

		// Must have a subject
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}

		// Must be allowed to view this member
		if (!$this -> _helper -> requireAuth() -> setAuthParams($subject, $viewer, 'view') -> isValid())
		{
			return;
		}

		$user = Engine_Api::_() -> core() -> getSubject('user');

		// Get subscription table
		$subscriptionTable = Engine_Api::_() -> getDbtable('subscriptions', 'mp3music');

		// Check if they are already subscribed
		if ($subscriptionTable -> checkSubscription($user, $viewer))
		{
			$this -> view -> status = true;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('You are already subscribed mp3 music to this member.');

			return $this -> _forward('success', 'utility', 'core', array(
				'parentRefresh' => true,
				'messages' => array($this -> view -> message)
			));
		}

		// Make form
		$this -> view -> form = $form = new Core_Form_Confirm( array(
			'title' => 'Subscribe?',
			'description' => 'Would you like to subscribe mp3 music to this member?',
			'class' => 'global_form_popup',
			'submitLabel' => 'Subscribe',
			'cancelHref' => 'javascript:parent.Smoothbox.close();',
		));

		// Check method
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		// Check valid
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$db = $user -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$subscriptionTable -> createSubscription($user, $viewer);
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		// Success
		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('You are now subscribed mp3 music to this member.');

		return $this -> _forward('success', 'utility', 'core', array(
			'parentRefresh' => true,
			'messages' => array($this -> view -> message)
		));
	}

	public function unsubscribeAction()
	{
		// Must have a viewer
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		// Get viewer and subject
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$this -> _helper -> requireAuth() -> setAuthParams('mp3music_album', $viewer, 'view') -> isValid())
		{
			return;
		}

		// Get subject
		if (($user_id = $this -> _getParam('user_id')) && ($user = Engine_Api::_() -> getItem('user', $user_id)) instanceof User_Model_User)
		{
			$subject = $user;
			Engine_Api::_() -> core() -> setSubject($subject);
		}
		else
		{
			$subject = null;
		}

		// Must have a subject
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}

		// Must be allowed to view this member
		if (!$this -> _helper -> requireAuth() -> setAuthParams($subject, $viewer, 'view') -> isValid())
		{
			return;
		}
		$user = Engine_Api::_() -> core() -> getSubject('user');

		// Get subscription table
		$subscriptionTable = Engine_Api::_() -> getDbtable('subscriptions', 'mp3music');

		// Check if they are already not subscribed
		if (!$subscriptionTable -> checkSubscription($user, $viewer))
		{
			$this -> view -> status = true;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('You are already not subscribed mp3 music to this member.');

			return $this -> _forward('success', 'utility', 'core', array(
				'parentRefresh' => true,
				'messages' => array($this -> view -> message)
			));
		}

		// Make form
		$this -> view -> form = $form = new Core_Form_Confirm( array(
			'title' => 'Unsubscribe?',
			'description' => 'Would you like to unsubscribe mp3 music from this member?',
			'class' => 'global_form_popup',
			'submitLabel' => 'Unsubscribe',
			'cancelHref' => 'javascript:parent.Smoothbox.close();',
		));

		// Check method
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		// Check valid
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$db = $user -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$subscriptionTable -> removeSubscription($user, $viewer);
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		// Success
		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('You are no longer subscribed mp3 music to this member.');

		return $this -> _forward('success', 'utility', 'core', array(
			'parentRefresh' => true,
			'messages' => array($this -> view -> message)
		));
	}

	/* Utility */
	protected $_navigation;
	public function getNavigation()
	{
		$tabs = array();
		$tabs[] = array(
			'label' => 'Browse Music',
			'route' => 'mp3music_browse',
			'action' => 'browse',
			'controller' => 'index',
			'module' => 'mp3music'
		);
		$tabs[] = array(
			'label' => 'My Albums',
			'route' => 'mp3music_manage_album',
			'action' => 'manage',
			'controller' => 'album',
			'module' => 'mp3music'
		);
		$tabs[] = array(
			'label' => 'My Playlists',
			'route' => 'mp3music_manage_playlist',
			'action' => 'manage',
			'controller' => 'playlist',
			'module' => 'mp3music'
		);
		$tabs[] = array(
			'label' => 'Upload Music',
			'route' => 'mp3music_create_album',
			'action' => 'create',
			'controller' => 'album',
			'module' => 'mp3music'
		);
		if (is_null($this -> _navigation))
		{
			$this -> _navigation = new Zend_Navigation();
			$this -> _navigation -> addPages($tabs);
		}
		return $this -> _navigation;
	}

}
?>