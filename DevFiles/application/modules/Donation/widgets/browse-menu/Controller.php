<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 19.07.12
 * Time: 17:18
 * To change this template use File | Settings | File Templates.
 */
class Donation_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    // Get navigation
    if ($request->getControllerName() == 'index') $type =  'donation_main_browse_charity';
    elseif ($request->getControllerName() == 'project') $type = 'donation_main_browse_project';
    else $type = 'donation_main_browse_fundraise';

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('donation_main', array(), $type);

  }
}
