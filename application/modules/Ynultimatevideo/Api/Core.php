<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
require_once GOOGLE_LIBS_PATH. '/autoload.php';
require_once GOOGLE_LIBS_PATH. '/Client.php';
require_once GOOGLE_LIBS_PATH. '/Service/YouTube.php';

class Ynultimatevideo_Api_Core extends Core_Api_Abstract
{
	public function getItemTable($type)
	{
		if ($type == 'ynultimatevideo_video')
		{
			return Engine_Loader::getInstance() -> load('Ynultimatevideo_Model_DbTable_Videos');
		}
		else
			if ($type == 'ynultimatevideo_category')
			{
				return Engine_Loader::getInstance() -> load('Ynultimatevideo_Model_DbTable_Categories');
			}
			else
			{
				$class = Engine_Api::_() -> getItemTableClass($type);
				return Engine_Api::_() -> loadClass($class);
			}
	}

	public function getCategories($catIds = null)
	{
		$table = Engine_Api::_() -> getDbTable('categories', 'ynultimatevideo');
		$select = $table -> select();
		if ($catIds)
		{
			$select -> where('category_id in (?)', $catIds);
		}
		$categories = $table -> fetchAll($select -> order('category_name ASC'));
		$cats = array();
		foreach ($categories as $category)
		{
			$cats[$category -> getIdentity()] = $category;
		}

		return $cats;
	}

	public function getCategory($category_id)
	{
		return Engine_Api::_() -> getDbtable('categories', 'ynultimatevideo') -> find($category_id) -> current();
	}

	public function getRating($video_id)
	{
		$table = Engine_Api::_() -> getDbTable('ratings', 'ynultimatevideo');
		$rating_sum = $table -> select() -> from($table -> info('name'), new Zend_Db_Expr('SUM(rating)')) -> group('video_id') -> where('video_id = ?', $video_id) -> query() -> fetchColumn(0);

		$total = $this -> ratingCount($video_id);
		if ($total)
			$rating = $rating_sum / $this -> ratingCount($video_id);
		else
			$rating = 0;

		return $rating;
	}

	public function getRatings($video_id)
	{
		$table = Engine_Api::_() -> getDbTable('ratings', 'ynultimatevideo');
		$rName = $table -> info('name');
		$select = $table -> select() -> from($rName) -> where($rName . '.video_id = ?', $video_id);
		$row = $table -> fetchAll($select);
		return $row;
	}

	public function checkRated($video_id, $user_id)
	{
		$table = Engine_Api::_() -> getDbTable('ratings', 'ynultimatevideo');

		$rName = $table -> info('name');
		$select = $table -> select() -> setIntegrityCheck(false) -> where('video_id = ?', $video_id) -> where('user_id = ?', $user_id) -> limit(1);
		$row = $table -> fetchAll($select);

		if (count($row) > 0)
			return 1;
		return 0;
	}

	public function setRating($video_id, $user_id, $rating)
	{
		$table = Engine_Api::_() -> getDbTable('ratings', 'ynultimatevideo');
		$rName = $table -> info('name');
		$select = $table -> select() -> from($rName) -> where($rName . '.video_id = ?', $video_id) -> where($rName . '.user_id = ?', $user_id);
		$row = $table -> fetchRow($select);
		if (empty($row))
		{
			// create rating
			Engine_Api::_() -> getDbTable('ratings', 'ynultimatevideo') -> insert(array(
					'video_id' => $video_id,
					'user_id' => $user_id,
					'rating' => $rating
			));
		}
	}

	public function ratingCount($video_id)
	{
		$table = Engine_Api::_() -> getDbTable('ratings', 'ynultimatevideo');
		$rName = $table -> info('name');
		$select = $table -> select() -> from($rName) -> where($rName . '.video_id = ?', $video_id);
		$row = $table -> fetchAll($select);
		$total = count($row);
		return $total;
	}

