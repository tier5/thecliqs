<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: featured auctions
 * @author     Minh Nguyen
 */
class Ynauction_Widget_FeaturedYnauctionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
   $headScript = new Zend_View_Helper_HeadScript();
   $headScript -> appendFile('application/modules/Ynauction/externals/scripts/jquery-1.5.1.min.js');
   $headScript -> appendFile('application/modules/Ynauction/externals/scripts/jquery.divslideshow-1.2-min.js');
   $now = date('Y-m-d H:i:s');
   $where = " display_home = '1' AND approved = '1' AND stop = '0' AND featured='1'  AND start_time <=  '$now' AND  end_time > '$now' AND status = 0"; 
   $data = Ynauction_Model_Product::getProducts($where,'rand()',5);
   if(count($data) > 0)
   {
   
	   $this->view->datas = $data;
	   $viewer = Engine_Api::_()->user()->getViewer();
	   $this->view->user_id = $viewer->getIdentity();
   }
   else
   {
   	   $this->view->data = "";
   }
 }
}
?>