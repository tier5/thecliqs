<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: listing auctions
 * @author     Minh Nguyen
 */
class Ynauction_Widget_SearchListingYnauctionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$values = $request -> getParams();
    // Do the show thing
	$where =" display_home = '1' AND stop = 0 AND approved = '1'";
	$values['where'] = $where;
	$values['page'] = $request -> getParam('page', 1);
	$this->view->homeres = $paginator = Engine_Api::_()->ynauction()->getProductsPaginator($values);
	$this->view->user_id = $viewer->getIdentity();
 }
}
?>
