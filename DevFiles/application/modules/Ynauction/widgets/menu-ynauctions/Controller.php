<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: menu auctions
 * @author     Minh Nguyen
 */
class Ynauction_Widget_MenuYnauctionsController extends Engine_Content_Widget_Abstract
{
   protected $_navigation;
  public function indexAction()
  {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_main');
  }
}
