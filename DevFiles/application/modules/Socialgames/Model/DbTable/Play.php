<?php

class Socialgames_Model_DbTable_Play extends Engine_Db_Table{

	protected $_rowClass = 'Socialgames_Model_Play';
	
	public function getUsers($game_id)
	{
		$table = Engine_Api::_()->getDbtable('users', 'user');
		$fName = $table->info('name');
		$ftable = Engine_Api::_()->getDbtable('play', 'socialgames');
		$rName = $ftable->info('name');
		$select = $table->select()->from($fName)->joinLeft($rName, "$rName.user_id = $fName.user_id", NUll)->where("$rName.game_id = ?",$game_id);
		return $table->fetchAll($select);
	}
	public function getTopUsers($game_id)
	{
		$table = Engine_Api::_()->getDbtable('users', 'user');
		$fName = $table->info('name');
		$ftable = Engine_Api::_()->getDbtable('play', 'socialgames');
		$rName = $ftable->info('name');
		$select = $table->select()->from($fName)->setIntegrityCheck(false)
		->joinLeft($rName, "$rName.user_id = $fName.user_id", array("total_played"=>"COUNT($rName.game_id )"))
		->order("total_played DESC")
		->group("$rName.user_id")
		->limit(5);
		
		return $table->fetchAll($select);
	}
	public function getUserPlayed($user_id)
	{
		$table = Engine_Api::_()->getDbtable('games', 'socialgames');
		$fName = $table->info('name');
		$ftable = Engine_Api::_()->getDbtable('play', 'socialgames');
		$rName = $ftable->info('name');
		$select = $table->select()->from($fName)->setIntegrityCheck(false)
		->joinLeft($rName, "$fName.game_id = $rName.game_id")
		->where("$rName.user_id = ?",$user_id);
		
		
		
		return $table->fetchAll($select);
	}
}