<?php
class Mp3music_Form_Playlist extends Engine_Form
{
  public $playlist;
  public $song;
  public function init()
  {
    // Init form
    $this
      ->setTitle('Add Song To Playlist')
      ->setAttrib('id',      'form-playlist-append')
      ->setAttrib('name',    'playlist_add')
      ->setAttrib('class',   '')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format'=>'smoothbox'), 'mp3music_playlist_append'))
      ;
    // Init playlist
    //Lay id cua playlist dang dc thuc hien  
    $orig_playlist = Zend_Controller_Front::getInstance()->getRequest()->getParam('playlist_id');  
    $song_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('song_id');  
    $obj = new Mp3music_Api_Core();
    foreach ($obj->getPlaylistRows(array('user'=>Engine_Api::_()->user()->getViewer(),'song_id'=>$song_id)) as $this_playlist)
      if ($this_playlist->playlist_id != $orig_playlist)
         $playlists[ $this_playlist->playlist_id ] = $this_playlist->getTitle();
  
    $playlists[0]  = Zend_Registry::get('Zend_Translate')->_('Create New Playlist');
    $this->addElement('Select', 'playlist_id', array(
      'label' => 'Choose Playlist',
      'multiOptions' => $playlists,
      'onchange' => "updateTextFields()",
    ));
    // Init new playlist field
    $this->addElement('Text', 'title', array(
      'label' => 'Playlist Name(*)',
      'style' => '',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    // Init hidden file IDs
    $this->addElement('Hidden', 'song_id', array(
      'value' => Zend_Controller_Front::getInstance()->getRequest()->getParam('song_id'),
    ));
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Song',
      'type' => 'submit',
      'decorators' => array(
          array('ViewScript', array(
                'viewScript' => '_formButtonCancel.tpl',
                'class'      => 'form element'
          ))
      ),
    ));
  }
  public function saveValues()
  {
  $playlist = null;
    $values = $this->getValues();
    if ($values['playlist_id'] != 0)
      $playlist = $this->playlist = Engine_Api::_()->getItem('mp3music_playlist', $values['playlist_id']); 
    else {
     $user = Engine_Api::_()->user()->getViewer();
      $max_playlists = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('mp3music_playlist', $user, 'max_playlists');
       if($max_playlists == "")
             {
                  $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
                 $maselect = $mtable->select()
                    ->where("type = 'mp3music_playlist'")
                    ->where("level_id = ?",$user->level_id)
                    ->where("name = 'max_playlists'");
                  $mallow_a = $mtable->fetchRow($maselect);          
                  if (!empty($mallow_a))
                    $max_playlists = $mallow_a['value'];
                  else
                     $max_playlists = 10;
             }
      $cout_playlist = Mp3music_Model_Playlist::getCountPlaylists($user);
      if($cout_playlist < $max_playlists)
      {
	      if (empty($values['title'])) {
	        $this->getElement('title')->addError('Required !');
	        return false;
	      }
	      $playlist = Engine_Api::_()->getDbtable('playlists', 'mp3music')->createRow();
	      $playlist->title       = trim($values['title']);
	      $str = $playlist->title;
	      $str= strtolower($str);
	      $str= preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/","a",$str);  
	      $str= preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/","e",$str);  
	      $str= preg_replace("/(ì|í|ị|ỉ|ĩ)/","i",$str);  
	      $str= preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/","o",$str);  
	      $str= preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/","u",$str);  
	      $str= preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/","y",$str);  
	      $str= preg_replace("/(đ)/","d",$str);  
	      $str= preg_replace("/(!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_)/","-",$str); 
	      $str= preg_replace("/(-+-)/","-",$str); //thay thế 2- thành 1- 
	      $str= preg_replace("/(^\-+|\-+$)/","",$str); 
	      $str= preg_replace("/(-)/"," ",$str); 
	      $playlist->title_url = $str;
	      $playlist->user_id    = Engine_Api::_()->user()->getViewer()->getIdentity();
	      $playlist->search      = 1;
	      $playlist->save();
	      $playlist = $this->playlist = Engine_Api::_()->getItem('mp3music_playlist', $playlist->playlist_id);
	      // Add action and attachments
	      $auth = Engine_Api::_()->authorization()->context;
	      $auth->setAllowed($playlist, 'registered', 'comment', true);
	      foreach( array('everyone', 'registered', 'member') as $role )
	        $auth->setAllowed($playlist, $role, 'view', true);
	      // Only create activity feed item if "search" is checked
	      if ($playlist->search) 
	      {
	        $activity = Engine_Api::_()->getDbtable('actions', 'activity');
	        $action   = $activity->addActivity(
	            Engine_Api::_()->user()->getViewer(),
	            $playlist,
	            'mp3music_playlist_new'
	        );
			if(null !== $action)
	        	$activity->attachActivity($action, $playlist);
	      }
      }
      else
      {
        echo '<script type="text/javascript"> alert("Number of playlist is limited!") </script>';
        return false;
      }
    }
    if( $playlist && $values['song_id'] > 0 )
    { 
      $this->song = Engine_Api::_()->getItem('mp3music_album_song', $values['song_id']);
      if ($playlist->getIdentity() && $this->song) {
        // ownership permission
        if ($playlist->user_id != Engine_Api::_()->user()->getViewer()->getIdentity()) {
          $this->getElement('playlist_id')->addError('This playlist does not belong to you.');
          return false;
        }
        // already exists in playlist
        /*foreach ($playlist->getSongs() as $song) {
          if ($song->file_id == $this->song->file_id) {
            $this->getElement('playlist_id')->addError('This playlist already has this song.');
            return false;
          }
        }    */                         
        $user = Engine_Api::_()->user()->getViewer();
        $max_songs =  Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('mp3music_playlist', $user, 'max_songs');
        if($max_songs == "")
             {
                  $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
                 $maselect = $mtable->select()
                    ->where("type = 'mp3music_playlist'")
                    ->where("level_id = ?",$user->level_id)
                    ->where("name = 'max_songs'");
                  $mallow_a = $mtable->fetchRow($maselect);          
                  if (!empty($mallow_a))
                    $max_songs = $mallow_a['value'];
                  else
                     $max_songs = 10;
             }
        $song_count = count($playlist->getSongs());
        if($song_count < $max_songs)
            $playlist->addSong($this->song->file_id,$values['song_id']);
        else
        {
                 echo '<script type="text/javascript"> alert("Number of songs in a playlist is limited!") </script>';
                 return false;
        }
        
      }
      return true; 
    } else
      return false;
  } // end function saveValues()

}
