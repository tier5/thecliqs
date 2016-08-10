<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Widget_ProfileOptionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	$this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

  	if (!$viewer->getIdentity()) {
  		return $this->setNoRender();
  	}

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      return $this->setNoRender();
    }

  	$this->view->navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('page_profile');
  }
} 