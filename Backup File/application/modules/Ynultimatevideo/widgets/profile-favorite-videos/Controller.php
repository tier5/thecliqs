<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ProfileFavoriteVideosController extends Engine_Content_Widget_Abstract {

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

        $params = $this -> _getAllParams();

        // view mode
        $mode_enabled = array();
        if(isset($params['mode_simple']) && $params['mode_simple'])
        {
            $mode_enabled[] = 'simple';
        }
        if(isset($params['mode_list']) && $params['mode_list'])
        {
            $mode_enabled[] = 'list';
        }
        if(isset($params['mode_casual']) && $params['mode_casual'])
        {
            $mode_enabled[] = 'casual';
        }
        if(isset($params['view_mode']) && in_array($params['view_mode'], $mode_enabled))
        {
            $view_mode = $params['view_mode'];
        } else if ($mode_enabled) {
            $view_mode = $mode_enabled[0];
        } else {
            $view_mode = 'simple';
        }

        $class_mode = 'ynultimatevideo_'. $view_mode .'-view';

        $this -> view -> mode_enabled = $mode_enabled;
        $this -> view -> class_mode = $class_mode;
        $this -> view -> view_mode = $view_mode;

        // Get paginator
        $profile_owner_id = $subject->getIdentity();
        $favoriteTable = Engine_Api::_()->getDbTable('favorites', 'ynultimatevideo');
        $favoriteTableName = $favoriteTable->info('name');
        $videoTable = Engine_Api::_()->getDbTable('videos', 'ynultimatevideo');
        $videoTableName = $videoTable->info('name');
        $select = $videoTable->select()->from($videoTableName)->setIntegrityCheck(false)
                ->join($favoriteTableName, $favoriteTableName . ".video_id = " . $videoTableName . ".video_id")
                ->where('user_id = ?', $profile_owner_id)
                ->where('status = 1')
                ->where('search = 1');
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('numberOfItems', 6));
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