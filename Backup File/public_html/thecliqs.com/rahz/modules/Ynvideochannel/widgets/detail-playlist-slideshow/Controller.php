<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_DetailPlaylistSlideshowController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (!Engine_Api::_() -> core() -> hasSubject('ynvideochannel_playlist')) {
            return $this -> setNoRender();
        }

        $this->view->playlist = $playlist = Engine_Api::_() -> core() -> getSubject();

        // HIDE IF PLAYLIST VIEW MODE IS GRID VIEW
        if (isset($playlist->view_mode) && !$playlist->view_mode)
            return $this -> setNoRender();

        $this->view->paginator = $paginator = Zend_Paginator::factory($playlist->getVideosSelect());
        $this->view->paginator->setItemCountPerPage(1000);
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
    }
}
