<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Netlogtemplatedefault
 * @copyright  Copyright 2010-2012 SocialEnginePro
 * @license    http://www.socialenginepro.com
 * @author     altrego aka Vadim ( provadim@gmail.com )
 */

class Netlogtemplatedefault_Widget_NetlogmainmenuController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

	$this->view->navigation = $navigation = Engine_Api::_()
		->getApi('menus', 'core')
		->getNavigation('core_main');

	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
	$require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
	if(!$require_check && !$viewer->getIdentity()){
		$navigation->removePage($navigation->findOneBy('route','user_general'));
	}

  }

}