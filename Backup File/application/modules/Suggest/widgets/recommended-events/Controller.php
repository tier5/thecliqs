<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_Widget_RecommendedEventsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $type = 'event';
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = (Engine_Api::_()->core()->hasSubject()) ? Engine_Api::_()->core()->getSubject() : null;

    if ($subject && $subject->getType() == 'event') {
      $exceptIds = array($subject->getIdentity());
    } else {
      $exceptIds = array();
    }
    $items = Engine_Api::_()->suggest()->getRecommendations($viewer->getIdentity(), $type, $exceptIds);

    if (!$viewer->getIdentity()) {
      return $this->setNoRender();
    }

     if (!$items || (count($items['admin']) <= 0 && count($items['user']) <= 0)) {
      return $this->setNoRender();
    }

    $this->view->type = $type;
    $this->view->items = $items;
  }
}
