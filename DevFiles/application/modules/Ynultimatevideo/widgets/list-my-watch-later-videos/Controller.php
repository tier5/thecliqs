<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListMyWatchLaterVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return $this->setNoRender();
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $videoTable = Engine_Api::_()->getDbTable('videos', 'ynultimatevideo');
        $videoTableName = $videoTable->info('name');
        $watchLaterTable = Engine_Api::_()->getDbTable('watchlaters', 'ynultimatevideo');
        $watchLaterTableName = $watchLaterTable->info('name');
        
        $params = $request->getParams();
        $select = $videoTable -> getVideosSelect($params);
        $select->setIntegrityCheck(false)
                ->join($watchLaterTableName, $watchLaterTableName . ".video_id = " . $videoTableName . ".video_id", "$watchLaterTableName.watched")
                ->order(array("$watchLaterTableName.watched ASC", "$watchLaterTableName.creation_date DESC"))
                ->where("$watchLaterTableName.user_id = ?", $viewer->getIdentity())
                ->where("$videoTableName.search = 1")
                ->where("$videoTableName.status = 1");
        $this->view->params = $_GET;
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        // Set item count per page and current page number
        $numberOfItems = $this->_getParam('numberOfItems', 10);
        $paginator->setCurrentPageNumber($request->getParam('page', 1));
        $paginator->setItemCountPerPage($numberOfItems);
    }
}