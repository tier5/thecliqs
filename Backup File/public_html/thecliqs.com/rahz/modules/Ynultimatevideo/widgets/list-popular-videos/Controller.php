<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListPopularVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->isPost()) {
            $element = $this->getElement();
            $element->clearDecorators();
        }

        $numberOfItems = $this->_getParam('numberOfItems', 6);

        // view mode
        $params = $this -> _getAllParams();
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

        // Should we consider views or comments popular?
        $popularType = $this->_getParam('popularType', 'view');
        if (!in_array($popularType, array('view', 'comment', 'rating'))) {
            $popularType = 'view';
        }

        if ($popularType == 'rating') {
            $popularCol = 'rating';
        } else {
            $popularCol = $popularType . '_count';
        }

        // Get paginator
        $table = Engine_Api::_()->getItemTable('ynultimatevideo_video');
        $select = $table->select()
                ->where('search = ?', 1)
                ->where('status = ?', 1)
                ->order($popularCol . ' DESC');
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $this->view->paginator->setItemCountPerPage($numberOfItems);
        $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }
}