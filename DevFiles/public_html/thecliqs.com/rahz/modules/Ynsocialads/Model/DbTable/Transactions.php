<?php
 
class Ynsocialads_Model_DbTable_Transactions extends Engine_Db_Table
{
  protected $_rowClass = 'Ynsocialads_Model_Transaction';
  
  public function getAdTransaction($id)
  {
  	  $select = $this->select();
	  $select -> where('ad_id = ?', $id);
	  return $this-> fetchRow($select);
  }
}
