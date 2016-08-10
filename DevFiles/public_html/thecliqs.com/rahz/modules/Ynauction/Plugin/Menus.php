<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Menus.php
 * @author     Minh Nguyen
 */
class Ynauction_Plugin_Menus
{
  public function canCreateAuctions()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    // Must be able to create Auction
    if( !Engine_Api::_()->authorization()->isAllowed('ynauction_product', $viewer, 'create') ) {
      return false;
    }
     if(!Engine_Api::_()->ynauction()->checkBecome($viewer->getIdentity()))
        return false;
    return true;
  }

  public function canViewAuctions()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    // Must be able to view Auction
    if( !Engine_Api::_()->authorization()->isAllowed('ynauction_product', $viewer, 'view') ) {
      return false;
    }
    return true;
  }
  public function canManageAuctions()
  {
  	 $viewer = Engine_Api::_()->user()->getViewer();
  if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    // Must be able to view Auction
    if( !Engine_Api::_()->authorization()->isAllowed('ynauction_product', $viewer, 'view') ) {
      return false;
    }
     //Check have account yet?   
    $account = Ynauction_Api_Cart::getFinanceAccount($viewer->getIdentity());
   if(!$account)
        return false; 
    if(!Engine_Api::_()->ynauction()->checkBecome($viewer->getIdentity()))
        return false;
    return true;
  }
  public function buyerConfirmAuction()
  {
      $viewer = Engine_Api::_()->user()->getViewer();
    
      if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    if(!Engine_Api::_()->ynauction()->checkConfirm($viewer->getIdentity()))
        return false;
    return true;
  }
  public function buyerBoughtAuction()
  {
  	$viewer = Engine_Api::_()->user()->getViewer();
    
  	if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    if(!Engine_Api::_()->ynauction()->checkBought($viewer->getIdentity()))
        return false;
    return true;
  }
  public function becomeSellerAuction()
  {
      $viewer = Engine_Api::_()->user()->getViewer();
      if( !$viewer || !$viewer->getIdentity() ) {
        return false;
    }
    if(Engine_Api::_()->ynauction()->checkBecome($viewer->getIdentity()))
        return false; 
    return true;
  }
  public function canFaqs()
  {
    //$viewer = Engine_Api::_()->user()->getViewer();
    //if( !$viewer || !$viewer->getIdentity() ) {
    //  return false;
    //}
    return true;
  }
  public function canHelp()
  {
    //$viewer = Engine_Api::_()->user()->getViewer();
    //if( !$viewer || !$viewer->getIdentity() ) {
    //  return false;
    //}
    return true;
  }
}