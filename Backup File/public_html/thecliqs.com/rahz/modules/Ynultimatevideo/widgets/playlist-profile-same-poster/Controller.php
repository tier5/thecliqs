<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_PlaylistProfileSamePosterController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        if (!Engine_Api::_() -> core() -> hasSubject()) {
            return $this -> setNoRender();
        }

        $playlist = $subject = Engine_Api::_() -> core() -> getSubject();
        // DO NOT SHOW IF THIS PLAYLIST BELONGS TO A DELETED MEMBER
        $owner = $playlist->getOwner();
        if (!$owner->getIdentity()) {
            return $this -> setNoRender();
        }

        // modify title
        $ownerTextLink = '<a href="' . $owner->getHref() . '" title="' . $owner->getTitle() . '">'
            . $this->view->string()->truncate($owner->getTitle(), 15) . '</a>';

        $this->getElement()->setTitle(Zend_Registry::get('Zend_Translate')->_("Playlists of") . ' ' . $ownerTextLink);

        $params = $this -> _getAllParams();
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $params = array_merge($params, $p);

        unset($params['title']);
        unset($params['controller']);
        unset($params['action']);
        $params['user_id'] = $owner->getIdentity();

        $numberOfPlaylists = $this->_getParam('numberOfPlaylists', 5);
        $playlists_select = Engine_Api::_()->getDbTable('playlists', 'ynultimatevideo')->getPlaylistsSelect($params);

        $paginator = Zend_Paginator::factory($playlists_select);
        $paginator->setCurrentPageNumber(1);
        $paginator->setItemCountPerPage($numberOfPlaylists);
        $this->view->paginator = $paginator;
        $this->view->formValues = $params;
    }
}