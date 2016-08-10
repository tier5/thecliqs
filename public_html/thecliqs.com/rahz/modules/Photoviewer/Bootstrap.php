<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 08.02.13 10:28 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Photoviewer_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

    Zend_Controller_Front::getInstance()->registerPlugin(new Photoviewer_Controller_Plugin_Photoviewer());

  }
}