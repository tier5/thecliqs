<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListWatchAgainVideosController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        // not show if no viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !$viewer->getIdentity() ) {
            return $this->setNoRender();
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->isPost()) {
            $element = $this->getElement();
            $element->clearDecorators();            
        }
        
        $params = $this -> _getAllParams();
        // view mode
        $mode_enabled = array();
        if(isset($params['mode_simple']) && $params['mode_simple'])
        {
            $mode_enabled[] = 'simple';
        }
        if(isset($params['mode_grid']) && $params['mode_grid'])
        {
            $mode_enabled[] = 'grid';
        }
        if(isset($params['mode_list']) && $params['mode_list'])
        {
            $mode_enabled[] = 'list';
        }
        if(isset($params['mode_casual']) && $params['mode_casual'])
        {
            $mode_enabled[] = 'casual';
        }
        if(isset($params['view_mode']) && in_array($params['view_mode'], $mode_enabled))
        {
            $view_mode = $params['view_mode'];
        } else if ($mode_enabled) {
            $view_mode = $mode_enabled[0];
        } else {
            $view_mode = 'simple';
        }

        $class_mode = 'ynultimatevideo_'. $view_mode .'-view';

        $this -> view -> mode_enabled = $mode_enabled;
        $this -> view -> class_mode = $class_mode;
        $this -> view -> view_mode = $view_mode;

        $params['user_id'] = $viewer->getIdentity();

        // Get paginator
        $tableHistory = Engine_Api::_()->getDbTable('history', 'ynultimatevideo');
        $this->view->paginator = $paginator = $tableHistory -> getVideoHistoryPaginator($params);

        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('numberOfItems', 6));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }
}