<?php
class Ynjobposting_Widget_CompanyProfileJobsController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject('ynjobposting_company'))
		{
			return $this->setNoRender();
		}
		$this -> view -> company = $company = Engine_Api::_()->core()->getSubject('ynjobposting_company');
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($company -> user_id == $viewer -> user_id)
		{
			$jobs = $company -> getJobs();
		}
		else 
		{
			$jobs = $company -> getJobsWithStatus('published');
		}
		$this -> view -> jobs = $jobs;
		
		// Set item count per page and current page number
		$this->view->paginator = $paginator = Zend_Paginator::factory($jobs);
	    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
	    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	
	    // Do not render if nothing to show
	    if( $paginator->getTotalItemCount() <= 0) {
			return $this->setNoRender();
	    }
	    
		// Add count to title if configured
	    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
			$this->_childCount = $paginator->getTotalItemCount();
	    }
	}
	
	public function getChildCount()
	{
    	return $this->_childCount;
	}
}
