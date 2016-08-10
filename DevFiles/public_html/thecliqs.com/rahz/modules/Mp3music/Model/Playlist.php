<?php
class Mp3music_Model_Playlist extends Core_Model_Item_Abstract
{
  // Interfaces
  public function getRichContent($view = false)
  {
    $videoEmbedded = '';
    // $view == false means that this rich content is requested from the activity feed
    if($view==false){
      $desc   = strip_tags($this->description);
      $desc   = "<div class='music_desc'>".(Engine_String::strlen($desc) > 255 ? Engine_String::substr($desc, 0, 255) . '...' : $desc)."</div>";
      $zview  = Zend_Registry::get('Zend_View');
      $zview->album     = $this;
      $zview->songs        = Engine_Api::_()->mp3music()->getservicesongs($this);
      $videoEmbedded       = $desc . $zview->render('application/modules/Mp3music/views/scripts/_Player.tpl');
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
    $params = array_merge(array('playlist_id' => $this->playlist_id), $params);
    if (isset($this->user_id))
      $params = array_merge(array('user_id' => $this->user_id), $params);
    //echo Zend_Controller_Front::getInstance()->getRouter();
    return Zend_Controller_Front::getInstance()->getRouter()
     ->assemble($params, 'mp3music_playlist', true);
  }
  public function getEditHref($params = array())
  {
    $params = array_merge(array('playlist_id' => $this->playlist_id), $params);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, 'mp3music_edit_playlist', true);
  }
  public function getDeleteHref($params = array())
  {
    $params = array_merge(array(
        'playlist_id' => $this->playlist_id,
        'module' => 'mp3music',
        'controller' => 'playlist',
        'action' => 'delete'), $params);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, 'default', true);
  }
  public function getPlayerHref($params = array())
  {
    #return $this->getHref($params);
    $params = array_merge(array(
        'playlist_id' => $this->playlist_id,
        'module' => 'mp3music',
        'controller' => 'playlist',
        'action' => 'playlist'), $params);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, 'default', true);
  }
  public function getTitle()
  {
      return $this->title;
  }
  public function getMediaType()
  {
	   return 'playlist';
  }
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }
  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }
  public function getCommentCount()
  {
    return $this->comments()->getCommentCount();
  }
  public function getParent()
  {
    return $this->getOwner();
  }
  public function getSongs($file_id=null)
  {
    $as_table = Engine_Api::_()->getDbTable('albumSongs', 'mp3music');
    $as_name  = $as_table->info('name');
    $ps_table = Engine_Api::_()->getDbTable('playlistSongs', 'mp3music'); 
    $ps_name  = $ps_table->info('name');       
    $select = $as_table->select()->from($as_name)->setIntegrityCheck(false);;
    $select->join($ps_name, "$as_name.song_id = $ps_name.album_song_id",array('playlist_id'=>"$ps_name.playlist_id"))
                    ->where('playlist_id = ?', $this->getIdentity())
                    ->order("$ps_name.order ASC");      
   if (!empty($file_id))
     $select->where("$ps_name.file_id = ?", $file_id);
   
    return $as_table->fetchAll($select);
      
    }
   public function getPSongs()
  {
    $table  = Engine_Api::_()->getDbtable('playlistSongs', 'mp3music');
    $select = $table->select()
                    ->where('playlist_id = ?', $this->getIdentity())->order("order ASC");
    return $table->fetchAll($select);
    }
  public function getNewPlaylists($limit = NULL)
  {
    $table  = Engine_Api::_()->getDbtable('playlists', 'mp3music');
    $select = $table->select()
                    ->order('creation_date  DESC')
					->where('search = 1')->limit($limit);
    return $table->fetchAll($select);
  }
  public function getOtherPlaylists($limit = NULL,$playlist_id = Null)
  {
    $table  = Engine_Api::_()->getDbtable('playlists', 'mp3music');
    $select = $table->select()
                    ->order('creation_date  DESC')
                    ->where('search = 1');
    if($playlist_id)
                    $select->where('playlist_id <> ?',$playlist_id);
    if($limit)                
                    $select ->limit($limit);
    return $table->fetchAll($select);
  }
  public function getRelatedPlaylists($limit = NULL,$user_id = Null)
  {
    $table  = Engine_Api::_()->getDbtable('playlists', 'mp3music');
    $select = $table->select()
                    ->order('creation_date  DESC')
                    ->where('search = 1')
                    ->where('user_id = ?',$user_id);
    if($limit)                
                    $select ->limit($limit);
    return $table->fetchAll($select);
  }
  public function getCountPlaylists($user = null)
  {
        $table  = Engine_Api::_()->getDbtable('playlists', 'mp3music');
        $select = $table->select();
        if($user)
            $select->where ('user_id = ?', $user->getIdentity());
        return count($table->fetchAll($select));
   }
  public function getCountSongs($user)
  {
        $table  = Engine_Api::_()->getDbtable('playlists', 'mp3music');
        $select = $table->select()
                        ->where ('user_id = ?', $user->getIdentity());
    $count_song = 0;
    $playlists = $table->fetchAll($select);
    foreach($playlists as $playlist)
    {
        $songs = $playlist->getSongs();
            foreach($songs as $song)
                $count_song ++;
    }
     return $count_song;
   }
  public function getPlaylists($params = array())
  {
    $table  = Engine_Api::_()->getDbtable('playlists', 'mp3music');
    $select = $table->select()
                    ->order('creation_date  DESC')
					->where('search = 1')
                   
                    ;
    if($params['title'])
    {
               $select ->where('title LIKE ?',"%{$params['title']}%") 
               ->order('creation_date  DESC') ;    
    }
    return $table->fetchAll($select);
  }
	public function getSong($file_id)
	{
		$songs = $this -> getSongs($file_id);
		return $songs -> current();
	}

	public function getSongIDFirst()
	{
		$songs = $this -> getSongs();
		if ($songs && $songs -> current())
			return $songs -> current() -> song_id;
		else
			return 0;
	}
  public function addSong($file_id,$album_songID)
  {
    if ($file_id instanceof Storage_Model_File)
      $file = $file_id;
    else
      $file = Engine_Api::_()->getItem('storage_file', $file_id);
    if ($file) {
      $playlist_song = Engine_Api::_()->getDbtable('playlistSongs', 'mp3music')->createRow();
      $playlist_song->playlist_id = $this->getIdentity();
      $playlist_song->file_id     = $file->getIdentity();
      $playlist_song->album_song_id  =  $album_songID;
      $playlist_song->save();
    }
    return $this;
  }
  public function setProfile()
  {
      $table = Engine_Api::_()->getDbtable('playlists', 'mp3music');
      $where = $table->getAdapter()->quoteInto('user_id = ?', $this->user_id);
    $table ->update(array(
         'profile' => 0,
      ), $where
      );
    $this->profile = !$this->profile;
    $this->save();
  }
  function isViewable()  { return $this->authorization()->isAllowed(null, 'view'); }
  function isEditable()  { return $this->authorization()->isAllowed(null, 'edit'); }
  function isDeletable() { return $this->authorization()->isAllowed(null, 'delete'); }
}