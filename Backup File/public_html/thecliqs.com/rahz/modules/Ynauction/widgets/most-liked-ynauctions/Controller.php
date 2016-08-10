<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: most rated auctions
 * @author     Minh Nguyen
 */
class Ynauction_Widget_MostLikedYnauctionsController extends Engine_Content_Widget_Abstract
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
   $data = Ynauction_Model_Product::getMostLikeProducts($limit);
   if(count($data) <= 0)
        $this->setNoRender();
   $this->view->data = $data;
   $viewer = Engine_Api::_()->user()->getViewer();
   $this->view->user_id = $viewer->getIdentity();
 }
}
?>