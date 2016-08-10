<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListRecentVideosController extends Engine_Content_Widget_Abstract {
    public function indexAction() {

        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->isPost()) {
            $element = $this->getElement();
            $element->clearDecorators();            
        }
        
        $numberOfItems = $this->_getParam('numberOfItems', 6);
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
        
        // Get paginator
        $recentType = $this->_getParam('recentType', 'creation');
        if (!in_array($recentType, array('creation', 'modified'))) {
            $recentType = 'creation';
        }
        $this->view->recentType = $recentType;
        $this->view->recentCol = $recentCol = $recentType . '_date';
        
        $table = Engine_Api::_()->getItemTable('ynultimatevideo_video');
        $select = $table->select()
                ->where('search = ?', 1)
                ->where('status = ?', 1);
        if ($recentType == 'creation') {
            // using primary should be much faster, so use that for creation
            $select->order('video_id DESC');
        } else {
            $select->order($recentCol . ' DESC');
        }
        $select->limit($numberOfItems);
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $this->view->paginator->setItemCountPerPage($numberOfItems);
        $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));

        $this->view->can_create = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')
                ->setAuthParams('ynultimatevideo_video', null, 'create')->checkRequire();
    }
}