<?php

class Ynresume_Model_DbTable_Views extends Engine_Db_Table
{
	public function trackingView($uid, $resumeID)
	{
		$now =  date("Y-m-d H:i:s");
		$select = $this -> select() 
						-> where('user_id = ?', $uid)
						-> where('resume_id = ?', $resumeID)
						-> limit(1);
		$row = $this -> fetchRow($select);
		if(empty($row))
		{
			$row = $this -> createRow();
			$row -> user_id = $uid;
			$row -> resume_id = $resumeID;
			$row -> creation_date = $now;
			$row -> modified_date = $now;
		}
		else 
		{
			$row -> modified_date = $now;
		}
		$row -> save();
	}
	
	public function getViewersPaginator($resume, $isWidget) {
        $paginator = Zend_Paginator::factory($this -> getViewersSelect($resume, $isWidget));
        return $paginator;
    }
	
	public function getViewersSelect($resume, $isWidget)
	{
		$select = $this -> select() 
						-> where('resume_id = ?', $resume -> getIdentity());
		$select -> order('modified_date DESC');
		if(!$resume -> serviced)
		{
			$select -> limit(2);
		}
		else 
		{
			if($isWidget)
			{
				$select -> limit(3);
			}	
		}		
		return $this -> fetchAll($select);
	}
	
	public function getCountViewer($resume)
	{
		$select = $this -> select();
     	$select -> from($this, array('count(*) as amount'));
		$select -> where('resume_id = ?', $resume -> getIdentity());
		$row = $this -> fetchRow($select);
		return ($row -> amount);
	}
}
