<?php

class Ynresume_Model_Industry extends Ynresume_Model_Node {
	
	protected $_parent_type = 'user';

	protected $_owner_type = 'user';

	protected $_type = 'ynresume_industry';
    
	
	public function getHref($params = array()) {
	    $params = array_merge(array(
            'route' => 'ynresume_general',
            'controller' => 'index',
            'action' => 'listing',
            'industry_id' => $this->getIdentity(),
        ), $params);
        $route = $params['route'];
        unset($params['route']);
        return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, true);
	}
	
	public function getTable() {
		if(is_null($this -> _table)) {
			$this -> _table = Engine_Api::_() -> getDbtable('industries', 'ynresume');
		}
		return $this -> _table;
	}
	
	public function checkHasResume()
	{
		$table = Engine_Api::_() -> getDbTable('resumes', 'ynresume');
		$select = $table -> select() -> where('industry_id = ?', $this->getIdentity()) -> limit(1);
		$row = $table -> fetchRow($select);
		if($row)
			return true;
		else {
			return false;
		}
	}
	
	public function getMoveIndustriesByLevel($level)
	{
		$table = Engine_Api::_() -> getDbtable('industries', 'ynresume');
		$select = $table -> select() 
				-> where('industry_id <>  ?', 1) // not default
				-> where('industry_id <>  ?', $this->getIdentity())// not itseft
				-> where('level = ?', $level);
		$result = $table -> fetchAll($select);
		return $result;
	}
	
	public function setTitle($newTitle) {
		$this -> title = $newTitle;
		$this -> save();
		return $this;
	}

	public function shortTitle() {
		return strlen($this -> title) > 20 ? (substr($this -> title, 0, 17) . '...') : $this -> title;
	}
	
    public function getChildList() {
        $table = Engine_Api::_()->getItemTable('ynresume_industry');
        $select = $table->select();
        $select->where('parent_id = ?', $this->getIdentity());
        $childList = $table->fetchAll($select);
        return $childList;
    }
    
    public function getTitle() {
    	$view = Zend_Registry::get('Zend_View');
        return $view->translate($this->title);
    }
    
}
