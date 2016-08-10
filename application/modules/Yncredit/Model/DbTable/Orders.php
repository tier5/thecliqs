<?php
class Yncredit_Model_DbTable_Orders extends Engine_Db_Table
{
  protected $_rowClass = "Yncredit_Model_Order";

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
