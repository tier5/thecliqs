<?php

class Yncontest_Widget_AdminMainMenuController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (Zend_Registry::isRegistered('admin_active_menu'))
		{
			$active_menu = Zend_Registry::get('admin_active_menu');
		}
		else
		{
			$active_menu = null;
		}

		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yncontest_admin_main', array(), $active_menu);
	}

}
