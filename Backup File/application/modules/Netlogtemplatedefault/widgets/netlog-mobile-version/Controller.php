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

class Netlogtemplatedefault_Widget_NetlogMobileVersionController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

	$modules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

	$router = Zend_Controller_Front::getInstance()->getRouter();
	$this->view->mobile_link = array('uri' => $router->assemble(array()).'?mobile=1', 'enabled' => 1, 'label' => "Mobile Site");

	if ( !in_array('mobi',$modules) )
		return $this->setNoRender();

  }

}