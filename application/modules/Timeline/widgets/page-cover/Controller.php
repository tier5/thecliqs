<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Timeline_Widget_PageCoverController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        $subject = Engine_Api::_()->core()->getSubject('page');
        // If Page module not enabled
        if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page')) {
            return $this->setNoRender();
        }

        $table = Engine_Api::_()->getDbTable('settings', 'user');


        if (!($position = $table->getSetting($subject->getOwner(), 'timeline-page-cover-position-'.$subject->getIdentity()))) {
            $this->view->position = array('top' => 0, 'left' => 0);
        } else {
            $this->view->position = unserialize($position);
        }

        /**
         * Cover minimum height
         */
        $this->view->albumPhoto = $subject->getTimelineAlbumPhoto('cover');

        $this->view->coverHeight = 128;
//        $height = Engine_Api::_()->timeline()->getHeight($this->view->itemPhoto($subject, 'thumb.profile'));
//        $this->view->coverHeight = ((($height < 200) ? 200 : $height) - 50);
        $this->view->coverExists = $subject->hasTimelinePhoto('cover');
        $this->view->isAlbumEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('album');

        $this->view->label = "TIMELINE_Edit Page Cover";
        $this->view->canEdit = ($subject->user_id == $viewer->getIdentity());

    }
}