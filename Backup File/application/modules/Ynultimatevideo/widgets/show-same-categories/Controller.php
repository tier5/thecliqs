<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Widget_ShowSameCategoriesController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Check subject
        if (!Engine_Api::_()->core()->hasSubject('ynultimatevideo_video')) {
            return $this->setNoRender();
        }
        
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('ynultimatevideo_video');

        // Set default title
        if (!$this->getElement()->getTitle()) {
            $this->getElement()->setTitle('Related Videos');
        }

        $numberOfItems = $this->_getParam('numberOfItems', 6);
        $itemTable = Engine_Api::_()->getItemTable($subject->getType());

        // Get other with same tags
        $select = $itemTable->select()
                ->where('category_id <> 0')
                ->where('category_id = ?', $subject->category_id)
                ->where('video_id != ?', $subject->getIdentity())
                ->where('search = 1')
                ->where('status = 1')
                ->limit($numberOfItems)
                ->order(new Zend_Db_Expr(('rand()')));
        
        $this->view->videos = $videos = $itemTable->fetchAll($select);

		if (count($videos) <= 0) {
            return $this->setNoRender();
        }
    }
}