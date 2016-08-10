<?php
class Ynbusinesspages_Widget_NewestBusinessesController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $table = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $params = $this -> _getAllParams();
        $mode_list = $mode_grid = $mode_pin = $mode_map = 1;
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
        if(isset($params['mode_pin']))
        {
            $mode_pin = $params['mode_pin'];
        }
        if($mode_pin)
        {
            $mode_enabled[] = 'pin';
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
        $class_mode = "ynbusinesspages-browse-business-viewmode-list";
        switch ($view_mode) {
            case 'grid':
                $class_mode = "ynbusinesspages-browse-business-viewmode-grid";
                break;
            case 'pin':
                $class_mode = "ynbusinesspages-browse-business-viewmode-pins";
                break;
            case 'map':
                $class_mode = "ynbusinesspages-browse-business-viewmode-maps";
                break;
            default:
                $class_mode = "ynbusinesspages-browse-business-viewmode-list";
                break;
        }
        
        $this->view->class_mode = $class_mode;
        
        $params = array();
        
        $params['limit'] = $this -> _getParam('itemCountPerPage', 12);
        $params['order'] = 'business.creation_date';
        //Set curent page
        $this -> view -> paginator = $paginator = $table -> getBusinessesPaginator($params);
        $paginator -> setItemCountPerPage($params['limit']);
        if ($paginator->getTotalItemCount() <= 0) {
            $this->setNoRender();
        }
        
        $businessIds = array();
        foreach ($paginator as $business){
            $businessIds[] = $business -> getIdentity();
        }
        $this->view->businessIds = implode("_", $businessIds);
        $this->view->idName = 'newest-businesses';
        $this->view->idPrefix = 'newestBusinesses';
    }
}