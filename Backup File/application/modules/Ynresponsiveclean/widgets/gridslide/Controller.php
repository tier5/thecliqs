<?php

class Ynresponsiveclean_Widget_GridslideController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $content_type =  $this->_getParam('content_type');
    $col_style =  $this->_getParam('col_style',2);
    $num_cols = $this->_getParam('num_cols',3);
    $num_rows = $this->_getParam('num_rows',1);
    $num_cols = intval($num_cols);
    $num_rows =  intval($num_rows);
    $limit = $num_cols * $num_rows;    
    
    list($module, $type) = explode('_', $content_type, 2);

	if (!Engine_Api::_() -> hasModuleBootstrap($module) || (YNRESPONSIVE_ACTIVE != 'ynresponsive1' && 'ynresponsiveclean' != substr(YNRESPONSIVE_ACTIVE, 0, 17)))
	{
		return $this -> setNoRender(true);
	}

	$api = Engine_Api::_() -> {$module}();

	if (!method_exists($api, 'getSliderContent'))
	{
		return $this -> setNoRender(true);
	}
	$params = $this -> _getAllParams();
	$params['itemCountPerPage'] = $limit;
	$items = $api -> getSliderContent($type, $params);
    
    if(empty($items))
    {
      return $this->setNoRender(true);
    }
    
    $this->view->items =  $items;
    $this->view->slider_id = '_'. uniqid();
    $this->view->num_cols =  $num_cols;
    $this->view->num_rows =  $num_rows;

    // add more grid 
    $class_view = 'col-md-'.(12/$num_cols);
    if ($num_cols%2==0) {
        $class_view .= ' col-sm-'.(12*2/$num_cols);
    }

    $this->view->col_class = $class_view;
    $this->view->col_style  =  $col_style;
    $this->view->show_title  = $this->_getParam('show_title',true);
    $this->view->show_readmore  = $this->_getParam('show_readmore',true);
    $this->view->show_description  = $this->_getParam('show_description',true);
    $this->view->id_gridslide = 'yn_grid_' . uniqid();
  }

}
