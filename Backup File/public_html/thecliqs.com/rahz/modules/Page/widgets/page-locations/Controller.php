<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-11-04 17:07:11 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Page_Widget_PageLocationsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		$this->view->locations = $locations = Engine_Api::_()->page()->getLocations();

		if (!count($locations)) {
			return $this->setNoRender();
		}
  }
}