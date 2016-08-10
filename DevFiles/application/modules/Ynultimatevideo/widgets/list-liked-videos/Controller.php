<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ListLikedVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $numberOfItems = $this->_getParam('numberOfItems', 6);
        
        $videoTable = Engine_Api::_()->getItemTable('ynultimatevideo_video');
        $select = $videoTable->select();
        $select->where('search = ?', 1)
                ->where('status = ?', 1)
                ->order("like_count DESC");
        $select->limit($numberOfItems);

        $this->view->videos = $videoTable->fetchAll($select);
        
        // Hide if nothing to show
        if ($this->view->videos->count() == 0) {
            return $this->setNoRender();
        }
    }

}