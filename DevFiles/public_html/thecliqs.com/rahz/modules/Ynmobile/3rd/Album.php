<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2014 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Album.php LONGL $
 * @author     LONGL
 */

class Ynmobile_Api_Album extends Core_Api_Abstract
{
	protected function getAlbums($aData)
	{
		extract($aData);
		if (!isset($iPage))
		{
			$iPage = 1;
		}
		if ($iPage == 0)
		{
			return array();
		}
		if (!isset($iLimit))
		{
			$iLimit = 10;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$params = array();
		if (isset($sView) && $sView != "")
		{
			$params['user'] = $viewer -> getIdentity();
		}
		if (isset($sSearch) && $sSearch != "")
		{
			$params['title'] = $sSearch;
		}
		if (isset($sOrder) && in_array($sOrder, array("recent", "popular")))
		{
			$params['sort'] = $sOrder;
		}
		else
		{ 
			$params['sort'] = 'recent';
		}
		$params['page'] = $iPage;
		$params['limit'] = $iLimit;
		$albums = Engine_Api::_()->mp3music()->getAlbumPaginator($params);
		$totalPage = (integer) ceil($albums->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
		{
			return array();
		}
		$aResult = array();
		foreach ($albums as $album)
		{
			//photoURL
			$sProfileImage = $album -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
			if ($sProfileImage)
			{
				$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
			}
			else
			{
				$sProfileImage = NO_ALBUM_MAIN;
			}
			$total_track = count($album -> getSongs());
	
			$create = strtotime($album -> creation_date);
			// Prepare data in locale timezone
			$timezone = null;
			if (Zend_Registry::isRegistered('timezone'))
			{
				$timezone = Zend_Registry::get('timezone');
			}
			if (null !== $timezone)
			{
				$prevTimezone = date_default_timezone_get();
				date_default_timezone_set($timezone);
			}
	
			$sTime = date("D, j M Y G:i:s O", $create);
	
			if (null !== $timezone)
			{
				date_default_timezone_set($prevTimezone);
			}
			$owner = $album -> getOwner();
			$sUserImageUrl = $owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sUserImageUrl != "")
			{
				$sUserImageUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImageUrl);
			}
			else
			{
				$sUserImageUrl = NO_USER_ICON;
			}
			$bCanComment = $bCanLike = (Engine_Api::_() -> authorization() -> isAllowed($album, null, 'comment')) ? true : false;
			$aResult[] = array(
					'bIsLiked' => $album -> likes() -> isLike($viewer),
					'iAlbumId' => $album -> getIdentity(),
					'iUserId' => $album -> user_id,
					'sName' => $album -> getTitle(),
					'sImagePath' => $sProfileImage,
					'iTotalTrack' => $total_track,
					'iTotalPlay' => $album -> play_count,
					'iTotalComment' => $album -> comments() -> getCommentCount(),
					'iTotalLike' => $album -> likes() -> getLikeCount(),
					'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($album),
					'iTimeStamp' => $create,
					'sTimeStamp' => $sTime,
					'sFullTimeStamp' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($create),
					'sFullname' => $owner -> getTitle(),
					'sUserImage' => $sUserImageUrl,
					'bIsInvisible' => !$album -> search,
					'bCanView' => (Engine_Api::_() -> authorization() -> isAllowed($album, null, 'view')) ? true : false,
					'bCanComment' => $bCanComment,
					'bCanLike' => $bCanLike,
			);
		}
		return $aResult;
	}
	
