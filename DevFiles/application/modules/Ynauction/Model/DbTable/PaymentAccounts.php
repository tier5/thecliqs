<?php
 /**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: PaymentAccounts.php
 * @author     Minh Nguyen
 */
class Ynauction_Model_DbTable_PaymentAccounts extends Engine_Db_Table
{
  protected $_name     = 'ynauction_payment_accounts';
  protected $_primary  = 'paymentaccount_id';
  protected $_rowClass = "Ynauction_Model_PaymentAccount";
}