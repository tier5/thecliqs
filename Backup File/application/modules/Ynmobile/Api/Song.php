<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Song.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Api_Song extends Core_Api_Abstract
{
	/**
	 * Input data:
	 * + sAction: string, optional. Ex: "new", "more"
	 * + iLastSongId: int, optional.
	 * + iLimit: int, optional.
	 * + sSearch: string, optional.
	 * + sView: string, optional. Ex: "my", "friend", "all"
	 * + bIsProfile: bool, optional.
	 * + iProfileId: int, optional.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + message: string.
	 * + sAlbumName: string.
	 * + bIsOnProfile: bool.
	 * + iSongId: int.
	 * + iAlbumId: int.
	 * + iUserId: int.
	 * + sTitle: string.
	 * + sSongPath: string.
	 * + iOrdering: int.
	 * + iTotalPlay: int.
	 * + iTimeStamp: int.
	 * + sTimeStamp: string.
	 * + sFullTimeStamp: string.
	 * + sFullname: string.
	 * + sUserImage: string.
	 * + sAlbumImage: string
	 * + bIsInvisible: bool.
	 * + iUserLevelId: int.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see song/filter
	 *
	 * @param array $aData
	 * @return array
	 */
	private function getSongs($aData)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$sAction = (isset($aData['sAction']) && $aData['sAction'] == 'new') ? 'new' : 'more';
		$iLastSongId = isset($aData['iLastSongId']) ? (int)$aData['iLastSongId'] : 0;
		$iLimit = isset($aData['iLimit']) ? (int)$aData['iLimit'] : 10;
		$sSearch = isset($aData['sSearch']) ? $aData['sSearch'] : '';
		$sView = isset($aData['sView']) ? $aData['sView'] : '';
		$bIsProfile = (isset($aData['bIsProfile']) && $aData['bIsProfile'] == 'true') ? true : false;
		$oUser = NULL;
		if ($bIsProfile)
		{
			$iProfileId = isset($aData['iProfileId']) ? (int)$aData['iProfileId'] : 0;
			$oUser = Engine_Api::_() -> user() -> getUser($iProfileId);
			if (!$oUser -> getIdentity())
			{
				return array(
					'result' => 0,
					'error_code' => 1,
					'message' => 'Profile is not valid!'
				);
			}
		}
		$table = Engine_Api::_() -> getDbtable('playlistSongs', 'music');
		$song_name = $table -> info('name');
		$playlistTable = Engine_Api::_() -> getDbtable('playlists', 'music');
		$playlist_name = $playlistTable -> info('name');
		$select = $table -> select() -> from($song_name);
		$select -> join($playlist_name, "$playlist_name.playlist_id = $song_name.playlist_id", "");
		// Check the action.
		if ($iLastSongId > 0)
		{
			if ($sAction == 'more')
			{
				$select -> where('song_id < ?', $iLastSongId);
			}
			else
			{
				$select -> where('song_id > ?', $iLastSongId);
			}
		}
		// Search case.
		if (!empty($sSearch))
		{
			$select -> where("$song_name.title LIKE ?", "%{$sSearch}%");
		}
		// Profile case.
		if ($bIsProfile && $oUser -> getIdentity())
		{
			$select -> where("$playlist_name.owner_id = ?", $oUser -> getIdentity());
		}
		if (!$bIsProfile && $sView == 'my' && $viewer -> getIdentity())
		{
			$select -> where("$playlist_name.owner_id = ?", $viewer -> getIdentity());
		}
		elseif (!$bIsProfile && $sView == 'friend' && $viewer -> getIdentity())
		{
			// Get an array of friend ids
			$afriendIds = $viewer -> membership() -> getMembershipsOfIds();
			$select -> where("$playlist_name.owner_id IN(?)", $afriendIds);
			$select -> where("$playlist_name.search = 1");
		}
		else
		{
			$select -> where("$playlist_name.search = 1");
		}
		$select -> order("$song_name.song_id DESC");
		$select -> limit($iLimit);
		$rows = $table -> fetchAll($select);
		$aResult = array();
		foreach ($rows as $song)
		{
			$playlist = $song -> getParent();
			$owner = $playlist -> getOwner();
			if (!Engine_Api::_() -> authorization() -> isAllowed($playlist, null, 'view'))
			{
				continue;
			}
			$songPath = $song -> getFilePath();
			//songURL
			if ($songPath)
			{
				$songPath = Engine_Api::_() -> ynmobile() -> finalizeUrl($songPath);
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
			$sProfileImage = $playlist -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
			if ($sProfileImage)
			{
				$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
			}
			else
			{
				$sProfileImage = NO_ALBUM_MAIN;
			}

			$create = strtotime($playlist -> creation_date);
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
				'sAlbumName' => $playlist -> getTitle(),
				'bIsOnProfile' => ($playlist -> profile) ? true : false,
				'iSongId' => $song -> getIdentity(),
				'iAlbumId' => $playlist -> getIdentity(),
				'iUserId' => $playlist -> owner_id,
				'sTitle' => $song -> getTitle(),
				'sSongPath' => $songPath,
				'iOrdering' => $song -> order,
				'iTotalPlay' => $song -> play_count,
				'iTimeStamp' => $create,
				'sTimeStamp' => $sTime,
				'sFullTimeStamp' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($create),
				'sFullname' => $owner -> getTitle(),
				'sUserImage' => $sUserImageUrl,
				'sAlbumImage' => $sProfileImage,
				'bIsInvisible' => !(bool)$playlist -> search,
				'iUserLevelId' => $owner -> level_id
			);
		}
		return $aResult;
	}

	/**
	 * Input data:
	 * + sAction: string, optional.
	 * + iLastSongId: int, optional.
	 * + iLimit: int, optional.
	 * + sSearch: string, optional.
	 * + sView: string, optional.
	 * + bIsProfile: bool, optional.
	 * + iProfileId: int, optional.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + message: string.
	 * + sAlbumName: string.
	 * + bIsOnProfile: bool.
	 * + iSongId: int.
	 * + iAlbumId: int.
	 * + iUserId: int.
	 * + sTitle: string.
	 * + sSongPath: string.
	 * + iOrdering: int.
	 * + iTotalPlay: int.
	 * + iTimeStamp: int.
	 * + sTimeStamp: string.
	 * + sFullTimeStamp: string.
	 * + sFullname: string.
	 * + sUserImage: string.
	 * + bIsInvisible: bool.
	 * + iUserLevelId: int.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see song/filter
	 *
	 * @param array $aData
	 * @return array
	 */
	public function filter($aData)
	{
		return $this -> getSongs($aData);
	}

	/**
	 * Input data:
	 * + iAlbumId: int, optional.
	 * + sNewAlbumTitle: string, optional.
	 * + sDescription: string, optional.

	 * + sTitle: string, optional.
	 * + sPrivacyView: string, optional.
	 * + sPrivacyComment: string, optional.
	 * + iSearch: int, optional

	 * + sType: string, optional. ex: 'profile', 'wall', 'message'
	 * + mp3: mp3 file, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + message: string.
	 * + iSongId: int.
	 * + sSongTitle: string.
	 * + iAlbumId: int.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see song/create
	 *
	 * @param array $aData
	 * @return array
	 */
	public function create($aData)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer || !Engine_Api::_() -> authorization() -> isAllowed('music_playlist', null, 'create'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You are not allowed to upload songs.")
			);
		}
		$playlistTable = Engine_Api::_() -> getDbTable('playlists', 'music');
		//get data
		$playlist_id = isset($aData['iAlbumId']) ? (int)$aData['iAlbumId'] : 0;
		$type = isset($aData['sType']) ? $aData['sType'] : false;
		$playlist_title = isset($aData['sNewAlbumTitle']) ? $aData['sNewAlbumTitle'] : '';
		$song_title = isset($aData['sTitle']) ? $aData['sTitle'] : '';
		$privacy_view = isset($aData['sPrivacyView']) ? $aData['sPrivacyView'] : 'everyone';
		$privacy_comment = isset($aData['sPrivacyComment']) ? $aData['sPrivacyComment'] : 'everyone';
		$playlist = null;
		// Get special playlist
		if (!$playlist_id && false != $type)
		{
			$playlist = $playlistTable -> getSpecialPlaylist($viewer, $type);
		}
		elseif ($playlist_id)
		{
			$playlist = Engine_Api::_() -> getItem("music_playlist", $playlist_id);
		}
		elseif (!$playlist_id && !$type)
		{
			$playlist = $playlistTable -> createRow();
		}

		// Check subject
		if (!$playlist)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Invalid playlist")
			);
		}

		// check auth
		if ($playlist_id && !Engine_Api::_() -> authorization() -> isAllowed($playlist, null, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You are not allowed to edit this playlist")
			);
		}

		// Check file
		if (empty($_FILES['mp3']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("No file")
			);
		}

		// Process
		$db = $playlistTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Create song
			$file = Engine_Api::_() -> getWorkingApi('core', 'music') -> createSong($_FILES['mp3']);
			if (!$file)
			{
				return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Song was not successfully attached")
				);
			}

			// Add song
			$song = $playlist -> addSong($file);
			if (!$song)
			{
				return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Song was not successfully attached")
				);
			}
			//update playlist info
			if (isset($aData['iSearch']))
			{
				$playlist -> search = (int)$aData['iSearch'];
			}
			if (isset($aData['sNewAlbumTitle']))
			{
				$playlist -> title = htmlspecialchars(trim($playlist_title), ENT_QUOTES, 'UTF-8');
			}
			if (isset($aData['sDescription']))
			{
				$playlist -> description = trim($values['sDescription']);
			}
			$playlist -> owner_type = 'user';
			$playlist -> owner_id = $viewer -> getIdentity();
			$playlist -> save();
			// Only create activity feed item if "search" is checked and add new
			if (!$playlist_id && !$type && $playlist -> search)
			{
				// Only create activity feed item if "search" is checked
				$activity = Engine_Api::_() -> getDbtable('actions', 'activity');
				$action = $activity -> addActivity($viewer, $playlist, 'music_playlist_new', null, array('count' => 1));
				if (null !== $action)
					$activity -> attachActivity($action, $playlist);
			}
			if (isset($aData['sPrivacyView']) || isset($aData['sPrivacyComment']))
			{
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
					if (isset($aData['sPrivacyView']))
					{
						// allow viewers
						if ($privacy_view == $role || $prev_allow_view)
						{
							$auth -> setAllowed($playlist, $role, 'view', true);
							$prev_allow_view = true;
						}
						else
							$auth -> setAllowed($playlist, $role, 'view', 0);
					}
					if (isset($aData['sPrivacyComment']))
					{
						// allow comments
						if ($privacy_comment == $role || $prev_allow_comment)
						{
							$auth -> setAllowed($playlist, $role, 'comment', true);
							$prev_allow_comment = true;
						}
						else
							$auth -> setAllowed($playlist, $role, 'comment', 0);
					}
				}
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($playlist) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}
			if ($song_title)
			{
				$song -> title = $song_title;
			}
			$song -> save();

			$db -> commit();
			return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Song successfully uploaded."),
				'iSongId' => $song -> getIdentity(),
				'sSongTitle' => $song -> getTitle(),
				'iAlbumId' => $playlist -> getIdentity()
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
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Upload failed by database query')
			);
		}
	}

	/**
	 * Input data:
	 * + iSongId: int, required.
	 * + sTitle: string, required.

	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + message: string.
	 * + iSongId: int.
	 * + sSongTitle: string.
	 * + iAlbumId: int.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see song/edit
	 *
	 * @param array $aData
	 * @return array
	 */
	public function edit($aData)
	{
		// Check subject
		if (!isset($aData['iSongId']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Not a valid song')
			);
		}
		if (!isset($aData['sTitle']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("The title  is required and can't be empty")
			);
		}

		// Get song/playlist
		$song = Engine_Api::_() -> getItem('music_playlist_song', $aData['iSongId']);
		// Check song/playlist
		if (!$song)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid song')
			);
		}
		$playlist = $song -> getParent();

		// Check song/playlist
		if (!$playlist)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid playlist')
			);
		}

		// Check auth
		if (!Engine_Api::_() -> authorization() -> isAllowed($playlist, null, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Not allowed to edit this playlist')
			);
		}

		// Process
		$db = $song -> getTable() -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$song -> setTitle($aData['sTitle']);
			$db -> commit();
			return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Song successfully edited."),
				'iSongId' => $song -> getIdentity(),
				'sSongTitle' => $song -> getTitle(),
				'iAlbumId' => $playlist -> getIdentity()
			);
		}
		catch (Exception $e)
		{
			$db -> rollback();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Unknown database error')
			);
		}
	}

	/**
	 * Input data:
	 * + iSongId: int, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + message: string.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see song/delete
	 *
	 * @param array $aData
	 * @return array
	 */
	public function delete($aData)
	{
		$iSongId = isset($aData['iSongId']) ? (int)$aData['iSongId'] : 0;
		if ($iSongId < 1)
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _('Song id is not valid!')
			);
		}
		// Get song
		$song = Engine_Api::_() -> getItem('music_playlist_song', $iSongId);

		// Check song
		if (!$song)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid song')
			);
		}
		$playlist = $song -> getParent();
		if (!$playlist)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid playlist')
			);
		}

		// Check auth
		if (!Engine_Api::_() -> authorization() -> isAllowed($playlist, null, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Not allowed to edit this playlist')
			);
		}

		// Get file
		$file = Engine_Api::_() -> getItem('storage_file', $song -> file_id);
		if (!$file)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid file')
			);
		}

		$db = $song -> getTable() -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$song -> deleteUnused();
			$db -> commit();
			return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Song successfully deleted.")
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

	/**
	 * Input data:
	 * + sAction: string, optional.
	 * + iLastSongId: int, optional.
	 * + iLimit: int, optional.
	 * + sSearch: string, optional.
	 * + sView: string, optional.
	 * + bIsProfile: bool, optional.
	 * + iProfileId: int, optional.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + message: string.
	 * + sAlbumName: string.
	 * + bIsOnProfile: bool.
	 * + iSongId: int.
	 * + iAlbumId: int.
	 * + iUserId: int.
	 * + sTitle: string.
	 * + sSongPath: string.
	 * + iOrdering: int.
	 * + iTotalPlay: int.
	 * + iTimeStamp: int.
	 * + sTimeStamp: string.
	 * + sFullTimeStamp: string.
	 * + sFullname: string.
	 * + sUserImage: string.
	 * + bIsInvisible: bool.
	 * + iUserLevelId: int.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see song/searchSong
	 *
	 * @param array $aData
	 * @return array
	 */
	public function searchSong($aData)
	{
		return $this -> getSongs($aData);
	}

	/**
	 * Input data:
	 * + iSongId: int, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + message: string.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see song/updateCounterMusic
	 *
	 * @param array $aData
	 * @return array
	 */
	public function updateCounterMusic($aData)
	{
		$iSongId = isset($aData['iSongId']) ? (int)$aData['iSongId'] : 0;
		// Check subject
		if (!$song = Engine_Api::_() -> getItem('music_playlist_song', $iSongId))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Not a valid song')
			);
		}

		// Get playlist
		$playlist = $song -> getParent();
		// Check playlist
		if (!$playlist)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Not a valid playlist')
			);
		}
		// Process
		$db = $song -> getTable() -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$song -> play_count++;
			$song -> save();

			$playlist -> play_count++;
			$playlist -> save();

			$db -> commit();
			return array('result' => 1);

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

	/**
	 * Input data:
	 * + iSongId: int, required.
	 *
	 * Output data:
	 * + iSongId: int.
	 * + sSongTitle: string.
	 * + iAlbumId: int.

	 *
	 * @see Mobile - API phpFox/Api V3.0
	 * @see song/get_song_for_edit
	 *
	 * @param array $aData
	 * @return array
	 */
	public function getSongForEdit($aData)
	{
		if (!isset($aData['iSongId']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Not a valid song')
			);
		}
		// Get song/playlist
		$song = Engine_Api::_() -> getItem('music_playlist_song', $aData['iSongId']);
		// Check song/playlist
		if (!$song)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid song')
			);
		}
		$playlist = $song -> getParent();

		// Check song/playlist
		if (!$playlist)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid playlist')
			);
		}
		return array(
			'result' => 1,
			'iSongId' => $song -> getIdentity(),
			'sSongTitle' => $song -> getTitle(),
			'iAlbumId' => $playlist -> getIdentity()
		);
	}

	/**
	 * Input data:
	 * + iSongId: int, required
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + message: string.
	 * + sAlbumName: string.
	 * + bIsOnProfile: bool.
	 * + iSongId: int.
	 * + iAlbumId: int.
	 * + iUserId: int.
	 * + sTitle: string.
	 * + sSongPath: string.
	 * + iOrdering: int.
	 * + iTotalPlay: int.
	 * + iTimeStamp: int.
	 * + sTimeStamp: string.
	 * + sFullTimeStamp: string.
	 * + sFullname: string.
	 * + sUserImage: string.
	 * + sAlbumImage: string
	 * + bIsInvisible: bool.
	 * + iUserLevelId: int.
	 * + bIsFriend: bool.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see song/detail
	 *
	 * @param array $aData
	 * @return array
	 */

	public function detail($aData)
	{
		if (!isset($aData['iSongId']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		// Get song/playlist
		$song = Engine_Api::_() -> getItem('music_playlist_song', $aData['iSongId']);
		// Check song/playlist
		if (!$song)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid song')
			);
		}
		$playlist = $song -> getParent();

		// Check song/playlist
		if (!$playlist)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid playlist')
			);
		}

		// Check auth
		if (!Engine_Api::_() -> authorization() -> isAllowed($playlist, null, 'view'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Not allowed to view this song')
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> updateCounterMusic($aData);
		$owner = $playlist -> getOwner();
		$songPath = $song -> getFilePath();
		//songURL
		if ($songPath)
		{
			$songPath = Engine_Api::_() -> ynmobile() -> finalizeUrl($songPath);
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
		$sProfileImage = $playlist -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
		if ($sProfileImage)
		{
			$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
		}
		else
		{
			$sProfileImage = NO_ALBUM_MAIN;
		}

		$create = strtotime($playlist -> creation_date);
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
		return array(
			'sAlbumName' => $playlist -> getTitle(),
			'bIsOnProfile' => ($playlist -> profile) ? true : false,
			'iSongId' => $song -> getIdentity(),
			'iAlbumId' => $playlist -> getIdentity(),
			'iUserId' => $playlist -> owner_id,
			'sTitle' => $song -> getTitle(),
			'sSongPath' => $songPath,
			'iOrdering' => $song -> order,
			'iTotalPlay' => $song -> play_count,
			'iTimeStamp' => $create,
			'sTimeStamp' => $sTime,
			'sFullTimeStamp' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($create),
			'sFullname' => $owner -> getTitle(),
			'sUserImage' => $sUserImageUrl,
			'sAlbumImage' => $sProfileImage,
			'bIsInvisible' => !(bool)$playlist -> search,
			'iUserLevelId' => $owner -> level_id,
			'bIsFriend' => $owner -> membership() -> isMember($viewer),
		);
	}

	/**
	 * Input data:
	 * + mp3: mp3 file or sFilePath string, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + iSongId: int.
	 * + sSongTitle: string.
	 * + sSongUrl: int.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see song/parser
	 *
	 * @param array $aData
	 * @return array
	 */
	public function parser($aData)
	{
		extract($aData);
		// Check auth
		if (!Engine_Api::_() -> authorization() -> isAllowed('music_playlist', null, 'create'))
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You are not allowed to upload songs"),
				'error_code' => 1
			);
		}

		// Prepare
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$playlistTable = Engine_Api::_() -> getDbTable('playlists', 'music');

		$playlist = $playlistTable -> getSpecialPlaylist($viewer, 'wall');
		
		// Check subject
		if (!$playlist)
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Invalid playlist"),
				'error_code' => 1
			);
		}

		// Get playlist identity
		$playlist_id = $playlist -> getIdentity();

		// check auth
		if (!Engine_Api::_() -> authorization() -> isAllowed($playlist, null, 'edit'))
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You are not allowed to edit this playlist"),
				'error_code' => 1
			);
		}
		// Check file
		if (empty($_FILES['mp3']) && $sFilePath == "")
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("No file")
			);
		}
		if (isset($_FILES['mp3']))
		{
			$file_mp3 = $_FILES['mp3'];
		}
		else
		{
			$file_mp3 = trim(strip_tags($sFilePath));
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('playlists', 'music') -> getAdapter();
		$db -> beginTransaction();

		try
		{

			// Create song
			$file = Engine_Api::_() -> getWorkingApi('core', 'music') -> createSong($file_mp3);
			if (!$file)
			{
				return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Song was not successfully attached")
				);
			}
			// Add song
			$song = $playlist -> addSong($file);
			if (!$song)
			{
				return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Song was not successfully attached")
				);
			}

			// Response
			return array(
				'result' => 1,
				'iSongId' => $song -> getIdentity(),
				'sSongTitle' => $song -> getTitle(),
				'sSongUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($song -> getFilePath())
			);
			$db -> commit();

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
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Upload failed by database query")
			);

		}
	}

}