	/**
	 *
	 * Fetch|Search albums
	 *
	 * Request params:
	 * - sSearchText	: 	string, optional.
	 * - sView			: 	string, optional. Ex: my, all
	 * - iLimit	: 	int, optional.
	 * - iPage			: 	int, optional.
	 * - sSearch		: 	string, optional.
	 *
	 * Responsed params:
	 * - bIsLiked		: 	boolean. viewer liked this album or NOT
	 * - iAlbumId		: 	int. album id
	 * - iUserId		: 	int. owner id
	 * - sName			: 	string. album name
	 * - sImagePath		: 	string. album cover path
	 * - iTotalTrack	: 	int. number of songs
	 * - iTotalPlay		: 	int. play count
	 * - iTotalComment	: 	int. total comment
	 * - iTotalLike		: 	int. total like
	 * - aUserLike 		:	array. users that liked this albums
	 * - iTimeStamp		: 	int. creation timestamp
	 * - sTimeStamp		: 	string. creation time string
	 * - sFullTimeStamp	: 	string. creation date
	 * - sFullname		: 	string. owner full name
	 * - sUserImage		: 	string. owner image url
	 * - bIsInvisible	: 	boolean. this album is invisible or not
	 * - bCanView		:	boolean. ability to view this album
	 * - bCanComment	:	boolean. ability to comment this album
	 * - bCanLike		: 	boolean. ability to like this album
	 * 
	 * @param array $aData
	 * @return array $aResult
	 * @access public
	 */
	public function fetch($aData)
	{
		return $this->getAlbums($aData);
	}
	
