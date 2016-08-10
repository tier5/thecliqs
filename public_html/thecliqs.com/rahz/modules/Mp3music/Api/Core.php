<?php
 class Mp3music_Api_Core extends Core_Api_Abstract
{
    //Get all artist
   public function getArtistRows($limit=null)
  {
    $arrArtist = array();
    $allow_artist = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.artist', 1);
    if($allow_artist)
    {
        $ab_table  = Engine_Api::_()->getDbTable('albums', 'mp3music');
        $ab_name   = $ab_table->info('name');
        $select = $ab_table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
                        ->setIntegrityCheck(false);
        $select->from('engine4_users',array("DISTINCT(engine4_users.user_id)","engine4_users.displayname"))
                            ->join($ab_name,"$ab_name.user_id = engine4_users.user_id")
                            ->order("Count($ab_name.album_id) DESC ")
                            ->group("engine4_users.user_id");
        if($limit)
                           $select ->limit($limit);
        $arrArtist = $ab_table->fetchAll($select)->toArray();
    }
    else
    {
         $table  = Engine_Api::_()->getDbtable('artists', 'mp3music');
         $select = $table->select()
                    ->order('artist_id ASC');
         if($limit)
                   $select ->limit($limit); 
         $arrArtist = $table->fetchAll($select);
    }
    return $arrArtist;
  }
  //check version of SE
  public function checkVersionSE()
  {
      $c_table  = Engine_Api::_()->getDbTable('modules', 'core');
      $c_name   = $c_table->info('name');
      $select   = $c_table->select()
                        ->where("$c_name.name LIKE ?",'core')->limit(1);
      
      $row = $c_table->fetchRow($select)->toArray();
      $strVersion = $row['version'];
      $intVersion = (int)str_replace('.','',$strVersion);
      return  $intVersion >= 410?true:false;
    }
  //Create album song
  public function createSong($file, $params=array())
  {
    // upload to storage system
    $song_path = pathinfo($file['name']);
    $params    = array_merge(array(
      'type'        => 'song',
      'name'        => $file['name'],
      'parent_type' => 'mp3music_song',
      'parent_id'   => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'user_id'     => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'extension'   => substr($file['name'], strrpos($file['name'], '.')+1),
    ), $params);
    $song = Engine_Api::_()->storage()->create($file, $params);
    return $song;
  }
  /**
  *  Get Select
  */
  //get list Cat to manage
  public function getCatSelect($params = array())
  {
      $table  = Engine_Api::_()->getDbtable('cats', 'mp3music');
      $select = $table->select()
	  ->where('parent_cat = ?', $params['parent_cat'])
      ->order('title ASC');
      return $select;
  }  
  //Select albums
  public function getAlbumSelect($params = array()) 
  {
    $ab_table  = Engine_Api::_()->getDbTable('albums', 'mp3music');
    $ab_name   = $ab_table->info('name');
    
    $select   = $ab_table->select()
                        ->from($ab_table)
                        ->group("$ab_name.album_id");
    // get all albums have updated from wall
    if (!empty($params['wall']))
      $select->where('composer = 1');
    // get all albums have updated from user
    if (!empty($params['user'])) {
      if (is_object($params['user']))
        $select->where('user_id = ?', $params['user']->getIdentity());
      elseif (is_numeric($params['user']))
        $select->where('user_id = ?', $params['user']);
    } else if (empty($params['admin'])) 
    {
      $select->where('search = 1');
    }
    // SORT
    if (!empty($params['sort'])) {
      $sort = $params['sort'];
      if ('recent' == $sort)
        $select->order('creation_date DESC');
      elseif ('popular' == $sort)
        $select->order("$ab_name.play_count DESC");
    }
    // STRING SEARCH
    if (!empty($params['title']))
    {
     $key = stripslashes($params['title']);  
      $select
          ->where("$ab_name.title_url LIKE ?", "%{$key}%");
    }
     if (!empty($params['owner']))
     {
          $key = stripslashes($params['owner']);  
         $select ->join('engine4_users as u',"u.user_id = $ab_name.user_id",'')
          ->where("u.displayname LIKE ?", "%{$key}%");
     }
    return $select;
  }
  //Select playlists
   public function getPlaylistSelect($params = array()) 
  {
    $p_table  = Engine_Api::_()->getDbTable('playlists', 'mp3music');
    $p_name   = $p_table->info('name');
    $select   = $p_table->select()
                        ->from($p_table)
                        ->group("$p_name.playlist_id");
    // USER SEARCH
    if (!empty($params['user'])) {
      if (is_object($params['user']))
        $select->where('user_id = ?', $params['user']->getIdentity());
      elseif (is_numeric($params['user']))
        $select->where('user_id = ?', $params['user']);
    } else if (empty($params['admin'])){
      $select->where('search = 1')
             ;
    }
    // SORT
    if (!empty($params['sort'])) {
      $sort = $params['sort'];
      if ('recent' == $sort)
        $select->order('creation_date DESC');
      elseif ('popular' == $sort)
        $select->order("$p_name.play_count DESC");
    }
    // STRING SEARCH
    if (!empty($params['search']))
      $select
          ->where("$p_name.title_url LIKE ?", "%{$params['search']}%")
          ;
     if (!empty($params['title']))
      $select
          ->where("$p_name.title_url LIKE ?", "%{$params['title']}%")
          ;
    if (!empty($params['owner']))
     {
      $select ->join('engine4_users as u',"u.user_id = $p_name.user_id",'')
          ->where("u.displayname LIKE ?", "%{$params['owner']}%")
          ;
     }
    return $select;
  }
  
