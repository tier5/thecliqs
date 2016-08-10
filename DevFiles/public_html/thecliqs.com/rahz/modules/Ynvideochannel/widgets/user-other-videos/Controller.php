<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_UserOtherVideosController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (!Engine_Api::_() -> core() -> hasSubject('ynvideochannel_video')) {
            return $this -> setNoRender();
        }
        $video = Engine_Api::_() -> core() -> getSubject('ynvideochannel_video');
        if (!$video->category_id) {
            return $this->setNoRender();
        }

        // Set user title
        if (!$this->getElement()->getTitle()) {
            $this->getElement()->setTitle($this -> view -> translate("%s's other videos", $video -> getOwner()));
        }

        $numberOfItems = $this->_getParam('itemCountPerPage', 5);
        $itemTable = Engine_Api::_()->getItemTable($video->getType());

        // Get other with same tags
        $select = $itemTable->select()
            ->where('owner_id = ?', $video->owner_id)
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
