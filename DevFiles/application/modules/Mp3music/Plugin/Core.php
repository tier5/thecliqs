<?php
class Mp3music_Plugin_Core
{
  public function onStatistics($event)
  {
    $table   = Engine_Api::_()->getDbTable('albums', 'mp3music');
    $select  = new Zend_Db_Table_Select($table);
    $select->from($table->info('name'), array('COUNT(*) AS count'));
    $event->addResponse($select->query()->fetchColumn('count'), 'music albums');
      
    $table   = Engine_Api::_()->getDbTable('playlists', 'mp3music');
    $select  = new Zend_Db_Table_Select($table);
    $select->from($table->info('name'), array('COUNT(*) AS count'));
    $event->addResponse($select->query()->fetchColumn('count'), 'playlist');
    
    
    $table   = Engine_Api::_()->getDbTable('albumSongs', 'mp3music');
    $select  = new Zend_Db_Table_Select($table);
    $select->from($table->info('name'), array('COUNT(*) AS count'));
    $event->addResponse($select->query()->fetchColumn('count'), 'song');
  }

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete Playlists
      $playlistTable = Engine_Api::_()->getDbtable('playlists', 'mp3music');
      $playlistSelect = $playlistTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $playlistTable->fetchAll($playlistSelect) as $playlist ) {
        foreach ($playlist->getPSongs() as $song)
          $song->delete();
        $playlist->delete();
      }
      // Delete Albums
      $albumTable = Engine_Api::_()->getDbtable('albums', 'mp3music');
      $albumSelect = $albumTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $albumTable->fetchAll($albumSelect) as $album ) {
        foreach ($album->getSongs() as $song)
          $song->deleteUnused();
        $album->delete();
      }
    }
  }
}