  /**
  * Get Paginator 
  */
  //Paging Category
   public function getCatPaginator($params = array())
  { 
    $catPaginator = Zend_Paginator::factory($this->getCatSelect($params));
    if( !empty($params['page']) )
    {
      $catPaginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $catPaginator->setItemCountPerPage($params['limit']);
    }   
    return $catPaginator;
  } 
  //Paging Album
  public function getAlbumPaginator($params = array())
  { 
    $albumPaginator = Zend_Paginator::factory($this->getAlbumSelect($params));
    if( !empty($params['page']) )
    {
      $albumPaginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $albumPaginator->setItemCountPerPage($params['limit']);
    }   
    return $albumPaginator;
  }
  //Paging Playlist
   public function getPaginator($params = array())
  { 
    $paginator = Zend_Paginator::factory($this->getPlaylistSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }   
    return $paginator;
  }
  //Paging song
  public function getSongPaginator($params = array())
  {   
    $songPaginator = Zend_Paginator::factory(Mp3music_Model_Album::getListSong($params));
    if( !empty($params['page']) )
    {
        //echo $params['page'];
        $songPaginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      
      $songPaginator->setItemCountPerPage($params['limit']);
    }
    return $songPaginator;  
  }
  //Paging top playlist
  public function getTopPlaylistPaginator($params = array())
  {
    $playlistPaginator = Zend_Paginator::factory(Mp3music_Model_Playlist::getPlaylists($params));
    if( !empty($params['page']) )
    {
      $playlistPaginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $playlistPaginator->setItemCountPerPage($params['limit']);
    }
    return $playlistPaginator;
  }
  //paging new album
  public function getNewAlbumPaginator($params = array())      
  {
    $newAlbumPaginator = Zend_Paginator::factory(Mp3music_Model_Album::getAlbums());
    if( !empty($params['page']) )
    {
      $newAlbumPaginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $newAlbumPaginator->setItemCountPerPage($params['limit']);
    }
    return $newAlbumPaginator;
  }
  /**
  * Get all
  */
  //Get all albums
  public function getAlbumRows($params = array())
  {
    return Engine_Api::_()->getDbTable('albums', 'mp3music')->fetchAll( $this->getAlbumSelect($params) );
  }
  //Get all playlists
  public function getPlaylistRows($params = array())
  {
      $playlists = Engine_Api::_()->getDbTable('playlists', 'mp3music')->fetchAll( $this->getPlaylistSelect($params) );
      $temp_playlist = array();
      foreach($playlists as $playlist)
      {
          $flag = true;
          foreach ($playlist->getPSongs() as $song) {
              if ($song->album_song_id == $params['song_id'])
                    $flag = false;
          }
          if($flag == true)
            $temp_playlist[] = $playlist; 
      } 
      return $temp_playlist;
  }
   //get service 
  public function getService()
  {
		$serviceTable = Engine_Api::_()->getDbtable('services', 'storage');
		$nameService = $serviceTable->info('name');
		$select = $serviceTable->select()->where("$nameService.servicetype_id <> ?", 1)
					->where("$nameService.enabled = ?",1)
					->where("$nameService.default = ?",1)
					->limit(1);
		return $serviceTable->fetchRow($select);
  }
  //get list song for player
  public function getServiceSongs($album = null, $idsong = null)
  {
      $musiclist =  $album->getSongs($idsong);
      $songs = array();
      foreach ( $musiclist as $index =>$music)
      {
          $songs[$index]['filepath'] = $music->getFilePath();

          $user = Engine_Api::_()->user()->getViewer();
          $user_id = $user->getIdentity();
          $votes = Mp3music_Model_Rating::checkUservote($music->song_id,$user_id)  ;

          if(count($votes) > 0)
              $songs[$index]['vote'] = number_format($votes[0]->rating);  
          else 
          {   
              $votes_song =  Mp3music_Model_Rating::getVotes($music->song_id); 
              $avgVote = 0;
              foreach($votes_song as $vote_info)
              {
                 $avgVote += $vote_info->rating; 
              }
              if(count($votes_song) > 0)
                  $avgVote = floor($avgVote/count($votes_song));
              $songs[$index]['vote'] = $avgVote;
          }

          if(count($votes) > 0 || $user->getIdentity() <= 0){
              $songs[$index]['isvote'] = false;
          }else{
              $songs[$index]['isvote'] = true;
          }
					
          $allowed_download = (bool) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('mp3music_album', $user, 'is_download');
          if($album->is_download == 1 && $allowed_download == true)
              $is_download = 'true';
          else
              $is_download = 'false';

          $songs[$index]['isdownload'] = $is_download;

          $string = $album->getOwner()->getTitle();

          $songs[$index]['artist'] = $string;  
          $songs[$index]['albumname'] = $album->title;  
          if($user->getIdentity() > 0)
          {   
              $songs[$index]['isadd'] = true;  
          }
          else
          {
              $songs[$index]['isadd'] = false;  
          }        
          $songs[$index]['order'] = $music->order;
          $songs[$index]['song_id'] = $music->song_id;
          $songs[$index]['title'] = $music->title;
          $songs[$index]['play_count'] = $music->play_count;             
      }
      return $songs;
  }
}   
?>
