<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 17.08.12
 * Time: 10:40
 * To change this template use File | Settings | File Templates.
 */
class Donation_Widget_BrowseMenuProjectQuickController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('donation_quick');

    $quickNavigation->removePage($quickNavigation->findOneBy('controller','charity'));
  }
}
