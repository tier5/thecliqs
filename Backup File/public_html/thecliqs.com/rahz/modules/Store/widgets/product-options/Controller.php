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

class Store_Widget_ProductOptionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_product_profile');
  }
}