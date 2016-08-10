<?php
class Ynbusinesspages_Model_DbTable_Options extends Engine_Db_Table
{
	protected $_name = 'ynbusinesspages_contact_fields_options';
	protected $_rowClass = 'Ynbusinesspages_Model_Option';
    
    public function getOptions($field_id) {
        if (!$field_id) return array();
        $select = $this->select()->where('field_id = ?', $field_id);
        $rows = $this->fetchAll($select);
        $result = array();
        foreach($rows as $row) {
            $result[$row->option_id] = $row->label;
        }
        return $result;
    }
	
	public function deleteItem($field_id)
	{
		$select = $this -> select() -> where('field_id = ?', $field_id);
		$rows = $this -> fetchAll($select);
		foreach($rows as $row)
		{
			$row -> delete();
		}
	}
	
	public function getLabel($option_id, $field_id)
	{
		$select = $this -> select() -> where('option_id = ?', $option_id) -> where('field_id = ?', $field_id) -> limit(1);
		$result = $this -> fetchRow($select);
		return $result -> label;
		
	}
}