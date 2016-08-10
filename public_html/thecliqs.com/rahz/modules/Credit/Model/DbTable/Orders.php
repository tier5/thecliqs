<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Orders.php 24.01.12 14:00 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Model_DbTable_Orders extends Engine_Db_Table
{
  protected $_rowClass = "Credit_Model_Order";

  public function getLastPendingOrder()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $select = $this->select()
      ->where('user_id = ?', $viewer->getIdentity())
      ->where('status = ?', 'pending')
      ->limit(1);
    ;
    return $this->fetchRow($select);
  }
}
