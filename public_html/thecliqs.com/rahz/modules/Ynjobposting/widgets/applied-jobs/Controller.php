<?php
class Ynjobposting_Widget_AppliedJobsController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	public function indexAction()
	{
		if( !Engine_Api::_()->core()->hasSubject('user') ) 
		{
			return $this->setNoRender();
		} 
		$user = Engine_Api::_()->core()->getSubject('user');
		if (!$user -> getIdentity())
		{
			return $this->setNoRender();
		}
		
		$applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
		$applyTblName = $applyTbl -> info('name');

		$jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
		$jobTblName = $jobTbl -> info('name');
		
		
		$select = $jobTbl -> select() -> setIntegrityCheck(false)
		-> from($jobTblName)
		-> join($applyTblName, "{$jobTblName}.job_id = {$applyTblName}.job_id AND {$applyTblName}.user_id = {$user->getIdentity()}")
		;
		
		$this -> view -> jobs = $jobs = $jobTbl -> fetchAll($select);
		if (count($jobs) == 0)
		{
			return $this -> setNoRender();
		}
		
		// Add count to title if configured
	    if(count($jobs) > 0 ) {	    	 
	       $this->_childCount = count($jobs);	
	    }
	}
	
	public function getChildCount() {
        return $this->_childCount;
    }
}
