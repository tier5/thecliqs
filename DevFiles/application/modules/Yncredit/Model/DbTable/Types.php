<?php
class Yncredit_Model_DbTable_Types extends Engine_Db_Table
{
	protected $_name = "yncredit_types";
	protected $_rowClass = "Yncredit_Model_Type";
	
	public function getTypeSelect($type = 'admin')
	{
		if($type != 'admin')
	    {
	    	$viewer = Engine_Api::_() -> user() -> getViewer();
			$levelId = 0;
			if($viewer)
			{
				$levelId = $viewer -> level_id;
			}
	    	$modules = Engine_Api::_() -> getDbTable('modules', 'yncredit') -> getModulesDisabled($levelId);
			$disableModules = array();
			foreach($modules as $module)
			{
				$disableModules[] = $module['name'];
			}
	    }
		
		$moduleTbl = Engine_Api::_()->getDbTable("modules", "core");
		$modules = $moduleTbl->select()->where("enabled = ?", 1)->query()->fetchAll();
		$enabledModules = array();
		foreach ($modules as $key => $module)
		{
			if(!in_array($module['name'], $disableModules))
				$enabledModules[] = $module['name'];
		}
		
		$enableNames = "";
		if($enabledModules)
			$enableNames = array_unique($enabledModules);
		
		$select = $this -> select()
			-> where("module in (?)", $enableNames)
			-> order("module ASC");
			
		// Check mp3 music and mp3 music selling
		if(in_array('mp3music', $enabledModules))
		{
			$select_mp3music = $moduleTbl->select()->where("name = 'mp3music'");
			$mp3music = $moduleTbl -> fetchRow($select_mp3music);
			if($mp3music && strpos($mp3music -> version, "s") == FALSE)
			{
				$select -> where("`action_type` <> 'buy_mp3music'");
			}
		}
		return $select;
	}
	
	public function getAllActions($type = 'admin')
	{
		$select = $this->getTypeSelect($type);
		return $this -> fetchAll($select);
	}
	
}