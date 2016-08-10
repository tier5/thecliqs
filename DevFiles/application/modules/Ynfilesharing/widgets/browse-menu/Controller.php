<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
class Ynfilesharing_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{

	public function indexAction()
	{
		// Get navigation
		$this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfilesharing_main');
	}

}
