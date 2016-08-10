<?php
class Ynjobposting_Model_DbTable_Packages extends Engine_Db_Table {
	protected $_rowClass = 'Ynjobposting_Model_Package';
	
	public function getPackagesPaginator($params = array()) {
		return Zend_Paginator::factory($this -> getPackagesSelect($params));
	}

	public function getPackagesSelect($params = array()) {
		$select = $this -> select() -> where('deleted = 0');
		
		if(isset($params['title']))
		{
			$select -> where('title LIKE ?', '%'.$params['title'].'%');
		}
		
		if (empty($params['direction'])) {
			$params['direction'] = 'DESC';
		}
			
	    if (!empty($params['order'])) {
			$select -> order($params['order'] . ' ' . $params['direction']);
		} else {
			$select -> order('order ASC');
		}
		
		return $select;
	}
}
