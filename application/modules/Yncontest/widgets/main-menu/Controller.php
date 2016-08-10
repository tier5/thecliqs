<?php

class Yncontest_Widget_MainMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(Zend_Registry::isRegistered('active_menu')){
  		$active_menu =  Zend_Registry::get('active_menu');
  	}else{
  		$active_menu = null;
  	}		
	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('yncontest_main',array(), $active_menu);
  }
}