<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2013-01-17 14:04:44 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedalbum_Widget_NavigationTabsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('headvancedalbum_main');

    $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    //$filter = $params['filter'];

    /*foreach( $navigation->getPages() as $page ) {
      if ($page->route == "headvancedalbum_".$filter) {
        $page->active = true;
      } else {
        $page->active = false;
      }
    }*/
  }
}