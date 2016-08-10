<?php
class Ynfeed_Model_DbTable_Customtypes extends Engine_Db_Table {

	protected $_name = 'ynfeed_customtypes';
	protected $_rowClass = 'Ynfeed_Model_Customtype';

	public function getCustomTypeList($params = array()) {
		$moduleTableName = Engine_Api::_() -> getDbtable('modules', 'core') -> info('name');
		$tableName = $this -> info('name');
		
		$contentTableName = Engine_Api::_() -> getDbtable('contents', 'ynfeed') -> info('name');

		$select = $this -> select() -> setIntegrityCheck(false) -> distinct(true)
			-> from($tableName, array("$tableName.*")) 
			-> join($moduleTableName, "$tableName.module_name = $moduleTableName.name", array("$moduleTableName.title as module_title")) 
			-> where($moduleTableName . '.enabled  = ?', 1);
		if (isset($params['enabled'])) {
			$select -> where($tableName . '.enabled= ?', $params['enabled']);
		}
		if (isset($params['module_name'])) 
		{
			$select -> where($tableName . '.module_name= ?', $params['module_name']);
		}
		else 
		{
			$select -> join($contentTableName, "$tableName.module_name = $contentTableName.module_name","") -> order("$contentTableName.order ASC");
		}
		$select -> order("$tableName.order ASC");
		return $this -> fetchAll($select);
	}
	public function getCustomTypes($params = array()) 
	{
		$moduleTableName = Engine_Api::_() -> getDbtable('modules', 'core') -> info('name');
		$tableName = $this -> info('name');

		$select = $this -> select()
			-> from($tableName, array("$tableName.*")) 
			-> join($moduleTableName, "$tableName.module_name = $moduleTableName.name", "") 
			-> where($moduleTableName . '.enabled  = ?', 1);
			
		if(isset($params['module_name']))
		{
			$select -> where("$tableName.module_name = ?", $params['module_name']);
		}
		return $this -> fetchAll($select);
	}
	public function getEnableCustomType($params = array()) {
		$moduleTableName = Engine_Api::_() -> getDbtable('modules', 'core') -> info('name');
		$tableName = $this -> info('name');

		$select = $this -> select() -> setIntegrityCheck(false) 
			-> from($tableName, $tableName . ".resource_type") 
			-> join($moduleTableName, "$tableName.module_name = $moduleTableName.name", array("$moduleTableName.title as module_title")) 
			-> where($moduleTableName . '.enabled  = ?', 1) 
			-> order($tableName . '.order');
		if (isset($params['enabled'])) {
			$select -> where($tableName . '.enabled= ?', $params['enabled']);
		}
		$enableResource = $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
		if(in_array('advgroup', $enableResource))
		{
			array_push($enableResource, 'group');
		}
		if(in_array('ynevent_event', $enableResource))
		{
			array_push($enableResource, 'event');
		}
		return $enableResource;
	}
	
}