	// handle video upload
	public function createVideo($params, $file, $values)
	{
		if ($file instanceof Storage_Model_File)
		{
			$params['file_id'] = $file -> getIdentity();
			$params['code'] = $file -> extension;
			// create video item
			$video = Engine_Api::_() -> getDbtable('videos', 'ynultimatevideo') -> createRow($params);
			$video -> save();
		}
		else
		{
			// create video item
			$video = Engine_Api::_() -> getDbtable('videos', 'ynultimatevideo') -> createRow($params);
			$file_ext = pathinfo($file['name']);
			$file_ext = $file_ext['extension'];
			$video -> code = $file_ext;
			$video -> save();

			// Store video in temporary storage object for ffmpeg to handle
			$storage = Engine_Api::_() -> getItemTable('storage_file');
			$storageObject = $storage -> createFile($file, array(
					'parent_id' => $video -> getIdentity(),
					'parent_type' => $video -> getType(),
					'user_id' => $video -> owner_id,
			));
			$video -> file_id = $storageObject -> file_id;
			$video -> save();

			if(!$video -> allow_upload_channel)
			{
				// Add to jobs
				Engine_Api::_() -> getDbtable('jobs', 'core') -> addJob('ynultimatevideo_encode', array('video_id' => $video -> getIdentity()));
			}
			@unlink($file['tmp_name']);
		}
		return $video;
	}

	public function deleteVideo($video)
	{

		// delete video ratings
		Engine_Api::_() -> getDbtable('ratings', 'ynultimatevideo') -> delete(array('video_id = ?' => $video -> video_id, ));

		// check to make sure the video did not fail, if it did we wont have files to
		// remove
		if ($video -> status == 1)
		{
			// delete storage files (video file and thumb)
			if ($video -> type == Ynultimatevideo_Plugin_Factory::getUploadedType())
			{
				try
				{
					Engine_Api::_() -> getItem('storage_file', $video -> file_id) -> remove();
				}
				catch (Exception $e)
				{
				}
			}
			if ($video -> photo_id)
			{
				try
				{
					Engine_Api::_() -> getItem('storage_file', $video -> photo_id) -> remove();
				}
				catch (Exception $e)
				{
				}
			}
		}

		// delete activity feed and its comments/likes
		$item = Engine_Api::_() -> getItem('ynultimatevideo_video', $video -> video_id);
		if ($item)
		{
			$item -> delete();
		}
	}

	public function getPlaylists($userId)
	{
		$table = Engine_Api::_() -> getDbTable('playlists', 'ynultimatevideo');
		$select = $table -> select();
		$select -> where('user_id = ?', $userId);
		$select -> order('creation_date DESC');
		return $table -> fetchAll($select);
	}

	public function checkVideoBelongsToAPlayList($videoId, $userId)
	{
		$playlistTbl = Engine_Api::_() -> getDbTable('playlists', 'ynultimatevideo');
		$playlistName = $playlistTbl -> info('name');

		$playlistAssoTbl = Engine_Api::_() -> getDbTable('playlistassoc', 'ynultimatevideo');
		$playlistAssoName = $playlistAssoTbl -> info('name');

		$select = $playlistTbl -> select() -> from($playlistName);
		$select -> join($playlistAssoName, "$playlistAssoName.playlist_id = $playlistName.playlist_id", null);
		$select -> where("$playlistName.user_id = $userId");
		$select -> where("$playlistAssoName.video_id = $videoId");
		$select -> limit(1);

		$row = $playlistTbl -> fetchAll($select);

		if (count($row) > 0)
			return true;
		return false;
	}

	public function addVideoToWatchLater($videoId, $userId) {
		$watchLaterTbl = Engine_Api::_()->getDbTable('watchlaters', 'ynultimatevideo');

		$row = $watchLaterTbl->fetchRow(array("video_id = $videoId", "user_id = $userId"));
		if (!$row) {
			$watchLater = $watchLaterTbl->createRow();
			$watchLater->video_id = $videoId;
			$watchLater->user_id = $userId;
			$watchLater->watched = 0;
			$watchLater->creation_date = date('Y-m-d H:i:s');
			$watchLater->save();

			return $watchLater;
		} else {
			return false;
		}
	}

