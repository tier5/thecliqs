<?php
class Ynjobposting_Widget_CompanyListingController extends Engine_Content_Widget_Abstract 
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
		$class_mode = "ynjobposting-browse-company-viewmode-list";
		switch ($view_mode) 
		{
			case 'grid':
				$class_mode = "ynjobposting-browse-company-viewmode-grid";
				break;
			case 'map':
				$class_mode = "ynjobposting-browse-company-viewmode-maps";
				break;
			default:
				$class_mode = "ynjobposting-browse-company-viewmode-list";
				break;
		}
    	
		$originalOptions = $searchParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
    	if (!isset($searchParams['page']) || $searchParams['page'] == '0')
		{
			$page = 1;
		}
		else
		{
			$page = (int)$searchParams['page'];
		}
		$this->view->class_mode = $class_mode;
		$this->view->paginator = $paginator = $companyTbl->getCompaniesPaginator($searchParams);
		
		unset($originalOptions['module']);
	    unset($originalOptions['controller']);
	    unset($originalOptions['action']);
	    unset($originalOptions['rewrite']);
	    $this->view->formValues = array_filter($originalOptions);
		$limit = $this->_getParam('itemCountPerPage', 8);
		$paginator->setItemCountPerPage($limit);
		$paginator->setCurrentPageNumber($page );
		$companyIds = array();
		foreach ($paginator as $company){
			$companyIds[] = $company -> getIdentity();
		}
		$this->view->companyIds = implode("_", $companyIds);
		
		$sponsorTbl = Engine_Api::_()->getDbTable('sponsors', 'ynjobposting');
		$select = $sponsorTbl -> select() -> from ($sponsorTbl->info('name'), 'company_id') -> where('active = 1');
		$sponsorIds = $select -> query() -> fetchAll();
		foreach ($sponsorIds as $k => $v)
		{
			$sponsorIds[$k] = $v['company_id'];
		}
		$this->view->sponsorIds = $sponsorIds;
    }
}