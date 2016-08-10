<?php
class Mp3music_Model_AlbumSong extends Core_Model_Item_Abstract
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
  public function getMediaType()
	{
	   return 'song';
	}
  public function getHref()
  {
    return $this->getParent()->getHref();
  }
  public function getParent()
  {
    return Engine_Api::_()->getItem('mp3music_album', $this->album_id);
  }
  public function getOwner()
  {
    $album = Engine_Api::_()->getItem('mp3music_album', $this->album_id);
	return Engine_Api::_()->getItem('user', $album->user_id);
  }
  public function getRichContent($view = false)
  {
    $album      = $this->getParent();
    $videoEmbedded = '';
    // $view == false means that this rich content is requested from the activity feed
    if($view == false){
      $desc   = strip_tags($album->description);
      $desc   = "<div class='music_desc'>".(Engine_String::strlen($desc) > 255 ? Engine_String::substr($desc, 0, 255) . '...' : $desc)."</div>";
      $zview  = Zend_Registry::get('Zend_View');
      $zview->album     = $album;
      $zview->songs        = Engine_Api::_() -> mp3music() -> getservicesongs($album, $this->song_id);
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
  /**
   * Deletes songs from the Storage engine if no other playlists are
   * using the file, and from the playlist
   *
   * @return null
   */
  public function deleteUnused()
  {
    $file   = Engine_Api::_()->getItem('storage_file', $this->file_id);
    if ($file) {
      $table  = Engine_Api::_()->getDbtable('albumSongs', 'mp3music');
      $select = $table->select()
                      ->where('file_id = ?', $file->getIdentity())
                      ->limit(1);
      $count  = count( $table->fetchAll($select) );
      if ($count >= 0)
        $file->delete();
    }
    $this->delete();
  }
   public function getInfo($song)
  {
      $file   = Engine_Api::_()->getItem('storage_file', $song->file_id);
      $path = $file->storage_path;
      $m = new Mp3music_Api_Mp3($path);
      $a = $m->get_metadata();
      return $a;
  }
}