<?php
class Ynmusic_Widget_UserProfileAlbumsController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
		
	public function indexAction() {
		// Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !Engine_Api::_()->core()->hasSubject() ) {
        return $this->setNoRender();
        }

        // Get subject and check auth
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
            return $this->setNoRender();
        }

        // Just remove the title decorator
        $this->getElement()->removeDecorator('Title');
		
        $params = $this -> _getAllParams();
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
		
		$paginator = Engine_Api::_()->getDbTable('albums', 'ynmusic')->getAlbumsPaginator(array('user_id'=>$subject->getIdentity()));

		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmusic_albumsPerPage', 8));	
		$this->view->paginator = $paginator;
		
		// Do not render if nothing to show
        if( $paginator->getTotalItemCount() <= 0 ) {
            return $this->setNoRender();
        }

        // Add count to title if configured
        if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
            $this->_childCount = $paginator->getTotalItemCount();
        }
		
		$this->view->formValues = $params;	
	}

	public function getChildCount() {
        return $this->_childCount;
    }
}