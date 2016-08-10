<?php
class Mp3music_Model_PlaylistSong extends Core_Model_Item_Abstract
{
  public function getShortType()
  {
    return 'song'; 
  }
  public function getTitle()
  {
    if (!empty($this->title))
      return $this->title;
    else
      return 'Untitled Song';
  }
  public function setTitle($newTitle)
  {
    $this->title = $newTitle;
    $this->save();
    return $this;
  }
  public function getFilePath()
  {
    $file = Engine_Api::_()->getItem('storage_file', $this->file_id);
    if( $file ) {
      return $file->map();
    }
  }
  public function getHref()
  {
    return $this->getParent()->getHref();
  }
  public function getParent()
  {
    return Engine_Api::_()->getItem('mp3music_playlist', $this->playlist_id);
  }
  public function getMediaType()
  {
	   return 'song';
  }
  public function getRichContent($view = false)
  {
    $playlist      = $this->getParent();
    $videoEmbedded = '';

    // $view == false means that this rich content is requested from the activity feed
    if($view==false){
      $desc   = strip_tags($playlist->description);
      $desc   = "<div class='music_desc'>".(Engine_String::strlen($desc) > 255 ? Engine_String::substr($desc, 0, 255) . '...' : $desc)."</div>";
      $zview  = Zend_Registry::get('Zend_View');
      $zview->playlist     = $playlist;
      $zview->songs        = array($this);
      $zview->short_player = true;
	  $zview->type = 'playlist';
      $videoEmbedded       = $desc . $zview->render('application/modules/Mp3music/views/scripts/_Player.tpl');
    }

    return $videoEmbedded;
  }
  /**
   * Returns languagified play count
   */
  public function playCountLanguagified()
  {
    return vsprintf(Zend_Registry::get('Zend_Translate')->_(array('%s play', '%s play', $this->play_count)),
                  Zend_Locale_Format::toNumber($this->play_count)                  
                  );
  }
  public function getSongID($playlist_id,$album_song_id)
  {
     $table  = Engine_Api::_()->getDbtable('playlistSongs', 'mp3music');
     $select = $table->select()
                      ->where('album_song_id = ?', $album_song_id)
                      ->where('playlist_id = ?',$playlist_id)
                      ->limit(1); 
    $songs =  $table->fetchAll($select);
    return $songs[0]->song_id;
  }
}