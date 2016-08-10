<?php
class Ynjobposting_Model_DbTable_Options extends Engine_Db_Table
{
	protected $_name = 'ynjobposting_submission_fields_options';
	protected $_rowClass = 'Ynjobposting_Model_Option';
    
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
}