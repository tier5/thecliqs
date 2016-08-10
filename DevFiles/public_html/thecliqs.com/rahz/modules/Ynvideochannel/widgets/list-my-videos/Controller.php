<?php

/**
 * @category ynvideochannel
 * @package widget
 */

class Ynvideochannel_Widget_ListMyVideosController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        $params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['controller']);
        unset($params['name']);
        unset($params['action']);
        unset($params['module']);
        unset($params['rewrite']);

        $params['user_id'] = $viewer->getIdentity();
        $this->view->formValues = $params;

        $page = $params['page'];
        if (!$page) $page = 1;
        $table = Engine_Api::_()->getItemTable('ynvideochannel_video');
        $items_per_page = $this->_getParam('itemCountPerPage', 10);
        $this->view->paginator = $paginator = $table->getVideosPaginator($params);
        $paginator->setItemCountPerPage($items_per_page);
        $paginator->setCurrentPageNumber($page);
    }
}