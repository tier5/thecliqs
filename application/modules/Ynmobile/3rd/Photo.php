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
class Ynmobile_Api_Photo extends Core_Api_Abstract
{
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
		extract ( $aData, EXTR_SKIP );
		
		if (!isset($iPage))
			$iPage = 1;

		if ($iPage == '0')
			return array();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$translate = Zend_Registry::get('Zend_Translate');
		
		if (isset($iAlbumId) && $iAlbumId)
		{
			$album = Engine_Api::_()->getItem('advalbum_album', $iAlbumId);
			
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
		
		$albumIds = $this->getAlbumsCanView();
		if (!count($albumIds))
		{
			return array ();
		}
		
		
		if (! empty ( $iLastPhotoIdViewed ) && ! is_numeric ( $iLastPhotoIdViewed ))
		{
			return array(
				'result' => 0,
				'error_code' => 4,
				'error_message' => $translate -> _("Invalid Last Viewed Photo Id")
			);
		}
		
		if (! empty ( $iCategoryId ) && ! is_numeric ( $iCategoryId ))
		{
			return array(
				'result' => 0,
				'error_code' => 5,
				'error_message' => $translate -> _("Invalid Category Id")
			);
		}
		
		if (! empty ( $iLimit ) && ! is_numeric ( $iLimit ))
		{
			return array (
					'result' => 0,
					'error_code' => 6,
					'error_message' => $translate-> _("Invalid Category Id") 
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
			return array (
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate-> _("Invalid Action Type")
			);			
		}
				
		// photo table
		$photoTable = Engine_Api::_ ()->getItemTable ( 'advalbum_photo' );
		$photoTableName = $photoTable->info ( 'name' );
		// album table
		$albumTable = Engine_Api::_ ()->getItemTable ( 'advalbum_album' );
		$albumTableName = $albumTable->info ( 'name' );
		// tag table
		$tagTable = Engine_Api::_ ()->getDbtable ( 'tags', 'core' );
		$tagTableName = $tagTable->info ( 'name' );
		// tag map table
		$tagMapTable = Engine_Api::_ ()->getDbtable ( 'TagMaps', 'core' );
		$tagMapTableName = $tagMapTable->info ( 'name' );
		
		$select = $photoTable->select ()->from ( $photoTableName )
		->where ( "$photoTableName.owner_type = ?", 'user' )
		->where ( "$photoTableName.album_id IN (?)", $albumIds );
		
		// process iFeatured
		if (!empty ( $iFeatured ))
		{
			$featuredTable = Engine_Api::_()->getDbtable('features', 'advalbum');
			$featuredTableName = $featuredTable->info('name');
			 
			$select
			->joinRight($featuredTableName, "$featuredTableName.photo_id = $photoTableName.photo_id",'')
			->where("photo_good  = ?","1");
		}
		
		
		
		// check viewerId, use for 'my' function
		$iViewerId = $viewer->getIdentity ();
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
				$select->where("$photoTableName.owner_id IN (?)", $aFriendsId);
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
			$select->where("$photoTableName.album_id = ?", $iAlbumId);
		}
		
		if (! empty ( $sType ) || (isset ( $iCategoryId ) && $iCategoryId >= 0))
		{
			$select->joinLeft ( $albumTableName, $albumTableName . '.album_id=' . $photoTableName . '.album_id', null );
		}
		
		if (! empty ( $sTag ))
		{
			$select->joinLeft ( $tagMapTableName, $photoTableName . '.photo_id=' . $tagMapTableName . '.resource_id' . 
						" AND $tagMapTableName.resource_type = 'album_photo'", null )
				->joinLeft ( $tagTableName, $tagTableName . '.tag_id=' . $tagMapTableName . '.tag_id', null )
				->where ( "$tagTableName.text = ?", $sTag );
		}
		
		// process sType
		if (! empty ( $sType ))
		{
			$select->where ( "$albumTableName.type = ?", $sType );
		}
		
		// process iCategoryId
		if (!empty ( $iCategoryId ))
		{
			$select->where ( "$albumTableName.category_id = ?", $iCategoryId );
		}
	
		$iLimit = (!empty($iLimit)) ? $iLimit : self::GETPHOTO_PHOTO_LIMIT;	
		if ($type == self::GETPHOTO_TYPE_SLIDE)
		{
			if (in_array($sAction, array(self::ACTION_TYPE_NEXT, self::ACTION_TYPE_PREVIOUS)))
			{
				if ($sAction == self::ACTION_TYPE_NEXT)
				{
					$select->where("$photoTableName.photo_id > $iCurrentPhotoId");
					$select->order ( "$photoTableName.photo_id ASC" );
				}
				else
				{
					$select->order ( "$photoTableName.photo_id DESC" );
					$select->where("$photoTableName.photo_id <= $iCurrentPhotoId");
				}
			}
			
			if (isset($iAlbumId))
			{
				$select -> where("$photoTableName.album_id = ?", $iAlbumId);
				//$select -> order("$photoTableName.photo_id ASC");
			}
			
		}
		else
		{
			//For searching api
			if (!empty ( $sOrder ))
			{
				if ($sOrder == 'recent')
				{
					$select->order("$photoTableName.creation_date DESC");
				}
				else if ($sOrder == 'popular')
				{
					$select -> order("$photoTableName.view_count DESC");
				}
				else if ($sOrder == 'top')
				{
					$select->order("$photoTableName.like_count DESC");
				}
				
			}
			else 
			{
				$select->order ( "$photoTableName.photo_id DESC" );
			}
			
		}
		
		if (isset($iUserId)){
			$select->where("$photoTableName.owner_type = 'user' and $photoTableName.owner_id = ?", $iUserId);			
		}
		$paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($iLimit);
		if (!empty($iPage))
		{
			$paginator -> setCurrentPageNumber($iPage, 1);
		}
		
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
			return array();
		
		$photos = $paginator;
		// array to contain results
		$results = array ();
		
		if (count ( $photos ))
		{
			foreach ( $photos as $photo )
			{
				// finalize photo url
				$photoUrl = Engine_Api::_ ()->ynmobile ()->finalizeUrl($photo->getPhotoUrl ( TYPE_OF_USER_IMAGE_PROFILE ));
				$thumbUrl = Engine_Api::_ ()->ynmobile ()->finalizeUrl($photo->getPhotoUrl ( TYPE_OF_USER_IMAGE_NORMAL ));
				
				if (isset($iInDetails) && $iInDetails == '1')
				{
					$album = Engine_Api::_()->getItem('advalbum_album', $photo->album_id);
					
					$iTotalComment = $photo->comments()->getCommentPaginator()->getTotalItemCount();
					$iTotalLike = $photo->likes()->getLikePaginator()->getTotalItemCount();
					$file = Engine_Api::_()->storage()->get($photo->file_id);
					
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
					
					$bCanLike = $bCanComment = (Engine_Api::_() -> authorization() -> isAllowed($photo, null, 'comment')) ? true : false;
					$results [] = array (
							'iPhotoId' => $photo->getIdentity(),
							'sTitle' => $photo->getTitle(),
							'sPhotoUrl' => $photoUrl,
							'bCanPostComment' => true,
							'iAlbumId' => $photo->album_id,
							'sAlbumName' => $album->getTitle(),
							'bIsLiked' => $photo -> likes() -> isLike($viewer),
							'iTotalComment' => $iTotalComment,
							'iTotalLike' => $iTotalLike,
							'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($photo),
							'iTotalView' => 0,
							'iAllowDownload' => 1,
							'sFileName' => $file->name,
							'sFileSize' => $file->size,
							'sMimeType' => $file->mime_minor,
							'sExtension'=> $file->extension,
							'sOriginalDestination' => $file->storage_path,
							'sDescription' => $photo->description,
							'sAlbumUrl' => $album->getHref(),
							'sAlbumTitle' => $album->getTitle(),
							'iUserId' => $owner -> getIdentity(),
							'sUserImageUrl' => $sUserImageUrl,
							'sUserName' => $owner -> getTitle(),
							'sItemType' => 'advalbum_photo',
							'iTimeStamp' => strtotime($photo -> creation_date),
							'bCanView' => (Engine_Api::_() -> authorization() -> isAllowed($photo, null, 'view')) ? true : false,
							'bCanComment' => $bCanComment,
							'bCanLike' => $bCanLike,
					);
				}	
				else 
				{
					$results [] = array (
							'iPhotoId' => $photo->getIdentity (),
							'sTitle' => $photo->getTitle (),
							'sPhotoUrl' => $thumbUrl,
					);
				}	
				
			}
		}
		return $results;
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
		if (! $viewerId)
		{
			return array (
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("No logged in users!")
			);
		}
		
 		$aData['iViewerId'] = $viewerId;
// 		return $this->getPhotos($aData, self::GETPHOTO_TYPE_MY);

		// photo table
		$photoTable = Engine_Api::_ ()->getItemTable ( 'advalbum_photo' );
		$photoTableName = $photoTable->info ( 'name' );
		
		// album table
		$albumTable = Engine_Api::_ ()->getItemTable ( 'advalbum_album' );
		$albumTableName = $albumTable->info ( 'name' );
		
		$select = $photoTable->select()->from($photoTableName);
		$select->joinLeft($albumTableName, $albumTableName . '.album_id=' . $photoTableName . '.album_id', null );
		$select->where("$albumTableName.owner_id = ?", $aData['iViewerId']);
		
		
		// process iLimit and sAction
		if (!empty ( $iLimit ))
		{
			$iLimit = (int) $iLimit;
			if ($iLimit <= 0)
			{
				$iLimit = self::GETPHOTO_PHOTO_LIMIT;
			}
		}
		else
		{
			$iLimit = self::GETPHOTO_PHOTO_LIMIT;
		}
		$select->limit ( $iLimit );
		
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
		
		$select->order ( "$photoTableName.photo_id DESC" );
		
		$photos = $photoTable->fetchAll ( $select );
		// array to contain results
		$results = array ();
		
		if (count ( $photos ))
		{
			foreach ( $photos as $photo )
			{
				// finalize photo url
				$photoUrl = Engine_Api::_ ()->ynmobile ()->finalizeUrl($photo->getPhotoUrl ( TYPE_OF_USER_IMAGE_PROFILE ));
				$thumbUrl = Engine_Api::_ ()->ynmobile ()->finalizeUrl($photo->getPhotoUrl ( TYPE_OF_USER_IMAGE_NORMAL ));
		
				if (isset($iInDetails) && $iInDetails == '1')
				{
					$album = Engine_Api::_()->getItem('advalbum_album', $photo->album_id);
						
					$iTotalComment = $photo->comments()->getCommentPaginator()->getTotalItemCount();
					$iTotalLike = $photo->likes()->getLikePaginator()->getTotalItemCount();
					$file = Engine_Api::_()->storage()->get($photo->file_id);
						
					$results [] = array (
							'iPhotoId' => $photo->getIdentity(),
							'sTitle' => $photo->getTitle(),
							'sPhotoUrl' => $photoUrl,
							'bCanPostComment' => true,
							'iAlbumId' => $photo->album_id,
							'sAlbumName' => $album->getTitle(),
							'bIsLiked' => $photo->likes()->isLike($viewer),
							'iTotalComment' => $iTotalComment,
							'iTotalLike' => $iTotalLike,
							'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($photo),
							'iTotalView' => 0,
							'iAllowDownload' => 1,
							'sFileName' => $file->name,
							'sFileSize' => $file->size,
							'sMimeType' => $file->mime_minor,
							'sExtension'=> $file->extension,
							'sOriginalDestination' => $file->storage_path,
							'sDescription' => $photo->description,
							'sAlbumUrl' => $album->getHref(),
							'sAlbumTitle' => $album->getTitle(),
							'iUserId' => $photo->getOwner()->getIdentity(),
							'sItemType' => 'advalbum_photo',
							'fRating' => $photo->rating,
							'iTotalRated' => Engine_Api::_() -> advalbum() -> countRating($photo->getIdentity(), 'photo')
					);
				}
				else
				{
					$results [] = array (
							'iPhotoId' => $photo->getIdentity (),
							'sTitle' => $photo->getTitle (),
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
		extract ( $aData, EXTR_SKIP );
		
		$translate = Zend_Registry::get('Zend_Translate');
		
		// check user id
		if (empty($iUserId))
		{
			$iUserId = 0;
		}
		if (!is_numeric ( $iUserId ))
		{
			return array (
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("User id is invalid!")
			);
		}
		
		$aData['iUserId'] = $iUserId;
		
		// get friends id
		if (!$iUserId)
		{
			$viewer = Engine_Api::_()->user()->getViewer();
			$friendsId = $viewer->membership()->getMembershipsOfIds();
			if (count ( $friendsId ))
			{
				$aData['aFriendsId'] = $friendsId;
			}
			else 
			{
				return array();
			}
		}
		
		return $this->getPhotos($aData, self::GETPHOTO_TYPE_FRIEND);
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
	public  function listalbumphoto($aData)
	{
		extract ( $aData, EXTR_SKIP );
		
		$translate = Zend_Registry::get('Zend_Translate');
		
		if (empty($iAlbumId) || !is_numeric($iAlbumId))
		{
			return array (
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Album Id is missing or invalid!")
			);					
		}
		
		if (! empty ( $iLimit ) && ! is_numeric ( $iLimit ))
		{
			return array(
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate -> _("Invalid iLimit ")
			);
		}
		
		$album = Engine_Api::_()->getItem('advalbum_album', $iAlbumId);
		
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

		if (!isset($iPage))
			$iPage = 1;

		if ($iPage == '0')
			return array();
		$translate = Zend_Registry::get('Zend_Translate');

		// album table
		$albumTable = Engine_Api::_ ()->getItemTable ( 'advalbum_album' );
		$albumTableName = $albumTable->info ( 'name' );
		
		
		$select = $albumTable -> select() -> where("$albumTableName.owner_type = ?", 'user');
		
		if ($type != self::GETALBUM_TYPE_MY)
		{
			$select = $select -> where("$albumTableName.photo_id <> 0");
		}
		
		// process sType
		if (! empty ( $sType ))
		{
			$select->where ( "$albumTableName.type = ?", $sType );
		}
				
		// process iCategoryId
		if (!empty ( $iCategoryId ) && $iCategoryId)
		{
			$select->where ( "$albumTableName.category_id = ?", $iCategoryId );
		}
		
		// process iFeatured
		if (!empty ( $iFeatured ))
		{
			$select->where ( "$albumTableName.featured = 1");
			$select->where ( "$albumTableName.search = 1");
		}
		
		if (!empty ( $sSearch )) {
			$select->where ( "$albumTableName.title LIKE ? OR $albumTableName.description LIKE ?", '%' . $sSearch . '%' );
		}
		
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$iViewerId = $viewer->getIdentity ();
		
		if ($type == self::GETALBUM_TYPE_MY)
		{
			$select->where("$albumTableName.owner_id = ?", $iViewerId);
		}
		else if (isset($iUserId))
		{
			$select->where ("$albumTableName.owner_id = ?", $iUserId );
		}
		
		// check friendsId, use for 'profile' album function
		if ($type == self::GETALBUM_TYPE_FRIEND)
		{
			if (!$iUserId)
			{
				$select->where("$albumTableName.owner_id IN (?)", $aFriendsId);
			}
			else 
			{
				$select->where ( "$albumTableName.owner_id = ?", $iUserId );
			}
		}
		if ($type != self::GETALBUM_TYPE_MY)
		{
			$select -> where("$albumTableName.search = 1");
		}
		
		// order
		if (!empty ( $sOrder ))
		{
			if ($sOrder == 'popular')
			{
				$select -> order("$albumTableName.view_count DESC");
			}
			else if ($sOrder == 'recent')
			{
				$select->order("$albumTableName.creation_date DESC");
			}
			else if ($sOrder == 'top')
			{
				$select->order("$albumTableName.like_count DESC");
			}
		}
		else 
		{
			$select->order ( "$albumTableName.album_id DESC" );
		}
		
		// limit
		if (!empty ( $iLimit ))
		{
			$iLimit = (int) $iLimit;
			if ($iLimit <= 0)
			{
				$iLimit = self::GETALBUM_ALBUM_LIMIT;
			}
		}
		else
		{
			$iLimit = self::GETALBUM_ALBUM_LIMIT;
		}
		$paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($iLimit);
		$paginator -> setCurrentPageNumber($iPage, 1);
		
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
			return array();
		
		$albums = $paginator;
		$results = array();
		foreach ($albums as $album)
		{
			// finalize photo url
			$photoUrl = $album -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
			if ($photoUrl)
				$photoUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($photoUrl);
			else
				$photoUrl = NO_ALBUM_THUMBNAIL;
				
			$albumOwner = Engine_Api::_()->user()->getUser($album->owner_id);
			$sUserImageUrl = $albumOwner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sUserImageUrl != "")
			{
				$sUserImageUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImageUrl);
			}
			else
			{
				$sUserImageUrl = NO_USER_ICON;
			}
			$bCanLike = $bCanComment = (Engine_Api::_() -> authorization() -> isAllowed($album, null, 'comment')) ? true : false;
			$results[] = array(
				'iAlbumId' => $album->getIdentity(),
				'sAlbumImageURL' => $photoUrl,
				'sName' => $album->getTitle(),
				'iTotalPhoto' => $album->count(),
				'iTimeStamp' => strtotime($album->creation_date),
				'iTimeStampUpdate' => strtotime($album->modified_date),
				'iTotalComment' => $album->comment_count,
				'iTotalLike' => $album -> likes() -> getLikeCount(),
				'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($album),
				'iUserId' => $album->owner_id,
				'bIsLiked' => $album -> likes() -> isLike($viewer),
				'sUserName' => $albumOwner->getTitle(),
				'sUserImageUrl' => $sUserImageUrl,
				'aSamplePhotos' => $this -> getSamplePhotos($album, $iNumberOfPhoto),
				'bCanView' => (Engine_Api::_() -> authorization() -> isAllowed($album, null, 'view')) ? true : false,
				'bCanComment' => $bCanComment,
				'bCanLike' => $bCanLike,
				'bCanShare' => false,
			);
		}
		
		return $results;
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
		
		$userId = Engine_Api::_ ()->user ()->getViewer ()->getIdentity ();
			
		// no logged in users
		if (! $userId)
		{
			return array (
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("No logged in users!")
			);
		}
		
		$aData['iViewerId'] = $userId;
		return $this->getAlbums($aData, self::GETALBUM_TYPE_MY);						
	}

	
	protected function userAlbums($userId)
	{
		$albumTable = Engine_Api::_()->getItemTable("advalbum_album");
		$select = $albumTable->select();
		$select = $select->where("owner_id = ?", $userId);
		
		$albums = $albumTable->fetchAll($select);
		$result = array();
		foreach ($albums as $album)
		{
			$result[] = $album->getIdentity();
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
		extract ( $aData, EXTR_SKIP );
		
		$translate = Zend_Registry::get('Zend_Translate');
		
		// check user id
		if (empty($iUserId))
		{
			$iUserId = 0;
		}
		if (!is_numeric ( $iUserId ))
		{
			return array (
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("User id is invalid!")
			);
		}
		
		$aData['iUserId'] = $iUserId;
		// get friends id
		if (!$iUserId)
		{
			$viewer = Engine_Api::_()->user()->getViewer();
			$friendsId = $viewer->membership()->getMembershipsOfIds();
			if (count ( $friendsId ))
			{
				$aData['aFriendsId'] = $friendsId;
			}
			else
			{
				return array();
			}
		}
		
		return $this->getAlbums($aData, self::GETALBUM_TYPE_FRIEND);
	}
	
	/**
	 * Create album with default photo_id = 0
	 * INPUT
	 * + sTitle: string, required, use "Untitled Album" by default
	 * + sDecription: string, optional.
     * + sType: string, optional, in array ('wall','profile','message','blog'), default is null
     * + iCategoryid: sstring, optional, use 0 by default
     * + iSearch: string, optional, use 1 by default
     * + sAuthView: string, optional, in array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone'),  'everyone' by default. 
     * + sAuthComment: string, optional, in array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone'),  'everyone' by default.
     * + sAuthTag: string, optional,  in array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone'),  'everyone' by default.
	 */
	public function albumcreate($aData)
	{
		extract ( $aData, EXTR_SKIP );
		
		$translate = Zend_Registry::get('Zend_Translate');
		
		if (!Engine_Api::_() -> authorization() -> isAllowed('advalbum_album', null, 'create'))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("You don't have permission to create a new album!"),
					'result' => 0
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iOwnerId = $viewer->getIdentity();
		
		// check sAuthView, sAuthComment, sAuthTag
		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		if (empty($sAuthView))
		{
			$sAuthView = 'everyone';
		}
		elseif (!in_array($sAuthView, $roles))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("sAuthView is invalid"),
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
					'error_message' => $translate->_("sAuthComment is invalid"),
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
					'error_message' => $translate->_("sAuthTag is invalid"),
					'result' => 0
			);
		}
		
		// PROCESS TO CREATE ALBUM
		$db = Engine_Api::_()->getItemTable('advalbum_album')->getAdapter();
		$db->beginTransaction();
		
		try
		{
			$params = array();
			
			$params['owner_id'] = $iOwnerId;
			$params['owner_type'] = 'user';
			
			// process sTitle
			$params['title'] = empty($sTitle)?$translate->_('Untitled Album'):$sTitle;
			 
			// process sDescription
			$params['description'] = empty($sDescription)?'':$sDescription;
	
			// process sType
			$params['type'] = empty($sType)?NULL:$sType;
			
			// process iCategoryId
			$params['category_id'] = empty($iCategoryId)?0:$iCategoryId;
			
			// process iSearch
			$params['search'] = empty($iSearch)?1:$iSearch;
	
			// set default photo id as album cover
			$params['photo_id'] = 0;
			$album = Engine_Api::_()->getDbtable('albums', 'advalbum')->createRow();
			$album->setFromArray($params);
			$album->save();
			
			// -- authen stuff
			$auth = Engine_Api::_()->authorization()->context;
			
			$viewMax = array_search($sAuthView, $roles);
			$commentMax = array_search($sAuthComment, $roles);
			$tagMax = array_search($sAuthTag, $roles);
	
			foreach( $roles as $i => $role ) {
				$auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
				$auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
				$auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
			}
			
			// Add action and attachments
			//$api = Engine_Api::_()->getDbtable('actions', 'activity');
			//$action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'advalbum_photo_new', null, array('count' => 0));
			
			// NOTE: activity feed if photo_id = 0
			// process upload - LATER
			
			$db->commit();
			
			return array(
					'result' => 1,
					'error_code' => 0,
					'error_message' => "",
					'iAlbumId' => $album->getIdentity()
			);
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(	
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate->_("Oops, Fail!")
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
		extract ( $aData, EXTR_SKIP );
		
		$translate = Zend_Registry::get('Zend_Translate');
		
		// check album id
		if (!isset($iAlbumId))
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Album Id is missing!")
			);
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$album = Engine_Api::_() -> getItem('advalbum_album', $iAlbumId);
		// check album
		if (!$album)
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Album is not available!")
			);
		}
		
		$bCanPostComment = Engine_Api::_() -> authorization() -> isAllowed($album, null, 'comment');
		$bCanView = Engine_Api::_() -> authorization() -> isAllowed($album, null, 'view');
		
		$bCanTag = Engine_Api::_() -> authorization() -> isAllowed($album, null, 'tag'); 
		// check bIsFriend
		$owner = $album -> getOwner();
		$is_friend = false;
		if ($owner -> getIdentity() != $viewer -> getIdentity())
		{
			$is_friend = $viewer -> membership() -> isMember($owner);
		}
	
		// finalize photo url
		$coreApi = Engine_Api::_ ()->ynmobile ();
		$photoUrl = $coreApi->finalizeUrl($album->getPhotoUrl ( TYPE_OF_USER_IMAGE_NORMAL ));
		if (empty($photoUrl))
		{
			$photoUrl = NO_ALBUM_THUMBNAIL;
		}
		
		// finalize user url
		$sUserImageUrl = $coreApi->finalizeUrl($owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON));
		if (empty($sUserImageUrl))
		{
			$sUserImageUrl = NO_USER_ICON;
		}	
			
		$auth = Engine_Api::_()->authorization()->context;
		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		foreach( $roles as $role )
		{
			if( 1 === $auth->isAllowed($album, $role, 'view') )
			{
				$sViewPrivacy = $role;
			}
			if( 1 === $auth->isAllowed($album, $role, 'comment') )
			{
				$sCommentPrivacy = $role;
			}
		}
		
		return array(
			'iAlbumId' => $album->getIdentity(),
			'bIsFriend' => $is_friend,
			'bIsLiked' => $album -> likes() -> isLike($viewer),
			'sTitle' => $album -> getTitle(),
			'sDescription' => $album -> description,
			'sAlbumImageUrl' => $photoUrl,
			'iUserId' => $album->owner_id,
			'sUserFullName' => $owner->getTitle(),
			'sUserImageUrl' => $sUserImageUrl,
			'iCategoryId' => $album->category_id,
			'iCreationDate' => strtotime($album->creation_date),
			'iModifiedDate' => strtotime($album->modified_date),				
			'iSearch' => $album->search,
			'sType' => ($album->type)?$album->type:'',
			'iTotalView' => $album->view_count,
			'iTotalComment' => $album -> comment_count,
			'iTotalLike' => $album -> likes() -> getLikeCount(),
			'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($album),
			'iTotalPhoto' => $album -> count(),
			'bCanComment' => $bCanPostComment,
			'bCanLike' => $bCanPostComment,
			'bCanView' => $bCanView,
			'bCanTag' => $bCanTag, 
			'sViewPrivacy' => $sViewPrivacy,
			'sCommentPrivacy' => $sCommentPrivacy,
			'bCanShare' => false,
		);
	}

