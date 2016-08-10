<?php
class Ynresume_Widget_InterestedJobsController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
		$checkModuleJob = Engine_Api::_() -> hasModuleBootstrap('ynjobposting');
		if(empty($checkModuleJob))
		{
			return $this -> setNoRender();
		}
	    $viewer = Engine_Api::_()->user()->getViewer();
     	if(!$viewer -> getIdentity())
		{
			return $this -> setNoRender();
		}
		$resumeTable = Engine_Api::_() -> getItemTable('ynresume_resume');
		$this -> view -> resume = $resume = $resumeTable -> getResume($viewer -> getIdentity());
		if(empty($resume))
		{
			return $this -> setNoRender();
		}
		elseif($resume -> industry_id == 0)
		{
			return $this -> setNoRender();
		}
		$jobTable = Engine_Api::_() -> getItemTable('ynjobposting_job');
		$job_industry_id = Engine_Api::_() -> getDbTable('industrymaps', 'ynresume') -> getRowByIndustryId($resume -> industry_id) -> job_industry_id;
		if(empty($job_industry_id))
		{
			return $this -> setNoRender();
		}
		$params['industry_id'] = $job_industry_id;
		$paginator = $jobTable -> getJobsPaginator($params);
		if ($paginator->getTotalItemCount() <= 0) {
            $this->setNoRender();
        }
		$itemCountPerPage = $this -> _getParam('itemCountPerPage', 5);
        if (!$itemCountPerPage) {
            $itemCountPerPage = 5;
        }
		$paginator -> setItemCountPerPage($itemCountPerPage);
		$this -> view -> paginator = $paginator;
	}
}
