<?php  
class Yncontest_Api_Rule extends Core_Api_Abstract {
	
  const IMAGE_WIDTH = 720;
  const IMAGE_HEIGHT = 720;
  const THUMB_WIDTH = 200;
  const THUMB_HEIGHT = 150;
  
  	
	public function getRulesPaginator($params = array())
	{
		$paginator = Zend_Paginator::factory($this->getRulesSelect($params));
	
		if( !empty($params['page']) )
		{
			$paginator->setCurrentPageNumber($params['page']);
		}
		if( !empty($params['limit']) )
		{
			$paginator->setItemCountPerPage($params['limit']);
		}
		return $paginator;
	}
	public function getRulesSelect($params = array())
	{
		$table = new Yncontest_Model_DbTable_Rules();
		$rName = $table->info('name');
		$select = $table->select()->from($rName)->setIntegrityCheck(false);
			
		//search by rule_id
		if( isset($params['rule_id']) && is_numeric($params['rule_id']))
			//$select->where($rName.".rule_id = ? ",$params['rule_id']);
		//search by rule_name
		if( isset($params['rule_name']) && $params['rule_name'] != ' ')		
			$select->where($rName.".rule_name = ? ",$params['rule_name']);		
		//search by user_id
		if(!empty($params['user_id']) && is_numeric($params['user_id']))
			$select->where("$rName.user_id = ?",$params['user_id']);
		//search by contest_id
		if( isset($params['contest_id']) && is_numeric($params['contest_id']))
			$select->where($rName.".contest_id = ? ",$params['contest_id']);
		//search by viewentries
		if( isset($params['viewentries']) && is_numeric($params['viewentries']))
			$select->where($rName.".viewentries = ? ",$params['viewentries']);
		//search by submitentries
		if( isset($params['submitentries']) && is_numeric($params['submitentries']))
			$select->where($rName.".submitentries = ? ",$params['submitentries']);
		//search by viewentries
		if( isset($params['voteentries']) && is_numeric($params['voteentries']))
			$select->where($rName.".voteentries = ? ",$params['voteentries']);
		
		return $select;
	}

}