<?php

class Ynlistings_Widget_ListMostItemsController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
    	$headScript = new Zend_View_Helper_HeadScript();
   		$headScript -> appendFile('application/modules/Ynlistings/externals/scripts/YnlistingsTabContent.js');
		$params = $this -> _getAllParams();
		
		$tab_recent = $tab_popular = $mode_list = $mode_grid = $mode_pin = $mode_map = 1;
		$tab_enabled = $mode_enabled = array();
		$view_mode = 'list';
		
		if(isset($params['tab_popular']))
		{
			$tab_popular = $params['tab_popular'];
		}
		if($tab_popular)
		{
			$tab_enabled[] = 'popular';
		}
		if(isset($params['tab_recent']))
		{
			$tab_recent = $params['tab_recent'];
		}
		if($tab_recent)
		{
			$tab_enabled[] = 'recent';
		}	
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
		
		$this -> view -> tab_enabled = $tab_enabled;	
		$this -> view -> mode_enabled = $mode_enabled;
		
		$class_mode = "ynlistings_list-view";
		switch ($view_mode) 
		{
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
		if(!$tab_enabled)
		{
			$this -> setNoRender();
		}
		$itemCount = $this->_getParam('num_of_listings', 6);
		
		$this->view->itemCount = $itemCount;
        $table = Engine_Api::_()->getItemTable('ynlistings_listing');
		$request = Zend_Controller_Front::getInstance()->getRequest();
		
		
		// recent listings		
	    $count = $itemCount;
	    
	    $recentType = $this->_getParam('recentType', 'creation');
	    if( !in_array($recentType, array('creation', 'modified', 'approved')) ) {
	      $recentType = 'approved';
	    }
	    $this->view->recentType = $recentType;
	    $this->view->recentCol = $recentCol = $recentType . '_date';
	    
	    // Get paginator
	    $table = Engine_Api::_()->getItemTable('ynlistings_listing');
	    $select = $table->select()
	      ->where('search = ?', 1)
          ->where('status = ?', 'open')
          ->where('approved_status = ?', 'approved') 
		 // ->where('creation_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL {$time} {$type})"))
	      ->limit($count);
	    if( $recentType == 'creation' ) {
	      // using primary should be much faster, so use that for creation
	      $select->order('listing_id DESC');
	    } else {
	      $select->order($recentCol . ' DESC');
	    }
	    $this->view->recentListings = $listings = $table->fetchAll($select);

		$count = $itemCount;
	    
	    $popularType = $this->_getParam('popularType', 'view');
	    if( !in_array($popularType, array('view', 'member')) ) {
	      $popularType = 'view';
	    }
	    $this->view->popularType = $popularType;
	    $this->view->popularCol = $popularCol = $popularType . '_count';
	    
	    // Get paginator
	    $table = Engine_Api::_()->getItemTable('ynlistings_listing');
	    $select = $table->select()
	      ->where('search = ?', 1)
          ->where('status = ?', 'open')
          ->where('approved_status = ?', 'approved')
	      ->order($popularCol . ' DESC')
	      ->limit($count);
	    $this->view->popularListings = $popularListings = $table->fetchAll($select);
    }

}
