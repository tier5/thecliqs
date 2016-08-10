<?php

class Yncontest_Model_DbTable_Entriesfollows extends Engine_Db_Table
{

    protected $_rowClass = 'Yncontest_Model_Entriesfollow';
    
    public function getUserFolowEntries($entry_id ){
    	$select = $this -> select() -> where('entry_id = ?', $entry_id);
    	$results = $this->fetchAll($select);
    	return $results;
    
    }
}
