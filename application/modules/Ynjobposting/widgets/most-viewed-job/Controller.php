<?php
class Ynjobposting_Widget_MostViewedJobController extends Engine_Content_Widget_Abstract
{
	public function indexAction() 
    {
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
    	
		$searchParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
    	if (!isset($searchParams['page']) || $searchParams['page'] == '0')
		{
			$page = 1;
		}
		else
		{
			$page = (int)$searchParams['page'];
		}
		$this->view->class_mode = $class_mode;
		$searchParams['status'] = 'published';
		$searchParams['order'] = 'view_count';
		$searchParams['direction'] = 'DESC';
		$this->view->paginator = $paginator = $jobTbl->getJobsPaginator($searchParams);
   		$jobIds = array();
		foreach ($paginator as $job){
			$jobIds[] = $job -> getIdentity();
		}
		
		$limit = $this->_getParam('itemCountPerPage', 5);
		if(!is_numeric($limit) || $limit <=0) $limit = 5;
		$paginator->setItemCountPerPage($limit);
		$paginator->setCurrentPageNumber($page );
		$this->view->jobIds = implode("_", $jobIds);
		$this->view->formValues = array_filter($searchParams);
		$this->getElement()->removeDecorator('Title');
    }
}
