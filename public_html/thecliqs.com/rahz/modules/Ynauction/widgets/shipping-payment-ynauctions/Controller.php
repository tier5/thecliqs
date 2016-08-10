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
class Ynauction_Widget_ShippingPaymentYnauctionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
      $product = Engine_Api::_()->core()->getSubject();
      $viewer = Engine_Api::_()->user()->getViewer();
      $user_id =  $viewer->getIdentity();  
      if(!$product)
      {
          $this->setNoRender();
          return;
      }
      if((($product->display_home != 0 &&  $product->approved == 1) || $product->user_id == $user_id) && $product->is_delete == 0)
      {
      $this->view->product = $product;
      }
      else
      {
           $this->setNoRender();
           return;
      }
      
 }
}
?>