	protected function saveAlbumValues($values, $albumId = null)
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!is_null($albumId))
		{
			$album = Engine_Api::_() -> getItem('mp3music_album', $albumId);
		}
		else
		{
			$album = Engine_Api::_() -> getDbtable('albums', 'mp3music') -> createRow();
		}
		$album -> title = trim(htmlspecialchars($values['title']));
		if (empty($album -> title))
			$album -> title = $translate -> _('_MUSIC_UNTITLED_ALBUM');
		$str = $album -> title;
		$str = strtolower($str);
		$str = preg_replace("/(Ã |Ã¡|áº¡|áº£|Ã£|Ã¢|áº§|áº¥|áº­|áº©|áº«|Äƒ|áº±|áº¯|áº·|áº³|áºµ)/", "a", $str);
		$str = preg_replace("/(Ã¨|Ã©|áº¹|áº»|áº½|Ãª|á»�|áº¿|á»‡|á»ƒ|á»…)/", "e", $str);
		$str = preg_replace("/(Ã¬|Ã­|á»‹|á»‰|Ä©)/", "i", $str);
		$str = preg_replace("/(Ã²|Ã³|á»�|á»�|Ãµ|Ã´|á»“|á»‘|á»™|á»•|á»—|Æ¡|á»�|á»›|á»£|á»Ÿ|á»¡)/", "o", $str);
		$str = preg_replace("/(Ã¹|Ãº|á»¥|á»§|Å©|Æ°|á»«|á»©|á»±|á»­|á»¯)/", "u", $str);
		$str = preg_replace("/(á»³|Ã½|á»µ|á»·|á»¹)/", "y", $str);
		$str = preg_replace("/(Ä‘)/", "d", $str);
		$str = preg_replace("/(!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_)/", "-", $str);
		$str = preg_replace("/(-+-)/", "-", $str);
		$str = preg_replace("/(^\-+|\-+$)/", "", $str);
		$str = preg_replace("/(-)/", " ", $str);
		$album -> title_url = $str;
		$album -> user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$album -> description = trim(htmlspecialchars($values['description']));
		$album -> search = $values['search'];
		$album -> save();
		$values['album_id'] = $album -> album_id;
		
		// Only create activity feed item if "search" is checked
		if ($album -> search)
		{
			$activity = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $activity -> addActivity(Engine_Api::_() -> user() -> getViewer(), $album, 'mp3music_album_new', null, array('count' => count($file_ids)));
			if (null !== $action)
				$activity -> attachActivity($action, $album);
		}
		
		// Authorizations
		$auth = Engine_Api::_() -> authorization() -> context;
		$prev_allow_comment = $prev_allow_view = false;
		$roles = array(
			'everyone' => 'Everyone',
			'registered' => 'All Registered Members',
			'owner_network' => 'Friends and Networks',
			'owner_member_member' => 'Friends of Friends',
			'owner_member' => 'Friends Only',
			'owner' => 'Just Me'
		);
		foreach ($roles as $role => $role_label)
		{
			// allow viewers
			if ($values['auth_view'] == $role || $prev_allow_view)
			{
				$auth -> setAllowed($album, $role, 'view', true);
				$prev_allow_view = true;
			}
			else
				$auth -> setAllowed($album, $role, 'view', 0);

			// allow comments
			if ($values['auth_comment'] == $role || $prev_allow_comment)
			{
				$auth -> setAllowed($album, $role, 'comment', true);
				$prev_allow_comment = true;
			}
			else
				$auth -> setAllowed($album, $role, 'comment', 0);
		}

		// Rebuild privacy
		$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
		foreach ($actionTable->getActionsByObject($album) as $action)
		{
			$actionTable -> resetActivityBindings($action);
		}

		if (!empty($_FILES['image']))
			$album -> setPhoto($_FILES['image']);
		return $album;
	}

	/**
	 *
	 * Create an album
	 * Request params:
	 * - sName			: 	string, optional. album title 
	 * - sDescription	: 	string, optional. album description
	 * - sPrivacyView	: 	string, optional. view privacy. values: 'everyone','registered','owner_network','owner_member_member','owner_member','owner'
	 * - sPrivacyComment: 	string, optional. comment privacy. values: 'everyone','registered','owner_network','owner_member_member','owner_member','owner'
	 * - iSearch		:	int, optional. can view by search or NOT. values: 0 OR 1
	 * - image			: 	binary, optional.
	 *
	 * Responsed params:
	 * - iAlbumId		: 	int. album id
	 * - iAlbumTitle	:	string. album title
	 * - message		: 	string. status message from server
	 * 
	 * @param array $aData
	 * @return array $aResult
	 * @access public
	 */
	public function create($aData)
	{
		extract($aData);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer || !Engine_Api::_() -> authorization() -> isAllowed('mp3music_album', null, 'create'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You are not allowed to create music album.")
			);
		}
		if (!isset($sName) || $sName == '')
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing album name"),
				'error_element' => 'sName',
				'error_code' => 1
			);
		}
		$values = array(
			'title' => $sName,
			'description' => isset($sDescription) ? $sDescription : '',
			'auth_view' => isset($sPrivacyView) ? $sPrivacyView : 'everyone',
			'auth_comment' => isset($sPrivacyComment) ? $sPrivacyComment : 'everyone',
			'search' => isset($iSearch) ? (int)$iSearch : 1,
		);
		$db = Engine_Api::_() -> getDbTable('albums', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$album = $this -> saveAlbumValues($values);
			// Send notifications for subscribers
			Engine_Api::_() -> getDbtable('subscriptions', 'mp3music') -> sendNotifications($album);
			$db -> commit();
			return array(
				'message' => Zend_Registry::get('Zend_Translate') -> _("Album successfully created."),
				'iAlbumId' => $album -> getIdentity(),
				'iAlbumTitle' => $album -> getTitle()
			);
		}
		catch (Exception $e)
		{
			$db -> rollback();
			return array(
				'error_code' => 1,
				'error_message' => $e -> getMessage()
			);
		}
	}

	/**
	 *
	 * Edit an album
	 * Request params:
	 * - iAlbumId		:	int, required. album id
	 * - sName			: 	string, optional. album title 
	 * - sDescription	: 	string, optional. album description
	 * - sPrivacyView	: 	string, optional. view privacy. values: 'everyone','registered','owner_network','owner_member_member','owner_member','owner'
	 * - sPrivacyComment: 	string, optional. comment privacy. values: 'everyone','registered','owner_network','owner_member_member','owner_member','owner'
	 * - iSearch		:	int, optional. can view by search or NOT. values: 0 OR 1
	 * - image			: 	binary, optional.
	 *
	 * Responsed params:
	 * - iAlbumId		: 	int. album id
	 * - iAlbumTitle	:	string. album title
	 * - message		: 	string. status message from server
	 * 
	 * @param array $aData
	 * @return array $aResult
	 * @access public
	 */
	public function edit($aData)
	{
		extract($aData);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!isset($iAlbumId))
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing album id"),
				'error_element' => 'iAlbumId',
				'error_code' => 1
			);
		}
		$album = Engine_Api::_() -> getItem('mp3music_album', $iAlbumId);
		if (!$album)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Invalid playlist")
			);
		}
		if (!$viewer -> getIdentity() || !Engine_Api::_() -> authorization() -> isAllowed($album, null, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You are not allowed to edit this playlist.")
			);
		}
		$values = array();
		if (isset($sName) && $sName != "")
		{
			$values['title'] = htmlspecialchars(trim($sName), ENT_QUOTES, 'UTF-8');
		}
		if (isset($sDescription) && $sDescription != "")
		{
			$values['description'] = trim($sDescription);
		}
		if (isset($iSearch))
		{
			$values['search'] = trim($iSearch);
		}
		try 
		{
			$album = $this -> saveAlbumValues($values, $iAlbumId);
			return array(
					'message' => Zend_Registry::get('Zend_Translate') -> _("Album successfully edited."),
					'iAlbumId' => $album -> getIdentity(),
					'iAlbumTitle' => $album -> getTitle()
			);
		} 
		catch (Exception $e)
		{
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage()
			);
		}
	}

	/**
	 * Delete an album
	 * Request params:
	 * - iAlbumId		:	int, required. album id
	 *
	 * Responsed params:
	 * - message		: 	string. status message from server
	 * 
	 * @param array $aData
	 * @return array $aResult
	 * @access public
	 */
	public function delete($aData)
	{
		extract($aData);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!isset($iAlbumId))
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing album id"),
				'error_element' => 'iAlbumId',
				'error_code' => 1
			);
		}
		$album = Engine_Api::_() -> getItem('mp3music_album', $iAlbumId);
		if (!$album)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Album doesn't exists or not authorized to delete")
			);
		}
		if (!$viewer -> getIdentity() || !Engine_Api::_() -> authorization() -> isAllowed($album, null, 'delete'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You are not allowed to delete this playlist.")
			);
		}
		$db = Engine_Api::_() -> getDbtable('albums', 'mp3music') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			foreach ($album->getSongs() as $song)
			{
				$song -> deleteUnused();
			}
			$album -> delete();
			$db -> commit();
			return array(
				'error_code' => 0,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Delete album successfully.")
			);
		}
		catch (Exception $e)
		{
			$db -> rollback();
			return array(
				'error_code' => 1,
				'error_message' => $e -> getMessage()
			);
		}
	}

	public function privacy($aData)
	{
		$roles = array(
			'everyone' => 'Everyone',
			'registered' => 'All Registered Members',
			'owner_network' => 'Friends and Networks',
			'owner_member_member' => 'Friends of Friends',
			'owner_member' => 'Friends Only',
			'owner' => 'Just Me'
		);
		$sType = isset($aData['sType']) ? $aData['sType'] : 'view';
		$viewer = Engine_Api::_() -> user() -> getViewer();
		switch ($sType)
		{
			case 'view' :
				$viewOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('music_playlist', $viewer, 'auth_view');
				return array_intersect_key($roles, array_flip($viewOptions));
				break;

			case 'comment' :
				$commentOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('music_playlist', $user, 'auth_comment');
				return array_intersect_key($roles, array_flip($commentOptions));
				break;
		}
	}

	/**
	 *
	 * Search albums
	 *
	 * Request params:
	 * - sSearchText	: 	string, optional.
	 * - sView			: 	string, optional. Ex: my, all
	 * - iLimit	: 	int, optional.
	 * - iPage			: 	int, optional.
	 * - sSearch		: 	string, optional.
	 *
	 * Responsed params:
	 * - bIsLiked		: 	boolean. viewer liked this album or NOT
	 * - iAlbumId		: 	int. album id
	 * - iUserId		: 	int. owner id
	 * - sName			: 	string. album name
	 * - sImagePath		: 	string. album cover path
	 * - iTotalTrack	: 	int. number of songs
	 * - iTotalPlay		: 	int. play count
	 * - iTotalComment	: 	int. total comment
	 * - iTotalLike		: 	int. total like
	 * - aUserLike 		:	array. users that liked this albums
	 * - iTimeStamp		: 	int. creation timestamp
	 * - sTimeStamp		: 	string. creation time string
	 * - sFullTimeStamp	: 	string. creation date
	 * - sFullname		: 	string. owner full name
	 * - sUserImage		: 	string. owner image url
	 * - bIsInvisible	: 	boolean. this album is invisible or not
	 * - bCanView		:	boolean. ability to view this album
	 * - bCanComment	:	boolean. ability to comment this album
	 * - bCanLike		: 	boolean. ability to like this album
	 * 
	 * @param array $aData
	 * @return array $aResult
	 * @access public
	 */
	public function search($aData)
	{
		return $this -> getAlbums($aData);
	}

	public function addsong($aData)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iAlbumId = isset($aData['iAlbumId']) ? $aData['iAlbumId'] : 0;
		$iSongId = isset($aData['iSongId']) ? $aData['iSongId'] : 0;
		
		$song = Engine_Api::_() -> getItem('music_playlist_song', $iSongId);
		$playlist = Engine_Api::_() -> getItem('music_playlist', $iAlbumId);
		if (!$playlist || !$song)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Playlist or song doesn't exists")
			);
		}
		if (!$viewer || !Engine_Api::_() -> authorization() -> isAllowed($playlist, null, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You are not allowed to delete this playlist.")
			);
		}

		// already exists in playlist
		$songTable = $song -> getTable();
		$alreadyExists = $songTable -> select() -> from($songTable, 'song_id') -> where('playlist_id = ?', $playlist -> getIdentity()) -> where('file_id = ?', $song -> file_id) -> limit(1) -> query() -> fetchColumn();
		if ($alreadyExists)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This playlist already has this song.")
			);
		}
		// Process
		$db = $song -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Add song
			$playlist -> addSong($song -> file_id);
			$db -> commit();
			return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Song successfully added.")
			);
		}
		catch( Music_Model_Exception $e )
		{
			$db -> rollback();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _($e -> getMessage())
			);

		}
		catch( Exception $e )
		{
			$db -> rollback();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Unknown database error')
			);
		}
	}

	public function deletesong($aData)
	{
		return Engine_Api::_() -> getApi('song', 'ynmobile') -> delete($aData);
	}

	
	/**
	 * List all songs in an album
	 *
	 * Request params:
	 * - iAlbumId		: 	int, required. album id
	 *
	 * Responsed params:
	 * - iSongId		:	int. song id
	 * - iAlbumId		: 	int. album id
	 * - iUserId		:	int. album owner id
	 * - sTitle			: 	string. song title
	 * - sSongPath		: 	string. song file path
	 * - iOrdering		: 	int. song order
	 * - iTotalPlay		: 	int. song play count
	 * - iTimeStamp		: 	int. timestamp
	 * - sTimeStamp		: 	string. timestamp as string
	 * - sFullTimeStamp	: 	string. full datetime
	 * - sFullname		: 	string. owner full name
	 * - sAlbumImage	: 	string. album cover image url
	 * - sUserImage		: 	string. owner photo url
	 * 
	 * @param array $aData
	 * @return array $aResult
	 * @access public
	 */
	public function list_songs($aData)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iAlbumId = isset($aData['iAlbumId']) ? (int)$aData['iAlbumId'] : 0;
		$album = Engine_Api::_() -> getItem('mp3music_album', $iAlbumId);
		if (!$album || !Engine_Api::_() -> authorization() -> isAllowed($album, null, 'view'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Playlist doesn't exists or not authorized to view")
			);
		}
		$songs = $album -> getSongs();
		$aResult = array();
		$owner = $album -> getOwner();
		$sProfileImage = $album -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
		if ($sProfileImage)
		{
			$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
		}
		else
		{
			$sProfileImage = NO_ALBUM_MAIN;
		}

		$sUserImageUrl = $owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		if ($sUserImageUrl != "")
		{
			$sUserImageUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImageUrl);
		}
		else
		{
			$sUserImageUrl = NO_USER_ICON;
		}
		foreach ($songs as $song)
		{
			$songPath = Engine_Api::_() -> ynmobile() -> finalizeUrl($song -> getFilePath());
			$create = strtotime($album -> creation_date);
			// Prepare data in locale timezone
			$timezone = null;
			if (Zend_Registry::isRegistered('timezone'))
			{
				$timezone = Zend_Registry::get('timezone');
			}
			if (null !== $timezone)
			{
				$prevTimezone = date_default_timezone_get();
				date_default_timezone_set($timezone);
			}

			$sTime = date("D, j M Y G:i:s O", $create);

			if (null !== $timezone)
			{
				date_default_timezone_set($prevTimezone);
			}

			$aResult[] = array(
				'iSongId' => $song -> getIdentity(),
				'iAlbumId' => $album -> getIdentity(),
				'iUserId' => $album -> user_id,
				'sTitle' => $song -> getTitle(),
				'sSongPath' => $songPath,
				'iOrdering' => $song -> order,
				'iTotalPlay' => $song -> play_count,
				'iTimeStamp' => $create,
				'sTimeStamp' => $sTime,
				'sFullTimeStamp' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($create),
				'sFullname' => $owner -> getTitle(),
				'sAlbumImage' => $sProfileImage,
				'sUserImage' => $sUserImageUrl,
				'bIsInvisible' => !(bool)$album -> search,
			);
		}
		return $aResult;
	}

	/**
	 * view an album
	 *
	 * Request params:
	 * - iAlbumId		: 	int, required. album id
	 *
	 * Responsed params:
	 *  - bIsLiked		:	int. this album is liked or NOT		
	 *  - iAlbumId		:	int. album id
	 *  - iUserId		: 	int. user id
	 *  - sAlbumName	:	string. album name
	 *  - sDescription	: 	string. album description
	 *  - sImagePath	: 	string. album image path
	 *  - iTotalTrack	:	int. total songs
	 *  - iTotalPlay	: 	int. total play
	 *  - iTotalComment	: 	int. total comment
	 *  - iTotalLike	: 	int. total like
	 *  - aUserLike		: 	array. list of users that liked this album
	 *  - iTimeStamp	: 	int. timestamp of creation date
	 *  - sTimeStamp	: 	string. timestamp of creation date
	 *  - sFullTimeStamp:	string. full date time of creation date
	 *  - sFullname		: 	string. album owner name
	 *  - sUserImage	: 	string. album owner image url
	 *  - bIsInvisible	:	boolean. this album can search or NOT
	 *  - bCanComment	: 	boolean. viewer has ability to comment on this album or NOT
	 *  - bCanLike		: 	boolean. viewer has ability to like this album or NOT
	 *  - bCanView		: 	boolean. viewer has ability to view this album or NOT
	 * 
	 * @param array $aData
	 * @return array $aResult
	 * @access public
	 */
	public function detail($aData)
	{
		extract($aData);
		if (!isset($iAlbumId))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iAlbumId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$album = Engine_Api::_() -> getItem('mp3music_album', (int)($iAlbumId));
		if (!$album)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Album is not valid!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$bCanView = Engine_Api::_() -> authorization() -> isAllowed($album, null, 'view');
		$bCanComment = Engine_Api::_() -> authorization() -> isAllowed($album, null, 'comment');
		$album -> play_count ++;
		//$album -> view_count++;
		$album -> save();
		
		//photoURL
		$sProfileImage = $album -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
		if ($sProfileImage)
		{
			$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
		}
		else
		{
			$sProfileImage = NO_ALBUM_MAIN;
		}
		$total_track = count($album -> getSongs());

		$create = strtotime($album -> creation_date);
		// Prepare data in locale timezone
		$timezone = null;
		if (Zend_Registry::isRegistered('timezone'))
		{
			$timezone = Zend_Registry::get('timezone');
		}
		if (null !== $timezone)
		{
			$prevTimezone = date_default_timezone_get();
			date_default_timezone_set($timezone);
		}

		$sTime = date("D, j M Y G:i:s O", $create);

		if (null !== $timezone)
		{
			date_default_timezone_set($prevTimezone);
		}
		$owner = $album -> getOwner();
		$sUserImageUrl = $owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		if ($sUserImageUrl != "")
		{
			$sUserImageUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImageUrl);
		}
		else
		{
			$sUserImageUrl = NO_USER_ICON;
		}

		return array(
			'bIsLiked' => ($album -> likes() -> isLike($viewer)) ? 1 : 0,
			'iAlbumId' => $album -> getIdentity(),
			'iUserId' => $album -> user_id,
			'sAlbumName' => $album -> getTitle(),
			'sDescription' => $album -> getDescription(),
			'sImagePath' => $sProfileImage,
			'iTotalTrack' => $total_track,
			'iTotalPlay' => $album -> play_count,
			'iTotalComment' => $album -> comments() -> getCommentCount(),
			'iTotalLike' => $album -> likes() -> getLikeCount(),
			'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($album),
			'iTimeStamp' => $create,
			'sTimeStamp' => $sTime,
			'sFullTimeStamp' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($create),
			'sFullname' => $owner -> getTitle(),
			'sUserImage' => $sUserImageUrl,
			'bIsInvisible' => !$album -> search,
			'bIsFriend' => $owner -> membership() -> isMember($viewer),
			'bCanComment' => $bCanComment,
			'bCanLike' => $bCanComment,
			'bCanView' => $bCanView,
		);
	}
	
	public function upload($aData)
	{
		extract($aData);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		if (!isset($iPlaylistId))
		{
			$playlist = $playlistTable->getSpecialPlaylist($viewer, 'profile');
		}
		else
		{
			$playlist = Engine_Api::_()->getItem('music_playlist', $iPlaylistId);
		}
		
		
		if (!($playlist->getIdentity()))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_("This playlist is not existed!"),
			);
		}
		
		if (!isset($_FILES['song']))
		{
			return array(
				'error_code' => 2,
				'error_message' => Zend_Registry::get('Zend_Translate')->_("No file!"),
			);
		}
		
		try 
		{
			// Create song
			$file = Engine_Api::_()->getApi('core', 'music')->createSong($_FILES['song']);
			if( !$file ) 
			{
				return array(
						'error_code' => 3,
						'error_message' => Zend_Registry::get('Zend_Translate')->_("Song was not successfully attached"),
				);
		    }
		
			// Add song
			$song = $playlist->addSong($file);
			if( !$song ) 
			{
				return array(
						'error_code' => 3,
						'error_message' => Zend_Registry::get('Zend_Translate')->_("Song was not successfully attached"),
				);
			}
			
			if (isset($aData['sStatus']))
			{
				$object = $viewer;
				$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
					
				$body = $aData['sStatus'];
				$type = 'post';
				$action = $activityApi -> addActivity($viewer, $object, $type, $body, array(
						'status' => ($aData['sStatus']) ? $aData['sStatus'] : "",
				));
					
				if ($action)
				{
					$activityApi -> attachActivity($action, $song);
				}
			}
			
			
			return array(
					'iSongId' => $song->getIdentity(),
					'iPlaylistId' => $iPlaylistId,
					'sSongTitle' => $song->title,
			);
		} 
		catch (Exception $e)
		{
			return array(
					'error_code' => 3,
					'error_message' => $e->getMessage()
			);
		}
		
	}

}
