<?php

/**
 * @category ynvideochannel
 * @package widget
 */

class Ynvideochannel_Widget_ListPlaylistsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $params = $this->_getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['controller']);
        unset($params['name']);
        unset($params['action']);
        unset($params['module']);
        unset($params['rewrite']);

        $this->view->formValues = $params;

        $page = $params['page'];
        $params['search'] = 1;
        if (!$page) $page = 1;
        $items_per_page = $this->_getParam('itemCountPerPage', 10);
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('ynvideochannel_playlist')->getPlaylistsPaginator($params);
        $paginator -> setCurrentPageNumber($page);
        $paginator -> setItemCountPerPage($items_per_page);
    }
}