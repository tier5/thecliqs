<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: running aucitons
 * @author     Minh Nguyen
 */
class Ynauction_Widget_EndingSoonYnauctionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
   if($this->_getParam('max') != ''){       
            $limit = $this->_getParam('max');
            if ($limit <=0)
            {
                $limit = 5;
            }
        }else{
        $limit = 5; }
   $now = date('Y-m-d H:i:s');
   $where = " display_home = '1' AND status = '0'  AND start_time <= '$now' AND end_time >= '$now' AND approved = '1'"; 
   $data = Ynauction_Model_Product::getProducts($where,'end_time ASC',$limit);
   if(count($data) <= 0)
        $this->setNoRender();
   $this->view->data = $data;
   $viewer = Engine_Api::_()->user()->getViewer();
   $this->view->user_id = $viewer->getIdentity();
 }
}
?>