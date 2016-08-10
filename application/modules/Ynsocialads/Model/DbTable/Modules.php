<?php

class Ynsocialads_Model_DbTable_Modules extends Engine_Db_Table {
	protected $_rowClass = 'Ynsocialads_Model_Module';

	public function getModulesSelect($params = array()) {

		$select = $this -> select();
		$moduleTbl = Engine_Api::_() -> getDbTable("modules", "core");
		$modules = $moduleTbl -> select() -> where("enabled = ?", 1) -> query() -> fetchAll();
		$enabledModules = array();
		foreach ($modules as $key => $module) {
			$enabledModules[] = $module['name'];
		}
		$enableNames = "";
		if ($enabledModules)
			$enableNames = array_unique($enabledModules);

		$select = $this -> select() -> where("module_name in (?)", $enableNames);
		if (!empty($params['module_name'])) {
			$select -> where('module_name LIKE ?', '%' . $params['module_name'] . '%');
		}
		if (empty($params['direction'])) {
			$params['direction'] = 'DESC';
		}
		if (!empty($params['order'])) {
			$select -> order($params['order'] . ' ' . $params['direction']);
		} else {
			$select -> order('module_id DESC');
		}
		return $select;
	}

	public function getModulesPaginator($params = array()) {
		$paginator = Zend_Paginator::factory($this -> getModulesSelect($params));
		if (!empty($params['page'])) {
			$paginator -> setCurrentPageNumber($params['page']);
		}
		return $paginator;
	}

	public function getModules() {
		$select = $this -> select();
		$moduleTbl = Engine_Api::_() -> getDbTable("modules", "core");
		$modules = $moduleTbl -> select() -> where("enabled = ?", 1) -> query() -> fetchAll();
		$enabledModules = array();
		foreach ($modules as $key => $module) {
			$enabledModules[] = $module['name'];
		}
		$enableNames = "";
		if ($enabledModules)
			$enableNames = array_unique($enabledModules);

		$select = $this -> select() -> where("module_name in (?)", $enableNames);
		$select -> distinct() -> from(array('p' => $this -> info('name')), array('module_name', 'module_title'));
		return $this -> fetchAll($select);
	}

}
