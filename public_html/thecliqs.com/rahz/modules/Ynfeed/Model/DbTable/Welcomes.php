<?php
class Ynfeed_Model_DbTable_Welcomes extends Engine_Db_Table {

	protected $_name = 'ynfeed_welcomes';
	protected $_rowClass = 'Ynfeed_Model_Welcome';
	
	public function getWelcomes($params = array()) 
	{
		$select = $this -> select();
		if(isset($params['show']))
		{
			$select -> where('`show` = ?', $params['show']);
		}
		$select -> order("order ASC");
		return $this -> fetchAll($select);
	}
	public function getWelcome($params = array()) 
	{
		$contents = $this -> getWelcomes(array('show' => 1));
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if(!$viewer -> getIdentity())
		{
			return NULL;
		}
		
		// Keep only content
		$content = null;
		foreach($contents as $item)
		{
			extract($item -> toArray());
			// check display limitation
			if($display_limit == 1) // signup days
			{
				$signUp_date = $viewer -> creation_date;
				$secs = time() - strtotime($signUp_date);
				$days = $secs / 86400;
				if($days > $number_of_limit)
				{
					continue;
				}
			}
			elseif($display_limit == 2) // friends
			{
				if($viewer -> member_count < $number_of_limit)
				{
					continue;
				}
			}
			
			// check member levels
			$level_id = $viewer -> level_id;
			$member_level_array = array();
			if($member_levels)
				$member_level_array = json_decode($member_levels);
			// Check if Member Level Matches Content Level
	      	if(empty($member_level_array) || !in_array( $level_id, $member_level_array)) 
	      	{
	       		 continue;
	      	}
			     
			// check networks
			$network_table = Engine_Api::_()->getDbtable('membership', 'network');
	      	$network_select = $network_table->select('resource_id')->where('user_id = ?', $viewer -> getIdentity());
	      	$network_id_query = $network_table->fetchAll($network_select);
	      	$network_id_query_count = count($network_id_query);
	      	$network_id_array = array();
	     	for($i = 0; $i < $network_id_query_count; $i++) {
	        	$network_id_array[$i] = $network_id_query[$i]['resource_id'];
	     	}
			$network_array = array();
			if($networks)
				$network_array = json_decode($networks); 
	        // Check if Member Networks Match Content Networks
	        if ($network_array != NULL) // else allow for all users. 
			{
				$continue = true;
	            foreach ($network_array as $value) 
	            {
	                if(in_array( $value, $network_id_array)) 
	                {
	                    $continue = false;
	                    break;
	                }
	            }
		        if ($continue)
		            continue;
			}
			$content = $item;
			break;
		}
		return $content;
	}
}