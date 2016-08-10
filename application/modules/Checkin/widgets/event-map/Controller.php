<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 09.12.11 11:32 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_Widget_EventMapController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event')) {
      return $this->setNoRender();
    }

    if (!Engine_Api::_()->core()->hasSubject('event')) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('event');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');

    $place = $placesTbl->findByObject($subject->getType(), $subject->getIdentity());
    $this->view->placeInfo = ($place) ? $place->toArray() : array();

    if (!$place && $viewer->getIdentity() != $subject->user_id) {
      return $this->setNoRender();
    }
  }
}