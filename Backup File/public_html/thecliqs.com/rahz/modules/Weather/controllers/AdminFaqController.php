<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminFaqController.php 2010-12-17 22:10 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Weather_AdminFaqController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('weather_admin_main', array(), 'weather_admin_main_faq');
  }
}