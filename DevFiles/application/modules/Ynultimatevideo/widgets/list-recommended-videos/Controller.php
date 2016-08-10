<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListRecommendedVideosController extends Engine_Content_Widget_Abstract {
    public function indexAction() {

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

        // build history array
        $tableHistory = Engine_Api::_()->getDbTable('history', 'ynultimatevideo');
        $viewer = Engine_Api::_()->user()->getViewer();
        if( $viewer->getIdentity() ) {
            $userId = $viewer->getIdentity();
            $categoryIdsHistory = $tableHistory->getCategoryIdsHistory($userId);
            $excludeVideos = $tableHistory->getVideoIdsHistory($userId);
        } else {
            $categoryIdsHistory = (empty($_COOKIE['ynultimatevideo_category_history'])) ? array() : json_decode($_COOKIE['ynultimatevideo_category_history']);
            $excludeVideos = (empty($_COOKIE['ynultimatevideo_video_history'])) ? array() : json_decode($_COOKIE['ynultimatevideo_video_history']);
        }

        //nothing to show
        if (count($categoryIdsHistory) == 0) {
            return $this->setNoRender();
        }

        $videoTable = Engine_Api::_()->getDbTable('videos', 'ynultimatevideo');

        $numberOfItems = $this->_getParam('numberOfItems', 6);
        $categoryMaps = array();
        foreach ($categoryIdsHistory as $categoryId) {
            $categoryMaps[$categoryId] = $videoTable->getVideoIdsByCategory($categoryId, $excludeVideos);
        }

        $resultIds = array();
        // for safe keeping from infinite loop
        $limit = 0;
        while ((count($resultIds) < $numberOfItems) && (count($categoryMaps) > 0) && $limit < 100) {
            $limit++;
            foreach ($categoryMaps as $key => $videoArr) {
                if (count($categoryMaps[$key]) > 0) {
                    $pop = array_pop($categoryMaps[$key]);
                    $resultIds[] = $pop;
                } else {
                    unset($categoryMaps[$key]);
                }
            }
        }

        //nothing to show
        if (count($resultIds) == 0) {
            return $this->setNoRender();
        }

        // Get videos
        $select = $videoTable->select()
            ->where('video_id IN (?)', $resultIds);
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $this->view->paginator->setItemCountPerPage($numberOfItems);
        $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }
}