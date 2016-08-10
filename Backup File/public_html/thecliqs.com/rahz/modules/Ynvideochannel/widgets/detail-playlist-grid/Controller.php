<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_DetailPlaylistGridController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (!Engine_Api::_() -> core() -> hasSubject('ynvideochannel_playlist')) {
            return $this -> setNoRender();
        }

        $this->view->playlist = $playlist = Engine_Api::_() -> core() -> getSubject();

        // HIDE IF PLAYLIST VIEW MODE IS SLIDESHOW
        if (isset($playlist->view_mode) && $playlist->view_mode)
            return $this -> setNoRender();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $page = $params['page'];
        $this->view->paginator = $paginator = Zend_Paginator::factory($playlist->getVideosSelect());
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
        $paginator->setCurrentPageNumber($page);
    }
}
