<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2010-12-17 22:10 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Weather_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('weather_admin_main', array(), 'weather_admin_main_settings');

  	$this->view->form = $form = new Weather_Form_Admin_Global();
  	$settings = Engine_Api::_()->getApi('settings', 'core');
  	
  	if (!$this->getRequest()->isPost()) {
  		return ;
  	}
  	
    if (!$form->isValid($this->getRequest()->getPost())) {
      return ;
    }

    $value = $form->getValue('default_location');
  	$settings->setSetting('weather.default_location', $value);
  	$form->default_location->setValue($value);

  	$value = $form->getValue('unit_system');
  	$settings->setSetting('weather.unit_system', $value);
  	$form->unit_system->setValue($value);

  }
}