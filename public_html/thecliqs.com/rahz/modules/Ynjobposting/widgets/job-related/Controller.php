<?php
class Ynjobposting_Widget_JobRelatedController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		
		if (!Engine_Api::_()->core()->hasSubject('ynjobposting_job'))
		{
			return $this->setNoRender();
		}
		$this -> view -> job = $job = Engine_Api::_()->core()->getSubject('ynjobposting_job');
		
		$jobTbl = Engine_Api::_() -> getItemTable('ynjobposting_job');
		$jobTblName = $jobTbl -> info('name');
		$limit = $this->_getParam('itemCountPerPage', 5);
		
		$select = $jobTbl -> select()
		-> where ("industry_id  = ?", $job -> industry_id)
		-> where ("job_id  <> ?", $job -> job_id)
		-> where ("status  = ?", 'published')
		-> limit ($limit);
		
		$jobs = $jobTbl -> fetchAll($select);
		
		if (!count($jobs))
		{
			return $this->setNoRender();
		}
		$this -> view -> jobs = $jobs; 
	}
}
