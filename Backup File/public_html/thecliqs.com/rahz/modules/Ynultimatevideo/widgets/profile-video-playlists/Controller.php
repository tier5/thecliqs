<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ProfileVideoPlaylistsController extends Engine_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject();
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }

        $params = $this -> _getAllParams();

        // view mode
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

        // Get paginator
        $profile_owner_id = $subject->getIdentity();
        $playlistTable = Engine_Api::_()->getItemTable('ynultimatevideo_playlist');
        $select = $playlistTable->select();
        $select->where('user_id = ?', $profile_owner_id);
        $select->where('search = 1');
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        
        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('numberOfItems', 6));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        } else {
            $this->_childCount = $paginator->getTotalItemCount();
        }
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}