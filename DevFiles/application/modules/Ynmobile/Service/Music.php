<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Album.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Service_Music extends Ynmobile_Service_Base
{
    /**
     * support module key
     * follow Ynmobile_AppMeta
     */
    protected $module = 'music';
    
    
    function select_mp3music_albums($params){
        $select =  $this->getWorkingTable('albums','music')->select();
        if($params['sView'] == 'my'){
            $viewer =  Engine_Api::_()->user()->getViewer();
            $select ->where('user_id=?', $viewer->getIdentity());
        }else{
            $select ->where('search=1');
        }
        
        return $select;
        
    }
    
    
    
    function select_music_playlists($params){
        $select =  $this->getWorkingTable('playlists','music')->select();
        if($params['sView'] == 'my'){
            $viewer =  Engine_Api::_()->user()->getViewer();
            $select ->where('owner_id=?', $viewer->getIdentity());
            $select->where('owner_type=?', $viewer->getType());
        }else{
            $select ->where('search=1');
        }
        
        return $select;
    }
    
    function get_select_playlists($params){
        if($this->getWorkingModule('music') == 'mp3music'){
            return $this->select_mp3music_albums($params);
        }else{
            return $this->select_music_playlists($params);
        }
    }
    
    /**
     * 
     */
    function find_music_playlist($iAlbumId){
        
         if($this->getWorkingModule('music') == 'mp3music'){
             
            return $this->getWorkingTable('albums','mp3music')->findRow($iAlbumId);
        }else{
            
            return $this->getWorkingTable('playlists','music')->findRow($iAlbumId);
        }
    }
    
	function fetch($aData)
	{
		extract($aData);
        
        $iPage = $iPage?intval($iPage):1;
        $iLimit  = $iLimit?intval($iLimit):10;
        
        
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $sAction = $sAction?strtolower($sAction):'new';
		$sView = $sView?strtolower($sView): 'all';
        
        
        if(empty($fields)) $fields  = 'listing';
        
        $fields  = explode(',', $fields);
        
		// get playlist db table
		
		$select  = $this->get_select_playlists($aData);
		
		if (!empty($sSearch))
		{
			$select -> where("title LIKE \"%{$sSearch}%\" OR description LIKE \"%{$sSearch}%\"");
		}
	
		// SORT
		if (isset($sOrder) && in_array($sOrder, array("recent", "popular")))
		{
			if( 'recent' == $sOrder ) 
			{
				$select->order('creation_date DESC');
			} 
			else if( 'popular' == $sOrder ) 
			{
				$select->order("play_count DESC");
			}
		}
		else
		{ 
			$select->order('creation_date DESC');
		}
		
		return Ynmobile_AppMeta::_exports_by_page($select, $iPage, $iLimit, $fields);
	}
	
	/**
	 * Input data:
	 * + iAlbumId: int, required.
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
	 * @see Mobile - API phpFox/Api V3.0
	 * @see album/list_songs
	 *
	 * @param array $aData
	 * @return array
	 */
	public function list_songs($aData)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iAlbumId = isset($aData['iAlbumId']) ? (int)$aData['iAlbumId'] : 0;
		$playlist = Engine_Api::_() -> getItem('music_playlist', $iAlbumId);
		if (!$playlist || !Engine_Api::_() -> authorization() -> isAllowed($playlist, null, 'view'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Playlist doesn't exists or not authorized to view")
			);
		}
		$table = Engine_Api::_() -> getDbtable('playlistSongs', 'music');
		$song_name = $table -> info('name');
		$select = $table -> select() -> from($song_name);
		$select -> where("playlist_id = ?", $iAlbumId);
		$rows = $table -> fetchAll($select);
		$aResult = array();
		$owner = $playlist -> getOwner();
		$sProfileImage = $playlist -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
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
		foreach ($rows as $song)
		{
			$songPath = Engine_Api::_() -> ynmobile() -> finalizeUrl($song -> getFilePath());
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
				'iSongId' => $song -> getIdentity(),
				'iUserId' => $playlist -> owner_id,
				'sTitle' => $song -> getTitle(),
				'sSongPath' => $songPath,
				'iTotalPlay' => $song -> play_count,
				'iTimeStamp' => $create,
				'sTimeStamp' => $sTime,
			);
		}
		return $aResult;
	}

	/**
	 * Input data:
	 * + iAlbumId: string, required.
	 *
	 * Output data:
	 * + bIsLiked: bool.
	 * + iAlbumId: int.
	 * + iUserId: int.
	 * + sAlbumName: string.
	 * + sDescription: string.
	 * + sImagePath: string.
	 * + iTotalTrack: int.
	 * + iTotalPlay: int.
	 * + iTotalComment: int.
	 * + iTotalLike: int.
	 * + aUserLike array
	 * + iTimeStamp: int.
	 * + sTimeStamp: string.
	 * + sFullTimeStamp: string.
	 * + sFullname: string.
	 * + sUserImage: string.
	 * + bIsInvisible: bool.
	 * + iUserLevelId: int.
	 * + bIsFriend: bool.
	 * + bCanPostComment: bool.

	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see album/detail
	 *
	 * @param array $aData
	 * @return array
	 */
	public function detail($aData)
	{
	    extract($aData);
        
        if(empty($fields)) $fields = 'detail';
        
        $fields = explode(',', $fields);
        
        $iAlbumId = intval($iAlbumId);
        
        $translate =  Zend_Registry::get('Zend_Translate');
        $playlist =  $this->find_music_playlist($iAlbumId);
        
		if (!$playlist){
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("Playlist not found!")
			);
		}
        
        // Increment view count
		$playlist -> play_count ++;
        
        if(isset($playlist->view_count)){
            $playlist -> view_count++;    
        }
		
		$playlist -> save();
		
        return Ynmobile_AppMeta::_export_one($playlist, $fields);
	}
}
