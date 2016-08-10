<?php
class Ynbusinesspages_Model_DbTable_Packages extends Engine_Db_Table {
	protected $_rowClass = 'Ynbusinesspages_Model_Package';
	protected $_serializedColumns = array('themes', 'category_id');
	
	public function getPackagesPaginator($params = array()) {
		return Zend_Paginator::factory($this -> getPackagesSelect($params));
	}

	public function getPackagesSelect($params = array()) {
		$select = $this -> select() -> where('deleted = 0') -> where('current = 1');
		
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
