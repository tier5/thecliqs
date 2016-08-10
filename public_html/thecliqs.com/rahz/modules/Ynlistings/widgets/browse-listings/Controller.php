<?php

class Ynlistings_Widget_BrowseListingsController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
		$params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['title']);
        unset($params['controller']);
        unset($params['module']);
        unset($params['action']);
        unset($params['rewrite']);
        if (isset($params['category_id'])) {
            $category = Engine_Api::_()->getItem('ynlistings_category', $params['category_id']);
            if ($category)
                $this->view->category = $category;
        }
        if (isset($params['category'])) {
            $categoryTbl = Engine_Api::_()->getItemTable('ynlistings_category');
            $categorySelect = $categoryTbl->select()->where('option_id = ?', $params['category']);
            $category = $categoryTbl->fetchRow($categorySelect);
            if ($category)
                $this->view->category = $category;
        }
        $this -> view -> formValues = $params;
        $p_arr = array();
        foreach ($params as $k => $v) {
            $p_arr[] = $k;
            $p_arr[] = $v;
        }
        $params_str = implode('/', $p_arr);
        $this->view->params_str = $params_str;
		$mode_list = $mode_grid = $mode_pin = $mode_map = 1;
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
        if(isset($params['mode_pin']))
        {
            $mode_pin = $params['mode_pin'];
        }
        if($mode_pin)
        {
            $mode_enabled[] = 'pin';
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
		
		$class_mode = "ynlistings_list-view";
		switch ($view_mode) {
			case 'grid':
				$class_mode = "ynlistings_grid-view";
				break;
			case 'map':
				$class_mode = "ynlistings_map-view";
				break;
            case 'pin':
                $class_mode = "ynlistings_pin-view";
                break;
			default:
				$class_mode = "ynlistings_list-view";
				break;
		}
		$this -> view -> class_mode = $class_mode;
		$this -> view -> view_mode = $view_mode;
		
        $page = $params['page'];
        if (!$page) $page = 1;
	    $paginator = Engine_Api::_() -> getItemTable('ynlistings_listing') -> getListingsPaginator($params);
        $paginator -> setCurrentPageNumber($page);
        $paginator -> setItemCountPerPage(10);
        $this -> view -> paginator = $paginator;
    }

}
