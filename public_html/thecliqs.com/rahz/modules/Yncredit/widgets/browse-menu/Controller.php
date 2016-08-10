<?php

class Yncredit_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
	 // Get navigation
	    $this->view->navigation = $navigation = Engine_Api::_()
	      ->getApi('menus', 'core')
	      ->getNavigation('yncredit_main', array());
	}
}
