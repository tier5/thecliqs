<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexController.php 2010-08-31 16:05 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_AdminIndexController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hecore_admin_main', array(), 'hecore_admin_main_plugins');
    $this->view->special_mode = _ENGINE_ADMIN_NEUTER;
  }

  public function indexAction()
  {
	return $this->_helper->redirector->gotoRoute(array('module' => 'hecore', 'controller' => 'settings', 'action' => 'index'), 'admin_default', true);
  }

  public function updateAction()
  {
  }

  public function checkAction()
  {
  }

  public function isSuperAdmin()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer || !$viewer->getIdentity()) {
      return false;
    }
    $viewerLevel = Engine_Api::_()->getDbtable('levels', 'authorization')->find($viewer->level_id)->current();

    if (null === $viewerLevel || $viewerLevel->flag != 'superadmin') {
      return false;
    }
    return true;
  }

  public function request($url, $post = false)
  {
  }

  public function putArchive($url)
  {
    $result = false;
    if ($contents = $this->request($url)) {
      $uid = substr(md5(rand(1111, 9999)), 0, 15);
      $fp = fopen(getcwd() . "/temporary/package/archives/" . $uid . ".tar", "w");
      $result = fwrite($fp, $contents);
      fclose($fp);
    }
    return $result;
  }

}