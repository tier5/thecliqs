<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListFeaturedVideosController extends Engine_Content_Widget_Abstract
{
    public function indexAction() 
    {
        $verticalThumbnails = $this->_getParam('verticalThumbnails', 0);
        $this->view->vertcialThumbnails = $verticalThumbnails;
        $numberOfItems = $this->_getParam('numberOfItems', 6);

		$videoTable = Engine_Api::_()->getDbTable('videos', 'ynultimatevideo');
        $select = $videoTable->select();
        $select->where('featured = 1')
            ->where('search = 1')
            ->where('status = 1')
            ->limit($numberOfItems)
            ->order(new Zend_Db_Expr("RAND()"));

        $videos = $videoTable->fetchAll($select);
        
        if ($videos->count() == 0) 
        {
            return $this->setNoRender();
        } 
        $this->view->videos = $videos;
    }
}