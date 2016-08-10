<?php
class Ynfeed_Model_DbTable_Contents extends Engine_Db_Table {

	protected $_name = 'ynfeed_contents';
	protected $_rowClass = 'Ynfeed_Model_Content';
	public function getContentList($params = array()) 
	{
		$moduleTableName = Engine_Api::_() -> getDbtable('modules', 'core') -> info('name');
		$tableName = $this -> info('name');

		$select = $this -> select() -> setIntegrityCheck(false) 
			-> from($tableName, array("$tableName.*")) 
			-> join($moduleTableName, "$tableName.module_name = $moduleTableName.name", array("$moduleTableName.title as module_title")) 
			-> where($moduleTableName . '.enabled  = ?', 1) 
			-> order($tableName . '.order');
		if(isset($params['show']))
		{
			 $select->where($tableName . '.show  = 1');
		}
		if(isset($params['content_tab']))
		{
			 $select->where($tableName . '.content_tab  = 1');
		}
		return $this -> fetchAll($select);
	}
	
	public function getContents($params = array()) 
	{
		$moduleTableName = Engine_Api::_() -> getDbtable('modules', 'core') -> info('name');
		$tableName = $this -> info('name');

		$select = $this -> select()
			-> from($tableName, array("$tableName.*")) 
			-> joinLeft($moduleTableName, "$tableName.module_name = $moduleTableName.name", "") 
			-> where($moduleTableName . '.enabled  = ?', 1) 
			-> order($tableName . '.order');
		if (isset($params['filter_type'])) {
	      $select->where($tableName . '.filter_type  = ?', $params['filter_type']);
	      return $this->fetchRow($select);
	    }
		return $this -> fetchAll($select);
	}

	public function getAddedModule() 
	{
		$select = $this -> select() -> setIntegrityCheck(false) 
			-> from($this -> info('name'), "module_name");
		return $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
	}

}
