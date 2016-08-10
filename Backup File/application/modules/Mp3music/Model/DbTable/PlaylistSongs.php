<?php
class Mp3music_Model_DbTable_PlaylistSongs extends Engine_Db_Table
{
  protected $_name     = 'mp3music_playlist_songs';
  protected $_primary  = 'song_id';
  protected $_rowClass = 'Mp3music_Model_PlaylistSong';
}