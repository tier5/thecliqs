<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 15.03.12
 * Time: 18:28
 * To change this template use File | Settings | File Templates.
 */
class Page_Widget_BrowseMenuQuickController extends Engine_Content_Widget_Abstract
{
   public function indexAction()
   {

     // Get quick navigation
     $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
       ->getNavigation('page_quick');
   }
}
