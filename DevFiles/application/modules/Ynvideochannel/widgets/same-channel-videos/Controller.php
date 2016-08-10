<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_SameChannelVideosController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (!Engine_Api::_()->core()->hasSubject('ynvideochannel_video')) {
            return $this->setNoRender();
        }
        $video = Engine_Api::_()->core()->getSubject('ynvideochannel_video');
        if (!$video->channel_id) {
            return $this->setNoRender();
        }

        $numberOfItems = $this->_getParam('itemCountPerPage', 5);
        $itemTable = Engine_Api::_()->getItemTable($video->getType());

        // Get other with same tags
        $select = $itemTable->select()
            ->where('channel_id <> 0')
            ->where('channel_id = ?', $video->channel_id)
            ->where('video_id <> ?', $video->getIdentity())
            ->where('search = 1')
            ->limit($numberOfItems)
            ->order(new Zend_Db_Expr(('rand()')));
        $this->view->videos = $videos = $itemTable->fetchAll($select);

        if (count($videos) <= 0) {
            return $this->setNoRender();
        }
    }
}
