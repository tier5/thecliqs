<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Photo.php trunglt $
 * @author     TrungLT
 */
class Ynmobile_Service_Photo extends Ynmobile_Service_Base
{
    
    protected $module = 'album';
    protected $mainItemType = 'album';
    
    const GETPHOTO_TYPE_MY = 'my';
	const GETPHOTO_TYPE_FEATURED = 'featured';
	const GETPHOTO_TYPE_FRIEND = 'friend';
	const GETPHOTO_TYPE_ALBUM = 'album';
	const GETPHOTO_TYPE_SLIDE = 'slide';
	const GETPHOTO_PHOTO_LIMIT = 10;

	const GETALBUM_TYPE_MY = 'my';
	const GETALBUM_TYPE_FEATURED = 'featured';
	const GETALBUM_TYPE_FRIEND = 'friend';
	const GETALBUM_ALBUM_LIMIT = 5;

	const ACTION_TYPE_MORE = 'more';
	const ACTION_TYPE_NEW = 'new';

	const ACTION_TYPE_NEXT = 'next';
	const ACTION_TYPE_PREVIOUS = 'previous';
	
	
	function fetch_slide($aData)
	{
		$sType = @$aData['sType']?$aData['sType']: 'album_photo';
		
		if(!isset($aData['sAction']))
		{
			$aData['sAction'] == 'next';
		}
		
		if(isset($aData['iUserId']) && $aData['iUserId'] == 0)
		{
			unset($aData['iUserId']);
		}
		
		if($sType == 'album_photo'){
			
			$aData['sType'] = 'photo';
			
			if(@$aData['iAlbumId']){
				return $this->fullalbumslide($aData);
			}	
			
			return $this->fullphotoslide($aData);
		}
	}
	/**
	 * Input Data:
	 * + iLimit: int, optional.
	 * + iPage: int, optional.
	 * + iUserId: int, optional.
	 * + bIsUserProfile: bool, optional. In profile.
	 * + sType: string, optional.
	 * + iCategory: int, optional.
	 * + sTag: string, optional.
	 * + iViewerId: int, current viewer id
	 * + aFriendsId: array, friends id
	 * + sAction: string, 'more' or 'new'
	 * Output Data:
	 * SUCCESS:
	 * + iPhotoId: int.
	 * + sTitle: string.
	 * + sPhotoUrl: string.
	 * FAIL:
	 * + error_code: int
	 * + error_message: string
	 * + result: int
	 */
	private function getPhotos($aData, $type)
	{
		extract($aData, EXTR_SKIP);

		if (!isset($iPage))
			$iPage = 1;

		if ($iPage == '0')
			return array();

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$translate = Zend_Registry::get('Zend_Translate');
        
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable = $this->getWorkingTable('photos','album');
		
		if (isset($iAlbumId))
		{
		    
            $album  = $albumTable ->findRow($iAlbumId);
			
			if ( !($album->getIdentity()) )
				return array(
					'error_code' => 1,
					'error_message' => $translate -> _("This album is not existed."),
					'result' => 0
				);
			
			$bCanView = Engine_Api::_() -> authorization() -> isAllowed($album, $viewer, 'view');
			if ( !($bCanView) )
			{
				return array(
					'error_code' => 2,
					'error_message' => $translate -> _("You do not have permission to view this album."),
					'result' => 0
				);
			}
		}
		
		$albumIds = $this -> getAlbumsCanView();
		if (!count($albumIds))
		{
			return array();
		}

		if (!empty($iLastPhotoIdViewed) && !is_numeric($iLastPhotoIdViewed))
		{
			return array(
				'result' => 0,
				'error_code' => 4,
				'error_message' => $translate -> _("Invalid Last Viewed Photo Id")
			);
		}

		if (!empty($iCategoryId) && !is_numeric($iCategoryId))
		{
			return array(
				'result' => 0,
				'error_code' => 5,
				'error_message' => $translate -> _("Invalid Category Id")
			);
		}

		if (!empty($iLimit) && !is_numeric($iLimit))
		{
			return array(
				'result' => 0,
				'error_code' => 6,
				'error_message' => $translate -> _("Invalid Category Id")
			);
		}

		if (!isset($sAction))
		{
			$sAction = 'new';
		}

		if (!in_array($sAction, array(
			self::ACTION_TYPE_MORE,
			self::ACTION_TYPE_NEW,
			self::ACTION_TYPE_NEXT,
			self::ACTION_TYPE_PREVIOUS
		)))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Invalid Action Type")
			);
		}

		// photo table
		$photoTableName = $photoTable -> info('name');
		
		$albumTableName = $albumTable -> info('name');
		// tag table
		$tagTable = Engine_Api::_() -> getDbtable('tags', 'core');
		$tagTableName = $tagTable -> info('name');
		// tag map table
		$tagMapTable = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tagMapTableName = $tagMapTable -> info('name');

		$select = $photoTable -> select() -> from($photoTableName) 
		-> where( "$photoTableName.owner_type = ?", 'user' ) 
		-> where( "$photoTableName.album_id IN (?)", $albumIds );

		// check viewerId, use for 'my' function
		$iViewerId = $viewer -> getIdentity();
		if ($type == self::GETPHOTO_TYPE_MY)
		{
			$userAlbumIds = $this -> userAlbums($iViewerId);
			$select -> where("$photoTableName.album_id IN (?)", $userAlbumIds);
		}

		// check iUserId, use for 'friend' function
		if ($type == self::GETPHOTO_TYPE_FRIEND || $type == self::GETPHOTO_TYPE_SLIDE)
		{
			if (isset($aFriendsId) && is_array($aFriendsId))
			{
				$select -> where("$photoTableName.owner_id IN (?)", $aFriendsId);
			}
			else
			if (isset($iUserId))
			{
				$userAlbumIds = $this -> userAlbums($iUserId);
				$select -> where("$photoTableName.album_id IN (?)", $userAlbumIds);
			}
		}

		// check iAlbumId, user for 'listalbumphoto' function
		if ($type == self::GETPHOTO_TYPE_ALBUM)
		{
			$select -> where("$photoTableName.album_id = ?", $iAlbumId);
		}

		if (!empty($sType) || (isset($iCategoryId) && $iCategoryId >= 0))
		{
			$select -> joinLeft($albumTableName, $albumTableName . '.album_id=' . $photoTableName . '.album_id', null);
		}

		if (!empty($sTag))
		{
			$select -> joinLeft($tagMapTableName, $photoTableName . '.photo_id=' . $tagMapTableName . '.resource_id' . " AND $tagMapTableName.resource_type = 'album_photo'", null) -> joinLeft($tagTableName, $tagTableName . '.tag_id=' . $tagMapTableName . '.tag_id', null) -> where("$tagTableName.text = ?", $sTag);
		}

		// process sType
		if (!empty($sType))
		{
			$select -> where("$albumTableName.type = ?", $sType);
		}

		// process iCategoryId
		if (!empty($iCategoryId))
		{
			$select -> where("$albumTableName.category_id = ?", $iCategoryId);
		}

		$iLimit = (!empty($iLimit)) ? $iLimit : self::GETPHOTO_PHOTO_LIMIT;

		if ($type == self::GETPHOTO_TYPE_SLIDE)
		{
			if (in_array($sAction, array(
				self::ACTION_TYPE_NEXT,
				self::ACTION_TYPE_PREVIOUS
			)))
			{
				if ($sAction == self::ACTION_TYPE_NEXT)
				{
					$select -> where("$photoTableName.photo_id > $iCurrentPhotoId");
					$select -> order("$photoTableName.photo_id ASC");
				}
				else
				{
					$select -> order("$photoTableName.photo_id DESC");
					$select -> where("$photoTableName.photo_id <= $iCurrentPhotoId");
				}
			}

			if (isset($iAlbumId))
			{
				$select -> where("$photoTableName.album_id = ?", $iAlbumId);
			}
		}
		else
		{
			//For searching api
			if (!empty($sOrder))
			{
				if ($sOrder == 'recent')
				{
					$select -> order("$photoTableName.modified_date DESC");
				}
				else
				if ($sOrder == 'popular')
				{
					$select -> order("$photoTableName.view_count DESC");
				}
			}
			else
			{
				$select -> order("$photoTableName.photo_id DESC");
			}
		}

		if (isset($iUserId)){
			$select->where("$photoTableName.owner_type = 'user' and $photoTableName.owner_id = ?", $iUserId);			
		}
		
        $fields = array('id','type','title','albumId','desc','imgNormal','user','stats','canEdit','canDelete');
        
        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields);
		
	}

    /**
	 * Get user photos
	 * Input Data:
	 * + iLimit: int, optional.
	 * + iLastPhotoIdViewed: int, optional.
	 * + iUserId: int, optional.
	 * + sType: string, optional ('wall','profile','message','blog').
	 * + iCategoryId: int, optional.
	 * + sTag: string, optional.
	 * + sAction: string, optional ('new', 'more')
	 *
	 * Output Data:
	 * SUCCESS:
	 * + iPhotoId: int.
	 * + sTitle: string.
	 * + sPhotoUrl: string.
	 * FAIL:
	 * + error_code: int
	 * + error_message: string
	 * + result: int
	 */
	public function my($aData)
	{
		extract($aData);

		$translate = Zend_Registry::get('Zend_Translate');

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewerId = $viewer -> getIdentity();

		// no logged in users
		if (!$viewerId)
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("No logged in users!")
			);
		}

		$aData['iViewerId'] = $viewerId;
		//return $this->getPhotos($aData, self::GETPHOTO_TYPE_MY);

		// photo table
		$albumTable = $this->getWorkingTable('albums','album');
        $photoTable = $this->getWorkingTable('photos','album');
        
		$photoTableName = $photoTable -> info('name');
		$albumTableName = $albumTable -> info('name');

		$select = $photoTable -> select() -> from($photoTableName);
		$select -> joinLeft($albumTableName, $albumTableName . '.album_id=' . $photoTableName . '.album_id', null);
		$select -> where("$albumTableName.owner_id = ?", $aData['iViewerId']);

		// process iLimit and sAction
		if (!empty($iLimit))
		{
			$iLimit = (int)$iLimit;
			if ($iLimit <= 0)
			{
				$iLimit = self::GETPHOTO_PHOTO_LIMIT;
			}
		}
		else
		{
			$iLimit = self::GETPHOTO_PHOTO_LIMIT;
		}
		$select -> limit($iLimit);

		// process iLastPhotoIdViewed and sAction
		if (!isset($iLastPhotoIdViewed) || $iLastPhotoIdViewed < 0)
		{
			$iLastPhotoIdViewed = 0;
		}

		if (isset($sAction))
		{
			if ($sAction == self::ACTION_TYPE_NEW || $iLastPhotoIdViewed == 0)
			{
				$select -> where("$photoTableName.photo_id > ?", $iLastPhotoIdViewed);
			}
			else
			{
				$select -> where("$photoTableName.photo_id < ?", $iLastPhotoIdViewed);
			}
		}

		$select -> order("$photoTableName.photo_id DESC");

		$photos = $photoTable -> fetchAll($select);
		// array to contain results
		$results = array();

		if (count($photos))
		{
			foreach ($photos as $photo)
			{
				// finalize photo url
				$photoUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($photo -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE));
				$thumbUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($photo -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL));

				if (isset($iInDetails) && $iInDetails == '1')
				{
					$album = $albumTable->findRow($photo->album_id);

					$iTotalComment = $photo -> comments() -> getCommentPaginator() -> getTotalItemCount();
					$iTotalLike = $photo -> likes() -> getLikePaginator() -> getTotalItemCount();
					$file = Engine_Api::_() -> storage() -> get($photo -> file_id);

					$results[] = array(
						'iPhotoId' => $photo -> getIdentity(),
						'sTitle' => $photo -> getTitle(),
						'sPhotoUrl' => $photoUrl,
						'bCanPostComment' => true,
						'iAlbumId' => $photo -> album_id,
						'sAlbumName' => $album -> getTitle(),
						'bIsLiked' => $photo -> likes() -> isLike($viewer),
						'iTotalComment' => $iTotalComment,
						'iTotalLike' => $iTotalLike,
						'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($photo),
						'iTotalView' => 0,
						'iAllowDownload' => 1,
						'sFileName' => $file -> name,
						'sFileSize' => $file -> size,
						'sMimeType' => $file -> mime_minor,
						'sExtension' => $file -> extension,
						'sOriginalDestination' => $file -> storage_path,
						'sDescription' => $photo -> description,
						'sAlbumUrl' => $album -> getHref(),
						'sAlbumTitle' => $album -> getTitle(),
						'iUserId' => $photo -> getOwner() -> getIdentity(),
						'sItemType' => 'photo',
						'sModelType'=>'album_photo'
					);
				}
				else
				{
					$results[] = array(
						'iPhotoId' => $photo -> getIdentity(),
						'sTitle' => $photo -> getTitle(),
						'sPhotoUrl' => $thumbUrl,
					);
				}

			}
		}
		return $results;
	}

	/**
	 * Get friend photos
	 * + If iUserId is available, get photos of user whose iUserId belongs to
	 * + Else get friends' photos of viewer
	 * Input Data:
	 * + iUserId: int
	 * + iLimit: int, optional.
	 * + iLastPhotoIdViewed: int, optional.
	 * + iUserId: int, optional.
	 * + sType: string, optional ('wall','profile','message','blog')
	 * + iCategoryId: int, optional.
	 * + sTag: string, optional.
	 * + sAction: string, optional ('new', 'more')
	 * Output Data:
	 * SUCCESS:
	 * + iPhotoId: int.
	 * + sTitle: string.
	 * + sPhotoUrl: string.
	 * FAIL:
	 * + error_code: int
	 * + error_message: string
	 * + result: int
	 */
	public function friend($aData)
	{
		extract($aData, EXTR_SKIP);

		$translate = Zend_Registry::get('Zend_Translate');

		// check user id
		if (empty($iUserId))
		{
			$iUserId = 0;
		}
		if (!is_numeric($iUserId))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("User id is invalid!")
			);
		}

		$aData['iUserId'] = $iUserId;

		// get friends id
		if (!$iUserId)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$friendsId = $viewer -> membership() -> getMembershipsOfIds();
			if (count($friendsId))
			{
				$aData['aFriendsId'] = $friendsId;
			}
			else
			{
				return array();
			}
		}

		return $this -> getPhotos($aData, self::GETPHOTO_TYPE_FRIEND);
	}

	/**
	 * Input data:
	 * + iLastAlbumIdViewed: int, optional.
	 * + sAction: string, optional ( "more", "new").
	 * + iLimit: int, optional.
	 * + sType: string, optional ('wall','profile','message','blog')
	 * + sAction: string, optional ('new', 'more')
	 * Output Data:
	 * SUCCESS:
	 * + iPhotoId: int.
	 * + sTitle: string.
	 * + sPhotoUrl: string.
	 * FAIL:
	 * + error_code: int
	 * + error_message: string
	 * + result: int
	 */
	public function listalbumphoto($aData)
	{
		extract($aData, EXTR_SKIP);

        $translate = Zend_Registry::get('Zend_Translate');

        // album table
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable = $this->getWorkingTable('photos','album');

		if (empty($iAlbumId) || !is_numeric($iAlbumId))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Album Id is missing or invalid!")
			);
		}

		
		if (!empty($iLimit) && !is_numeric($iLimit))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Invalid iLimit ")
			);
		}
		
		$album = $albumTable->findRow($iAlbumId);
		
		if ($album->getIdentity())
			return $this -> getPhotos($aData, self::GETPHOTO_TYPE_ALBUM);
		else
			return array(
					'error_code' => 1,
					'error_message' => $translate -> _("This album is not existed."),
					'result' => 0
			);
	}

	/**
	 * Input data:
	 * + iViewerId: int,
	 * + iPage: int, optional.
	 * + iLimit: int, optional.
	 * + sType: string, optional ('wall','profile','message','blog')
	 * + sAction: string, optional ('new', 'more')
	 */
	private function getAlbums($aData, $type)
	{
		extract($aData, EXTR_SKIP);
        
        $iPage = @$iPage?intval($iPage):1;
        $iLimit  = @$iLimit?intval($iLimit):12;
        
        if(empty($fields)){
            $fields = 'listing';
        }

        $fields = explode(',', $fields);
        
		$translate = Zend_Registry::get('Zend_Translate');

		// album table
		$albumTable = $this->getWorkingTable('albums','album');
        $photoTable = $this->getWorkingTable('photos','album');
        
		$albumTableName = $albumTable -> info('name');

		$select = $albumTable -> select() 
		  -> where("$albumTableName.owner_type = ?", 'user');
        
		if ($type != self::GETALBUM_TYPE_MY)
		{
			$select = $select -> where("$albumTableName.photo_id <> 0");
		}

		// process sType
		if (!empty($sType))
		{
			$select -> where("$albumTableName.type = ?", $sType);
		}

		// process iCategoryId
		if (!empty($iCategoryId) && $iCategoryId)
		{
			$select -> where("$albumTableName.category_id = ?", $iCategoryId);
		}

		if (!empty($sSearch))
		{
			$select -> where("$albumTableName.title LIKE ? OR $albumTableName.description LIKE ?", '%' . $sSearch . '%');
		}

		$iViewerId = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		if ($type == self::GETALBUM_TYPE_MY)
		{
			$select -> where("$albumTableName.owner_id = ?", $iViewerId);
		}
		else
		if (isset($iUserId))
		{
			$select -> where("$albumTableName.owner_id = ?", $iUserId);
		}

		// check friendsId, use for 'profile' album function
		if ($type == self::GETALBUM_TYPE_FRIEND)
		{
			if (!$iUserId)
			{
				$select -> where("$albumTableName.owner_id IN (?)", $aFriendsId);
			}
			else
			{
				$select -> where("$albumTableName.owner_id = ?", $iUserId);
			}
		}

		if ($type != self::GETALBUM_TYPE_MY)
		{
			$select -> where("$albumTableName.search = 1");
		}
		
		// order
		if (!empty($sOrder))
		{
			if ($sOrder == 'popular')
			{
				$select -> order("$albumTableName.view_count DESC");
			}
			else if ($sOrder == 'recent')
			{
				$select -> order("$albumTableName.modified_date DESC");
			}
		}
		else
		{
			$select -> order("$albumTableName.album_id DESC");
		}
        
		return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields);
	}

	/**
	 * Input data:
	 * + iLastAlbumIdViewed: int, optional.
	 * + sAction: string, optional ("more" or "new").
	 * + iLimit: int, optional.
	 * + sType: string, optional ('wall','profile','message','blog')
	 * + sAction: string, optional ('new', 'more')
	 *
	 * OUTPUT DATA:
	 * Success:
	 * + iAlbumId: int.
	 * + sAlbumImageURL: string.
	 * + sName: string.
	 * + iTotalPhoto: int.
	 * + iTimeStamp: int.
	 * + iTimeStampUpdate: int.
	 * + iTotalComment: int.
	 * + iTotalLike: int.
	 * + aUserLike array
	 * + iUserId: int.
	 *
	 * Failure:
	 *
	 */
	public function myalbum($aData)
	{
		$translate = Zend_Registry::get('Zend_Translate');

		$userId = Engine_Api::_() -> user() -> getViewer() -> getIdentity();

		// no logged in users
		if (!$userId)
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("No logged in users!")
			);
		}

		if(!@$aData['iUserId']){
			$aData['iViewerId'] = $userId;
		}else{
			$aData['iViewerId'] = @$aData['iUserId'];
		}
		
		return $this -> getAlbums($aData, self::GETALBUM_TYPE_MY);
	}

	protected function userAlbums($userId)
	{
		$albumTable = Engine_Api::_() -> getItemTable("album");
		$select = $albumTable -> select();
		$select = $select -> where("owner_id = ?", $userId);

		$albums = $albumTable -> fetchAll($select);
		$result = array();
		foreach ($albums as $album)
		{
			$result[] = $album -> getIdentity();
		}
		return $result;
	}

	/**
	 * Input data:
	 * + iLastAlbumIdViewed: int, optional.
	 * + sAction: string, optional ("more" or "new").
	 * + iLimit: int, optional.
	 * + sType: string, optional ('wall','profile','message','blog')
	 * + sAction: string, optional ('new', 'more')
	 * + iUserId: int, optional. Default 0 for all friends.
	 *
	 * OUTPUT DATA:
	 * Success:
	 * + iAlbumId: int.
	 * + sAlbumImageURL: string.
	 * + sName: string.
	 * + iTotalPhoto: int.
	 * + iTimeStamp: int.
	 * + iTimeStampUpdate: int.
	 * + iTotalComment: int.
	 * + iTotalLike: int.
	 * + aUserLike array
	 * + iUserId: int.
	 *
	 * Failure:
	 *
	 */
	public function profilealbum($aData)
	{
		extract($aData, EXTR_SKIP);

		$translate = Zend_Registry::get('Zend_Translate');

		// check user id
		if (empty($iUserId))
		{
			$iUserId = 0;
		}
		if (!is_numeric($iUserId))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("User id is invalid!")
			);
		}

		$aData['iUserId'] = $iUserId;
		// get friends id
		if (!$iUserId)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$friendsId = $viewer -> membership() -> getMembershipsOfIds();
			if (count($friendsId))
			{
				$aData['aFriendsId'] = $friendsId;
			}
			else
			{
				return array();
			}
		}

		return $this -> getAlbums($aData, self::GETALBUM_TYPE_FRIEND);
	}

	/**
	 * Create album with default photo_id = 0
	 * INPUT
	 * + sTitle: string, required, use "Untitled Album" by default
	 * + sDecription: string, optional.
	 * + sType: string, optional, in array ('wall','profile','message','blog'), default is null
	 * + iCategoryid: sstring, optional, use 0 by default
	 * + iSearch: string, optional, use 1 by default
	 * + sAuthView: string, optional, in array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered',
	 * 'everyone'),  'everyone' by default.
	 * + sAuthComment: string, optional, in array('owner', 'owner_member', 'owner_member_member', 'owner_network',
	 * 'registered', 'everyone'),  'everyone' by default.
	 * + sAuthTag: string, optional,  in array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered',
	 * 'everyone'),  'everyone' by default.
	 */
	public function albumcreate($aData)
	{
		extract($aData, EXTR_SKIP);

		$translate = Zend_Registry::get('Zend_Translate');

		if (!Engine_Api::_() -> authorization() -> isAllowed('album', null, 'create'))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("You don't have permission to create a new album!"),
				'result' => 0
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iOwnerId = $viewer -> getIdentity();

		// check sAuthView, sAuthComment, sAuthTag
		$roles = array(
			'owner',
			'owner_member',
			'owner_member_member',
			'owner_network',
			'registered',
			'everyone'
		);
		if (empty($sAuthView))
		{
			$sAuthView = 'everyone';
		}
		elseif (!in_array($sAuthView, $roles))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("sAuthView is invalid"),
				'result' => 0
			);
		}

		if (empty($sAuthComment))
		{
			$sAuthComment = 'everyone';
		}
		elseif (!in_array($sAuthComment, $roles))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("sAuthComment is invalid"),
				'result' => 0
			);
		}

		if (empty($sAuthTag))
		{
			$sAuthTag = 'everyone';
		}
		elseif (!in_array($sAuthTag, $roles))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("sAuthTag is invalid"),
				'result' => 0
			);
		}

		// PROCESS TO CREATE ALBUM
		$albumTable = $this->getWorkingTable('albums','album');
        $photoTable = $this->getWorkingTable('photos','album');
        
		$db = $albumTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$params = array();

			$params['owner_id'] = $iOwnerId;
			$params['owner_type'] = 'user';

			// process sTitle
			$params['title'] = empty($sTitle) ? $translate -> _('Untitled Album') : $sTitle;

			// process sDescription
			$params['description'] = empty($sDescription) ? '' : $sDescription;

			// process sType
			$params['type'] = empty($sType) ? NULL : $sType;

			// process iCategoryId
			$params['category_id'] = empty($iCategoryId) ? 0 : $iCategoryId;

			// process iSearch
			$params['search'] = empty($iSearch) ? 1 : $iSearch;

			// set default photo id as album cover
			$params['photo_id'] = 0;
			$album = $albumTable -> createRow();
			$album -> setFromArray($params);
			$album -> save();

			// -- authen stuff
			$auth = Engine_Api::_() -> authorization() -> context;

			$viewMax = array_search($sAuthView, $roles);
			$commentMax = array_search($sAuthComment, $roles);
			$tagMax = array_search($sAuthTag, $roles);

			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($album, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($album, $role, 'comment', ($i <= $commentMax));
				$auth -> setAllowed($album, $role, 'tag', ($i <= $tagMax));
			}

			// Add action and attachments
			//$api = Engine_Api::_() -> getDbtable('actions', 'activity');
			//$action = $api -> addActivity(Engine_Api::_() -> user() -> getViewer(), $album, 'album_photo_new', null, array('count'
			// => 0));

			// NOTE: activity feed if photo_id = 0
			// process upload - LATER

			$db -> commit();

			return array(
				'result' => 1,
				'error_code' => 0,
				'error_message' => "",
				'iAlbumId' => $album -> getIdentity()
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Oops, Fail!")
			);
		}

	}

	/**
	 * INPUT
	 * + iAlbumId: int, required.
	 *
	 * OUTPUT
	 * + iAlbumId: int.
	 * + bIsFriend: bool.
	 * + sTitle: int.
	 * + sDescription: string.
	 * + sAlbumImageUrl: string.
	 * + iUserId: int.
	 * + sUserFullName: string.
	 * + sUserImageUrl: string.
	 * + iCategoryId: int.
	 * + iCreationDate: int.
	 * + iModifiedDate: int.
	 * + iSearch: int
	 * + sType: int
	 * + iTotalView: int.
	 * + iTotalLike: int.
	 * + aUserLike array
	 * + iTotalPhoto: int.
	 * + bCanComment: int.
	 * + bCanView: int
	 * + bCanTag: int
	 */
	public function albumview($aData)
	{
		extract($aData, EXTR_SKIP);

        if(empty($fields)){
            $fields = 'id,title,stats,desc,type,category,auth,user,imgNormal';
        }

        $fields = explode(',', $fields);
        
        $translate = Zend_Registry::get('Zend_Translate');
        
        // album table
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable = $this->getWorkingTable('photos','album');
        
    
		// check album id
		if (!isset($iAlbumId)){
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Album Id is missing!")
			);
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$album = $albumTable->findRow($iAlbumId);
        
		// check album
		if (!$album){
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Album is not available!")
			);
		}
        
		return Ynmobile_AppMeta::getInstance()->getModelHelper($album)->toArray($fields);
	}

	/**
	 * INPUT
	 * + iAlbumId: int, required.
	 * + sTitle: int, optional.
	 * + sDescription: string, optional.
	 * + iPhotoID: int, optional, album cover
	 * + iCategoryId: int.
	 * + bSearch: int
	 * + sAuthView: string, optional, in array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered',
	 * 'everyone'),  'everyone' by default.
	 * + sAuthComment: string, optional, in array('owner', 'owner_member', 'owner_member_member', 'owner_network',
	 * 'registered', 'everyone'),  'everyone' by default.
	 * + sAuthTag: string, optional,  in array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered',
	 * 'everyone'),  'everyone' by default.
	 */
	public function albumedit($aData)
	{
		extract($aData, EXTR_SKIP);

		$translate = Zend_Registry::get('Zend_Translate');
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');

		// check album id
		if (!isset($iAlbumId))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Album Id is missing!")
			);
		}
		// check category id
		if (isset($iCategoryId) && !is_numeric($iCategoryId))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => "Invalid Category Id"
			);
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
        $album  = $albumTable->findRow($iAlbumId);
		
		// check album
		if (!$album){
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Album is not available!")
			);
		}

		// check edit permission
		if (!$album -> authorization() -> isAllowed($viewer, 'edit')){
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("You don't have permission to edit this album!"),
				'result' => 0
			);
		}

		$roles = array(
			'owner',
			'owner_member',
			'owner_member_member',
			'owner_network',
			'registered',
			'everyone'
		);
		if (empty($sAuthView))
		{
			$sAuthView = 'everyone';
		}
		elseif (!in_array($sAuthView, $roles))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("sAuthView is invalid"),
				'result' => 0
			);
		}

		if (empty($sAuthComment))
		{
			$sAuthComment = 'everyone';
		}
		elseif (!in_array($sAuthComment, $roles))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("sAuthComment is invalid"),
				'result' => 0
			);
		}

		if (empty($sAuthTag))
		{
			$sAuthTag = 'everyone';
		}
		elseif (!in_array($sAuthTag, $roles))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("sAuthTag is invalid"),
				'result' => 0
			);
		}

		// Process
		$db = $album -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// contain edit album values
			$params = array();
			// process title
			if (!empty($sTitle))
			{
				$params['title'] = $sTitle;
			}

			// process description
			if (isset($sDescription))
			{
				$params['description'] = $sDescription;
			}

			// process photo id
			if (!empty($iPhotoId))
			{
				$params['photo_id'] = $iPhotoId;
			}

			// process category id, even if it equals 0
			if (isset($iCategoryId))
			{
				$params['category_id'] = $iCategoryId;
			}

			// process search
			if (isset($bSearch))
			{
				$params['search'] = $bSearch;
			}

			$album -> setFromArray($params);
			$album -> save();

			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;

			$viewMax = array_search($sAuthView, $roles);
			$commentMax = array_search($sAuthComment, $roles);
			$tagMax = array_search($sAuthTag, $roles);

			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($album, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($album, $role, 'comment', ($i <= $commentMax));
				$auth -> setAllowed($album, $role, 'tag', ($i <= $tagMax));
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($album) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}

			$db -> commit();

			return array(
				'result' => 1,
				'error_code' => 0,
				'error_message' => "",
				'iAlbumId' => $iAlbumId
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Oops, Fail!")
			);
		}
	}

	/**
	 * INPUT
	 * + $iAlbumId: int, required.
	 */
	public function albumdelete($aData)
	{
		extract($aData, EXTR_SKIP);

		$translate = Zend_Registry::get('Zend_Translate');
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');

		// check album id
		if (!isset($iAlbumId))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Album Id is missing!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$album = $albumTable->findRow($iAlbumId);
		// check album
		if (!$album){
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Album is not available!")
			);
		}

		// check view permission
		if (!$album -> authorization() -> isAllowed($viewer, 'delete'))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("You don't have permission to delete this album!"),
				'result' => 0
			);
		}

		$db = $album -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$album -> delete();
			$db -> commit();

			return array(
				'result' => 1,
				'error_code' => 0,
				'error_message' => "",
				'iAlbumId' => $iAlbumId
			);

		}

		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Oops, Fail!")
			);
		}
	}

	/**
	 * Input data:
	 * + iAlbumId: int, optional.
	 * + sTitle: string, optional.
	 * + sDescription: string, optional.
	 * + $_FILE['Filedata']: photo file, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + message: string.
	 * + iPhotoId: int.
	 * + sPhotoTitle: string.
	 * + iAlbumId: int.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see photo/upload
	 *
	 * @param array $aData
	 * @return array
	 */
	public function upload($aData)
	{
		if (($aData['sParentType'] == "group" || $aData['sParentType'] == 'advgroup')  && isset($aData['iParentId']))
		{
			$aData['iGroupId'] = $aData['iParentId'];
			return Engine_Api::_()->getApi('group','ynmobile')->upload_photo($aData);
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$translate = Zend_Registry::get('Zend_Translate');
		
        $checkType = 'album';
        if($this->getWorkingModule('album') == 'advalbum'){
            $checkType = 'advalbum_album';
        }
        
        
		if (!Engine_Api::_() -> authorization() -> isAllowed($checkType, null, 'create'))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("You don't have permission to upload photo!"),
				'result' => 0
			);
		}

		//Will open when $_FILE is posted actually
		if (empty($aData['image']) && !isset($_FILES['image']))
		{
			return array(
				'error_code' => 2,
				'error_message' => $translate -> _("No file!"),
				'result' => 0
			);
		}

        $photoTable = $this -> getWorkingTable('photos', 'album');
		$db = $photoTable -> getAdapter();
		$db -> beginTransaction();
		

		if (!isset($aData['iAlbumId']) || $aData['iAlbumId'] == 0)
		{
			if (!isset($aData['sAlbumType']))
				$aData['sAlbumType'] == 'wall';
			
			$albumOwner = $viewer;
			if (isset($aData['iSubjectId']))
			{
				$albumOwner = Engine_Api::_()->user()->getUser($aData['iSubjectId']);
				if (!$albumOwner->getIdentity())
					$albumOwner = $viewer;
			}	
			
			$albumTable = $this->getWorkingTable('albums', 'album');
			
			if (in_array($aData['sAlbumType'], array(
				'wall',
				'profile',
				'message'
			)))
			{
				$album = $albumTable -> getSpecialAlbum($albumOwner, $aData['sAlbumType']);
			}
			else
			{
				$album = $albumTable -> getSpecialAlbum($albumOwner, 'wall');
			}
			$aData['iAlbumId'] = $album -> getIdentity();
		}

		try
		{
			$photo = $photoTable -> createRow();
			$photo -> setFromArray(array(
				'owner_type' => 'user',
				'owner_id' => $viewer -> getIdentity()
			));
			$photo -> save();

			$photo -> order = $photo -> photo_id;
			$photo -> title = isset($aData['sTitle']) ? $aData['sTitle'] : '';
			$photo -> album_id = isset($aData['iAlbumId']) ? $aData['iAlbumId'] : 0;
			$photo -> description = isset($aData['sDescription']) ? $aData['sDescription'] : '';

			$photo = Engine_Api::_() -> ynmobile() -> setPhoto($photo, $_FILES['image']);

			$photo -> save();
            
            if($album && $album->photo_id ==0){
                $album->photo_id =  $photo->getIdentity();
                $album->modified_date =  date('Y-m-d H:i:s');
                $album->save();
            }

			$db -> commit();

		}
		catch( Album_Model_Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e -> getMessage(),
				'result' => 0
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e -> getMessage(),
				'result' => 0
			);
		}

		//$action = Engine_Api::_() -> getDbtable('actions', 'activity') ->addActivity($viewer, $album, 'album_photo_new', null,
		// array('count' => 1));
		//Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $photo);

		return array(
			'result' => 1,
			'message' => Zend_Registry::get('Zend_Translate') -> _("Photo successfully uploaded."),
			'iPhotoId' => $photo -> getIdentity(),
			'sPhotoTitle' => $photo -> getTitle(),
			'sType' => 'photo'
		);
	}

	public function setprofile($aData)
	{
		$translate = Zend_Registry::get('Zend_Translate');
		$user = Engine_Api::_() -> user() -> getViewer();

		$sItemType  = isset($aData['sItemType'])?$aData['sItemType']: 'photo';
		
		// Get photo
		$photo = Engine_Api::_() -> getItem($sItemType, $aData['iPhotoId']);

		if (!$photo || !($photo instanceof Core_Model_Item_Abstract) || empty($photo -> photo_id))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("An error occurred!"),
				'photo_id'=> $photo->getIdentity(),
				'result' => 0
			);
		}

		if (!$photo -> authorization() -> isAllowed(null, 'view'))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("You don't have permission to set this photo to profile photo!"),
				'result' => 0
			);
		}

		// Process
		$db = $user -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Get the owner of the photo
			$photoOwnerId = null;
			if (isset($photo -> user_id))
			{
				$photoOwnerId = $photo -> user_id;
			}
			else
			if (isset($photo -> owner_id) && (!isset($photo -> owner_type) || $photo -> owner_type == 'user'))
			{
				$photoOwnerId = $photo -> owner_id;
			}

			// if it is from your own profile album do not make copies of the image
			if ($photo instanceof Album_Model_Photo && ($photoParent = $photo -> getParent()) instanceof Album_Model_Album && $photoParent -> owner_id == $photoOwnerId && $photoParent -> type == 'profile')
			{

				// ensure thumb.icon and thumb.profile exist
				$newStorageFile = Engine_Api::_() -> getItem('storage_file', $photo -> file_id);
				$filesTable = Engine_Api::_() -> getDbtable('files', 'storage');
				if ($photo -> file_id == $filesTable -> lookupFile($photo -> file_id, 'thumb.profile'))
				{
					try
					{
						$tmpFile = $newStorageFile -> temporary();
						$image = Engine_Image::factory();
						$image -> open($tmpFile) -> resize(200, 400) -> write($tmpFile) -> destroy();
						$iProfile = $filesTable -> createFile($tmpFile, array(
							'parent_type' => $user -> getType(),
							'parent_id' => $user -> getIdentity(),
							'user_id' => $user -> getIdentity(),
							'name' => basename($tmpFile),
						));
						$newStorageFile -> bridge($iProfile, 'thumb.profile');
						@unlink($tmpFile);
					}
					catch( Exception $e )
					{
						return array(
							'error_code' => 1,
							'error_message' => $e,
							'result' => 0
						);
					}
				}
				if ($photo -> file_id == $filesTable -> lookupFile($photo -> file_id, 'thumb.icon'))
				{
					try
					{
						$tmpFile = $newStorageFile -> temporary();
						$image = Engine_Image::factory();
						$image -> open($tmpFile);
						$size = min($image -> height, $image -> width);
						$x = ($image -> width - $size) / 2;
						$y = ($image -> height - $size) / 2;
						$image -> resample($x, $y, $size, $size, 48, 48) -> write($tmpFile) -> destroy();
						$iSquare = $filesTable -> createFile($tmpFile, array(
							'parent_type' => $user -> getType(),
							'parent_id' => $user -> getIdentity(),
							'user_id' => $user -> getIdentity(),
							'name' => basename($tmpFile),
						));
						$newStorageFile -> bridge($iSquare, 'thumb.icon');
						@unlink($tmpFile);
					}
					catch( Exception $e )
					{
						return array(
								'error_code' => 1,
								'error_message' => $translate->_("An error occurred!"),
								'result' => 0
						);
					}
				}

				// Set it
				$user -> photo_id = $photo -> file_id;
				$user -> save();

				// Insert activity
				// @todo maybe it should read "changed their profile photo" ?
				$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $user, 'profile_photo_update', '{item:$subject} changed their profile photo.');
				if ($action)
				{
					// We have to attach the user himself w/o album plugin
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $photo);
				}
			}

			// Otherwise copy to the profile album
			else
			{
				$user -> setPhoto($photo);

				// Insert activity
				$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $user, 'profile_photo_update', '{item:$subject} added a new profile photo.');

				// Hooks to enable albums to work
				$newStorageFile = Engine_Api::_() -> getItem('storage_file', $user -> photo_id);
				$event = Engine_Hooks_Dispatcher::_() -> callEvent('onUserProfilePhotoUpload', array(
					'user' => $user,
					'file' => $newStorageFile,
				));

				$attachment = $event -> getResponse();
				if (!$attachment)
				{
					$attachment = $newStorageFile;
				}

				if ($action)
				{
					// We have to attach the user himself w/o album plugin
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $attachment);
				}
			}

			$db -> commit();
		}

		// Otherwise it's probably a problem with the database or the storage system (just throw it)
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return array(
			'result' => 1,
			'message' => Zend_Registry::get('Zend_Translate') -> _("Set as profile photo successfully"),
			'sProfileImage' => Engine_Api::_() -> ynmobile() -> finalizeUrl($user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON)),
			'iPhotoId' => $photo -> getIdentity()
		);
	}

	/**
	 * Input Data:
	 * + iCurrentPhotoId: int, optional.
	 * + sAction: string, 'next' or 'previous'
	 *
	 * Output Data:
	 * SUCCESS:
	 * return one photo with
	 * + iPhotoId: int.
	 * + sTitle: string.
	 * + sPhotoUrl: string.
	 *
	 * FAIL:
	 * + error_code: int
	 * + error_message: string
	 * + result: int
	 */
	public function photoslide($aData, $limit = null)
	{
	    extract($aData);
        
		$translate = Zend_Registry::get('Zend_Translate');
        
        

        // album table
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable = $this->getWorkingTable('photos','album');
        
        $iCurrentPhotoId = intval($iCurrentPhotoId);
        
		$photo = $photoTable->findRow($iCurrentPhotoId);

		if (!$photo){
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("This photo is not existed."),
				'result' => 0
			);
		}
		return $this -> getPhotos($aData, self::GETPHOTO_TYPE_SLIDE);
	}
    
    
        
    

    public function getAllAlbumCanView($albumTable, $owner =  null)
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        
        $select = $albumTable->select();
        
        if($owner){
            
            if($owner->getType() == 'user'){
                $select->where('owner_id=?',$owner->getIdentity());
                $select->where('owner_type=?', $owner->getType());    
            }else{
                $owner_column = array_pop(array_values($owner->getTable()->info('primary')));
                $select->where("$owner_column=?", $owner->getIdentity());
            }
        }
        
        
        $albumIds = array();

        foreach ($albumTable -> fetchAll($select) as $album)
        {
            $bCanView = Engine_Api::_() 
                 -> authorization() 
                 -> isAllowed($album, $viewer, 'view');
                 
            if ($bCanView){
                $albumIds[] = $album -> getIdentity();
            }
        }
        return $albumIds;
    }
    
    function select_message_photo($iItemId){
        $photoTable =  $this->getWorkingTable('photos','album');
        return $photoTable->select()->where('photo_id=?', $iItemId);
        
    }
    
    function select_all_photo($aData){
        
        extract($aData);
        
        if(empty($fields)){
            $fields  = 'id,type,desc,imgNormal,imgFull,user,stats';
        }
        
        $fields = explode(',', $fields);
        $iLimit  = $iLimit?intval($iLimit):10;
        $iPage   = $iPage?intval($iPage):1;
        $sOrder  = $sOrder ? strtolower($sOrder):'recent';
        
        $albumTable =  $this->getWorkingTable('albums','album');
        $photoTable =  $this->getWorkingTable('photos','album');
        
        $select =  $photoTable->select();
        
        $albumIds = $this -> getAllAlbumCanView($albumTable);
        
        if(empty($albumIds)){
            return array();
        }
        
        $select->where('album_id IN (?)', $albumIds)
                ;
                
        return $select;
        
    }
    
    /**
     * for user, group, event, classifieds etc.
     */
    function select_parent_photo($parent){
        
        $select = null;
        if($parent->getType() == 'user'){
            
            $albumTable =  $this->getWorkingTable('albums','album');
            $photoTable =  $this->getWorkingTable('photos','album');
            
            $select =  $photoTable->select();
            
            $albumIds = $this -> getAllAlbumCanView($albumTable, $parent);
            
            if(empty($albumIds)){
                $select->where('photo_id=0'); // empty result
            }else{
                $select->where('owner_type=?', $parent->getType())
                        ->where('owner_id=?', $parent->getIdentity())
                        ->where('album_id IN (?)', $albumIds)
                        ;    
            }
        }else{
            
            $specs =  explode('_', $parent->getType());
            
            $photoTable  =  $this->getWorkingTable('photos', $specs[0]);
            $albumTable  = $this->getWorkingTable('albums', $specs[0]);
            
            $albumIds = $this -> getAllAlbumCanView($albumTable, $parent);
            
            $select =  $photoTable->select();
            
            // return album_id
            $parentCol = array_pop(array_values($parent->getTable()->info('primary')));
            $select -> where("$parentCol=?", $parent->getIdentity());    
        }
        
        return $select;
    }

    function fetch_photo($aData){
        
        extract($aData);
        
        if(!isset($sParentType) || empty($sParentType)){
            $select=  $this->select_all_photo($aData);
        }else if($sParentType == 'message'){
            $select =  $this->select_message_photo($iItemId);
        }else{
            $parent  =  $this->getWorkingItem($sParentType, $iParentId);
            
            if(!$parent){
                return array(
                    'error_code'=>1,
                    'error_message'=>'Parent not found',
                );
            }
            $select  = $this->select_parent_photo($parent);
        }
        
        if(empty($fields)){
            $fields  = 'id,type,desc,imgNormal,imgFull,user,stats';
        }
        
        $fields = explode(',', $fields);
        $iLimit  = $iLimit?intval($iLimit):10;
        $iPage   = $iPage?intval($iPage):1;
        
        if($sOrder == 'popular'){
            $select->order('view_count desc');
        }else{
            $select->order('photo_id desc');    
        }
        
        
        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields);
    }

    function slider($aData){
        
        extract($aData);
        
        if(empty($fields)){
            $fields  = 'id,type,desc,imgNormal,imgFull,user,stats';
        }
        
        $fields = explode(',', $fields);
        $sAction =  $sAction?$sAction: 'more';
        
        $photo = $this->getWorkingItem($sItemType, $iCurrentPhotoId);
        
        $iLimit =  $iLimit?intval($iLimit):5;
        // i page will caculate later.
        
        if(!$photo){
            return array(
                'error_code'=>1,
                'error_message'=>'Photo not found',
            );    
        }
        
        if(empty($iParentId)){
            // select specifict one photo by item type and item id.s
            $select = $this->getWorkingItemTable($sItemType)
                  ->select()
                  ->where('photo_id=?', intval($iCurrentPhotoId));
        }else if($iParentId == -1)
        {
            $select=  $this->select_all_photo($aData);
        }else if($sParentType == 'messages_message'){
            $select =  $this->select_message_photo($iCurrentPhotoId);
        }else
        {
            $parent  =  $this->getWorkingItem($sParentType, $iParentId);
            
            if(!$parent){
                return array(
                    'error_code'=>1,
                    'error_message'=>'Parent not found',
                );
            }
            $select  = $this->select_parent_photo($parent);
        }
        
        if($sAction == 'next'){
            $select->where('photo_id <=?', $photo->getIdentity());
            $select->order('photo_id desc');
            $iPage  =  $iNextPage;
        }else{
            $select->where('photo_id >?', $photo->getIdentity());
            $select->order('photo_id asc');
            $iPage  = $iPrevPage;
        }

    
        
        return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields);
    }

	/**
	 * Input Data:
	 * + iCurrentPhotoId: int, optional.
	 * + iLimitPhoto: int, optional
	 *
	 * Output Data:
	 * SUCCESS:
	 * return list of photos with
	 * + iPhotoId: int.
	 * + sTitle: string.
	 * + sPhotoUrl: string.
	 *
	 * FAIL:
	 * + error_code: int
	 * + error_message: string
	 * + result: int
	 */
	public function fullphotoslide($aData)
	{
	    return $this -> photoslide($aData);
        
		$aPreviousData = $aData;

		$aPreviousData['sAction'] = 'previous';
		$aPreviousPhotos = $this -> photoslide($aPreviousData, $aData['iLimit']);
		$aPreviousPhotos = array_reverse($aPreviousPhotos);
		
		$aNextData = $aData;
		$aNextData['sAction'] = 'next';
		$aNextPhotos = $this -> photoslide($aNextData, $aData['iLimit']);
		
		if (count($aNextPhotos))
		{
			foreach ($aNextPhotos as $aPhoto)
			{
				$aPreviousPhotos[] = $aPhoto;
			}
		}

		return $aPreviousPhotos;
	}

	/**
	 * Input Data:
	 * + iCurrentPhotoId: int, optional.
	 * + sAction: string, 'next' or 'previous'
	 * + iAlbumId: int, required
	 *
	 * Output Data:
	 * SUCCESS:
	 * return one photo with
	 * + iPhotoId: int.
	 * + sTitle: string.
	 * + sPhotoUrl: string.
	 *
	 * FAIL:
	 * + error_code: int
	 * + error_message: string
	 * + result: int
	 */
	public function albumslide($aData)
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!isset($aData['iAlbumId']) || ($aData['iAlbumId'] == 0))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("iAlbumId is not valid!"),
				'result' => 0
			);
		}

		return $this -> photoslide($aData);
	}

	/**
	 * Input Data:
	 * + iCurrentPhotoId: int, optional.
	 * + iAlbumId: int, required
	 * + iLimitPhoto: int, optional
	 *
	 * Output Data:
	 * SUCCESS:
	 * return list of photos with
	 * + iPhotoId: int.
	 * + sTitle: string.
	 * + sPhotoUrl: string.
	 *
	 * FAIL:
	 * + error_code: int
	 * + error_message: string
	 * + result: int
	 */
	public function fullalbumslide($aData)
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!isset($aData['iAlbumId']) || ($aData['iAlbumId'] == 0))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("Mising album identity"),
				'result' => 0
			);
		}
		
		return $this -> slider($aData);
	}

	/**
	 * Edit photo information
	 * INPUT
	 * + iPhotoId: int, required.
	 * + iAlbumId: int, optional, album to move photo to
	 * + sTitle: string, optional
	 * + sDescription: string, optional
	 * + iOrder: int, optional
	 */
	public function edit($aData)
	{
	    
        extract($aData, EXTR_SKIP);

		$translate = Zend_Registry::get('Zend_Translate');
        
        $iItemId =  intval($iItemId);
        
        $photo =  $this->getWorkingItem($sItemType, $iItemId);
        
        
		// check photo id
		if (!$photo){
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("Photo not found!")
			);
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		// check edit permission
		if (!$photo -> authorization() -> isAllowed($viewer, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("You don't have permission to edit this photo!"),
			);
		}

		try
		{
			$photo -> title = (string) $sTitle;
            $photo -> description = (string)$sDescription;


			$photo -> save();

			return array(
				'error_message' => "",
				'iPhotoId' => $iPhotoId
			);

		}catch (Exception $e){
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("Oops, Fail!")
			);
		}

	}

	/**
	 * INPUT
	 * + iPhotoId: int, required.
	 */
	public function delete($aData)
	{
		extract($aData, EXTR_SKIP);

		$translate = Zend_Registry::get('Zend_Translate');
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');

		// check photo id
		if (!isset($iPhotoId))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Photo Id is missing!")
			);
		}

		$sItemType = isset($aData['sItemType']) ? $aData['sItemType'] : '';
        
        if(null == $sItemType){
            $sItemType =  $photoTable->fetchNew()->getType();
        }
		
        // delete any photo
		$photo = Engine_Api::_() -> getItem($sItemType, $iPhotoId);
		
		// check album
		if (!$photo)
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Photo is not available!")
			);
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		// check edit permission
		if (!$photo -> authorization() -> isAllowed($viewer, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("You don't have permission to delete this photo!"),
				'result' => 0
			);
		}

		$db = $albumTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();
			$db -> commit();
			return array(
				'result' => 1,
				'error_code' => 0,
				'error_message' => "",
				'iPhotoId' => $iPhotoId
			);

		}catch( Exception $e ){
			$db -> rollBack();
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Oops, Fail!")
			);
		}
	}
	
	/**
     * INPUT
     * + iPhotoId: int, required.
     */
    public function delete_photos($aData)
    {
        extract($aData, EXTR_SKIP);

        $translate = Zend_Registry::get('Zend_Translate');
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');

        // check photo id
        if (!isset($iPhotoIds))
        {
            return array(
                'result' => 0,
                'error_code' => 1,
                'error_message' => $translate -> _("Photo Id is missing!")
            );
        }

        $sItemType = isset($aData['sItemType']) ? $aData['sItemType'] : '';
        
        if(null == $sItemType){
            $sItemType = $photoTable->fetchNew()->getType();
        }
        
        $viewer = Engine_Api::_() -> user() -> getViewer();
        
        foreach($iPhotoIds as $iPhotoId){
            
            $photo = Engine_Api::_() -> getItem($sItemType, $iPhotoId);
        
            // check album
            if (!$photo)
            {
                continue;
            }
    
            // check edit permission
            if (!$photo -> authorization() -> isAllowed($viewer, 'edit'))
            {
                return array(
                    'error_code' => 1,
                    'error_message' => $translate -> _("You don't have permission to delete this photo!"),
                    'result' => 0
                );
            }
    
            $db = $albumTable -> getAdapter();
            
    
            try{
                $photo -> delete();
            
            }catch( Exception $e ){
            
            }
        }
        return array(
            'result' => 1,
            'error_code' => 0,
            'error_message' => "",
            'iPhotoId' => $iPhotoId
        );
    }

	/**
	 * INPUT
	 * + iPhotoId: int, required.
	 *
	 * OUTPUT
	 * + iPhotoId: int.
	 * + iAlbumId: int.
	 * + sTitle: int.
	 * + sDescription: string.
	 * + sPhotoImageUrl: string.
	 * + iCategoryId: int.
	 * + iNextPhotoId: int.
	 * + iPreviousPhotoId: int.
	 * + iCreationDate: int.
	 * + iModifiedDate: int.
	 * + bCover: boolean.
	 * + sType: int
	 * + iTotalView: int.
	 * + iTotalLike: int.
	 * + aUserLike array
	 * + bCanComment: int.
	 * + bCanView: int
	 * + bCanTag: int
	 * + sFileName: string.
	 * + sFileSize: string.
	 * + sFileExtension: string.
	 * + iUserId: int.
	 * + sUserFullName: string.
	 * + sUserImageUrl: string.
	 */

	public function view($aData)
	{
		extract($aData, EXTR_SKIP);

		$translate = Zend_Registry::get('Zend_Translate');
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');

		// check album id
		if (!isset($iPhotoId))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Photo Id is missing!")
			);
		}

		
        $photo =  $photoTable->findRow($iPhotoId);
		// check album
		if (!$photo){
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("Photo is not available!")
			);
		}

		// check view permission
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$photo -> authorization() -> isAllowed($viewer, 'view')){
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("You don't have permission to view this photo!"),
			);
		}

		$bCanComment = Engine_Api::_() -> authorization() -> isAllowed($photo, null, 'comment');
		$bCanView = Engine_Api::_() -> authorization() -> isAllowed($photo, null, 'view');
		$bCanTag = Engine_Api::_() -> authorization() -> isAllowed($photo, null, 'tag');

		$coreApi = Engine_Api::_() -> ynmobile();
		// check album
		$album = $photo -> getAlbum();
		if (!$album)
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("Album is not available!")
			);
		}

		$owner = $album -> getOwner();
		// finalize user url
		if ($owner -> getIdentity())
		{
			$sUserImageUrl = $coreApi -> finalizeUrl($owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON));
		}
		if (empty($sUserImageUrl))
		{
			$sUserImageUrl = NO_USER_ICON;
		}

		// finalize photo url
		$photoUrl = $coreApi -> finalizeUrl($photo -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL));
		if (empty($photoUrl))
		{
			$photoUrl = NO_PHOTO_THUMBNAIL;
		}
		else
		{
			// check file exist
			$file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($photo -> file_id, TYPE_OF_USER_IMAGE_NORMAL);
		}

		$results = array(
			'iPhotoId' => $photo -> getIdentity(),
			'iAlbumId' => $album -> getIdentity(),
			'sTitle' => $photo -> getTitle(),
			'sDescription' => $photo -> description,
			'sPhotoImageUrl' => $photoUrl,
			'iCategoryId' => $album -> category_id,
			'iNextPhotoId' => $photo -> getNextPhoto() -> getIdentity(),
			'iPreviousPhotoId' => $photo -> getPreviousPhoto() -> getIdentity(),
			'iCreationDate' => strtotime($photo -> creation_date),
			'iModifiedDate' => strtotime($photo -> modified_date),
			'sType' => ($album -> type) ? ($album -> type) : '',
			'iSearch' => $album -> search,
			'bCover' => ($album -> photo_id == $photo -> getIdentity()) ? 1 : 0,
			'iTotalView' => $photo -> view_count,
			'iTotalLike' => $photo -> likes() -> getLikeCount(),
			'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($photo),
			'bCanComment' => $bCanComment,
			'bCanView' => $bCanView,
			'bCanTag' => $bCanTag,
			'iUserId' => $album -> owner_id,
			'sUserFullName' => $owner -> getTitle(),
			'sUserImageUrl' => $sUserImageUrl,
		);
		if ($file)
		{
			$results['sFileName'] = $file -> name;
			$results['sFileSize'] = $file -> size;
			$results['sFileExtension'] = $file -> extension;
			$results['sMimeType'] = $file -> mime_major;
		}

		return $results;
	}

	/**
	 * INPUT
	 * + iPhotoId: int, required
	 *
	 */
	public function setcover($aData)
	{
		extract($aData, EXTR_SKIP);
        
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');
        
		// check album id
		if (!isset($iPhotoId))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Photo Id is missing!")
			);
		}
        $photo =  $photoTable->findRow($iPhotoId);
		// check album
		if (!$photo){
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Photo is not available!")
			);
		}

		// check view permission
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$photo -> authorization() -> isAllowed($viewer, 'view'))
		{
			return array(
			    'error_code' => 1,
			    'error_message' => $translate -> _("You don't have permission to set this photo as album cover!"),
			);
		}

		$coreApi = Engine_Api::_() -> ynmobile();
		// check album
		$album = $photo -> getAlbum();
        
		if (!$album){
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("Album is not available!")
			);
		}

		$db = $albumTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$album -> photo_id = $iPhotoId;
			$album -> save();
			$db -> commit();
			return array(
				'result' => 1,
				'error_code' => 0,
				'error_message' => "",
				'iPhotoId' => $iPhotoId
			);

		}catch( Exception $e ){
			$db -> rollBack();
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Oops, Fail!")
			);
		}
	}

	public function next($aData)
	{
	    extract($aData);
		
		$translate = Zend_Registry::get('Zend_Translate');
        
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');
        
        
		if (!isset($aData['iCurrentPhotoId']) || ($aData['iCurrentPhotoId'] == 0))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("iCurrentPhotoId is not valid!"),
				'result' => 0
			);
		}
        
        $photo =  $photoTable->findRow($iCurrentPhotoId);
        
		
		$nextPhoto = $photo -> getNextPhoto();
        
		return array(
			'iPhotoId' => $nextPhoto -> getIdentity(),
			'sTitle' => $nextPhoto -> getTitle(),
			'sPhotoUrl' => $nextPhoto -> getPhotoUrl()
		);
	}

	public function previous($aData)
	{
	    extract($aData);
        
		$translate = Zend_Registry::get('Zend_Translate');
        
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');
        
        
		if (!isset($aData['iCurrentPhotoId']) || ($aData['iCurrentPhotoId'] == 0))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("iCurrentPhotoId is not valid!"),
				'result' => 0
			);
		}

		$photo = $photoTable->findRow($iCurrentPhotoId);
		$previousPhoto = $photo -> getPreviousPhoto();
		
		return array(
			'iPhotoId' => $previousPhoto -> getIdentity(),
			'sTitle' => $previousPhoto -> getTitle(),
			'sPhotoUrl' => $previousPhoto -> getPhotoUrl()
		);
	}
   

	public function getAlbumsCanView($owner =  null)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
        
		$albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');
        
        $select = $albumTable->select();
        
        if($owner){
            $select->where('owner_id=?',$owner->getIdentity());
            $select->where('owner_type=?', $owner->getType());
        }
        
		$albumIds = array();

		foreach ($albumTable -> fetchAll($select) as $album)
		{
			$bCanView = Engine_Api::_() 
			     -> authorization() 
			     -> isAllowed($album, $viewer, 'view');
			if ($bCanView)
			{
				$albumIds[] = $album -> getIdentity();
			}
		}
		return $albumIds;
	}

	public function filter_album($aData)
	{
		//Getting Featured ALBUMs
		if (@$aData['sFilterBy'] == self::GETALBUM_TYPE_FEATURED)
		{
			$aData['iFeatured'] = 1;
			$aData['iSearch'] = 1;
		}

		//My ALBUMs
		if (@$aData['sFilterBy'] == self::GETALBUM_TYPE_MY)
			return $this -> getAlbums($aData, self::GETALBUM_TYPE_MY);
		//All ALBUMs
		else
			return $this -> getAlbums($aData);
	}

	/**
	 * INPUT
	 * + album: mix, required.
	 * + iNumberOfPhoto: int
	 *
	 * OUTPUT
	 * + iPhotoId: int.
	 * + iAlbumId: int.
	 * + sThumbUrl: string
	 * + sPhotoUrl: string
	 */
	public function getSamplePhotos($album, $iNumberOfPhoto)
	{
	    $translate = Zend_Registry::get('Zend_Translate');
        
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');
        
        
		if (!isset($iNumberOfPhoto) || $iNumberOfPhoto == 0){
			$iNumberOfPhoto = 3;
		}

		if (is_int($album)){
			$album = $albumTable->findRow($album);
		}

		$photos = array();
		if (is_object($album) && $album -> getIdentity() != 0)
		{
			$select = $photoTable -> select() -> where("album_id = ?", $album -> getIdentity()) -> order("photo_id") -> limit($iNumberOfPhoto);
			$photos = $photoTable -> fetchAll($select);
		}
		if (count($photos) == 0){
		    return array();
		}	

		$result = array();
		foreach ($photos as $photo)
		{
			$result[] = array(
				'iPhotoId' => $photo -> getIdentity(),
				'sThumbUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($photo -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL)),
				'sPhotoUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($photo -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE))
			);
		}
		return $result;
	}

	/**
	 * Input data:
	 * + sPhotoIds: string, required.
	 * + iAlbumId: int, required.
	 * 
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + iActionId: int.
	 */
	public function postfeed($aData)
	{
	    $translate = Zend_Registry::get('Zend_Translate');
        
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');
        
        
		if (!isset($aData['sPhotoIds'])){
			return array(
					'error_code' => 1,
					'error_element' => 'sPhotoIds',
					'error_message' => $translate -> _("Parameter is not valid!")
			);
		}
		
		$ids = explode(',', $aData['sPhotoIds']);
		$api = Engine_Api::_()->getDbtable('actions', 'activity');
        
		if (isset($aData['sParentType']) && ($aData['sParentType']  == "group" || $aData['sParentType']  == "advgroup" ))
		{
		    
			if (!isset($aData['iParentId'])){
				return array(
						'error_code' => 1,
						'error_element' => 'iAlbumId',
						'error_message' => $translate -> _("Missing Parent ID!")
				);
			}
            
            $subjectTable = $this->getWorkingTable('groups','group');
			$subject =  $subjectTable->findRow($aData['iParentId']);
            
            $subjectPhotoTable = $this->getWorkingTable('photos','group');
            
			if (!$subject){
				return array(
						'error_code' => 1,
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("Group is not valid!")
				);
			}
			$count = 0;
			
			$actiontype  = 'group_photo_upload';
			
			if($this->getWorkingModule('group') == 'advgroup'){
				$actiontype = 'advgroup_photo_upload';
			}
            
			$action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $subject, $actiontype, null, array('count' => count($ids)));
            
            if(!$action)
			{
				return array(
							'error_code' => 1,
							'error_element' => 'action',
							'error_message' => Zend_Registry::get('Zend_Translate') -> _("Action is null!")
					);
			}
			
			foreach( $ids as $photo_id )
			{
				$photo = $subjectPhotoTable->findRow($photo_id);
                
				if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() )
				{ 
					continue;
				}
                
				if( $action instanceof Activity_Model_Action && $count < 8 )
				{
					$api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
				}
				$count++;
			}
		}
		
		//DEFAULT
		else 
		{
			if (!isset($aData['iAlbumId']))
			{
				return array(
						'error_code' => 1,
						'error_element' => 'iAlbumId',
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
				);
			}
			
            $album  = $albumTable->findRow($aData['iAlbumId']);
            
			if (!$album){
				return array(
						'error_code' => 1,
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("Album is not valid!")
				);
			}
			$count = 0;
            $type = 'album_photo_new';
            if($album->getType() == 'advalbum_album'){
                $type = 'advalbum_photo_new';
            }
			$action = $api -> addActivity(Engine_Api::_() -> user() -> getViewer(), $album, $type, null, array('count' => count($ids)));
            
			foreach ($ids as $photo_id)
			{
				$photo = $photoTable->findRow($photo_id);
				if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
				{
					continue;
				}
				if( $action instanceof Activity_Model_Action && $count < 8)
				{
					$api -> attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
				}
				$count++;
			}
		}
		
		return array(
				'error_code' => 0,
				'error_message' => "",
				'iActionId' => $action -> action_id,
				'type'=>$type,
				'ids'=>$ids,
				'action'=> Ynmobile_AppMeta::_export_one($action, array('detail'))
		);
	}
	
	
	/**
	 * all data needed for event edit form.
	 */
	public function formedit($aData){
		
		return array_merge(	
				$this->view($aData), 
				$this->formadd(array()
			));
	}
	
	
	public function get_message_photo($aData)
	{
		extract($aData);
        
		if (!isset($iPhotoId)){
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPhotoId!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$result = array();
        
        $translate = Zend_Registry::get('Zend_Translate');
        
        $albumTable = $this->getWorkingTable('albums','album');
        $photoTable  = $this->getWorkingTable('photos','album');
        
        $photo =  $photoTable->findRow($iPhotoId);
		
		// finalize photo url
		$photoUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($photo -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE));
		$thumbUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($photo -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL));
		
		$album = $photo -> getAlbum();
	
		$iTotalComment = $photo -> comments() -> getCommentPaginator() -> getTotalItemCount();
		$iTotalLike = $photo -> likes() -> getLikePaginator() -> getTotalItemCount();
		$file = Engine_Api::_() -> storage() -> get($photo -> file_id);
	
		$owner = $photo -> getOwner();
		$sUserImageUrl = $owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		if ($sUserImageUrl != "")
		{
			$sUserImageUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImageUrl);
		}
		else
		{
			$sUserImageUrl = NO_USER_ICON;
		}
		$bCanComment = $bCanLike = (Engine_Api::_() -> authorization() -> isAllowed($photo, null, 'comment')) ? true : false;
		$result[] = array(
				'iPhotoId' => $photo -> getIdentity(),
				'sTitle' => $photo -> getTitle(),
				'sPhotoUrl' => $photoUrl,
				'bCanPostComment' => true,
				'iAlbumId' => $photo -> album_id,
				'sAlbumName' => $album -> getTitle(),
				'bIsLiked' => $photo -> likes() -> isLike($viewer),
				'iTotalComment' => $iTotalComment,
				'iTotalLike' => $iTotalLike,
				'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($photo),
				'iTotalView' => 0,
				'iAllowDownload' => 1,
				'sFileName' => $file -> name,
				'sFileSize' => $file -> size,
				'sMimeType' => $file -> mime_minor,
				'sExtension' => $file -> extension,
				'sOriginalDestination' => $file -> storage_path,
				'sDescription' => $photo -> description,
				'sAlbumUrl' => $album -> getHref(),
				'sAlbumTitle' => $album -> getTitle(),
				'iUserId' => $owner -> getIdentity(),
				'sUserImageUrl' => $sUserImageUrl,
				'sUserName' => $owner -> getTitle(),
				'sItemType' => 'photo',
				'sModelType'=>'album_photo',
				'iTimeStamp' => strtotime($photo -> creation_date),
				'sTimeConverted' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp( strtotime($photo -> creation_date)),
				'bCanView' => (Engine_Api::_() -> authorization() -> isAllowed($photo, null, 'view')) ? true : false,
				'bCanComment' => $bCanComment,
				'bCanLike' => $bCanLike,
				'bCanShare' => false,
		);
		return $result;
	}
	
}
