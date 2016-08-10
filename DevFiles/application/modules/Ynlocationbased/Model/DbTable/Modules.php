<?php
class Ynlocationbased_Model_DbTable_Modules extends Engine_Db_Table
{
	protected $_name = 'ynlocationbased_modules';

	public function checkModule($name)
	{
		$select = $this -> select();
		$select -> where('enabled = 1') -> where('module_name = ?', $name);
		return $this -> fetchRow($select)?1:0;
	}

	public function getModule($name)
	{
		$select = $this -> select();
		$select -> where('module_name = ?', $name);
		return $this -> fetchRow($select);
	}

	public function getModulePaginator($params = array())
	{
		$select = $this -> getModuleSelect($params);
		return Zend_Paginator::factory($select);
	}

	public function getModuleSelect($params = array())
	{
		$moduleTableName = Engine_Api::_() -> getDbtable('modules', 'core') -> info('name');
		$tableName = $this -> info('name');
		$select = $this -> select() -> setIntegrityCheck(false);
		$select -> from("$tableName", array("$tableName.*"))
				-> join($moduleTableName, "$tableName.module_name = $moduleTableName.name", array("$moduleTableName.title as module_title"))
				-> order("$moduleTableName.title ASC");
		return $select;
	}
}
