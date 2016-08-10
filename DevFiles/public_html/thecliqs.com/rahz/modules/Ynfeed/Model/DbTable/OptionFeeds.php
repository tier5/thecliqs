<?php
class Ynfeed_Model_DbTable_OptionFeeds extends Engine_Db_Table {

  protected $_name = 'ynfeed_optionfeeds';

  public function getOptionFeed($user, $action_id, $type) 
  {
    return $this->select()
                    ->from($this, 'action_id')
                    ->where('user_id = ?', $user->getIdentity())
                    ->where('action_id = ?', $action_id)
                    ->where('type = ?', $type)
                    ->query()
                    ->fetchColumn();
  }
  
  public function getActiveNotification($action_id) 
  {
    $select = $this->select()
                    ->where('action_id = ?', $action_id)
                    ->where('type = ?', 'notification')
                    ->where('value = ?', '1');
	return $this -> fetchAll($select);
  }
  public function getDeactiveNotification(User_Model_User $user, $action_id) 
  {
  	$select = $this->select()
					->where('user_id = ?', $user->getIdentity())
                    ->where('action_id = ?', $action_id)
                    ->where('type = ?', 'notification')
                    ->where('value = ?', '0');
	return $this -> fetchRow($select);
  }
  
  public function setOptionFeeds(User_Model_User $user, $action_id, $action_type, $type, $value = 0) 
  {
    if (null === ($prev = $this->getOptionFeed($user, $action_id, $type)) ||
            false === $prev) {
      $this->insert(array(
          'user_id' => $user->getIdentity(),
          'action_type' => $action_type,
          'type' => $type,
          'value' => $value,
          'action_id' => $action_id
      ));
    } else 
    {
       $select = $this->select()
                    ->where('user_id = ?', $user->getIdentity())
                    ->where('action_id = ?', $action_id)
                    ->where('type = ?', $type)
                    ->limit(1);
		$optionFeed = $this -> fetchRow($select);
		if($optionFeed)
			$optionFeed -> delete();
    }

    return $this;
  } 
  public function getNotificationFeeds(User_Model_User $user, $types, $params = array()) 
  {
    $limit = (!empty($params['limit']) ? $params['limit'] : 15) * 2;
    $max_id = $params['max_id'];
    $select = $this->select()
            ->where('user_id = ?', $user->getIdentity())
            ->where('action_type IN(?)', (array) $types)
			->where("type = 'notification'") -> where('value = 1')
            ->limit($limit);
    if (null !== $max_id) 
    {
      $select->where('action_id <= ?', $max_id);
    }
    $data = $select
            ->query()
            ->fetchAll();

    $settings = array();
    foreach ($data as $row) 
    {
      $settings[] = $row['action_id'];
    }	
    return $settings;
  }
}