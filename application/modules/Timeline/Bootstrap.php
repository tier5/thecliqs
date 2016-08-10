<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Timeline_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    // Add view helper and action helper paths
      parent::__construct($application);
      $this->initViewHelperPath();
  }

  public function _bootstrap($resource = null)
  {
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Timeline_Plugin_Core, 203);
  }
}

