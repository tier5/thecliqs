<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: HandlerController.php 2010-07-02 19:54 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Suggest
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Suggest_HandlerController extends Core_Controller_Action_Standard
{
  public function requestAction()
  {
    $this->view->notification = $notification = $this->_getParam('notification');
    $this->view->suggest = $notification->getObject();
  }
}