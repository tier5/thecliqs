<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Core.php
 * @author     Minh Nguyen
 */
class Ynauction_Plugin_Core
{
  public function onStatistics($event)
  {
    $table  = Engine_Api::_()->getDbTable('products', 'ynauction');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count')->where('is_delete = 0');
    $event->addResponse($select->query()->fetchColumn(0), 'ynauction');
  }
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete auctuons
      $ynauctionTable = Engine_Api::_()->getDbtable('products', 'ynauction');
      $ynauctionSelect = $ynauctionTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $ynauctionTable->fetchAll($ynauctionSelect) as $ynauction ) {
        $ynauction->is_delete = 1;
        $ynauction->save();
      }
      
       $ynauctionSelect = $ynauctionTable->select()->where('bider_id = ?', $payload->getIdentity());
      foreach( $ynauctionTable->fetchAll($ynauctionSelect) as $ynauction ) {
      if($ynauction->bider_id == $payload->getIdentity())
      {
         $ynauction->bider_id = -1;  
      }
      $bids = Engine_Api::_()->ynauction()->getBidUserHis($ynauction->product_id,$payload->getIdentity());
        $ynauction->total_bids = $ynauction->total_bids - Count($bids);
        $ynauction->save();
      }
      
      foreach(Engine_Api::_()->ynauction()->getBidUser($payload->getIdentity()) as $bid ) {
        $bid->delete();
      }
    }
  }
}