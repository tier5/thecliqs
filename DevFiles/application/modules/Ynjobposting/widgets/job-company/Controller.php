<?php
class Ynjobposting_Widget_JobCompanyController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject('ynjobposting_job'))
		{
			return $this->setNoRender();
		}
		$this -> view -> job = $job = Engine_Api::_()->core()->getSubject('ynjobposting_job');
		if (is_null($job))
		{
			$this -> setNoRender();
		}
		$jobTbl = Engine_Api::_() -> getItemTable('ynjobposting_job');
		$jobTblName = $jobTbl -> info('name');
		$this -> view -> limit = $limit = $this->_getParam('itemCountPerPage', 5);
		$this -> view -> company = $job -> getCompany();
		$select = $jobTbl -> select()
		-> where ("company_id  = ?", $job -> company_id)
		-> where ("job_id  <> ?", $job -> job_id)
		-> where ("status  = ?", 'published');
		
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage($limit);
		$paginator->setCurrentPageNumber(1);
		
		if ($paginator->getTotalItemCount() == 0)
		{
			return $this->setNoRender();
		}
	}
}
