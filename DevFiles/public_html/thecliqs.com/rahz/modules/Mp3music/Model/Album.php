<?php
class Mp3music_Model_Album extends Core_Model_Item_Abstract
{
	// Interfaces
	public function getRichContent($view = false)
	{
		$videoEmbedded = '';
		// $view == false means that this rich content is requested from the activity feed
		if ($view == false)
		{
			$desc = strip_tags($this -> description);
			$desc = "<div class='music_desc'>" . (Engine_String::strlen($desc) > 255 ? Engine_String::substr($desc, 0, 255) . '...' : $desc) . "</div>";
			$zview = Zend_Registry::get('Zend_View');
			$zview -> album = $this;
			$zview -> songs = Engine_Api::_() -> mp3music() -> getservicesongs($this);
			$videoEmbedded = $desc . $zview -> render('application/modules/Mp3music/views/scripts/_Player.tpl');
		}
		return $videoEmbedded;
	}

	/**
	 * Gets an absolute URL to the page to view this item
	 *
	 * @return string
	 */
	public function getHref($params = array())
	{
		$params = array_merge(array('album_id' => $this -> album_id), $params);
		if (isset($this -> user_id))
			$params = array_merge(array('user_id' => $this -> user_id), $params);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'mp3music_album', true);
	}

	public function getEditHref($params = array())
	{
		$params = array_merge(array('album_id' => $this -> album_id), $params);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'mp3music_edit_album', true);
	}

	public function getDeleteHref($params = array())
	{
		$params = array_merge(array(
			'album_id' => $this -> album_id,
			'module' => 'mp3music',
			'controller' => 'album',
			'action' => 'delete'
		), $params);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'default', true);
	}

	public function getPlayerHref($params = array())
	{
		#return $this->getHref($params);
		$params = array_merge(array(
			'album_id' => $this -> album_id,
			'module' => 'mp3music',
			'controller' => 'album',
			'action' => 'album'
		), $params);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'default', true);
	}

	public function getTitle()
	{
		if ($this -> composer)
			return Zend_Registry::get('Zend_Translate') -> _('_MUSIC_DEFAULT_ALBUM');
		else
			return $this -> title;
	}

	/**
	 * Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function comments()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function likes()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}

	public function getCommentCount()
	{
		return $this -> comments() -> getCommentCount();
	}

	public function getParent()
	{
		return $this -> getOwner();
	}
	
	public function getMediaType()
	{
	   return 'album';
	}
	//getCountalbum
	public function getCountAlbums($user = null)
	{
		$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
		$ab_name = $ab_table -> info('name');
		$select = $ab_table -> select() -> from($ab_table);
		if ($user)
			$select -> where('user_id = ?', $user -> getIdentity());
		return count($ab_table -> fetchAll($select));
	}

	public function getCountSongs($user = null)
	{
		$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
		$ab_name = $ab_table -> info('name');

		$select = $ab_table -> select() -> from($ab_table);
		if ($user)
			$select -> where('user_id = ?', $user -> getIdentity());
		$count_song = 0;
		$albums = $ab_table -> fetchAll($select);
		foreach ($albums as $album)
		{
			$songs = $album -> getSongs();
			foreach ($songs as $song)
				$count_song++;
		}
		return $count_song;
	}

	public function getVideosSelect($song_id = null)
	{
		$table = Engine_Api::_() -> getDbtable('videos', 'mp3music');
		$rName = $table -> info('name');

		$select = $table -> select() -> from($rName);
		$select -> where("$rName.song_id = ?", $song_id);
		$videos = $table -> fetchAll($select);
		return $videos[0];
	}

	public function getReSongs($user_id = null, $limit = null)
	{
		$as_table = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music');
		$as_name = $as_table -> info('name');
		$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
		$ab_name = $ab_table -> info('name');
		$select = $as_table -> select() -> from($as_table);
		$select -> joinLeft($ab_name, "$ab_name.album_id = $as_name.album_id", '') -> where("$ab_name.user_id = ?", $user_id);
		$select -> where("$ab_name.search = ?", "1") -> limit($limit);
		return $as_table -> fetchAll($select);
	}

	public function getTopMusics()
	{
		$as_table = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music');
		$as_name = $as_table -> info('name');
		$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
		$ab_name = $ab_table -> info('name');
		$select = $as_table -> select() -> from($as_table);
		$select -> order('play_count DESC');
		$select -> joinLeft($ab_name, "$ab_name.album_id = $as_name.album_id", '');
		$select -> where("$ab_name.search = ?", "1");
		return $as_table -> fetchAll($select);
	}

	public function getFirstSongArtists($artist_id = null)
	{
		$as_table = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music');
		$as_name = $as_table -> info('name');
		$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
		$ab_name = $ab_table -> info('name');
		$select = $as_table -> select() -> from($as_table);
		$select -> joinLeft($ab_name, "$ab_name.album_id = $as_name.album_id", '');
		$select -> where("$ab_name.search = ?", "1");
		$select -> where("$as_name.artist_id = ?", $artist_id);
		$songs = $as_table -> fetchAll($select);
		return $songs[0];
	}

	public function getStorage($user)
	{
		$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
		$ab_name = $ab_table -> info('name');

		$select = $ab_table -> select() -> from($ab_table) -> where('user_id = ?', $user -> getIdentity());
		$albums = $ab_table -> fetchAll($select);
		$sumFileSize = 0;
		foreach ($albums as $album)
		{
			$songs = $album -> getSongs();
			foreach ($songs as $song)
				$sumFileSize += $song -> filesize;

		}
		return $sumFileSize;
	}

	public function getSongs($song_id = null)
	{
		$table = Engine_Api::_() -> getDbtable('albumSongs', 'mp3music');
		$select = $table -> select() -> where('album_id = ?', $this -> getIdentity()) -> order('order ASC');
		if (!empty($song_id))
			$select -> where('song_id = ?', $song_id);

		return $table -> fetchAll($select);
	}

	public function getListSong($params = array())
	{
		$as_table = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music');
		$as_name = $as_table -> info('name');
		$c_table = Engine_Api::_() -> getDbTable('cats', 'mp3music');
		$c_name = $c_table -> info('name');
		$s_table = Engine_Api::_() -> getDbTable('singers', 'mp3music');
		$s_name = $s_table -> info('name');
		$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
		$ab_name = $ab_table -> info('name');
		$a_table = Engine_Api::_() -> getDbTable('artists', 'mp3music');
		$a_name = $a_table -> info('name');
		//check privacy
		$btable = Engine_Api::_() -> getDbtable('allow', 'authorization');
		$b_table_name = $btable -> info('name');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$select = $as_table -> select() -> from($as_table) -> distinct();
		if (!empty($params['search']))
		{
			if ($params['search'] == 'songs' && !empty($params['title']))
			{
				$keyword = $params['title'];
				$select -> where("$as_name.title_url LIKE ?", "%{$keyword}%");
			}
			if ($params['search'] == 'categories')
			{
				if ($params['id'])
				{
					$keyword = stripslashes($params['id']);
					$str_cats = "'".$keyword."'";
					$cats = Mp3music_Model_Cat::getCats($params['id']);
					if(count($cats) > 0)
					{
						foreach($cats as $cat)
						{
							$str_cats .= ",'".$cat->getIdentity()."'";
						}
					}
					$select -> where("$as_name.cat_id IN ($str_cats)");
				}
				if (!$params['id'] && !$params['title'])
				{
					$select -> where("$as_name.cat_id = ?", '0');
				}
			}
			if ($params['search'] == 'singer')
			{
				if ($params['title'])
				{
					$keyword = Mp3music_Model_Album::locdau(stripslashes($params['title']));
					$title = Mp3music_Model_Album::locdau($params['title']);
					$select -> joinLeft($s_name, "$as_name.singer_id = $s_name.singer_id", '') -> where("$s_name.title_url LIKE ?", "%{$title}%") -> orWhere("$as_name.other_singer_title_url LIKE ?", "%{$keyword}%");
				}
				if ($params['id'])
				{
					$select -> where("$as_name.singer_id = ?", $params['id']);
				}
				if (!$params['id'] && !empty($params['title']))
					$select -> where("$as_name.singer_id = ?", '0');
			}
			if ($params['search'] == 'artist')
			{
				$select -> joinLeft($a_name, "$as_name.artist_id = $a_name.artist_id", "");
				if ($params['title'])
				{
					$keyword = stripslashes($params['title']);
					$select -> where("$a_name.title LIKE ?", "%{$params['title']}%");
				}
				if ($params['id'])
				{
					$select -> where("$as_name.artist_id = ?", $params['id']);
				}
				if (!$params['id'] && !$params['title'])
				{
					$select -> where("$as_name.artist_id = ?", '0');
				}
			}
		}
		$select -> joinLeft($ab_name, "$ab_name.album_id = $as_name.album_id", '');
		if (!empty($params['search']))
		{
			if ($params['search'] == 'owner')
			{
				if ($params['title'])
				{
					$keyword = stripslashes($params['title']);
					$select -> joinLeft("engine4_users", "engine4_users.user_id = $ab_name.user_id", '') -> where("engine4_users.displayname LIKE ?", "%{$params['title']}%");
				}
				if ($params['id'])
				{
					$select -> joinLeft("engine4_users", "engine4_users.user_id = $ab_name.user_id", '') -> where("engine4_users.user_id = ?", $params['id']);
				}
			}
		}
		if (empty($params['admin']))
		{
			$select -> where("$ab_name.search = ?", "1");
		}
		else
		{
			if (!empty($params['title']))
				$select -> where("$as_name.title_url LIKE ?", "%{$params['title']}%");
			if (!empty($params['album']))
			{
				$select -> where("$ab_name.title_url LIKE ?", "%{$params['album']}%");
			}
		}
		if (!empty($params['search']))
		{
			if ($params['search'] == 'all')
			{
				if ($params['title'])
				{
					$keyword = stripslashes($params['title']);
					$select -> join("engine4_users", "engine4_users.user_id = $ab_name.user_id", '') -> joinLeft($s_name, "$as_name.singer_id = $s_name.singer_id", '') -> Where("$s_name.title_url LIKE ?", "%{$keyword}%") -> orWhere("$as_name.other_singer_title_url LIKE ?", "%{$keyword}%") -> orWhere("engine4_users.displayname LIKE ?", "%{$keyword}%") -> orWhere("$as_name.title_url LIKE ?", "%{$keyword}%") -> orWhere("$ab_name.title_url LIKE ?", "%{$keyword}%");
				}
			}
			if ($params['search'] == 'browse_topsongs')
			{
				$select -> order("$as_name.play_count DESC");
			}
			elseif ($params['search'] == 'browse_topdownloads')
			{
				$select -> order("$as_name.download_count DESC");
			}
			else
			{
				$select -> order("$as_name.song_id DESC");
			}
		}
		
		$songs = $as_table -> fetchAll($select);
		$songTemps = null;

		foreach ($songs as $song)
		{
			$album = $song -> getParent();
			if ($album -> search == 1 || !empty($params['admin']))
				$songTemps[] = $song;
		}
		if ($songTemps != null)
			return $songTemps;
		else
		{
			$select1 = $as_table -> select() -> from($as_table);
			$select1 -> where('song_id = -1');
			return $as_table -> fetchAll($select1);
		}
	}

	public function getTopAlbums($limit = null)
	{
		$table = Engine_Api::_() -> getDbtable('albums', 'mp3music');
		$select = $table -> select() -> order('play_count  DESC') -> where('search = 1') -> limit($limit);
		return $table -> fetchAll($select);
	}
	
	public function getFeaturedAlbums($limit = null)
	{
		$table = Engine_Api::_() -> getDbtable('albums', 'mp3music');
		$select = $table -> select() 
			-> order('creation_date  DESC') 
			-> where('search = 1') 
			-> where('is_featured = 1') 
			-> limit($limit);
		return $table -> fetchAll($select);
	}
	
	public function getAlbums($limit = null)
	{
		$table = Engine_Api::_() -> getDbtable('albums', 'mp3music');
		$select = $table -> select() -> order('creation_date  DESC') -> where('search = 1');
		if ($limit)
			$select -> limit($limit);
		return $table -> fetchAll($select);
	}

	public function getTopSongs($limit = null)
	{
		$as_table = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music');
		$as_name = $as_table -> info('name');
		$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
		$ab_name = $ab_table -> info('name');

		$btable = Engine_Api::_() -> getDbtable('allow', 'authorization');
		$b_table_name = $btable -> info('name');
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$select = $as_table -> select() -> from($as_table) -> distinct();
		$select -> order('play_count DESC') -> limit($limit);
		$select -> joinLeft($ab_name, "$ab_name.album_id = $as_name.album_id", '');
		$select -> where("$ab_name.search = ?", "1");
		
		return $as_table -> fetchAll($select);
	}

	public function getTopDownloads($limit = null)
	{
		$as_table = Engine_Api::_() -> getDbTable('albumSongs', 'mp3music');
		$as_name = $as_table -> info('name');
		$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
		$ab_name = $ab_table -> info('name');
		$btable = Engine_Api::_() -> getDbtable('allow', 'authorization');
		$b_table_name = $btable -> info('name');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$select = $as_table -> select() -> from($as_table) -> distinct();
		$select -> order('download_count DESC') -> limit($limit);
		$select -> joinLeft($ab_name, "$ab_name.album_id = $as_name.album_id", '');
		$select -> where("$ab_name.search = ?", "1");
		
		return $as_table -> fetchAll($select);
	}

	public function getSong($file_id)
	{
		$songs = $this -> getSongs($file_id);
		return $songs -> getRow(0);
	}

	public function getSongIDFirst()
	{
		$songs = $this -> getSongs();
		if (count($songs) > 0)
			return $songs -> getRow(0) -> song_id;
		else
			return NULL;
	}

	public function addSong($file_id, $cat_id, $singer_id, $other_singer, $artist = 0)
	{
		if ($file_id instanceof Storage_Model_File)
			$file = $file_id;
		else
			$file = Engine_Api::_() -> getItem('storage_file', $file_id);
		if ($file)
		{
			$album_song = Engine_Api::_() -> getDbtable('albumSongs', 'mp3music') -> createRow();
			$album_song -> album_id = $this -> getIdentity();
			$album_song -> file_id = $file -> getIdentity();
			$album_song -> title = preg_replace('/\.(mp3|m4a|aac|mp4)$/i', '', $file -> name);
			$str = $album_song -> title;
			$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
			$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
			$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
			$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
			$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
			$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
			$str = preg_replace("/(đ)/", "d", $str);
			$str = preg_replace("/(!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_)/", "-", $str);
			$str = preg_replace("/(-+-)/", "-", $str);
			//thay thế 2- thành 1-
			$str = preg_replace("/(^\-+|\-+$)/", "", $str);
			$str = preg_replace("/(-)/", " ", $str);
			$album_song -> title_url = $str;
			$album_song -> filesize = $file -> size;
			$album_song -> cat_id = $cat_id;
			$album_song -> singer_id = $singer_id;
			$allow_artist = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.artist', 1);
			if (!$allow_artist)
				$album_song -> artist_id = $artist;
			if ($singer_id == 0 && trim($other_singer) != "")
			{
				$album_song -> other_singer = $other_singer;
				$str = $album_song -> other_singer;
				$str = strtolower($str);
				$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
				$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
				$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
				$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
				$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
				$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
				$str = preg_replace("/(đ)/", "d", $str);
				$str = preg_replace("/(!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_)/", "-", $str);
				$str = preg_replace("/(-+-)/", "-", $str);
				//thay thế 2- thành 1-
				$str = preg_replace("/(^\-+|\-+$)/", "", $str);
				$str = preg_replace("/(-)/", " ", $str);
				$album_song -> other_singer_title_url = $str;
			}
			$album_song -> ext = $file -> extension;
			$album_song -> order = count($this -> getSongs());
			$album_song -> save();
		}
		return $album_song;
	}

	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Music_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'mp3music_album',
			'parent_id' => $this -> getIdentity()
		);
		// Save
		$storage = Engine_Api::_ ()->storage ();

		// Resize image (main)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 720, 720 )->write ( $path . '/m_' . $name )->destroy ();

		// Resize image (profile)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 240, 240 )->write ( $path . '/p_' . $name )->destroy ();

		// Resize image (normal)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 110, 110 )->write ( $path . '/in_' . $name )->destroy ();

		// Resize image (icon)
		$image = Engine_Image::factory ();
		$image->open ( $file );

		$size = min ( $image->height, $image->width );
		$x = ($image->width - $size) / 2;
		$y = ($image->height - $size) / 2;

		$image->resample ( $x, $y, $size, $size, 50, 50 )->write ( $path . '/is_' . $name )->destroy ();

		// Store
		$iMain = $storage->create ( $path . '/m_' . $name, $params );
		$iProfile = $storage->create ( $path . '/p_' . $name, $params );
		$iIconNormal = $storage->create ( $path . '/in_' . $name, $params );
		$iSquare = $storage->create ( $path . '/is_' . $name, $params );

		$iMain->bridge ( $iProfile, 'thumb.profile' );
		$iMain->bridge ( $iIconNormal, 'thumb.normal' );
		$iMain->bridge ( $iSquare, 'thumb.icon' );

		// Remove temp files
		@unlink ( $path . '/p_' . $name );
		@unlink ( $path . '/m_' . $name );
		@unlink ( $path . '/in_' . $name );
		@unlink ( $path . '/is_' . $name );
		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $iMain -> getIdentity();
		$this -> save();

		return $this;
	}

	function isViewable()
	{
		return $this -> authorization() -> isAllowed(null, 'view');
	}

	function isEditable()
	{
		return $this -> authorization() -> isAllowed(null, 'edit');
	}

	function isDeletable()
	{
		return $this -> authorization() -> isAllowed(null, 'delete');
	}

	function locdau($str)
	{
		$str = strtolower($str);
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
		$str = preg_replace("/(đ)/", "d", $str);
		$str = preg_replace("/(!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_)/", "-", $str);
		$str = preg_replace("/(-+-)/", "-", $str);
		//thay thế 2- thành 1-
		$str = preg_replace("/(^\-+|\-+$)/", "", $str);
		$str = preg_replace("/(-)/", " ", $str);
		return $str;
	}

}
