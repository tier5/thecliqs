<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 14.11.11 11:06 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Widget_PageStatisticsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	$this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

		$this->view->isAdmin = $subject->isAdmin();
  }
}