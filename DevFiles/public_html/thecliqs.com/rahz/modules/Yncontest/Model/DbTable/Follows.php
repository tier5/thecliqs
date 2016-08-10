<?php

class Yncontest_Model_DbTable_Follows extends Engine_Db_Table
{

    protected $_rowClass = 'Yncontest_Model_Follow';
    
    public function getUserFolowContest($contest_id ){    	
    	$select = $this -> select() -> where('contest_id = ?', $contest_id);
    	$results = $this->fetchAll($select);
    	return $results;
    	 
    }
}
