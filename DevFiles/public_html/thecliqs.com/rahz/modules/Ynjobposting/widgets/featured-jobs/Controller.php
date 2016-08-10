<?php
class Ynjobposting_Widget_FeaturedJobsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$featureTbl = Engine_Api::_()->getDbTable('features', 'ynjobposting');
		$featureTblName = $featureTbl -> info('name');
        
        $this->view->viewer = Engine_Api::_()->user()->getViewer();
		$jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
		$jobTblName = $jobTbl -> info('name');
		$select = $jobTbl -> select() -> setIntegrityCheck(false)
		-> from($jobTblName)
		-> joinLeft($featureTblName, "{$jobTblName}.job_id = {$featureTblName}.job_id", null)
		-> where("{$jobTblName}.status = ?", 'published')
		-> where("{$featureTblName}.active = ?", '1')
		;
		$this -> view -> jobs = $jobs = $jobTbl -> fetchAll($select);
		if (count($jobs) == 0){
			return $this->setNoRender();
		}
        
	}
}
