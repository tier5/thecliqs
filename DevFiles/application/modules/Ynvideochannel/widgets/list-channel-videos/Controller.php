<?php

/**
 * @category ynvideochannel
 * @package widget
 */

class Ynvideochannel_Widget_ListChannelVideosController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (!Engine_Api::_() -> core() -> hasSubject('ynvideochannel_channel')) {
            return $this -> setNoRender();
        }
        $items_per_page = $this->_getParam('itemCountPerPage', 6);
        $this->view->channel = $channel = Engine_Api::_()->core()->getSubject();
        $this->view->itemCountPerPage = $items_per_page;
    }
}