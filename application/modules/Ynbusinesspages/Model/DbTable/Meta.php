<?php
class Ynbusinesspages_Model_DbTable_Meta extends Engine_Db_Table
{
	protected $_name = 'ynbusinesspages_contact_fields_meta';
	protected $_rowClass = 'Ynbusinesspages_Model_Meta';
	
	public function getField($field_id)
	{
		$select = $this -> select() -> where ("field_id = ? ", $field_id) -> limit(1);
		$field = $this -> fetchRow($select);
		return $field;
	}
	
	public function deleteField($field_id)
	{
		$field = $this -> getField($field_id);
		if($field)
		{
			$tableOptions = Engine_Api::_() -> getDbTable('options', 'ynbusinesspages');
			$tableOptions -> deleteItem($field_id);
			$field -> delete();
		}
	}
	
	public function getFields($business)
	{
		if (is_null($business))
		{
			return array();
		}
		$select = $this -> select() -> where ("business_id = ? ", $business->getIdentity());
		$fields = $this -> fetchAll($select);
		return $fields;
	}
	
	public function setEnabled($ids, $value)
	{
		if (!is_array($ids)){
			return;
		}
		if (!count($ids))
		{
			return;
		}
		if ($value != '1' && $value != '0')
		{
			return;
		}
		foreach ($ids as $id){
			$this->update(array(
				'enabled' => $value
			), array(
				"field_id = ?" => $id
			));
		}
	}
	
	public function setRequired($ids, $value)
	{
		if (!is_array($ids)){
			return;
		}
		if (!count($ids))
		{
			return;
		}
		if ($value != '1' && $value != '0')
		{
			return;
		}
		foreach ($ids as $id){
			$this->update(array(
				'required' => $value
			), array(
				"field_id = ?" => $id
			));
		}
	}
}