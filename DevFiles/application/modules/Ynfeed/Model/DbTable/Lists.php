<?php
class Ynfeed_Model_DbTable_Lists extends Engine_Db_Table {

  protected $_rowClass = 'Ynfeed_Model_List';

  public function getMemberOfList(Core_Model_Item_Abstract $resource) {
    $select = $this->select()
            ->where('owner_id 	 = ?', $resource->getIdentity());
    return $this->fetchAll($select);
  }

}