<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListMyPlaylistsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }
        $params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);
        unset($params['title']);
        unset($params['controller']);
        unset($params['module']);
        unset($params['action']);
        unset($params['rewrite']);


        $params['user_id'] = $viewer->getIdentity();
        $page = (!empty($params['page'])) ? $params['page'] : 1;
        $playlists_select = Engine_Api::_()->getDbTable('playlists', 'ynultimatevideo')->getPlaylistsSelect($params);
        $numberOfItems = $this->_getParam('numberOfItems', 10);
        $paginator = Zend_Paginator::factory($playlists_select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($numberOfItems);
        $this->view->paginator = $paginator;
        $this->view->formValues = $params;
    }
}