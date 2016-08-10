<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Video.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Api_Video extends Core_Api_Abstract
{
	/**
	 * form add
	 */
	public function formadd($aData){
		
		$privacyApi  = Engine_Api::_()->getApi('privacy','ynmobile');
		
		$response  =  array(
			'view_options'=> $privacyApi->privacy(),
			'comment_options'=> $privacyApi->privacycomment(),
			'category_options'=> $this->categories($aData),
		);
		
		return $response;
	}
	
	/**
	 * Input data:
	 * + sAction: string, optional, ex: "more" or "new".
	 * + iLastVideoId: int, optional.
	 * + iLimit: int, optional.
	 * + sView: string, optional. ex: "my" or "friend" or "pending"
	 * + sTag: string, optional.
	 * + iCategory: int, optional.
	 * + sParentType: int, optional.
	 * + iParentId: int, optional.
	 * + sSearch: string, optional.
	 * + bIsUserProfile: string, optional, ex: "true" or "false".
	 * + iProfileId: int, optional.
	 *
	 * Output data:
	 * + iVideoId: int.
	 * + bInProcess: bool.
	 * + sParentType: string.
	 * + iParentId: int.
	 * + iParentUserId: int.
	 * + sTitle: string.
	 * + iUserId: int.
	 * + sDescription: string.
	 * + sCode : string.
	 * + iDuration: int.
	 * + iTotalComment: int.
	 * + iTotalLike: int.
	 * + aUserLike array
	 * + sVideoImage: string.
	 * + fRating: float.
	 * + iRatingCount: int.
	 * + iTimeStamp: int.
	 * + sTimeStamp: string.
	 * + sFullTimeStamp: string.
	 * + iTotalView: int.
	 * + sUserImage: string.
	 * + sFullname: string.
	 * + iCategory: int.
	 * + bIsInvisible: bool.
	 * + iUserLevelId: int.
	 *
	 *
	 * @param array $aData
	 * @return array
	 */
	private function getVideos($aData)
	{
		if (!isset($aData['iPage']))
			$aData['iPage'] = 1;
		
		if ($aData['iPage'] == '0')
			return array();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		$iLimit = isset($aData['iLimit']) ? (int)$aData['iLimit'] : 10;
		$sView = isset($aData['sView']) ? $aData['sView'] : '';
		$bIsProfile = (isset($aData['bIsProfile']) && $aData['bIsProfile'] == 'true') ? true : false;
		$iProfileId = isset($aData['iProfileId']) ? (int)$aData['iProfileId'] : 0;

		if ($bIsProfile)
		{
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

		$table = Engine_Api::_() -> getDbtable('videos', 'ynvideo');
		$rName = $table -> info('name');

		$tmTable = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tmName = $tmTable -> info('name');

		$select = $table -> select() -> from($table -> info('name'));
		if (isset($aData['sOrder']) && ($aData['sOrder'] != ''))
		{
			if (in_array($aData['sOrder'], array('creation_date', 'view_count', 'rating')))
				$select->order($aData['sOrder'] . " DESC");
			else
				$select->order("$rName.creation_date DESC" );
		}
		else 
		{
			$select = $select->order("$rName.video_id DESC");
		}

		if (!empty($aData['sSearch']))
		{
			$searchTable = Engine_Api::_() -> getDbtable('search', 'core');
			$db = $searchTable -> getAdapter();
			$sName = $searchTable -> info('name');
			
			$select
			->joinRight($sName, $sName . '.id=' . $rName . '.video_id', null)
			->where($sName . '.type = ?', 'video')
			->where($sName . '.title LIKE ?', "%{$aData['sSearch']}%");
		}

		if ($sView == 'pending')
		{
			$select -> where($rName . '.status = 0');
		}

		if (!$bIsProfile && $sView == 'my' && $viewer -> getIdentity())
		{
			$select -> where("$rName.owner_id = ?", $viewer -> getIdentity());
		}
		elseif (!$bIsProfile && $sView == 'friend' && $viewer -> getIdentity())
		{
			// Get an array of friend ids
			$afriendIds = $viewer -> membership() -> getMembershipsOfIds();
			$select -> where("$rName.owner_id IN(?)", $afriendIds);
			$select -> where("$rName.search = 1");
		}
		else
		{
			$select -> where("$rName.search = 1");
		}

		if ($bIsProfile && $iProfileId)
		{
			$select -> where($rName . '.owner_id = ?', $iProfileId);
		}

		if (!empty($aData['iCategory']))
		{
			$select -> where($rName . '.category_id = ?', $aData['iCategory']);
		}

		if (!empty($aData['iFeatured']))
		{
			$select -> where($rName . '.featured = 1');
		}
		
		if (!empty($aData['sTag']))
		{
			$select -> joinLeft($tmName, "$tmName.resource_id = $rName.video_id", NULL) -> where($tmName . '.resource_type = ?', 'video') -> where($tmName . '.tag_id = ?', $params['tag']);
		}
		if (!empty($aData['sParentType']) && !empty($aData['iParentId']))
		{
			$select -> where($rName . '.parent_type = ?', $aData['sParentType']);
			$select -> where($rName . '.parent_id = ?', $aData['parent_id']);
		}
		if (!empty($aData['iFeatured']) && ($aData['iFeatured'] == '1'))
		{
			$select -> where($rName . '.featured = 1');
		}
		
		$paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($iLimit);
		if (!empty($aData['iPage'])) {
			$paginator -> setCurrentPageNumber($aData['iPage'], 1);
		}
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);

		if ($aData['iPage'] > $totalPage)
			return array();
		
		
		$aResult = array();
		foreach ($paginator as $video)
		{
			$owner = $video -> getOwner();
			
			$sUserImageUrl = $owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sUserImageUrl != "")
			{
				$sUserImageUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImageUrl);
			}
			else
			{
				$sUserImageUrl = NO_USER_ICON;
			}
			$sProfileImage = $video -> getPhotoUrl('thumb.large');
			if ($sProfileImage)
			{
				$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
			}
			else
			{
				$sProfileImage = NO_VIDEO_MAIN;
			}

			$create = strtotime($video -> creation_date);
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

			$iParentUserId = 0;
			if ($video->parent_type && $video->parent_id)
			{
				try {
					if ($parent = $video -> getParent())
					{
						$iParentUserId = $parent -> getOwner() -> getIdentity();
					}
				} catch (Exception $e) {
					continue;
				}
					
			}
			$code = $video -> code;
			if($video -> type == 1)
			{
				$sProfileImage = "http://img.youtube.com/vi/$code/hqdefault.jpg";
			}
			else if($video -> type == 2)
			{
				$data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
				$sProfileImage = $data -> video -> thumbnail_large;
				$sProfileImage = sprintf("%s",$sProfileImage);
			}
			$bCanLike = $bCanComment = (Engine_Api::_() -> authorization() -> isAllowed($video, null, 'comment')) ? true : false;
			$aResult[] = array(
				'iVideoId' => $video -> getIdentity(),
				'bInProcess' => ($video -> status) ? false : true,
				'sParentType' => $video -> parent_type,
				'iParentId' => $video -> parent_id,
				'iParentUserId' => $iParentUserId,
				'iUserId' => $video -> owner_id,
				'sTitle' => $video -> getTitle(),
				'sDescription' => $video -> description,
				'sCode' => $video -> code,
				'iDuration' => $video -> duration,
				'iTotalComment' => $video -> comment_count,
				'bIsLiked' => $video -> likes() -> isLike($viewer),
				'iTotalLike' => $video -> likes() -> getLikeCount(),
				'aUserLike' => Engine_Api::_()-> getApi('like','ynmobile') -> getUserLike($video),
				'iTotalView' => $video -> view_count,
				'fRating' => $video -> rating,
				'iRatingCount' => Engine_Api::_() -> ynvideo() -> ratingCount($video -> getIdentity()),
				'bIsRating' => (Engine_Api::_() -> ynvideo() -> checkRated($video -> getIdentity(), $viewer -> getIdentity())) ? true : false,
				'iTimeStamp' => $create,
				'sTimeStamp' => $sTime,
				'sFullTimeStamp' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($create),
				'sFullname' => $owner -> getTitle(),
				'sUserImage' => $sUserImageUrl,
				'sVideoImage' => $sProfileImage,
				'bIsInvisible' => !(bool)$video -> search,
				'iUserLevelId' => $owner -> level_id,
				'iCategory' => $video -> category_id,
				'bCanView' => (Engine_Api::_() -> authorization() -> isAllowed($video, null, 'view')) ? true : false,
				'bCanComment' => $bCanComment,
				'bCanLike' => $bCanLike,
			);
		}
		return $aResult;
	}

	/**
	 * Input data:
	 * + sAction: string, optional, ex: "more" or "new".
	 * + iLastVideoId: int, optional.
	 * + iLimit: int, optional.
	 * + sView: string, optional. ex: "my" or "friend" or "pending"
	 * + sTag: string, optional.
	 * + iCategory: int, optional.
	 * + sParentType: int, optional.
	 * + iParentId: int, optional.
	 * + sSearch: string, optional.
	 * + bIsUserProfile: string, optional, ex: "true" or "false".
	 * + iProfileId: int, optional.
	 *
	 * Output data:
	 * + iVideoId: int.
	 * + bInProcess: bool.
	 * + sParentType: string.
	 * + iParentId: int.
	 * + iParentUserId: int.
	 * + sTitle: string.
	 * + iUserId: int.
	 * + sDescription: string.
	 * + sCode : string.
	 * + iDuration: int.
	 * + iTotalComment: int.
	 * + iTotalLike: int.
	 * + aUserLike: array
	 * + sVideoImage: string.
	 * + fRating: float.
	 * + iTimeStamp: int.
	 * + sTimeStamp: string.
	 * + sFullTimeStamp: string.
	 * + iTotalView: int.
	 * + sUserImage: string.
	 * + sFullname: string.
	 * + iCategory: int.
	 * + bIsInvisible: bool.
	 * + iUserLevelId: int.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see video/filter
	 *
	 * @param array $aData
	 * @return array
	 */
	public function filter($aData)
	{
		return $this -> getVideos($aData);
	}

	/**
	 * Input data:
	 * + sType : string, required.
	 *
	 * Output data:
	 - sPrivacyValue
	 - sPrivacyName
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see album/privacy
	 *
	 * @param array $aData
	 * @return array
	 */
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
				$viewOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynvideo', $viewer, 'auth_view');
				return array_intersect_key($roles, array_flip($viewOptions));
				break;

			case 'comment' :
				$commentOptions = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynvideo', $user, 'auth_comment');
				return array_intersect_key($roles, array_flip($commentOptions));
				break;
		}
	}
	/**
	 * Input data:
	 * + sLink: string, required.
	 * + iType: string, required.
	 *
	 * Output data:
	 * + iVideoId: integer.
	 * + iPhotoId: integer.
	 * + sTitle: string.
	 * + sDescription: string.
	 * + sThumb: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see link/parser
	 *
	 * @param array $aData
	 * @return array
	 */
	public function parser($aData)
	{
		$sLink = isset($aData['sLink']) ? $aData['sLink'] : '';
		$iType = isset($aData['iType']) ? $aData['iType'] : 0;
		if (empty($sLink) || !$iType)
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!"),
				'error_code' => 1
			);
		}
		if (isset($aData['sParentType']) && $aData['sParentType'] == "message")
		{
			$composer_type = 'message';
		}
		else
		{
			$composer_type = 'wall';
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$sLink = trim(strip_tags($sLink));
		$code = $this -> extractCode($sLink, $iType);
		// check if code is valid
		// check which API should be used
		if ($iType == 1)
		{
			$valid = $this -> checkYouTube($code);
		}
		if ($iType == 2)
		{
			$valid = $this -> checkVimeo($code);
		}

		// check to make sure the user has not met their quota of # of allowed video uploads
		// set up data needed to check quota
		$values['user_id'] = $viewer -> getIdentity();
		$paginator = Engine_Api::_() -> getApi('core', 'ynvideo') -> getVideosPaginator($values);
		$quota = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'video', 'max');
		$current_count = $paginator -> getTotalItemCount();

		if (($current_count >= $quota) && !empty($quota))
		{
			// return error message
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first."),
				'error_code' => 1
			);
		}
		
		else
		if ($valid)
		{
			$db = Engine_Api::_() -> getDbtable('videos', 'ynvideo') -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$information = $this -> handleInformation($iType, $code);

				// create video
				$table = Engine_Api::_() -> getDbtable('videos', 'ynvideo');
				$video = $table -> createRow();
				$video -> title = $information['title'];
				$video -> description = $information['description'];
				$video -> duration = $information['duration'];
				$video -> owner_id = $viewer -> getIdentity();
				$video -> code = $code;
				$video -> type = $iType;
				$video -> parent_id = $viewer -> getIdentity();
				$video -> parent_type = 'user';
				if (isset($aData['sParentType']) && $aData['sParentType'] == "message")
				{
					$video -> search = 0;
				}
				$video -> save();

				// Now try to create thumbnail
				$thumbnail = $this -> handleThumbnail($video -> type, $video -> code);
				$ext = ltrim(strrchr($thumbnail, '.'), '.');
				$thumbnail_parsed = @parse_url($thumbnail);

				$tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
				$thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
				$src_fh = fopen($thumbnail, 'r');
				$tmp_fh = fopen($tmp_file, 'w');
				stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

				$image = Engine_Image::factory();
				$image -> open($tmp_file) -> resize(640, 480) -> write($thumb_file) -> destroy();

				$thumbFileRow = Engine_Api::_() -> storage() -> create($thumb_file, array(
					'parent_type' => $video -> getType(),
					'parent_id' => $video -> getIdentity()
				));

				// If video is from the composer, keep it hidden until the post is complete
				if ($composer_type)
					$video -> search = 0;

				$video -> photo_id = $thumbFileRow -> file_id;
				$video -> status = 1;
				$video -> save();
				$db -> commit();
			}

			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			// make the video public
			$auth = Engine_Api::_() -> authorization() -> context;
			if ($composer_type === 'wall')
			{
				// CREATE AUTH STUFF HERE
				$roles = array(
					'owner',
					'owner_member',
					'owner_member_member',
					'owner_network',
					'registered',
					'everyone'
				);
				foreach ($roles as $i => $role)
				{
					$auth -> setAllowed($video, $role, 'view', ($i <= $roles));
					$auth -> setAllowed($video, $role, 'comment', ($i <= $roles));
				}
			}
			else if ($composer_type == 'message')
			{
				$auth -> setAllowed($video, 'owner', 'view', 1);
				$auth -> setAllowed($video, 'owner', 'comment', 1);
			}
			
			$sProfileImage = $video -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
			if ($sProfileImage)
			{
				$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
			}
			else
			{
				$sProfileImage = NO_VIDEO_MAIN;
			}
			return array(
				'iVideoId' => $video -> video_id,
				'iPhotoId' => $video -> photo_id,
				'sTitle' => $video -> title,
				'sDescription' => $video -> description,
				'sThumb' => $sProfileImage,
			);
		}
		else
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("We could not find a video there - please check the URL and try again."),
				'error_code' => 1
			);
		}
	}
	/**
	 * Input data:
	 * + sAction: string, optional, ex: "more" or "new".
	 * + iLastVideoId: int, optional.
	 * + iLimit: int, optional.
	 * + sView: string, optional. ex: "my" or "friend" or "pending"
	 * + sTag: string, optional.
	 * + iCategory: int, optional.
	 * + sParentType: int, optional.
	 * + iParentId: int, optional.
	 * + sSearch: string, optional.
	 * + bIsUserProfile: string, optional, ex: "true" or "false".
	 * + iProfileId: int, optional.
	 *
	 * Output data:
	 * + iVideoId: int.
	 * + bInProcess: bool.
	 * + sParentType: string.
	 * + iParentId: int.
	 * + iParentUserId: int.
	 * + sTitle: string.
	 * + iUserId: int.
	 * + sDescription: string.
	 * + sCode : string.
	 * + iDuration: int.
	 * + iTotalComment: int.
	 * + iTotalLike: int.
	 * + aUserLike array
	 * + sVideoImage: string.
	 * + fRating: float.
	 * + iTimeStamp: int.
	 * + sTimeStamp: string.
	 * + sFullTimeStamp: string.
	 * + iTotalView: int.
	 * + sUserImage: string.
	 * + sFullname: string.
	 * + iCategory: int.
	 * + bIsInvisible: bool.
	 * + iUserLevelId: int.
	 *
	 * @see Mobile - API phpFox/Api V3.0
	 * @see video/search
	 *
	 * @param array $aData
	 * @return array
	 */
	public function search($aData)
	{
		return $this -> getVideos($aData);
	}

	/**
	 * Input data: N/A
	 *
	 * Output data:
	 * + iCategoryId: int.
	 * + sName: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see video/listcategories
	 *
	 * @param array $aData
	 * @return array
	 */
	public function categories($aData)
	{
		// Populate with categories
		$categories = Engine_Api::_() -> ynvideo() -> getCategories();
		$categoryOptions = array();
		foreach ($categories as $category)
		{
			$categoryOptions[] = array(
				'iCategoryId' => $category -> category_id,
				'sName' => $category -> category_name
			);
		}
		return $categoryOptions;
	}

	/**
	 * Input data:
	 * + iVideoId: int, required.
	 *
	 * Output data:
	 * + iVideoId: int.
	 * + bInProcess: bool.
	 * + sParentType: string.
	 * + iParentId: int.
	 * + iParentUserId: int.
	 * + sTitle: string.
	 * + iUserId: int.
	 * + sDescription: string.
	 * + sCode : string.
	 * + iDuration: int.
	 * + iTotalComment: int.
	 * + iTotalLike: int.
	 * + aUserLike array
	 * + sVideoImage: string.
	 * + fRating: float.
	 * + iRatingCount: int.
	 * + bIsReating: bool.
	 * + iTimeStamp: int.
	 * + sTimeStamp: string.
	 * + sFullTimeStamp: string.
	 * + iTotalView: int.
	 * + sUserImage: string.
	 * + sFullname: string.
	 * + iCategory: int.
	 * + bIsInvisible: bool.
	 * + iUserLevelId: int.
	 * + sEmbedCode: string.
	 * + sVideoUrl: string.
	 * + sEmbed: string.
	 * + bCanPostComment: bool
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see video/detail
	 *
	 * @param array $aData
	 * @return array
	 */
	public function detail($aData)
	{
		if (!isset($aData['iVideoId']))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iVideoId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$video = Engine_Api::_() -> getItem('ynvideo_video', (int)$aData['iVideoId']);
		if (!$video)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Video is not valid!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();

		
		$owner = $video -> getOwner();
		$sUserImageUrl = $owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		if ($sUserImageUrl != "")
		{
			$sUserImageUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImageUrl);
		}
		else
		{
			$sUserImageUrl = NO_USER_ICON;
		}
		$sProfileImage = $video -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
		if ($sProfileImage)
		{
			$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
		}
		else
		{
			$sProfileImage = NO_VIDEO_MAIN;
		}

		$create = strtotime($video -> creation_date);
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

		$iParentUserId = 0;
		if ($video->parent_type && $video->parent_id)
		{
			if ($parent = $video -> getParent())
			{
				$iParentUserId = $parent -> getOwner() -> getIdentity();
			}	
		}

		$can_embed = true;
		if (!Engine_Api::_() -> getApi('settings', 'core') -> getSetting('video.embeds', 1))
		{
			$can_embed = false;
		}
		else
		if (isset($video -> allow_embed) && !$video -> allow_embed)
		{
			$can_embed = false;
		}
		$embedCode = "";
		if ($can_embed)
		{
			// Get embed code
			$embedCode = $video -> getEmbedCode();
		}
		// increment count
		$embedded = "";
		if ($video -> status == 1)
		{
			if (!$video -> isOwner($viewer))
			{
				$video -> view_count++;
				$video -> save();
			}
			$embedded = $this -> getRichContent($video, true);
		}
		$video_location = "";
		if ($video -> type == 1)
		{
			if (isset($aData['sPlatform']) && $aData['sPlatform'] == 'android')
				$video_location = $this->getYoutubeDownloadLink($video -> code);
		}
		
		if ($video -> type == 3 && //uploaded video 
			$video -> status == 1 && //converted or not
			$video->file1_id) // converted by mobile or not
		{
			if (!empty($video -> file_id))
			{
				//GETING H264 video
				$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if ($storage_file)
				{
					$video_location = $storage_file -> map();
					$video_location = Engine_Api::_() -> ynmobile() ->finalizeUrl($video_location);
				}
			}
		}
		$bCanComment = Engine_Api::_() -> authorization() -> isAllowed($video, null, 'comment');
		$bCanView = Engine_Api::_() -> authorization() -> isAllowed($video, null, 'view');
		if (isset($aData['sParentType']) && isset($aData['iParentId']))
		{
			$message_view = false;
			if ($aData['sParentType'] == "message")
			{
				$message_id = $aData['iParentId'];
				if( $message_id ) 
				{
					$conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
					if( $conversation->hasRecipient($viewer)) 
					{
						$bCanView = true;
					}
				}
			}
		}
		
		$sViewPrivacy = $sCommentPrivacy = ""; 
		
		$auth = Engine_Api::_()->authorization()->context;
		$roles = array(
			'owner',
			'owner_member',
			'owner_member_member',
			'owner_network',
			'registered',
			'everyone'
		);
		foreach( $roles as $role )
		{
			if( 1 === $auth->isAllowed($video, $role, 'view') )
			{
				$sViewPrivacy = $role;
			}
			if( 1 === $auth->isAllowed($video, $role, 'comment') )
			{
				$sCommentPrivacy = $role;
			}
		}
		
		$types = $this->getVideoTypes();
		$videoTags = $video->tags()->getTagMaps();
		$aTags = array();
		foreach($videoTags as $tag)
		{
			$aTags[] = $tag->getTag()->text;
		}
		
		return array(
			'bIsLiked' => ($video -> likes() -> isLike($viewer)) ? 1 : 0,
			'iVideoId' => $video -> getIdentity(),
			'bInProcess' => ($video -> status) ? false : true,
			'sParentType' => $video -> parent_type,
			'iParentId' => $video -> parent_id,
			'iParentUserId' => $iParentUserId,
			'iUserId' => $video -> owner_id,
			'sTitle' => $video -> getTitle(),
			'sDescription' => $video -> description,
			'sType' => $types[$video->type],
			'sCode' => $video -> code,
			'iDuration' => $video -> duration,
			'iTotalComment' => $video -> comment_count,
			'bIsLike' => $video -> likes() -> isLike($viewer),
			'iTotalLike' => $video -> likes() -> getLikeCount(),
			'aUserLike' => Engine_Api::_()-> getApi('like','ynmobile') -> getUserLike($video),
			'iTotalView' => $video -> view_count,
			'fRating' => $video -> rating,
			'iRatingCount' => Engine_Api::_() -> ynvideo() -> ratingCount($video -> getIdentity()),
			'bIsRating' => (Engine_Api::_() -> ynvideo() -> checkRated($video -> getIdentity(), $viewer -> getIdentity())) ? true : false,
			'iTimeStamp' => $create,
			'sTimeStamp' => $sTime,
			'sFullTimeStamp' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($create),
			'sFullname' => $owner -> getTitle(),
			'sUserImage' => $sUserImageUrl,
			'sVideoImage' => $sProfileImage,
			'bIsInvisible' => !(bool)$video -> search,
			'iUserLevelId' => $owner -> level_id,
			'iCategory' => $video -> category_id,
			'sEmbedCode' => $embedCode,
			'sVideoUrl' => $video_location,
			'sEmbed' => $embedded,
			'bCanPostComment' => $bCanComment,
			'bCanComment' => $bCanComment,
			'bCanLike' => $bCanComment,
			'bCanView' => $bCanView,
			'sViewPrivacy' => $sViewPrivacy,
			'sCommentPrivacy' => $sCommentPrivacy,
			'sTags' => implode(", ", $aTags),
		);
	}

	/**
	 * Delete image only.
	 *
	 * Input data:
	 * + iVideoId: int, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + message: string.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see video/deleteImage
	 *
	 * @param array $aData
	 * @return array
	 */
	public function deleteImage($aData)
	{
		if (!isset($aData['iVideoId']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$video = Engine_Api::_() -> getItem('ynvideo_video', (int)$aData['iVideoId']);
		if (!$video)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Video is not valid!")
			);
		}
		if (!Engine_Api::_() -> authorization() -> isAllowed($video, null, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this video!"),
				'result' => 0
			);
		}
		$video -> photo_id = 0;
		$video -> save();

		return array(
			'result' => 1,
			'error_code' => 0,
			'message' => 'Delete image successfully!'
		);
	}

	/** input data:
	 * + parent_type: string, optional.
	 * + parent_id: int, optional.
	 * + category_id: string, optional.
	 * + title: string, required.
	 * + description: string, optional.
	 * + search: int, optional.
	 * + auth_view: string, optional.
	 * + auth_comment: string, optional.
	 * + type: int, required Ex: 1,2,4,5 (3: upload video from PC)
	 * + sUrl: string, required
	 *
	 * output data:
	 * + result: 1 if success and 0 otherwise.
	 * + error_code: 1 if error, and 0 otherwise.
	 * + message: Message to show the bug.
	 * + iVideoId: Video id.
	 * + sVideoTitle: Title of video.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see video/create
	 *
	 * @param array $aData
	 * @return array
	 *
	 */
	public function create($aData)
	{
		$sUrl = isset($aData['sUrl']) ? $aData['sUrl'] : '';
		if (!$sUrl)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Url is not valid!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		
		if (!Engine_Api::_() -> authorization() -> isAllowed('video', null, 'create'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to create video!"),
				'result' => 0
			);
		}
		$values['user_id'] = $viewer -> getIdentity();
		$paginator = Engine_Api::_() -> getApi('core', 'ynvideo') -> getVideosPaginator($values);
		$quota = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynvideo', 'max');
		$current_count = $paginator -> getTotalItemCount();

		if (($current_count >= $quota) && !empty($quota))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first."),
				'result' => 0
			);
		}

		$parent_type = 'user';
		$parent_id = $viewer -> getIdentity();
		$type = 0;
		if (!empty($aData['parent_type']))
		{
			$parent_type = $aData['parent_type'];
		}
		if (!empty($aData['parent_id']))
		{
			$parent_id = $aData['parent_id'];
		}
		if (!empty($aData['type']))
		{
			$type = $aData['type'];
		}

		$values = $aData;
		$values['owner_id'] = $viewer -> getIdentity();
		$values['parent_type'] = $parent_type;
		$values['parent_id'] = $parent_id;
		$values['type'] = $type;
		$code = $this -> extractCode($sUrl, $type);
		// check if code is valid
		// check which API should be used
		if ($type == 1)
		{
			$valid = $this -> checkYouTube($code);
		}
		if ($type == 2)
		{
			$valid = $this -> checkVimeo($code);
		}
		if (!$valid)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Url is not valid!")
			);
		}
		$values['code'] = $code;
		$insert_action = false;

		$db = Engine_Api::_() -> getDbtable('videos', 'ynvideo') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			// Create video
			$table = Engine_Api::_() -> getDbtable('videos', 'ynvideo');
			$video = $table -> createRow();
			$video -> setFromArray($values);
			$video -> save();

			// Now try to create thumbnail
			$thumbnail = $this -> handleThumbnail($video -> type, $video -> code);
			$ext = ltrim(strrchr($thumbnail, '.'), '.');
			$thumbnail_parsed = @parse_url($thumbnail);

			if (@GetImageSize($thumbnail))
			{
				$valid_thumb = true;
			}
			else
			{
				$valid_thumb = false;
			}

			if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array(
				'jpg',
				'jpeg',
				'gif',
				'png'
			)))
			{
				$tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
				$thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

				$src_fh = fopen($thumbnail, 'r');
				$tmp_fh = fopen($tmp_file, 'w');
				stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

				$image = Engine_Image::factory();
				$image -> open($tmp_file) -> resize(120, 240) -> write($thumb_file) -> destroy();

				try
				{
					$thumbFileRow = Engine_Api::_() -> storage() -> create($thumb_file, array(
						'parent_type' => $video -> getType(),
						'parent_id' => $video -> getIdentity()
					));

					// Remove temp file
					@unlink($thumb_file);
					@unlink($tmp_file);
				}
				catch( Exception $e )
				{

				}
				$information = $this -> handleInformation($video -> type, $video -> code);

				$video -> duration = $information['duration'];
				if (!$video -> description)
				{
					$video -> description = $information['description'];
				}
				$video -> photo_id = $thumbFileRow -> file_id;
				$video -> status = 1;
				// Add tags
				if (isset($values['tags']))
				{
					$tags = preg_split('/[,]+/', $values['tags']);
					$video->tags()->addTagMaps($viewer, $tags);
				}
				$video -> save();

				// Insert new action item
				$insert_action = true;
			}

			if ($valid)
			{
				$video -> status = 1;
				$video -> save();
				$insert_action = true;
			}

			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'registered',
				'everyone'
			);
			if (isset($values['auth_view']))
				$auth_view = $values['auth_view'];
			else
				$auth_view = "everyone";
			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'view', ($i <= $viewMax));
			}
			if (isset($values['auth_comment']))
				$auth_comment = $values['auth_comment'];
			else
				$auth_comment = "everyone";
			$commentMax = array_search($auth_comment, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'comment', ($i <= $commentMax));
			}
			if ($insert_action)
			{
				$owner = $video -> getOwner();
				$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($owner, $video, 'video_new');
				if ($action != null)
				{
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $video);
				}
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($video) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}
			$db -> commit();
			return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Video successfully created."),
				'iVideoId' => $video -> getIdentity(),
				'iVideoTitle' => $video -> getTitle()
			);
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Upload failed by database query')
			);
		}
	}

	/** input data:
	 * + parent_type: string, optional.
	 * + parent_id: int, optional.
	 * + category_id: string, optional.
	 * + title: string, required.
	 * + description: string, optional.
	 * + search: int, optional.
	 * + auth_view: string, optional.
	 * + auth_comment: string, optional.
	 * + video: file, required
	 *
	 * output data:
	 * + result: 1 if success and 0 otherwise.
	 * + error_code: 1 if error, and 0 otherwise.
	 * + message: Message to show the bug.
	 * + iVideoId: Video id.
	 * + sVideoTitle: Title of video.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see video/upload
	 *
	 * @param array $aData
	 * @return array
	 *
	 */
	public function upload($aData)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$values['user_id'] = $viewer -> getIdentity();
		$paginator = Engine_Api::_() -> getApi('core', 'ynvideo') -> getVideosPaginator($values);
		$quota = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynvideo', 'max');
		$current_count = $paginator -> getTotalItemCount();

		if (($current_count >= $quota) && !empty($quota))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first."),
				'result' => 0
			);
		}

		if( empty($aData['video']) && !isset($_FILES['video']))
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("No file!"),
					'result' => 0
			);
		}

		$illegal_extensions = array(
			'php',
			'pl',
			'cgi',
			'html',
			'htm',
			'txt'
		);
		if (in_array(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION), $illegal_extensions))
		{
			return array(
				'error_code' => 3,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid Upload'),
				'result' => 0
			);
		}
		
		$parent_type = 'user';
		$parent_id = $viewer -> getIdentity();
		$type = 3;
		if (!empty($aData['parent_type']))
		{
			$parent_type = $aData['parent_type'];
		}
		if (!empty($aData['parent_id']))
		{
			$parent_id = $aData['parent_id'];
		}

		$db = Engine_Api::_() -> getDbtable('videos', 'ynvideo') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$values['default_video_module'] = false;
			$params = array(
				'owner_type' => 'user',
				'owner_id' => $viewer -> getIdentity()
			);
			$video = Engine_Api::_() -> ynmobile() -> createVideo($params, $_FILES['video'], $values);
			
			// sets up title and owner_id now just incase members switch page as soon as upload is completed
			$video -> title = $_FILES['video']['name'];
			$video -> owner_id = $viewer -> getIdentity();
			$video -> type = 3;
			$video -> parent_type = $parent_type;
			$video -> parent_id = $parent_id;
			
			if (!empty($aData['title']))
			{
				$video -> title = $aData['title'];
			}
			if (!empty($aData['description']))
			{
				$video -> description = $aData['description'];
			}
			if (!empty($aData['search']))
			{
				$video -> search = $aData['search'];
			}
			if (!empty($aData['category_id']))
			{
				$video -> category_id = $aData['category_id'];
			}
			if (isset($aData['status_text']))
			{
				$video->status_text = $aData['status_text'];
			}
			// Add tags
			if (isset($aData['tags']))
			{
				$tags = preg_split('/[,]+/', $aData['tags']);
				$video->tags()->addTagMaps($viewer, $tags);
			}
			$video -> save();
			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'registered',
				'everyone'
			);
			if (isset($aData['auth_view']))
				$auth_view = $aData['auth_view'];
			else
				$auth_view = "everyone";
			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'view', ($i <= $viewMax));
			}
			if (isset($aData['auth_comment']))
				$auth_comment = $aData['auth_comment'];
			else
				$auth_comment = "everyone";
			$commentMax = array_search($auth_comment, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'comment', ($i <= $commentMax));
			}
			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($video) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}
			$db -> commit();
			return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Video successfully created."),
				'iVideoId' => $video -> getIdentity(),
				'iVideoTitle' => $video -> getTitle()
			);
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 4,
				'error_message' => $e->getMessage()
			);

		}
	}

	/** input data:
	 * + iVideoId: int, required.
	 * + category_id: string, optional.
	 * + title: string, required.
	 * + description: string, optional.
	 * + search: int, optional.
	 * + auth_view: string, optional.
	 * + auth_comment: string, optional.
	 *
	 * output data:
	 * + result: 1 if success and 0 otherwise.
	 * + error_code: 1 if error, and 0 otherwise.
	 * + message: Message to show the bug.
	 * + iVideoId: Video id.
	 * + sVideoTitle: Title of video.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see video/edit
	 *
	 * @param array $aData
	 * @return array
	 *
	 */
	public function edit($aData)
	{
		if (!isset($aData['iVideoId']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$video = Engine_Api::_() -> getItem('ynvideo_video', $aData['iVideoId']);
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$video)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Video doesn't exists!")
			);
		}
		if (!Engine_Api::_() -> authorization() -> isAllowed($video, null, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this video!"),
				'result' => 0
			);
		}
		if (!isset($aData['title']) || trim($aData['title']) == "")
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Title is not valid!")
			);
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('videos', 'ynvideo') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$values = $aData;
			$video -> setFromArray($values);
			// Add tags
			if (isset($values['tags']))
			{
				$tags = preg_split('/[,]+/', $values['tags']);
				$video->tags()->addTagMaps($viewer, $tags);
			}
			$video -> save();

			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'registered',
				'everyone'
			);
			if (isset($values['auth_view']))
				$auth_view = $values['auth_view'];
			else
				$auth_view = "everyone";
			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'view', ($i <= $viewMax));
			}

			if (isset($values['auth_comment']))
				$auth_comment = $values['auth_comment'];
			else
				$auth_comment = "everyone";
			$commentMax = array_search($auth_comment, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'comment', ($i <= $commentMax));
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($video) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}
			$db -> commit();
			return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Video successfully edited."),
				'iVideoId' => $video -> getIdentity(),
				'iVideoTitle' => $video -> getTitle()
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('An error occurred.')
			);
		}
	}

	/** input data:
	 * + iVideoId: int, required.
	 *
	 * output data:
	 * + result: 1 if success and 0 otherwise.
	 * + error_code: 1 if error, and 0 otherwise.
	 * + error_message: Message to show the bug.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see video/delete
	 *
	 * @param array $aData
	 * @return array
	 *
	 */
	public function delete($aData)
	{
		if (!isset($aData['iVideoId']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$video = Engine_Api::_() -> getItem('ynvideo_video', $aData['iVideoId']);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$video)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Video doesn't exists!")
			);
		}
		if (!Engine_Api::_() -> authorization() -> isAllowed($video, null, 'delete'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this video!"),
				'result' => 0
			);
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('videos', 'ynvideo') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			Engine_Api::_() -> getApi('core', 'ynvideo') -> deleteVideo($video);
			$db -> commit();
			return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Video has been deleted."),
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('An error occurred.')
			);
		}
	}

	private function extractCode($url, $type)
	{
		switch ($type)
		{
			//youtube
			case "1" :
				// change new youtube URL to old one
				$new_code = @pathinfo($url);
				$url = preg_replace("/#!/", "?", $url);

				// get v variable from the url
				$arr = array();
				$arr = @parse_url($url);
				$code = "code";
				$parameters = $arr["query"];
				parse_str($parameters, $data);
				$code = $data['v'];
				if ($code == "")
				{
					$code = $new_code['basename'];
				}

				return $code;
			//vimeo
			case "2" :
				// get the first variable after slash
				$code = @pathinfo($url);
				return $code['basename'];
		}
	}

	// YouTube Functions
	private function checkYouTube($code)
	{
		if (!$data = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/" . $code))
			return false;
		if ($data == "Video not found")
			return false;
		return true;
	}

	// Vimeo Functions
	private function checkVimeo($code)
	{
		//http://www.vimeo.com/api/docs/simple-api
		//http://vimeo.com/api/v2/video
		$data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
		$id = count($data -> video -> id);
		if ($id == 0)
			return false;
		return true;
	}

	// handles thumbnails
	private function handleThumbnail($type, $code = null)
	{
		switch ($type)
		{
			//youtube
			case "1" :
				//http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
				return "http://img.youtube.com/vi/$code/default.jpg";
			//vimeo
			case "2" :
				//thumbnail_medium
				$data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
				$thumbnail = $data -> video -> thumbnail_medium;
				return $thumbnail;
		}
	}

	// retrieves infromation and returns title + desc
	private function handleInformation($type, $code)
	{
		switch ($type)
		{
			//youtube
			case "1" :
				$yt = new Zend_Gdata_YouTube();
				$youtube_video = $yt -> getVideoEntry($code);
				$information = array();
				$information['title'] = $youtube_video -> getTitle();
				$information['description'] = $youtube_video -> getVideoDescription();
				$information['duration'] = $youtube_video -> getVideoDuration();
				//http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
				return $information;
			//vimeo
			case "2" :
				//thumbnail_medium
				$data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
				$thumbnail = $data -> video -> thumbnail_medium;
				$information = array();
				$information['title'] = $data -> video -> title;
				$information['description'] = $data -> video -> description;
				$information['duration'] = $data -> video -> duration;
				//http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
				return $information;
		}
	}

	private function getRichContent($video, $view = false)
	{
		// if video type is youtube
		if ($video -> type == 1)
		{
			$videoEmbedded = '
		        <iframe
		         title="YouTube video player"
		        id="videoFrame'.$video -> video_id.'"
		        class="youtube_iframe_big"'.
		        'width="640"
		        height="360"
		        src="http://www.youtube.com/embed/'.$video -> code.'?wmode=opaque"
		        frameborder="0"
		        allowfullscreen=""
		        scrolling="no">
		        </iframe>';
			
		}
		// if video type is vimeo
		if ($video -> type == 2)
		{
			$videoEmbedded = '<iframe
		        title="Vimeo video player"
		        id="videoFrame'.$video -> video_id.'"
		        class="vimeo_iframe_big"'.
		        'width="640"
		        height="360"
		        src="http://player.vimeo.com/video/'.$video -> code.'?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque"
		        frameborder="0"
		        allowfullscreen=""
		        scrolling="no">
		        </iframe>';
		}

		// if video type is uploaded
		if ($video -> type == 3)
		{
			$videoEmbedded = "";
		}
		return $videoEmbedded;
	}
	
	
	public function featured($aData)
	{
		$aData['iFeatured'] = 1;
		return $this->getVideos($aData);
	}
	
	public function create_playlist($aData) 
	{
		if (!isset($aData['sTitle']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Playlist title!")
			);
		}
	
		$viewer = Engine_Api::_() -> user() -> getViewer();
	
		// Process saving the new playlist
		$values['title'] = $aData['sTitle'];
		$values['user_id'] = $viewer->getIdentity();
		$values['description'] = $aData['sDescription'];
		$values['auth_view'] = $aData['sAuthView'];
		$values['auth_comment'] = $aData['sAuthComment'];
		
		$playlistTable = Engine_Api::_()->getDbtable('playlists', 'ynvideo');
		
		$db = $playlistTable->getAdapter();
		$db->beginTransaction();
		
		try {
			$playlist = $playlistTable->createRow();
			$playlist->setFromArray($values);
			$playlist->save();
	
			if (!empty($values['photo'])) {
				try {
					$playlist->setPhoto($form->photo);
				} catch (Engine_Image_Adapter_Exception $e) {
					Zend_Registry::get('Zend_Log')->log($e->__toString(), Zend_Log::WARN);
				}
			}
	
			// Auth
			$auth = Engine_Api::_()->authorization()->context;
	
			if (empty($values['auth_view'])) {
				$values['auth_view'] = 'everyone';
			}
	
			if (empty($values['auth_comment'])) {
				$values['auth_comment'] = 'everyone';
			}
	
			$viewMax = array_search($values['auth_view'], $this->_roles);
			$commentMax = array_search($values['auth_comment'], $this->_roles);
	
			foreach ($this->_roles as $i => $role) {
				$auth->setAllowed($playlist, $role, 'view', ($i <= $viewMax));
				$auth->setAllowed($playlist, $role, 'comment', ($i <= $commentMax));
			}
	
			$db->commit();
		} catch (Exception $e) {
			$db->rollback();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	
		// add activity feed for creating a new playlist
		$db->beginTransaction();
		try {
			$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $playlist, 'ynvideo_playlist_new');
			if ($action != null) {
				Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $playlist);
			}
	
			// Rebuild privacy
			$actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($playlist) as $action) {
				$actionTable->resetActivityBindings($action);
			}
	
			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	
		return array(
			'iPlaylistId' => $playlist -> getIdentity()
		);
	}
	
	
	public function playlists($aData)
	{
		$limit  = 10;
		if (isset($aData['iLimit']))
		{
			$limit  = (int) $aData['iLimit'];
		}
		
		$table = Engine_Api::_()->getDbTable("playlists", "ynvideo");
		$select = $table->select()->limit($limit)->order("playlist_id DESC");
		
		if ($aData['iLastPlaylistId'])
		{
			$select = $select->where("playlist_id < {$aData['iLastPlaylistId']} ");
		}
		
		$playlists = $table->fetchAll($select);
		$result = array();
		$userApi = Engine_Api::_()->user();
		foreach ($playlists as $playlist)
		{
			$user = $userApi -> getUser($playlist -> user_id);
			
			$result[] = array(
				'iPlaylistId' => $playlist->playlist_id,
				'sTitle' => $playlist -> getTitle(),
				'sDescription' => $playlist -> getDescription(),
				'iUserId' => $playlist -> user_id,
				'sUserName' => $user -> getTitle() 
			);
		}
		return $result;
	}
	
	public function getVideoTypes()
	{
		return array(
			'1' => 'youtube',
			'2' => 'vimeo',
			'3' => 'uploaded',
			'4' => 'dailymotion'	
		);
	}
	
	
	protected  function parseVideo($videos)
	{
		$aResult = array();
		
		if (!count($videos))
		{
			return $aResult;	
		}
		
		foreach ($videos as $video)
		{
			$owner = $video -> getOwner();
			if (!Engine_Api::_() -> authorization() -> isAllowed($video, null, 'view'))
			{
				continue;
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
				
			$sProfileImage = $video -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
			if ($sProfileImage)
			{
				$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
			}
			else
			{
				$sProfileImage = NO_VIDEO_MAIN;
			}
		
			$create = strtotime($video -> creation_date);
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
		
			$iParentUserId = 0;
			if ($video->parent_type && $video->parent_id)
			{
				if ($parent = $video -> getParent())
				{
					$iParentUserId = $parent -> getOwner() -> getIdentity();
				}
			}
			$types = $this->getVideoTypes();
				
			$aResult[] = array(
					'iVideoId' => $video -> getIdentity(),
					'bInProcess' => ($video -> status) ? false : true,
					'sParentType' => $video -> parent_type,
					'iParentId' => $video -> parent_id,
					'iParentUserId' => $iParentUserId,
					'iUserId' => $video -> owner_id,
					'sTitle' => $video -> getTitle(),
					'sDescription' => $video -> description,
					'sType' => $types[$video->type],
					'sCode' => $video -> code,
					'iDuration' => $video -> duration,
					'iTotalComment' => $video -> comment_count,
					'iTotalLike' => $video -> likes() -> getLikeCount(),
					'iTotalView' => $video -> view_count,
					'fRating' => $video -> rating,
					'iRatingCount' => Engine_Api::_() -> ynvideo() -> ratingCount($video -> getIdentity()),
					'iTimeStamp' => $create,
					'sTimeStamp' => $sTime,
					'sFullTimeStamp' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($create),
					'sFullname' => $owner -> getTitle(),
					'sUserImage' => $sUserImageUrl,
					'sVideoImage' => $sProfileImage,
					'bIsInvisible' => !(bool)$video -> search,
					'iUserLevelId' => $owner -> level_id,
					'iCategory' => $video -> category_id
			);
		}
		return $aResult;
	}
	
	public function watchlater($aData)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$videoTbl = Engine_Api::_()->getDbTable('videos', 'ynvideo');
		$videoTblName = $videoTbl->info('name');
		
		$watchLaterTbl = Engine_Api::_()->getDbTable('watchlaters', 'ynvideo');
		$watchLaterTblName = $watchLaterTbl->info('name');
		
		$limit = 10;
		if (isset($aData['iLimit']))
		{
			$limit  = (int) $aData['iLimit'];
		}
		
		$select = Engine_Api::_()->ynvideo()->getVideosSelect(null, false);
		
		$select->setIntegrityCheck(false)
		->join($watchLaterTblName, $watchLaterTblName . ".video_id = " . $videoTblName . ".video_id", "$watchLaterTblName.watched")
		->where("$watchLaterTblName.user_id = ?", $viewer->getIdentity())
		->where("$videoTblName.search = 1")
		->where("$videoTblName.status = 1")
		->where("$watchLaterTblName.watched = 0")
		->order("$watchLaterTblName.watchlater_id DESC")
		->limit($limit);
		
		if ($aData['iLastWatchLaterId'])
		{
			$select = $select->where("$watchLaterTblName.watchlater_id < {$aData['iLastWatchLaterId']} ");
		}
		
		
		$paginator = Zend_Paginator::factory($select);
		// Set item count per page and current page number
		$paginator->setCurrentPageNumber(1);
		$paginator->setItemCountPerPage($limit);
		
		return $this->parseVideo($paginator);
		
	}
	
	public function add_watchlater($aData)
	{
		if (!isset($aData['iVideoId']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Video Id!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$watchlater = Engine_Api::_() -> ynvideo() -> addVideoToWatchLater($aData['iVideoId'], $viewer->getIdentity() );
		if ($watchlater->getIdentity() > 0)
		{
			return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Video has been added to watch later list."),
			);
		}
		
		return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('An error occurred.')
		);
		
	}
	
	public function favorite($aData)
	{
		$viewer = Engine_Api::_()->user()->getViewer();

		$limit = 10;
		if (isset($aData['iLimit']))
		{
			$limit  = (int) $aData['iLimit'];
		}
		
		$select = Engine_Api::_()->ynvideo()->getVideosSelect();
		
		$videoTable = Engine_Api::_()->getDbTable('videos', 'ynvideo');
		$videoTableName = $videoTable->info('name');
		
		$favoriteTable = Engine_Api::_()->getDbTable('favorites', 'ynvideo');
		$favoriteTableName = $favoriteTable->info('name');
		
		$select->setIntegrityCheck(false)
		->join($favoriteTableName, $favoriteTableName . ".video_id = " . $videoTableName . ".video_id")
		->where("$favoriteTableName.user_id = ?", $viewer->getIdentity())
		->order("$favoriteTableName.video_id DESC")
		->limit($limit);
		
		if ($aData['iLastVideoId'])
		{
			$select = $select->where("$favoriteTableName.video_id < {$aData['iLastVideoId']} ");
		}
		
		
		$paginator = Zend_Paginator::factory($select);
		// Set item count per page and current page number
		$paginator->setCurrentPageNumber(1);
		$paginator->setItemCountPerPage($limit);
		
		return $this->parseVideo($paginator);
	}
	
	
	public function add_favorite($aData)
	{
		if (!isset($aData['iVideoId']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Video Id!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$favorite = Engine_Api::_() -> ynvideo() -> addVideoToFavorite($aData['iVideoId'], $viewer->getIdentity() );
		if ($favorite->getIdentity() > 0)
		{
			return array(
					'error_code' => 0,
					'result' => 1,
					'message' => Zend_Registry::get('Zend_Translate') -> _("Video has been added to favorite list."),
			);
		}
	
		return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('An error occurred.')
		);
	
	}
	
	public function rate($aData)
	{
		if (!isset($aData['iVideoId']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Video Id!")
			);
		}
		
		if (!isset($aData['iRating']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Rating value!")
			);
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$video = Engine_Api::_() -> getItem('ynvideo_video', $aData['iVideoId']);
		if (!is_object($video))
		{
			return array(  
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Can not find this video!")
			);
		}
		
		$bCanView = Engine_Api::_() -> authorization() -> isAllowed($video, null, 'view');
		if (!($bCanView))
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to rate this video!")
			);
		}
		
		$ratedAlready = Engine_Api::_()->ynvideo()->checkRated($video->getIdentity(), $viewer->getIdentity());
	
		if ($ratedAlready){
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You rated already!")
			);
		}
		
		//process for rating a video
		$table = Engine_Api::_()->getDbTable('ratings', 'ynvideo');
		$rName = $table->info('name');
		$select = $table->select()
						->from($rName)
						->where($rName . '.video_id = ?', $aData['iVideoId'])
						->where($rName . '.user_id = ?', $viewer->getIdentity())
						->limit(1);
		
		$result = $table->fetchAll($select);
		if (!count($result)) {
			// create rating
			Engine_Api::_()->getDbTable('ratings', 'ynvideo')->insert(array(
			'video_id' => $aData['iVideoId'],
			'user_id' => $viewer->getIdentity(),
			'rating' => $aData['iRating']
			));
		}
		
		//save rating to video table
		$video -> rating = Engine_Api::_() -> ynvideo() -> getRating($aData['iVideoId']);
		$video -> save();
		$total = Engine_Api::_() -> ynvideo() -> ratingCount($aData['iVideoId']);
		
		return array(
				'error_code' => 0,
				'iTotal' => $total,
				'fRating' => $video -> rating,
		);
		
	}
	
	protected function getYoutubeDownloadLink($sCode)
	{
		$r= file_get_contents("http://www.youtube.com/watch?v=$sCode");
		
		preg_match_all('/url_encoded_fmt_stream_map": "([^"]*)/', $r, $fvars);
		
		$fvars = explode(',', $fvars[1][0]);
		
		$result = array();
		
		$aItags = array('18','43','44','22','45');
		
		foreach($fvars as $item)
		{
			$params = explode("\\u0026", $item);
			$itag = ''; $url = '';
			foreach ($params as $p)
			{
				$vars = explode("=", $p);
				$key = $vars[0]; $val = $vars[1];
				if ($key == 'itag') $itag = $val;
				if ($key == 'url') $url = $val;
			}
			$result[] = array(
					'itag' => $itag,
					'url' => urldecode($url)
			);
		
		}
		
		foreach ($aItags as $itag)
		{
			foreach($result as $res) {
				if($res['itag'] == $itag) {
					return $res['url'];
				}
			}
		}
		
		return $result[0]['url'];
	}
	
	
}
