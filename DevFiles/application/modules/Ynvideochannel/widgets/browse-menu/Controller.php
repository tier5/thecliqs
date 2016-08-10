<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Get navigation
		$this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynvideochannel_main', array(), null);
	}

}
