<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
	{
		parent::__construct($application);
		$this->initViewHelperPath();
	}

  protected function _initFrontController()
  {
		$this->initActionHelperPath();
    Zend_Controller_Action_HelperBroker::addHelper(new Suggest_Controller_Action_Helper_Popups());
  }
}