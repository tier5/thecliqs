<?php
class Ynsocialads_Model_DbTable_Virtuals extends Engine_Db_Table {
    protected $_rowClass = 'Ynsocialads_Model_Virtual';
	public function GetRowByUser($user_id)
	{
		return $this->fetchRow($this->select()->where('user_id = ?', $user_id)->limit(1));
	}
}