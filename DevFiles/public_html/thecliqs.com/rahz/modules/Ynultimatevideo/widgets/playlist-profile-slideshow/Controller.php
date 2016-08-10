<?php

/**
 * @category ynultimatevideo
 * @package widget
 * @subpackage search-manage-videos
 * @author dang tran
 */
class Ynultimatevideo_Widget_PlaylistProfileSlideshowController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        if (!Engine_Api::_() -> core() -> hasSubject()) {
            return $this -> setNoRender();
        }

        $playlist = $subject = Engine_Api::_() -> core() -> getSubject();
        // Check authorization to view album.
        if (!$subject->isViewable()) {
            return $this -> setNoRender();
        }

        if (isset($playlist->view_mode) && !$playlist->view_mode) return $this -> setNoRender();

        $params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['controller']);
        unset($params['name']);
        unset($params['action']);
        unset($params['module']);

        $this -> view -> formValues = $params;
        $this -> view -> playlist = $subject = Engine_Api::_() -> core() -> getSubject();

        $page = $params['page'];
        if (!$page) $page = 1;
        $numberOfItems = $this->_getParam('numberOfItems', 60);
        $select = $playlist -> getVideos();
        $paginator = Zend_Paginator::factory($select);
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
        $paginator -> setCurrentPageNumber($page);
        $paginator -> setItemCountPerPage($numberOfItems);
        $this -> view -> paginator = $paginator;
    }
}