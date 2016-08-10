<?php

class Yncontest_Model_Location extends Core_Model_Item_Abstract {
	
	protected $_searchTriggers = false;
	
	const MAX_LEVEL =  8;


	public function getId() {
		return $this -> location_id;
	}

	public function getType() {
		return 'location';
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getTitle(){
		return $this->name;
	}
	
	public function getAscendant(){
		// check if it has breadcrumb.
		$pids = array();
		for($i=0;$i<=$this->level; ++$i){
			$pids[]=  $this->{"p$i"};
		}
		$table =  $this->_table;
		$select =  $table->select()->where('location_id in (?)', $pids)->order('level')->order('ordering');
		return $table->fetchAll($select);
	}
	
	public function getHref(){
		return $this->slug;
	}
	
	public function getPath(){
		foreach($this->getAscendant() as $item){
			$result[] =  sprintf('<a href="%s">%s</a>', $item->getHref(),$item->getName());
		}
		return implode(' - ', $result);
	}
		
	public function getSimplePath($glue =  ' &raquo; '){
		
		$result =  array();
		foreach($this->getAscendant() as $item){
			$result[] =  $item->getName();
		}
		return implode($glue, $result);
	}
	
	public function getParent(){
		if($this->pid){
			return $this->_table->find($this->pid)->current();	
		}
		return NULL;
	}
	
	public function getLevel(){
		return $this->level;
	}
	
	public function getIndexKey($index){
		return 'p'.$index;
	}
	
	public function getIndexValue($index){
		return $this->{'p'.($index)};
	}
	
	public function getIndexTree($index){
		return $this->{'p'.$index};
	}
	
	public function countSub(){
		$table =  $this->_table;
		$key = 'p'. $this->level;
		$select =  $table->select()->where("$key=?", $this->getIdentity());
		return 0;
	}
	
	public function getDescendantIds(){
		$key =  $this->getIndexKey($this->getLevel());
		$value =  $this->getIndexValue($this->getLevel());
		$table =  $this->_table;
		$db =  $this->_table->getAdapter();	
		$name =  $table->info('name');
		return $db->fetchCol("select * from $name where $key=$value");
	}
	public function getDescendant(){
		$key =  $this->getIndexKey($this->getLevel());
		$value =  $this->getIndexValue($this->getLevel());
		$table =  $this->_table;
		$db =  $this->_table->getAdapter();	
		$name =  $table->info('name');
		$select = $table->select()->where("$key= ?", $value);
		return $table->fetchAll($select);
	}
	public function getUsedCount() {
		$table = Engine_Api::_() -> getDbTable('Entries', 'yncontest');
		$rName = $table -> info('name');
		$ids =  $this->getDescendantIds();
		$select = $table -> select() -> from($rName) -> where($rName . '.location_id in (?)', $ids)->where('deleted = 0');
		$row = $table -> fetchAll($select);
		return $row;
	}
	
}
