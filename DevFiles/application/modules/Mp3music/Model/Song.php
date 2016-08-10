<?php
class Mp3music_Model_Song extends Core_Model_Item_Abstract
{
  // ID3 reader
  public function readID3($mp3_filename)
  {
      // get file's ID3 tags
      set_include_path(
          get_include_path() . PS .
          APPLICATION_PATH . DS . 'application' . DS . 'libraries' . DS . 'php-reader' . PS .
          APPLICATION_PATH . DS . 'application' . DS . 'libraries' . DS . 'php-reader' . DS . 'ID3');
      require_once('libraries/php-reader/ID3v1.php');
      require_once('libraries/php-reader/ID3v2.php');
      $song_id3 = array();
      if (is_numeric($mp3_filename)) {
          $file = Engine_Api::_()->getItem('storage_file', $mp3_filename);
          if ($file)
            $mp3_filename = $file->storage_path;
          else
            return;
      }
      try {
        $id3 = new ID3v2($mp3_filename);
        if (!$id3)
          $song_id3 = array(
            'id3_v'   => 2,
            'title'   => $id3->TIT2->getText(),
            'artist'  => $id3->TPE1->getText(),
            /*
            'album'   => $id3->album,
            'date'    => $id3->year,
            'track'   => $id3->track,
            'comment' => $id3->comment,
            */
          );
        else {
          $id3 = new ID3v1($mp3_filename);
          if ($id3)
            $song_id3   = array(
              'id3_v'   => 1,
              'title'   => $id3->title,
              'artist'  => $id3->artist,
              'album'   => $id3->album,
              'date'    => $id3->year,
              'track'   => $id3->track,
              'comment' => $id3->comment,
            );
        }
        echo "<pre>";print_r($song_id3);
        return $song_id3;
      } catch (Exception $e) {
        throw $e;
      }
  }
}