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

class Page_Widget_ProfileNoteController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	$this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
		$this->view->isAdmin = $subject->isAdmin();
		
		if (!$this->view->isAdmin && trim($subject->note) == ''){
			return $this->setNoRender();
		}
  }
}