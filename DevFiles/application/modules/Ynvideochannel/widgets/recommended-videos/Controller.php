<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_RecommendedVideosController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Get favorite video
        $viewer = Engine_Api::_()->user()->getViewer();
        $videoTable = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');
        $videoTableName = $videoTable->info('name');
        $row = null;
        if ($viewer->getIdentity()) {
            $owner_id = $viewer->getIdentity();
            $favoriteTable = Engine_Api::_()->getDbTable('favorites', 'ynvideochannel');
            $favoriteTableName = $favoriteTable->info('name');
            $select = $videoTable->select()->from($videoTableName)->setIntegrityCheck(false)
                ->join($favoriteTableName, $favoriteTableName . ".video_id = " . $videoTableName . ".video_id")
                ->where('user_id = ?', $owner_id)
                ->where('search = 1')
                ->order('rand()')
                ->limit(1);
            $row = $videoTable->fetchRow($select);
        }

        // Get offset
        if ($row) {
            $params['category_id'] = $row -> category_id;
        }
        $params['search'] = 1;
        $allVideos = $videoTable->getVideosPaginator($params);

        $limit = $this->_getParam('itemCountPerPage', 5);
        $totalItems = $allVideos->getTotalItemCount();
        $max = $totalItems - $limit ? $totalItems - $limit : $totalItems;
        $offset = rand(0, $max);
        if($max == 1) $offset = 0;

        $select = $videoTable->select()->from($videoTableName)
            ->where('search = 1')
            ->limit($limit, $offset);

        if ($row) {
            $select -> where('category_id = ?', $row -> category_id);
        }
        $videos = $videoTable -> fetchAll($select);
        $this->view->paginator = $paginator = Zend_Paginator::factory($videos);

        // Hide if nothing to show
        if (!$paginator->getTotalItemCount()) {
            //return $this->setNoRender();
        }
    }
}