	public function addVideoToFavorite($videoId, $userId)
	{
		$favoriteTbl = Engine_Api::_() -> getDbTable('favorites', 'ynultimatevideo');
		$row = $favoriteTbl -> fetchRow(array(
				"video_id = $videoId",
				"user_id = $userId"
		));
		if ($row == null)
		{
			$favorite = $favoriteTbl -> createRow();
			$favorite -> video_id = $videoId;
			$favorite -> user_id = $userId;
			$favorite -> save();

			$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $videoId);
			$video -> favorite_count = new Zend_Db_Expr('favorite_count + 1');
			$video -> save();

			return $favorite;
		}
		else
		{
			return false;
		}
	}

	public function removeVideoFromPlaylist($videoId, $playlistId)
	{
		$playlistAssoc = Engine_Api::_() -> getDbTable('playlistassoc', 'ynultimatevideo') -> fetchRow(array(
				"video_id = $videoId",
				"playlist_id = $playlistId"
		));
		if ($playlistAssoc)
		{
			if ($playlistAssoc -> delete())
			{
				$playlist = Engine_Api::_() -> getItem('ynultimatevideo_playlist', $playlistId);
				if ($playlist -> video_count > 0)
				{
					$playlist -> video_count = new Zend_Db_Expr('video_count - 1');
					$playlist -> save();
				}

				return true;
			}
		}
		return false;
	}

	public function removeVideoFromWatchLater($videoId, $userId)
	{
		$watchLater = Engine_Api::_() -> getDbTable('watchlaters', 'ynultimatevideo') -> fetchRow(array(
				"video_id = $videoId",
				"user_id = $userId"
		));
		if ($watchLater)
		{
			return $watchLater -> delete();
		}
		return false;
	}

	public function removeItemFromHistory($videoId, $userId, $itemType)
	{

		$history = Engine_Api::_() -> getDbTable('history', 'ynultimatevideo') -> fetchRow(array(
				"item_id = $videoId",
				"user_id = $userId",
				"item_type = '$itemType'",
		));
		if ($history)
		{
			return $history -> delete();
		}
		return false;
	}

	public function removeAllItemsFromHistory($type)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		Engine_Api::_()->getDbtable('history', 'ynultimatevideo')->delete(array(
				'user_id = ?' => $viewer->getIdentity(),
				'item_type = ?' => $type
		));
		return true;
	}

	public function removeVideoFromFavorite($videoId, $userId)
	{
		$favorite = Engine_Api::_() -> getDbTable('favorites', 'ynultimatevideo') -> fetchRow(array(
				"video_id = $videoId",
				"user_id = $userId"
		));
		if ($favorite)
		{
			return $favorite -> delete();
		}
		return false;
	}

	public function getAllowedMaxValue($type, $levelId, $name)
	{
		$mtable = Engine_Api::_() -> getDbtable('permissions', 'authorization');
		$msselect = $mtable -> select() -> where('type = ?', $type) -> where('level_id = ?', $levelId) -> where('name = ?', $name);
		$allow = $mtable -> fetchRow($msselect);

		switch ($allow->value)
		{
			case 3 :
			case 5 :
				if (!empty($allow -> params))
				{
					return $allow -> params;
				}
				else
				{
					return $allow -> value;
				}
			default :
				return $allow -> value;
		}
	}

	public function hasImported($from)
	{
		$itemMatch = array(
				'video' => 'ynultimatevideo_video',
				'ynvideo_playlist' => 'ynultimatevideo_playlist',
		);
		if (!in_array($from -> getType(), array_keys($itemMatch)))
		{
			return true;
		}

		return Engine_Api::_() -> getItemTable($itemMatch[$from -> getType()]) -> hasImportedItem($from);
	}

	public function cloneAuth($from, $to)
	{
		if (!$from || !$to)
			return false;
		$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'registered',
				'everyone',
		);
		$auth_arr = array(
				'view',
				'comment'
		);
		$auth = Engine_Api::_() -> authorization() -> context;
		foreach ($auth_arr as $elem)
		{
			$from_role = 'owner';
			foreach ($roles as $role)
			{
				if (1 === $auth -> isAllowed($from, $role, $elem))
				{
					$from_role = $role;
				}
			}
			$authMax = array_search($from_role, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($to, $role, $elem, ($i <= $authMax));
			}
		}
	}

	public function canUpdateImport($to, $from)
	{
		if (!$to || !$from)
		{
			return false;
		}
		if ($to -> getType() == 'ynultimatevideo_playlist')
		{
			$videos = $from -> getVideos();
			foreach ($videos as $video)
			{
				if (!$this -> hasImported($video))
				{
					return true;
				}
				else
				{
					$importVideo = Engine_Api::_() -> getItemTable('ynultimatevideo_video') -> getImportedItem($video);
					$table = Engine_Api::_() -> getDbTable('playlistassoc', 'ynultimatevideo');
					$row = $table -> getMapRow($to -> getIdentity(), $importVideo -> getIdentity());
					if (!$row)
						return true;
				}
			}
			return false;
		}
		else
		{
			if (!$this -> hasImported($from))
			{
				return true;
			}
		}
		return false;
	}

	public function typeCreate($label)
	{
		$field = Engine_Api::_() -> fields() -> getField('1', 'ynultimatevideo_video');
		// Create new blank option
		$option = Engine_Api::_() -> fields() -> createOption('ynultimatevideo_video', $field, array(
				'field_id' => $field -> field_id,
				'label' => $label,
		));
		// Get data
		$mapData = Engine_Api::_() -> fields() -> getFieldsMaps('ynultimatevideo_video');
		$metaData = Engine_Api::_() -> fields() -> getFieldsMeta('ynultimatevideo_video');
		$optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynultimatevideo_video');
		// Flush cache
		$mapData -> getTable() -> flushCache();
		$metaData -> getTable() -> flushCache();
		$optionData -> getTable() -> flushCache();

		return $option -> option_id;
	}

	public function uploadVideoToChannel($uploadedVideo)
	{
		/*
		 * You can acquire an OAuth 2.0 client ID and client secret from the
		 * Google Developers Console <https://console.developers.google.com/>
		 * For more information about using OAuth 2.0 to access Google APIs, please see:
		 * <https://developers.google.com/youtube/v3/guides/authentication>
		 * Please ensure that you have enabled the YouTube Data API for your project.
		 */
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$owner = $uploadedVideo -> getOwner();

		// get allow to upload to youtube (user and admin)
		$youtube_allow = $settings->getSetting('ynultimatevideo_youtube_allow', 0);
		$user_allow = $uploadedVideo -> allow_upload_channel;
		if(!$youtube_allow || !$user_allow)
		{
			throw new Ynultimatevideo_Model_Exception('Not allow upload to YouTube');
		}

		// Get token from user (owner of video)
		$token = $uploadedVideo -> user_token;

		// Get Client ID and Client secret key from Youtube API
		$OAUTH2_CLIENT_ID = $settings->getSetting('ynultimatevideo_youtube_clientid', "");
		$OAUTH2_CLIENT_SECRET = $settings->getSetting('ynultimatevideo_youtube_secret', "");

		if(empty($OAUTH2_CLIENT_ID) || empty($OAUTH2_CLIENT_SECRET) || empty($token)) {
			throw new Ynultimatevideo_Model_Exception('YouTube settings were missing');
		}

		// get new google client
		$client = new Google_Client();
		$client->setClientId($OAUTH2_CLIENT_ID);
		$client->setClientSecret($OAUTH2_CLIENT_SECRET);
		$client->setAccessType('offline');
		$client->setAccessToken($token);

		/**
		 * Check to see if our access token has expired. If so, get a new one and save it to file for future use.
		 */
		if($client->isAccessTokenExpired()) {
			$newToken = json_decode($client->getAccessToken());
			$client->refreshToken($newToken->refresh_token);
		}

		// Check to ensure that the access token was successfully acquired.
		if ($client->getAccessToken()) {
			try{

				// Define an object that will be used to make all API requests.
				$youtube = new Google_Service_YouTube($client);

				$storageObject = Engine_Api::_() -> getItem('storage_file', $uploadedVideo -> file_id);
				if (!$storageObject)
				{
					throw new Ynultimatevideo_Model_Exception('Video storage file was missing');
				}

				$originalPath = $storageObject -> temporary();
				if (!file_exists($originalPath))
				{
					throw new Ynultimatevideo_Model_Exception('Could not pull to temporary file');
				}

				// Create a snippet with title, description, tags and category ID
				// Create an asset resource and set its snippet metadata and type.
				// This example sets the video's title, description, keyword tags, and
				// video category.
				$snippet = new Google_Service_YouTube_VideoSnippet();
				$snippet->setTitle($uploadedVideo -> getTitle());
				$snippet->setDescription($uploadedVideo -> getDescription());

				// Numeric video category. See
				// https://developers.google.com/youtube/v3/docs/videoCategories/list
				$snippet->setCategoryId("22");

				// Set the video's status to "public". Valid statuses are "public",
				// "private" and "unlisted".
				$status = new Google_Service_YouTube_VideoStatus();
				$status->privacyStatus = "public";

				// Associate the snippet and status objects with a new video resource.
				$video = new Google_Service_YouTube_Video();
				$video->setSnippet($snippet);
				$video->setStatus($status);

				// Specify the size of each chunk of data, in bytes. Set a higher value for
				// reliable connection as fewer chunks lead to faster uploads. Set a lower
				// value for better recovery on less reliable connections.
				$chunkSizeBytes = 1 * 1024 * 1024;

				// Setting the defer flag to true tells the client to return a request which can be called
				// with ->execute(); instead of making the API call immediately.
				$client->setDefer(true);

				// Create a request for the API's videos.insert method to create and upload the video.
				$insertRequest = $youtube->videos->insert("status,snippet", $video);

				// Create a MediaFileUpload object for resumable uploads.
				$media = new Google_Http_MediaFileUpload(
						$client,
						$insertRequest,
						'video/*',
						null,
						true,
						$chunkSizeBytes
				);
				$media->setFileSize(filesize($originalPath));

				// Read the media file and upload it chunk by chunk.
				$status = false;
				$handle = fopen($originalPath, "rb");
				while (!$status && !feof($handle)) {
					$chunk = fread($handle, $chunkSizeBytes);
					$status = $media->nextChunk($chunk);
				}
				fclose($handle);

				// If you want to make other calls after the file upload, set setDefer back to false
				$client->setDefer(false);

				//update video data (replace uploaded video to youtube video)
				$uploadedVideo -> code = $status['id'];
				$uploadedVideo -> file_id = 0;
				$uploadedVideo -> type = 1;
				$uploadedVideo -> status = 1;
				$uploadedVideo -> allow_upload_channel = 0;
				$uploadedVideo -> user_token = "";
				$uploadedVideo -> save();

				// save thumbnail
				$adapter = Ynultimatevideo_Plugin_Factory::getPlugin($uploadedVideo -> type);
				$adapter -> setParams(array(
						'code' => $uploadedVideo -> code,
						'video_id' => $uploadedVideo -> getIdentity()
				));

				if($adapter -> getVideoLargeImage())
					$uploadedVideo -> setPhoto($adapter -> getVideoLargeImage());

				if($adapter -> getVideoDuration())
					$uploadedVideo -> duration = $adapter -> getVideoDuration();
				$uploadedVideo -> save();

				// delete old video
				$storageObject -> delete();

				// delete temporary file
				unlink($originalPath);

				// insert action in a seperate transaction if video status is a success
				$actionsTable = Engine_Api::_() -> getDbtable('actions', 'activity');
				$db = $actionsTable -> getAdapter();
				$db -> beginTransaction();

				try
				{
					// new action
					$item = Engine_Api::_() -> getItem($uploadedVideo -> parent_type, $uploadedVideo -> parent_id);
					if ($uploadedVideo -> parent_type == 'group')
					{
						$action = $actionsTable -> addActivity($owner, $item, 'advgroup_video_create');
					}
					elseif ($uploadedVideo -> parent_type == 'event')
					{
						$action = $actionsTable -> addActivity($owner, $item, 'ynevent_video_create');
					}
					else
					{
						$action = $actionsTable -> addActivity($owner, $uploadedVideo, 'ynultimatevideo_new');
					}
					if ($action)
					{
						$actionsTable -> attachActivity($action, $uploadedVideo);
					}

					// notify the owner
					Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($owner, $owner, $uploadedVideo, 'ynultimatevideo_processed');

					$db -> commit();
				}
				catch (Exception $e)
				{
					$db -> rollBack();
					throw $e;
				}

			}
			catch (Exception $e)
			{
				unlink($originalPath);
				$uploadedVideo -> status = 7;
				$uploadedVideo -> save();
				// notify the owner
				$translate = Zend_Registry::get('Zend_Translate');
				Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($owner, $owner, $uploadedVideo, 'ynultimatevideo_processed_failed', array(
						'message' => $translate -> translate('Video conversion failed.'),
						'message_link' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'ynultimatevideo_general', true),
				));
				throw new Ynultimatevideo_Model_Exception($e -> getMessage());
			}
		}
	}

	/**
	 * @param null $item
	 * @param null $user
	 * @return bool
	 */
	public function canAddToPlaylist($item = null, $user = null) {
		$user = Engine_Api::_()->user()->getViewer();
		$level_id = ($user->getIdentity()) ? $user->level_id : 5;
		$addToPlaylistPermission = Engine_Api::_()->authorization()->getPermission($level_id, 'ynultimatevideo_playlist', 'edit');
		return $addToPlaylistPermission;
	}

	public function canCreatePlaylist($item = null, $user = null) {
		return true;
	}
}
