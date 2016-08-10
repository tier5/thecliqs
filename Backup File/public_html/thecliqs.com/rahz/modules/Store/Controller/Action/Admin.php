<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Admin.php 4/3/12 3:35 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

abstract class Store_Controller_Action_Admin extends Core_Controller_Action_Admin
{
  public function preDispatch()
  {
    $this->view->menu = $this->_getParam('action');
    return parent::preDispatch();
  }
}
