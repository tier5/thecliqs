<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_ProfileVideoPlaylistsController extends Engine_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject();
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }

        // Get paginator
        $params['user_id'] = $subject->getIdentity();
        $params['search'] = 1;
        $playlistTable = Engine_Api::_()->getItemTable('ynvideochannel_playlist');
        $this->view->paginator = $paginator = $playlistTable -> getPlaylistsPaginator($params);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 6));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        } else {
            $this->_childCount = $paginator->getTotalItemCount();
        }
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}