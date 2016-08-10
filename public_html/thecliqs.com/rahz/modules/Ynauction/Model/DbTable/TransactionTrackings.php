<?php
 /**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: TransactionTrackings.php
 * @author     Minh Nguyen
 */
class Ynauction_Model_DbTable_TransactionTrackings extends Engine_Db_Table
{
   protected $_name     = 'ynauction_transaction_trackings';
   protected $_primary  = 'transactiontracking_id';   
   protected $_rowClass = "Ynauction_Model_TransactionTracking";

}