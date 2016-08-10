<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Widget_NavigationTabsController extends Engine_Content_Widget_Abstract
{
  public function indexAction(  )
  {

    /**
     * @var $request Zend_Controller_Request_Http
     */
    $menu = null;
    $request = Zend_Registry::get('Zend_Controller_Front')->getRequest();
    if($request->getControllerName() == 'panel')
    {
      $menu = 'store_main_panel';
    }

    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_main', array(), $menu);
  }
}