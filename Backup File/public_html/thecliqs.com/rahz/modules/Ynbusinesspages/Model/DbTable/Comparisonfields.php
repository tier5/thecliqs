<?php
class Ynbusinesspages_Model_DbTable_Comparisonfields extends Engine_Db_Table {
    protected $_rowClass = 'Ynbusinesspages_Model_Comparisonfield';
    
    public function getAvailableComparisonFields() {
        $select = $this->select()->where('`show` = ?', 1)->order('order ASC');
        $result = $this->fetchAll($select);
        return $result;
    }
}