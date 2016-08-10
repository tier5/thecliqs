<?php

class Ynjobposting_Model_Industry extends Ynjobposting_Model_Node {
	
	protected $_parent_type = 'user';

	protected $_owner_type = 'user';

	protected $_type = 'ynjobposting_industry';
	
	protected $_searchTriggers = false;
    
	public function getParentIndustryLevel1()
	{
		$i = 1;
		$loop_item = $this;
		while($i < 4)
		{
			$item = $loop_item -> getParent($loop_item -> getIdentity());
			if(count($item->themes) > 0)
			{
				return $item;
			}
			$loop_item = $item;
			$i++;
		}
	}
	
	public function getHref($type = null) {
	    if (is_null($type)) {
	        $type = 'job';
	    }
        $params = array(); 
        if ($type == 'company') {
            $params['route'] = 'ynjobposting_extended';
            $params['controller'] = 'company';
            $params['industry_id'] = $this->getIdentity();
        }
        else {
            $params['route'] = 'ynjobposting_job';
            $params['controller'] = 'jobs';
            $params['action'] = 'listing';
            $params['industry_id'] = $this->getIdentity();
        }
        $route = $params['route'];
        unset($params['route']);
        return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, true);
	}
    
	public function getTable() {
		if(is_null($this -> _table)) {
			$this -> _table = Engine_Api::_() -> getDbtable('industries', 'ynjobposting');
		}
		return $this -> _table;
	}
	
	public function checkHasCompany()
	{
		$table = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
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
		$table = Engine_Api::_() -> getDbtable('industries', 'ynjobposting');
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
        $table = Engine_Api::_()->getItemTable('ynjobposting_industry');
        $select = $table->select();
        $select->where('parent_id = ?', $this->getIdentity());
        $childList = $table->fetchAll($select);
        return $childList;
    }
    
}
