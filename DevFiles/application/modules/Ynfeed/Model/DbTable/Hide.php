<?php
class Ynfeed_Model_DbTable_Hide extends Engine_Db_Table {

  public function getHideItemByMember($user, $params=array()) 
  {
    $hideItems = array();
    $select = $this->select()
            ->where('user_id  = ?', $user->getIdentity());
    if (isset($params['not_activity_action']) && $params['not_activity_action'])
	{
    	$select->where('hide_resource_type  != ?', 'activity_action');
	}
    $results = $select->query()
            ->fetchAll();
    foreach ($results as $result) 
    {
    	$hideItems[$result['hide_resource_type']][] = $result['hide_resource_id'];
    }
    return $hideItems;
  }

}