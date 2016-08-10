<?php
class Ynresume_Model_DbTable_EndorseNotify extends Engine_Db_Table
{
	protected $_rowClass = 'Ynresume_Model_EndorseNotify';
	
	public function saveNotify($resume, $value)
	{
		$select = $this -> select() -> where("resume_id = ?", $resume -> getIdentity()) -> limit(1);
		$row = $this -> fetchRow($select);
		if (is_null($row))
		{
			$row = $this -> createRow();
			$row -> resume_id = $resume -> getIdentity();
		}
		if ($value == true){
			$row -> value = 1; 
		}
		else 
		{
			$row -> value = 0;
		}
		$row -> save();
	}
	
	public function needNotify($resume)
	{
		$select = $this -> select() -> where("resume_id = ?", $resume -> getIdentity()) -> limit(1);
		$row = $this -> fetchRow($select);
		if (is_null($row))
		{
			return false;
		}
		if ($row -> value == '0')
		{
			return false;
		}
		return true;
	}
}