<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */

class Ynvideochannel_Widget_ListChannelsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['controller']);
        unset($params['name']);
        unset($params['action']);
        unset($params['module']);
        unset($params['rewrite']);

        if (isset($params['category_id'])) {
            $category = Engine_Api::_()->getItem('ynvideochannel_category', $params['category_id']);
            if ($category)
                $this->view->category = $category;
        }
        if (isset($params['category'])) {
            $categoryTbl = Engine_Api::_()->getItemTable('ynvideochannel_category');
            $categorySelect = $categoryTbl->select()->where('option_id = ?', $params['category']);
            $category = $categoryTbl->fetchRow($categorySelect);
            if ($category)
                $this->view->category = $category;
        }
        $this -> view -> formValues = $params;
        $page = $params['page'];
        $params['search'] = 1;
        if (!$page) $page = 1;
        $items_per_page = $this->_getParam('itemCountPerPage', 10);
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynvideochannel_channel') -> getChannelsPaginator($params);
        $paginator -> setCurrentPageNumber($page);
        $paginator -> setItemCountPerPage($items_per_page);
    }
}