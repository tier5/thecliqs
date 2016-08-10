<?php
class Ynresume_Widget_MostEndorsedResumeController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
	    // Just remove the title decorator
        $this->getElement()->removeDecorator('Title');
		
 		$resumeTbl = Engine_Api::_() -> getItemTable('ynresume_resume');
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
 		$params = $this -> _getAllParams();
 		$mode_list = $mode_grid = $mode_map = 1;
		$mode_enabled = array();
		$view_mode = 'list';
		
		if(isset($params['mode_list'])) {
			$mode_list = $params['mode_list'];
		}
		if($mode_list) {
			$mode_enabled[] = 'list';
		}
		
		if(isset($params['mode_grid'])) {
			$mode_grid = $params['mode_grid'];
		}
		if($mode_grid) {
			$mode_enabled[] = 'grid';
		}
		
		if(isset($params['mode_map'])) {
			$mode_map = $params['mode_map'];
		}
		if($mode_map) {
			$mode_enabled[] = 'map';
		}
		
		if(isset($params['view_mode'])) {
			$view_mode = $params['view_mode'];
		}
		if($mode_enabled && !in_array($view_mode, $mode_enabled)) {
			$view_mode = $mode_enabled[0];
		}
			
		$this -> view -> mode_enabled = $mode_enabled;
		$class_mode = "ynresume-resume-listing-viewmode-list";
		switch ($view_mode) {
			case 'grid':
				$class_mode = "ynresume-layout-content-grid-view";
				break;
			case 'map':
				$class_mode = "ynresume-layout-content-map-view";
				break;
			default:
				$class_mode = "ynresume-layout-content-list-view";
				break;
		}
		
		$this->view->class_mode = $class_mode;
		
	  	//Setup params
	  	$request = Zend_Controller_Front::getInstance()->getRequest();
	    $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
	    $originalOptions = $params;
 		if (!isset($params['page']) || $params['page'] == '0') {
			$page = 1;
		}
		else {
			$page = (int)$params['page'];
		}
		$itemCountPerPage = $this -> _getParam('itemCountPerPage', 16);
        if (!$itemCountPerPage) {
            $itemCountPerPage = 16;
        }
        $params['order'] = 'resume.endorse_count';
		$params['direction'] = 'DESC';
		//Set curent page
		$this -> view -> paginator = $paginator = $resumeTbl -> getResumesPaginator($params);
		$paginator -> setItemCountPerPage($itemCountPerPage);
		$paginator -> setCurrentPageNumber($page);
		$resumeIds = array();
		foreach ($paginator as $resume){
			$resumeIds[] = $resume -> getIdentity();
		}
		$this->view->resumeIds = implode("_", $resumeIds);
	    $this->view->totalResumes = $paginator->getTotalItemCount();
	}
}
