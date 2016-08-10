<?php
class Mp3music_Model_DbTable_SingerTypes extends Engine_Db_Table
{
  protected $_name     = 'mp3music_singer_types';
  protected $_primary  = 'singertype_id';
  protected $_rowClass = 'Mp3music_Model_SingerType';
}