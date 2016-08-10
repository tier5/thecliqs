<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_BrowseMenuCreationController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if(!$viewer -> getIdentity())
		{
			$this -> setNoRender();
		}
		// Get navigation
		$this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynvideochannel_create_quick', array(), null);
	}

}
