<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

//ini_set('display_errors', 1);

class Store_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
	public function __construct($application)
  {
    $this->initViewHelperPath();
    parent::__construct($application);

    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile('application/modules/Store/externals/scripts/manager.js');
  }

  public function _bootstrap()
  {
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Store_Plugin_Core, 202);
	}
}