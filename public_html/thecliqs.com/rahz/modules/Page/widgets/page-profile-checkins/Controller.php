<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-12-07 16:05 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Widget_PageProfileCheckinsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'hecore');

    if (!$modulesTbl->isModuleEnabled('checkin')) {
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    if ($subject->getType() != 'page') {
      return $this->setNoRender();
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

    $table = Engine_Api::_()->getDbTable('checks', 'checkin');

    $this->view->users = $users = $table->getMatchedChekinsCount(0, $subject->user_id, $subject->getIdentity(), false);
    $this->view->count = $table->getMatchedChekinsCount(0, $subject->user_id, $subject->getIdentity()) - 1;

    if (!count($users)) {
      return $this->setNoRender();
    }
  }
}