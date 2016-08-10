<?php
class Yncredit_Model_DbTable_Credits extends Engine_Db_Table
{
	protected $_name = "yncredit_credits";
	protected $_rowClass = "Yncredit_Model_Credit";
	
	public function getCreditSelect($levelId = 0)
	{
		$creditTypeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
		$creditTypeTblName = $creditTypeTbl->info("name");
		$creditTblName = $this->info('name');
		
		$disableNames = "";
		$disableModules = array();
		if($levelId)
		{
			$modules = Engine_Api::_() -> getDbTable('modules', 'yncredit') -> getModulesDisabled($levelId);
			foreach($modules as $module)
			{
				$disableModules[] = $module['name'];
			}
		}
		if($disableModules)
			$disableNames = array_unique($disableModules);
		
		$select = $this->select()
			-> setIntegrityCheck(false)
			-> from($creditTblName)
			-> joinLeft($creditTypeTblName, "$creditTblName.type_id = $creditTypeTblName.type_id", array("action_type", "module", "content", "credit_default"))
			-> where("$creditTypeTblName.module not in (?)", $disableNames);
		return $select;
	}
	
	public function getCreditByActionType($actionType, $user)
	{
		$select = $this->getCreditSelect($user->level_id);
		$select ->where("level_id = ?", $user->level_id)
				->where("action_type = ?", $actionType);
		return $this->fetchRow($select);
	}
	
	public function getAllActionEnableByLevel($levelId, $type = 'admin')
	{
		$creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
	    $creditTblName = $creditTbl->info("name");
	    $typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
	    $typeTblName = $typeTbl->info("name");
		$moduleTbl = Engine_Api::_()->getDbTable("modules", "core");
	    
		$disableModules = array();
	    if($type != 'admin')
	    {
	    	$modules = Engine_Api::_() -> getDbTable('modules', 'yncredit') -> getModulesDisabled($levelId);
			foreach($modules as $module)
			{
				$disableModules[] = $module['name'];
			}
	    }
		
		$modules = $moduleTbl->select()->where("enabled = ?", 1)->query()->fetchAll();
	    $enabledModules = array();
	    foreach ($modules as $key => $module)
	    {
	    	if(!in_array($module['name'], $disableModules))
			{
	    		$enabledModules[] = $module['name'];
			}
	    }
	    
	    $enableNames = "";
		if($enabledModules)
			$enableNames = array_unique($enabledModules);
	    
	    $select = $creditTbl->select()
	    	->from($creditTblName)
	    	->setIntegrityCheck(false)
	    	->joinLeft($typeTblName, "$creditTblName.type_id = $typeTblName.type_id", array("action_type", "module", 'link_params'))
	    	->where("$creditTblName.level_id = ?", $levelId)
	    	->where("$typeTblName.module in (?)", $enableNames)
			->where("$typeTblName.module <> 'yncredit'")
	    	->order("$typeTblName.module ASC");
			
		// Check mp3 music and mp3 music selling
		if(in_array('mp3music', $enabledModules))
		{
			$select_mp3music = $moduleTbl->select()->where("name = 'mp3music'");
			$mp3music = $moduleTbl -> fetchRow($select_mp3music);
			if($mp3music && strpos($mp3music -> version, "s") == FALSE)
			{
				$select -> where("$typeTblName.`action_type` <> 'buy_mp3music'");
			}
		}
		return $creditTbl->fetchAll($select);
	}
	
}