<?php
class Ynresume_Model_DbTable_Skips extends Engine_Db_Table 
{
    protected $_rowClass = 'Ynresume_Model_Skip';
    
    public function setSkip($resume, $user)
    {
    	$select = $this -> select()
    	->where ("user_id = ? ", $user->getIdentity())
    	->where ("resume_id = ? ", $resume->getIdentity())
    	->limit(1);
    	$row = $this -> fetchRow($select);
    	if (is_null($row))
    	{
    		$row = $this -> createRow();
    		$row -> setFromArray(array(
    			'user_id' => $user->getIdentity(),
    			'resume_id' => $resume->getIdentity(),
    		));
    	}
    	$row -> value = 1;
    	$row -> save();
    }
}
