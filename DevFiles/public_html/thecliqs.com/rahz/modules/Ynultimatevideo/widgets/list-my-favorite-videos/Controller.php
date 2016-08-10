<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListMyFavoriteVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();

        $videoTable = Engine_Api::_()->getDbTable('videos', 'ynultimatevideo');
        $select = $videoTable -> getVideosSelect($params);
        $videoTableName = $videoTable->info('name');
        $favoriteTable = Engine_Api::_()->getDbTable('favorites', 'ynultimatevideo');
        $favoriteTableName = $favoriteTable->info('name');
        $select->setIntegrityCheck(false)
            ->join($favoriteTableName, $favoriteTableName . ".video_id = " . $videoTableName . ".video_id")
            ->where("$favoriteTableName.user_id = ?", $viewer->getIdentity())
            ->where("$videoTableName.status = 1")
        ;

        $numberOfItems = $this->_getParam('numberOfItems', 10);
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($request->getParam('page'), 1);
        $paginator->setItemCountPerPage($numberOfItems);
    }

}