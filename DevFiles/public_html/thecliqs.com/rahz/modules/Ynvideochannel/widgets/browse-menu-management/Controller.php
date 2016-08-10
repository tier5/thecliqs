<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_BrowseMenuManagementController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Get navigation
//		$this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynvideochannel_manage_quick', array(), null);
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$this -> view -> controller = $request -> getControllerName();
		$this -> view -> action = $request -> getActionName();
	}

}
