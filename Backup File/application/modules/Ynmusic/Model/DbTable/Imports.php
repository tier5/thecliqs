<?php
class Ynmusic_Model_DbTable_Imports extends Engine_Db_Table {
	protected $_name = 'ynmusic_imports';
	
	public function updateItem($item, $from, $status) {
		$select = $this->select()
			->where('from_id = ?', $from->getIdentity())
			->where('from_type = ?', $from->getType());
		$row = $this->fetchRow($select);
		if (!$row) 
		{
			if($from -> getType == 'music_playlist')
			{
				$user_id = $from -> owner_id;
			}
			else {
				$user_id = $from->user_id;
			}
			$row = $this->createRow();
			$row->user_id = $user_id;
			$row->from_type = $from->getType();
			$row->from_id = $from->getIdentity();
			$row->creation_date = date('Y-m-d H:i:s');
		}
		$row->item_type = $item->getType();
		$row->item_id = $item->getIdentity();
		$row->modified_date = date('Y-m-d H:i:s');
		$row->status = $status;
		$row->save();
	}
	
	public function addItem($from) 
	{
		if($from -> getType() == 'music_playlist')
		{
			$user_id = $from -> owner_id;
		}
		else {
			$user_id = $from->user_id;
		}
		$row = $this->createRow();
		$row->user_id = $user_id;
		$row->item_type = '';
		$row->item_id = 0;
		$row->from_type = $from->getType();
		$row->from_id = $from->getIdentity();
		$row->creation_date = date('Y-m-d H:i:s');
		$row->status = 'processing';
		$row->save();
	}

	public function getImportedIdsByType($type) {
		return $this->select()->from($this->info('name'), 'from_id')->where('from_type = ?', $type)->query()->fetchAll(Zend_Db::FETCH_COLUMN);
	}
}
