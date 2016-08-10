<?php
class Ynjobposting_Widget_JobsListingController extends Engine_Content_Widget_Abstract {
 	public function indexAction() {
 		$tableJob = Engine_Api::_() -> getItemTable('ynjobposting_job');
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
 		$params = $this -> _getAllParams();
 		$mode_list = $mode_grid = $mode_map = 1;
		$mode_enabled = array();
		$view_mode = 'list';
		
		if(isset($params['mode_list']))
		{
			$mode_list = $params['mode_list'];
		}
		if($mode_list)
		{
			$mode_enabled[] = 'list';
		}
		
		if(isset($params['mode_grid']))
		{
			$mode_grid = $params['mode_grid'];
		}
		if($mode_grid)
		{
			$mode_enabled[] = 'grid';
		}
		
		if(isset($params['mode_map']))
		{
			$mode_map = $params['mode_map'];
		}
		if($mode_map)
		{
			$mode_enabled[] = 'map';
		}
		
		if(isset($params['view_mode']))
		{
			$view_mode = $params['view_mode'];
		}
		if($mode_enabled && !in_array($view_mode, $mode_enabled))
		{
			$view_mode = $mode_enabled[0];
		}
			
		$this -> view -> mode_enabled = $mode_enabled;
		$class_mode = "ynjobposting-browse-job-viewmode-list";
		switch ($view_mode) 
		{
			case 'grid':
				$class_mode = "ynjobposting-browse-job-viewmode-grid";
				break;
			case 'map':
				$class_mode = "ynjobposting-browse-job-viewmode-maps";
				break;
			default:
				$class_mode = "ynjobposting-browse-job-viewmode-list";
				break;
		}
		
		$this->view->class_mode = $class_mode;
		
	  	//Setup params
	  	$request = Zend_Controller_Front::getInstance()->getRequest();
	    $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
	    $originalOptions = $params;
 		if (!isset($params['page']) || $params['page'] == '0')
		{
			$page = 1;
		}
		else
		{
			$page = (int)$params['page'];
		}
		if(empty($params['status']))
		{
			$params['status'] = 'published';
		}
		//Set curent page
		$this -> view -> paginator = $paginator = $tableJob -> getJobsPaginator($params);
		
		//Getting Job Ids before setting limit for the paginator
		$limit = $this->_getParam('itemCountPerPage', 5);
		$limit = ($limit == '') ? 15 : $limit; 
		$paginator->setItemCountPerPage($limit);
		$paginator->setCurrentPageNumber($page );
		$jobIds = array();
		foreach ($paginator as $job){
			$jobIds[] = $job -> getIdentity();
		}
		$this->view->jobIds = implode("_", $jobIds);
	    $this->view->totalJobs = $paginator->getTotalItemCount();
	    
	    unset($originalOptions['module']);
	    unset($originalOptions['controller']);
	    unset($originalOptions['action']);
	    unset($originalOptions['rewrite']);
	    $this->view->formValues = array_filter($originalOptions);
	}
}