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
class Ynauction_Widget_ListingYnauctionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $now = date('Y-m-d H:i:s');
    // Do the show thing
    $where = " display_home = '1' AND stop = 0 AND status = '0' AND approved = '1' AND start_time <=  '$now' AND end_time >=  '$now'";// AND product_id <> ".$featured;
    $values['where'] = $where;
    $values['limit'] = 10;
    $this->view->homeres = $paginator = Engine_Api::_()->ynauction()->getProductsPaginator($values);
    if(count($paginator) <= 0)
        $this->setNoRender();
    $this->view->user_id = $viewer->getIdentity();

 }
}
?>
