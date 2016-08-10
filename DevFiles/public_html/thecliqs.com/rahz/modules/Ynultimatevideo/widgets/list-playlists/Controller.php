<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListPlaylistsController extends Engine_Content_Widget_Abstract {

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

        $class_mode = "ynultimatevideo_list-view";
        switch ($view_mode) {
            case 'grid':
                $class_mode = "ynultimatevideo_grid-view";
                break;
            default:
                $class_mode = "ynultimatevideo_list-view";
                break;
        }
        $this -> view -> class_mode = $class_mode;
        $this -> view -> view_mode = $view_mode;

        $page = (!empty($params['page'])) ? $params['page'] : 1;

        $playlists_select = Engine_Api::_()->getDbTable('playlists', 'ynultimatevideo')->getPlaylistsSelect($params);
        $numberOfItems = $this->_getParam('numberOfItems', 10);
        $paginator = Zend_Paginator::factory($playlists_select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($numberOfItems);
        $this->view->paginator = $paginator;
        $this->view->formValues = $params;
    }
}