	/**
	 * INPUT
	 * + iAlbumId: int, required.
     * + sTitle: int, optional.
     * + sDescription: string, optional.
	 * + iPhotoID: int, optional, album cover
     * + iCategoryId: int.
     * + bSearch: int
     * + sAuthView: string, optional, in array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone'),  'everyone' by default. 
     * + sAuthComment: string, optional, in array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone'),  'everyone' by default.
     * + sAuthTag: string, optional,  in array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone'),  'everyone' by default.
	 */
	public function albumedit($aData)
	{
		extract($aData, EXTR_SKIP);
		
		$translate = Zend_Registry::get('Zend_Translate');
		
		// check album id
		if (!isset($iAlbumId))
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Album Id is missing!")
			);
		}
		// check category id
		if (isset($iCategoryId) && ! is_numeric ( $iCategoryId ))
		{
			return array (
					'result' => 0,
					'error_code' => 1,
					'error_message' => "Invalid Category Id"
			);
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$album = Engine_Api::_() -> getItem('advalbum_album', $iAlbumId);
		// check album
		if (!$album)
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Album is not available!")
			);
		}
		
		// check edit permission
		if (!$album -> authorization() -> isAllowed($viewer, 'edit'))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("You don't have permission to edit this album!"),
					'result' => 0
			);
		}

		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		if (empty($sAuthView))
		{
			$sAuthView = 'everyone';
		}
		elseif (!in_array($sAuthView, $roles))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("sAuthView is invalid"),
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
					'error_message' => $translate->_("sAuthComment is invalid"),
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
					'error_message' => $translate->_("sAuthTag is invalid"),
					'result' => 0
			);
		}
		
		// Process
		$db = $album->getTable()->getAdapter();
		$db->beginTransaction();
		
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
			
			$album->setFromArray($params);
			$album->save();
			
			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_()->authorization()->context;
			
			$viewMax = array_search($sAuthView, $roles);
			$commentMax = array_search($sAuthComment, $roles);
			$tagMax = array_search($sAuthTag, $roles);
			
			foreach( $roles as $i => $role ) 
			{
				$auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
				$auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
				$auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
			}		
			
			// Rebuild privacy
			$actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
			foreach( $actionTable->getActionsByObject($album) as $action ) 
			{
				$actionTable->resetActivityBindings($action);
			}
			
			$db->commit();

			return array(
				'result' => 1,
				'error_code' => 0,
				'error_message' => "",
				'iAlbumId' => $iAlbumId	
			);
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(	
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate->_("Oops, Fail!")
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
		
		// check album id
		if (!isset($iAlbumId))
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Album Id is missing!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$album = Engine_Api::_() -> getItem('advalbum_album', $iAlbumId);
		// check album
		if (!$album)
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Album is not available!")
			);
		}
		
		// check view permission
		if (!$album -> authorization() -> isAllowed($viewer, 'delete'))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("You don't have permission to delete this album!"),
					'result' => 0
			);
		}

		$db = $album->getTable()->getAdapter();
		$db->beginTransaction();
		
		try
		{
			$album->delete();
			$db->commit();
			
			return array(
					'result' => 1,
					'error_code' => 0,
					'error_message' => "",
					'iAlbumId' => $iAlbumId
			);
				
		}
		
		catch( Exception $e )
		{
			$db->rollBack();
			return array(	
				'result' => 0,
				'error_code' => 1,
				'error_message' => $translate->_("Oops, Fail!")
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
		if ($aData['sParentType'] == "group" && isset($aData['iParentId']))
		{
			$aData['iGroupId'] = $aData['iParentId'];
			return Engine_Api::_()->getApi('group','ynmobile')->upload_photo($aData);
		}
			
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$translate = Zend_Registry::get('Zend_Translate');	
		
		if (!Engine_Api::_() -> authorization() -> isAllowed('advalbum_album', null, 'create'))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("You don't have permission to upload photo!"),
					'result' => 0
			);
		}
		
		//Will open when $_FILE is posted actually
		if( empty($aData['image']) && !isset($_FILES['image']))
		{
			return array(
				'error_code' => 2,
				'error_message' => $translate -> _("No file!"),
				'result' => 0
			);
		}
		
		$db = Engine_Api::_()->getDbtable('photos', 'advalbum')->getAdapter();
		$db->beginTransaction();
		$photoTable = Engine_Api::_()->getDbtable('photos', 'advalbum');
		
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
			$albumTable = Engine_Api::_()->getDbtable('albums', 'advalbum');
			if (in_array($aData['sAlbumType'], array('wall', 'profile', 'message')))
			{
				$album = $albumTable -> getSpecialAlbum($albumOwner, $aData['sAlbumType']);
			}
			else
			{
				$album = $albumTable -> getSpecialAlbum($albumOwner, 'wall');
			}
			$aData['iAlbumId'] = $album->getIdentity();
		}
		
		try
		{
			$photo = $photoTable->createRow();
			$photo->setFromArray(array(
					'owner_type' => 'user',
					'owner_id' => $viewer->getIdentity()
			));
			$photo->save();
		
			$photo->order = $photo->photo_id;
			$photo->title = isset($aData['sTitle']) ? $aData['sTitle'] : '';
			$photo->album_id = isset($aData['iAlbumId']) ? $aData['iAlbumId'] : 0;
			$photo->description = isset($aData['sDescription']) ? $aData['sDescription'] : '';

			$photo = Engine_Api::_()->ynmobile()->setPhoto($photo, $_FILES['image']);
			$photo->save();
		
			$db->commit();
		
		} catch( Album_Model_Exception $e ) 
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
					'result' => 0
			);
		} 
		catch( Exception $e ) 
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
					'result' => 0
			);
		}
		
		//$action = Engine_Api::_() -> getDbtable('actions', 'activity') ->addActivity($viewer, $album, 'advalbum_photo_new', null, array('count' => 1));
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
		
		if( !$photo || !($photo instanceof Core_Model_Item_Abstract) || empty($photo->photo_id) ) 
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("An error occurred!"),
					'result' => 0
			);
		}
		
		if( !$photo->authorization()->isAllowed(null, 'view') ) 
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("An error occurred!"),
					'result' => 0
			);
		}
		
		// Process
		$db = $user->getTable()->getAdapter();
		$db->beginTransaction();
		
		try 
		{
			// Get the owner of the photo
			$photoOwnerId = null;
			if( isset($photo->user_id) ) 
			{
				$photoOwnerId = $photo->user_id;
			} 
			else if( isset($photo->owner_id) && (!isset($photo->owner_type) || $photo->owner_type == 'user') ) {
				$photoOwnerId = $photo->owner_id;
			}
		
			// if it is from your own profile album do not make copies of the image
			if( $photo instanceof Album_Model_Photo &&
					($photoParent = $photo->getParent()) instanceof Album_Model_Album &&
					$photoParent->owner_id == $photoOwnerId &&
					$photoParent->type == 'profile' ) 
			{
		
				// ensure thumb.icon and thumb.profile exist
				$newStorageFile = Engine_Api::_()->getItem('storage_file', $photo->file_id);
				$filesTable = Engine_Api::_()->getDbtable('files', 'storage');
				if( $photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.profile') ) 
				{
					try 
					{
						$tmpFile = $newStorageFile->temporary();
						$image = Engine_Image::factory();
						$image -> open($tmpFile) -> resize(200, 400) -> write($tmpFile) -> destroy();
						$iProfile = $filesTable -> createFile($tmpFile, array(
							'parent_type' => $user -> getType(),
							'parent_id' => $user -> getIdentity(),
							'user_id' => $user -> getIdentity(),
							'name' => basename($tmpFile),
						));
						$newStorageFile->bridge($iProfile, 'thumb.profile');
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
				if( $photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.icon') ) 
				{
					try {
						$tmpFile = $newStorageFile->temporary();
						$image = Engine_Image::factory();
						$image->open($tmpFile);
						$size = min($image->height, $image->width);
						$x = ($image->width - $size) / 2;
						$y = ($image->height - $size) / 2;
						$image->resample($x, $y, $size, $size, 48, 48)
						->write($tmpFile)
						->destroy();
						$iSquare = $filesTable->createFile($tmpFile, array(
								'parent_type' => $user->getType(),
								'parent_id' => $user->getIdentity(),
								'user_id' => $user->getIdentity(),
								'name' => basename($tmpFile),
						));
						$newStorageFile->bridge($iSquare, 'thumb.icon');
						@unlink($tmpFile);
					} 
					catch( Exception $e ) {
						return array(
								'error_code' => 1,
								'error_message' => $translate->_("An error occurred!"),
								'result' => 0
						);
					}
				}
		
				// Set it
				$user->photo_id = $photo->file_id;
				$user->save();
		
				// Insert activity
				// @todo maybe it should read "changed their profile photo" ?
				$action = Engine_Api::_()->getDbtable('actions', 'activity')
				->addActivity($user, $user, 'profile_photo_update',
						'{item:$subject} changed their profile photo.');
				if( $action ) {
					// We have to attach the user himself w/o album plugin
					Engine_Api::_()->getDbtable('actions', 'activity')
					->attachActivity($action, $photo);
				}
			}
		
			// Otherwise copy to the profile album
			else {
				$user->setPhoto($photo);
		
				// Insert activity
				$action = Engine_Api::_()->getDbtable('actions', 'activity')
				->addActivity($user, $user, 'profile_photo_update',
						'{item:$subject} added a new profile photo.');
		
				// Hooks to enable albums to work
				$newStorageFile = Engine_Api::_()->getItem('storage_file', $user->photo_id);
				$event = Engine_Hooks_Dispatcher::_()
				->callEvent('onUserProfilePhotoUpload', array(
						'user' => $user,
						'file' => $newStorageFile,
				));
		
				$attachment = $event->getResponse();
				if( !$attachment ) 
				{
					$attachment = $newStorageFile;
				}
		
				if( $action  ) 
				{
					// We have to attach the user himself w/o album plugin
					Engine_Api::_()->getDbtable('actions', 'activity')
					->attachActivity($action, $attachment);
				}
			}
		
			$db->commit();
		}
		
		// Otherwise it's probably a problem with the database or the storage system (just throw it)
		catch( Exception $e )
		{
			$db->rollBack();
			throw $e;
		}
		
		return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _("Set as profile photo successfully"),
				'sProfileImage' => Engine_Api::_() -> ynmobile() ->finalizeUrl($photo->getPhotoUrl()),
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
		$translate = Zend_Registry::get('Zend_Translate');
		if (!isset($aData['iCurrentPhotoId']) || $aData['iCurrentPhotoId'] < 1)
		{
			return array(
				'error_message' => $translate->_('Current photo id is not valid!'),
				'error_code' => 1,
				'result' => 0
			);
		}
		
		// Get photo
		$photo = Engine_Api::_()->getItem('advalbum_photo', $aData['iCurrentPhotoId']);
		
		if( !$photo || !($photo instanceof Core_Model_Item_Abstract) || empty($photo->photo_id) )
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("This photo is not existed."),
				'result' => 0
			);
		}
		
		if ($aData['sAction'] != self::ACTION_TYPE_NEXT && $aData['sAction'] != self::ACTION_TYPE_PREVIOUS)
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("Action is not valid!"),
					'result' => 0
			);
		}
		$aData['iLimit'] = isset($limit) ? $limit : 10;
		return $this -> getPhotos($aData, self::GETPHOTO_TYPE_SLIDE);
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
		$aPreviousData = $aData;
		
		$aPreviousData['sAction'] = 'previous';
		$aPreviousPhotos = $this -> photoslide($aPreviousData, $aData['iLimit']);
		$aPreviousPhotos = array_reverse($aPreviousPhotos);
		
		$aNextData = $aData;
		$aNextData['sAction'] = 'next';
		$aNextPhotos = $this -> photoslide($aNextData, $aData['iLimit']);
	
		if (count($aNextPhotos))
		{
			foreach($aNextPhotos as $aPhoto)
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
					'error_message' => $translate->_("iAlbumId is not valid!"),
					'result' => 0
			);
		}
		
		return $this->photoslide($aData);
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
		
		return $this->fullphotoslide($aData);
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
		if(isset($aData['sItemType']) && ($aData['sItemType'] == 'event_photo' || $aData['sItemType'] == 'ynevent_photo'))
		{
			return Engine_Api::_()->getApi('event','ynmobile')->photo_edit($aData);
		}
		
		if(isset($aData['sItemType']) && ($aData['sItemType'] == 'group_photo' || $aData['sItemType'] == 'advgroup_photo'))
		{
			return Engine_Api::_()->getApi('group','ynmobile')->photo_edit($aData);
		}
		
		extract($aData, EXTR_SKIP);
		
		$translate = Zend_Registry::get('Zend_Translate');
		
		// check photo id
		if (!isset($iPhotoId))
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Photo Id is missing!")
			);
		}
		
		$photo = Engine_Api::_() -> getItem('advalbum_photo', $iPhotoId);
		// check photo
		if (!$photo)
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Photo is not available!")
			);
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// check edit permission
		if (!$photo -> authorization() -> isAllowed($viewer, 'edit'))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("You don't have permission to edit this photo!"),
					'result' => 0
			);
		}
		
		// check album if album id is available
		if (!empty($iAlbumId))
		{
			$album = Engine_Api::_() -> getItem('advalbum_album', $iAlbumId);
			// check album
			if (!$album)
			{
				return array(
						'result' => 0,
						'error_code' => 1,
						'error_message' => $translate->_("Album is not available!")
				);
			}
		}
		
		if (isset($iOrder) && !is_numeric($iOrder))
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Order Id is invalid!")
			);			
		}
		// Process
		$db = $photo->getTable()->getAdapter();
		$db->beginTransaction();
		
		try 
		{
			if (isset($sTitle))
			{
				$photo->title = $sTitle;
			}

			if (isset($sDescription))
			{
				$photo->description = $sDescription;
			}
			
			if (!empty($iAlbumId))
			{
				$photo->album_id = $iAlbumId;
			}

			if (isset($iOrder))
			{
				$photo->order = $iOrder;
			}
			
			$photo->save();
			$db->commit();
			
			return array(
					'result' => 1,
					'error_code' => 0,
					'error_message' => "",
					'iPhotoId' => $iPhotoId
			);
			
		}
		catch (Exception $e)
		{
			$db->rollBack();
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Oops, Fail!")
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
		
		// check photo id
		if (!isset($iPhotoId))
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Photo Id is missing!")
			);
		}
		
		$sItemType = isset($aData['sItemType']) ? $aData['sItemType'] : 'advalbum_photo';
		
		$photo = Engine_Api::_() -> getItem($sItemType, $iPhotoId);
		// check album
		if (!$photo)
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Photo is not available!")
			);
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// check edit permission
		if (!$photo -> authorization() -> isAllowed($viewer, 'edit'))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("You don't have permission to delete this photo!"),
					'result' => 0
			);
		}
		
		$db = $photo->getTable()->getAdapter();
		$db->beginTransaction();
		
		try 
		{
			$photo->delete();
			$db->commit();
			return array(
					'result' => 1,
					'error_code' => 0,
					'error_message' => "",
					'iPhotoId' => $iPhotoId
			);		


		} 
		catch( Exception $e ) 
		{
			$db->rollBack();
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Oops, Fail!")
			);
		}
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
		extract ( $aData, EXTR_SKIP );
		
		$translate = Zend_Registry::get('Zend_Translate');
		
		// check album id
		if (!isset($iPhotoId))
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Photo Id is missing!")
			);
		}
		
		$photo = Engine_Api::_() -> getItem('advalbum_photo', $iPhotoId);
		// check album
		if (!$photo)
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Photo is not available!")
			);
		}
		
		// check view permission
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$photo -> authorization() -> isAllowed($viewer, 'view'))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("You don't have permission to view this photo!"),
					'result' => 0
			);
		}		
		
		$bCanComment = Engine_Api::_() -> authorization() -> isAllowed($photo, null, 'comment');
		$bCanView = Engine_Api::_() -> authorization() -> isAllowed($photo, null, 'view');
		$bCanTag = Engine_Api::_() -> authorization() -> isAllowed($photo, null, 'tag'); 

		$coreApi = Engine_Api::_ ()->ynmobile ();
		// check album
		$album = Engine_Api::_()->getItem('advalbum_album', $photo->album_id);
		if (!$album)
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Album is not available!")
			);
		}
		
		$owner = $album -> getOwner();			
		// finalize user url
		if ($owner->getIdentity())
		{
			$sUserImageUrl = $coreApi->finalizeUrl($owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON));
		}
		if (empty($sUserImageUrl))
		{
			$sUserImageUrl = NO_USER_ICON;
		}		
		
		// finalize photo url
		$photoUrl = $coreApi->finalizeUrl($photo->getPhotoUrl ( TYPE_OF_USER_IMAGE_NORMAL ));
		if (empty($photoUrl))
		{
			$photoUrl = NO_PHOTO_THUMBNAIL;
		}
		else 
		{
			// check file exist
			$file = Engine_Api::_()->getItemTable('storage_file')->getFile($photo->file_id, TYPE_OF_USER_IMAGE_NORMAL);
		}
		
		$results = array(
				'iPhotoId' => $photo->getIdentity(),	
				'iAlbumId' => $album->getIdentity(),
				'sTitle' => $photo->getTitle(),
				'sDescription' => $photo->description,
				'sPhotoImageUrl' => $photoUrl,
				'iCategoryId' => $album->category_id,
				'iNextPhotoId' => $photo->getNextPhoto()->getIdentity(),
				'iPreviousPhotoId' => $photo->getPreviousPhoto()->getIdentity(), 		
				'iCreationDate' => strtotime($photo->creation_date),
				'iModifiedDate' => strtotime($photo->modified_date),
				'sType' => ($album->type)?($album->type):'',
				'iSearch' => $album->search,
				'bCover' => ($album->photo_id == $photo->getIdentity())?1:0,				
				'iTotalView' => $photo->view_count,
				'iTotalLike' => $photo -> likes() -> getLikeCount(), 		
				'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($photo),
				'bCanComment' => $bCanComment,
				'bCanView' => $bCanView,
				'bCanTag' => $bCanTag,
				'iUserId' => $album->owner_id,
				'sUserFullName' => $owner->getTitle(),
				'sUserImageUrl' => $sUserImageUrl,

		);
		if ($file)
		{
			$results['sFileName'] = $file->name;
			$results['sFileSize'] = $file->size;
			$results['sFileExtension'] = $file->extension; 
			$results['sMimeType'] = $file->mime_major; 
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
		extract ( $aData, EXTR_SKIP );
		
		// check album id
		if (!isset($iPhotoId))
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Photo Id is missing!")
			);
		}
		
		$photo = Engine_Api::_() -> getItem('advalbum_photo', $iPhotoId);
		// check album
		if (!$photo)
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Photo is not available!")
			);
		}
		
		// check view permission
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$photo -> authorization() -> isAllowed($viewer, 'view'))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("You don't have permission to set this photo as album cover!"),
					'result' => 0
			);
		}
		
		$coreApi = Engine_Api::_ ()->ynmobile ();
		// check album
		$album = Engine_Api::_()->getItem('advalbum_album', $photo->album_id);
		if (!$album)
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Album is not available!")
			);
		}
		
		$db = $album->getTable()->getAdapter();
		$db->beginTransaction();
		
		try {
			$album->photo_id = $iPhotoId;
			$album->save();
			$db->commit();
			return array(
					'result' => 1,
					'error_code' => 0,
					'error_message' => "",
					'iPhotoId' => $iPhotoId
			);
		
		} catch( Exception $e ) {
			$db->rollBack();
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Oops, Fail!")
			);
		}
	}
	
	public function next($aData)
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!isset($aData['iCurrentPhotoId']) || ($aData['iCurrentPhotoId'] == 0))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("iCurrentPhotoId is not valid!"),
					'result' => 0
			);
		}
		
		$photo = Engine_Api::_()->getItem('advalbum_photo', $aData['iCurrentPhotoId']);
		$nextPhoto = $photo->getNextPhoto();
		return array(
					'iPhotoId' => $nextPhoto->getIdentity(),
					'sTitle' => $nextPhoto->getTitle(),
					'sPhotoUrl' => $nextPhoto->getPhotoUrl()
				);
	}
	
	public function previous($aData)
	{
		$translate = Zend_Registry::get('Zend_Translate');
		if (!isset($aData['iCurrentPhotoId']) || ($aData['iCurrentPhotoId'] == 0))
		{
			return array(
					'error_code' => 1,
					'error_message' => $translate->_("iCurrentPhotoId is not valid!"),
					'result' => 0
			);
		}
	
		$photo = Engine_Api::_()->getItem('advalbum_photo', $aData['iCurrentPhotoId']);
		$previousPhoto = $photo->getPreviousPhoto();
		return array(
				'iPhotoId' => $previousPhoto->getIdentity(),
				'sTitle' => $previousPhoto->getTitle(),
				'sPhotoUrl' => $previousPhoto->getPhotoUrl()
		);
	}
	
	
	public function getAlbumsCanView()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$albumTable = Engine_Api::_ ()->getItemTable ( 'advalbum_album' );
		$albums = $albumTable->fetchAll();
		$albumIds = array();
		
		foreach ($albums as $album)
		{
			$bCanView = Engine_Api::_() -> authorization() -> isAllowed($album, $viewer, 'view');
			if ($bCanView){
				$albumIds[] = $album->getIdentity();
			}
		}
		return $albumIds;
	}
	
	
	public function rate_album($aData)
	{
		extract($aData, EXTR_SKIP);
		
		$translate = Zend_Registry::get('Zend_Translate');
		
		// check album id
		if (!isset($iAlbumId))
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("iAlbumId is missing!")
			);
		}
		
		if (!isset($iRating))
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("iRating is missing!")
			);
		}
		
		$album = Engine_Api::_() -> getItem('advalbum_album', $iAlbumId);
		$type = Advalbum_Plugin_Constants::RATING_TYPE_ALBUM;
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$user_id = $viewer -> getIdentity();
		
		$rating = (int)$iRating;
		$ratingTbl = Engine_Api::_() -> getDbtable('ratings', 'advalbum');
		// save to rating table
		Engine_Api::_() -> advalbum() -> setRating($iAlbumId, $user_id, $rating, $type);
		
		$album -> rating = Engine_Api::_() -> advalbum() -> getRating($iAlbumId, $type);
		$album -> save();
		$total = Engine_Api::_() -> advalbum() -> countRating($iAlbumId, $type);
		
		$data = array();
		$data[] = array(
				'iTotal' => $total,
				'fRating' => $album -> rating,
		);
		return $data;
	}
	
	
	public function rate_photo($aData)
	{
		extract($aData, EXTR_SKIP);
	
		$translate = Zend_Registry::get('Zend_Translate');
	
		// check album id
		if (!isset($iPhotoId))
		{
			return array(
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("iPhotoId is missing!")
			);
		}
	
		$photo = Engine_Api::_() -> getItem('advalbum_photo', $iPhotoId);
		$type = Advalbum_Plugin_Constants::RATING_TYPE_PHOTO;
	
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$user_id = $viewer -> getIdentity();
	
		$rating = (int)$iRating;
		$ratingTbl = Engine_Api::_() -> getDbtable('ratings', 'advalbum');
		// save to rating table
		Engine_Api::_() -> advalbum() -> setRating($iPhotoId, $user_id, $rating, $type);
	
		$photo -> rating = Engine_Api::_() -> advalbum() -> getRating($iPhotoId, $type);
		$photo -> save();
		$total = Engine_Api::_() -> advalbum() -> countRating($iPhotoId, $type);
	
		$data = array();
		$data[] = array(
				'iTotal' => $total,
				'fRating' => $photo -> rating,
		);
		return $data;
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
	 * + iUserId: int.
	 *
	 * Failure:
	 *
	 */
	public function get_featured_albums($aData)
	{
		$translate = Zend_Registry::get('Zend_Translate');
	
		$userId = Engine_Api::_ ()->user ()->getViewer ()->getIdentity ();
			
		// no logged in users
		if (! $userId)
		{
			return array (
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("No logged in users!")
			);
		}
	
		$aData['iFeatured'] = 1;
		$aData['iSearch'] = 1;
		return $this->getAlbums($aData);
	}
	
	/**
	 * Get user photos
	 * Input Data:
	 * + iLimit: int, optional.
	 * + iLastPhotoIdViewed: int, optional.
	 * + iUserId: int, optional.
	 * + sType: string, optional ('wall','profile','message','blog').
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
	public function get_featured_photos($aData)
	{
		$aData['iFeatured'] = 1;
		return $this->getPhotos($aData);
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
	
	public function filter($aData)
	{
		if(isset($aData['iUserId']) && $aData['iUserId'] == 0){
			unset($aData['iUserId']);
		}
		if(@$aData['sItemType'] == 'event'){
			$aData['iEventId'] = $aData['iItemId'];
			return Engine_Api::_()->getApi('event','ynmobile')->listphotos($aData);
		}
		elseif(@$aData['sItemType'] == 'group')
		{
			$aData['iGroupId'] = $aData['iItemId'];
			return Engine_Api::_()->getApi('group','ynmobile')->photos($aData);
		}
		//Getting Featured PHOTOs
		if ($aData['sFilterBy'] == self::GETPHOTO_TYPE_FEATURED)
		{
			$aData['iFeatured'] = 1;
		}

		//My PHOTOs
		if ($aData['sFilterBy'] == self::GETPHOTO_TYPE_MY)
			return $this -> getPhotos($aData, self::GETPHOTO_TYPE_MY);
		//All PHOTOs
		else
			return $this -> getPhotos($aData);
	}
	/**
	 * INPUT
	 * N/A
	 *
	 * OUTPUT
	 * + iPhotoId: int.
	 * + iAlbumId: int.
	 * + sThumbUrl: string
	 * + sPhotoUrl: string
	 */
	public function categories()
	{
		$categories = Engine_Api::_()->advalbum()->getCategories();
		$result = array();
		foreach($categories as $cat)
		{
			$result[] = array(
				'iId' => $cat->category_id,
				'iCategoryId'=> $cat->getIdentity(),
				'sName' => $cat->category_name
			);
		}

		return $result;
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
		if (!isset($iNumberOfPhoto) || $iNumberOfPhoto == 0)
		{
			$iNumberOfPhoto = 3;
		}

		if (is_int($album))
		{
			$album = Engine_Api::_() -> getItem('advalbum_album', $album);
		}

		$photos = array();
		if (is_object($album) && $album -> getIdentity() != 0)
		{
			$photoTable = Engine_Api::_() -> getItemTable('advalbum_photo');
			$select = $photoTable -> select() -> where("album_id = ?", $album -> getIdentity()) -> order("photo_id") -> limit($iNumberOfPhoto);
			$photos = $photoTable -> fetchAll($select);
		}
		if (count($photos) == 0)
			return array();

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
		if (!isset($aData['sPhotoIds']))
		{
			return array(
					'error_code' => 1,
					'error_element' => 'sPhotoIds',
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
	
		$ids = explode(',', $aData['sPhotoIds']);
		$api = Engine_Api::_()->getDbtable('actions', 'activity');
		if (isset($aData['sParentType']) && ($aData['sParentType']  == "group" ))
		{
			if (!isset($aData['iParentId']))
			{
				return array(
						'error_code' => 1,
						'error_element' => 'iAlbumId',
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing Parent ID!")
				);
			}
			$group = null;
			if (Engine_Api::_()->hasModuleBootstrap("group"))
			{
				$group = Engine_Api::_()->getItem("group", $aData['iParentId']);
			}
			else if (Engine_Api::_()->hasModuleBootstrap("advgroup"))
			{
				$group = Engine_Api::_()->getItem("advgroup", $aData['iParentId']);
			}
				
			if (is_null($group))
			{
				return array(
						'error_code' => 1,
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("Group is not valid!")
				);
			}
			$count = 0;
			$action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $group, 'group_photo_upload', null, array('count' => count($ids)));
			
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
				$photo = Engine_Api::_()->getItem("group_photo", $photo_id);
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
			$album = Engine_Api::_() -> getItem('advalbum_album', $aData['iAlbumId']);
			if (!$album)
			{
				return array(
						'error_code' => 1,
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("Album is not valid!")
				);
			}
			$count = 0;
			$action = $api -> addActivity(Engine_Api::_() -> user() -> getViewer(), $album, 'advalbum_photo_new', null, array('count' => count($ids)));
			foreach ($ids as $photo_id)
			{
				$photo = Engine_Api::_() -> getItem("advalbum_photo", $photo_id);
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
				'iActionId' => $action -> action_id
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
	
	public function get_message_photo($aData)
	{
		extract($aData);
		if (!isset($iPhotoId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPhotoId!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$result = array();
		$photo = Engine_Api::_() -> getItem('advalbum_photo', $iPhotoId);//NOTICE
		// finalize photo url
		$photoUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($photo -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE));
		$thumbUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($photo -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL));
		$album = Engine_Api::_()->getItem('advalbum_album', $photo->album_id);//NOTICE
		
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
