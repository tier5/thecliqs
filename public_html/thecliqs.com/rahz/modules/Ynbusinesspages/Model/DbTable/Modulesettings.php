<?php
class Ynbusinesspages_Model_DbTable_Modulesettings extends Engine_Db_Table 
{
	protected $_rowClass = 'Ynbusinesspages_Model_Modulesetting';
	
	public function getEnabledModuleSettings()
	{
		$settings = array();
		foreach ($this -> fetchAll($this -> select()) as $row)
		{
			if (in_array($row->key, array('music_create', 'music_delete'))) {
				if (Engine_Api::_()->hasModuleBootstrap('music') || Engine_Api::_()->hasModuleBootstrap('mp3music') || Engine_Api::_()->hasModuleBootstrap('ynmusic'))
					$settings[] = $row;
			}
			else if (Engine_Api::_()->hasModuleBootstrap($row->module_name))
			{
				$settings[] = $row;
			}
		}
		return $settings;
	}
}
