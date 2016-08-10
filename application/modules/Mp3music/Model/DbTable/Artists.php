<?php
class Mp3music_Model_DbTable_Artists extends Engine_Db_Table
{
   protected $_name     = 'mp3music_artists';
   protected $_primary  = 'artist_id';
   protected $_rowClass = 'Mp3music_Model_Artist';
}