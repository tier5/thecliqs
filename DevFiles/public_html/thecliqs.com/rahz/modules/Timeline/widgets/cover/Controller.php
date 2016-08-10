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

class Timeline_Widget_CoverController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    /**
     * @var $subject Timeline_Model_User
     * @var $table User_Model_DbTable_Settings
     */
    $subject = Engine_Api::_()->core()->getSubject('user');
    $table = Engine_Api::_()->getDbTable('settings', 'user');
    if (!($position = $table->getSetting($subject, 'timeline-cover-position'))) {
      $this->view->position = array('top' => 0, 'left' => 0);
    } else {
      $this->view->position = unserialize($position);
    }

    /**
     * Cover minimum height
     */
    $this->view->albumPhoto = $subject->getTimelineAlbumPhoto('cover');

    $height = Engine_Api::_()->timeline()->getHeight($this->view->itemPhoto($subject, 'thumb.profile'));
    $this->view->coverHeight = ((($height < 200) ? 200 : $height) - 50);
    $this->view->coverExists = $subject->hasTimelinePhoto('cover');
    $this->view->isAlbumEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('album');

    $this->view->label = $viewer->isSelf($subject)? "TIMELINE_Edit My Cover":"TIMELINE_Edit Member Cover";
    $this->view->canEdit = $subject->authorization()->isAllowed($viewer, 'edit');
  }
}