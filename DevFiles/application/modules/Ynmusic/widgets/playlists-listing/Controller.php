<?php
class Ynmusic_Widget_PlaylistsListingController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
        $params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['title']);
        unset($params['controller']);
        unset($params['module']);
        unset($params['action']);
        unset($params['rewrite']);
        $mode_list = $mode_grid = 1;
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
        
        if(isset($params['view_mode'])) {
            $view_mode = $params['view_mode'];
        }

        if($mode_enabled && !in_array($view_mode, $mode_enabled)) {
            $view_mode = $mode_enabled[0];
        }

        $this -> view -> mode_enabled = $mode_enabled;

        $class_mode = "ynmusic_list-view";
        switch ($view_mode) {
            case 'grid':
                $class_mode = "ynmusic_grid-view";
                break;
            default:
                $class_mode = "ynmusic_list-view";
                break;
        }
        $this -> view -> class_mode = $class_mode;
        $this -> view -> view_mode = $view_mode;

        $page = (!empty($params['page'])) ? $params['page'] : 1;
		
		$playlists_select = Engine_Api::_()->getDbTable('playlists', 'ynmusic')->getSelect($params);

		$paginator = Zend_Paginator::factory($playlists_select);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_playlistsPerPage', 8));	
		$this->view->paginator = $paginator;
		
		$this->view->formValues = $params;	
	}
}