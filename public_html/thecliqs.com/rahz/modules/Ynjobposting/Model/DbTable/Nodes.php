<?php

class Ynjobposting_Model_DbTable_Nodes extends Engine_Db_Table {

	protected $_rowClass = 'Ynjobposting_Model_Node';

	protected $_rootLabel = 'All Industries';

	protected $_relationTableName = '';

	protected function _insertRoot() {
		$row = $this -> fetchNew();
		$row -> pleft = 1;
		$row -> pright = 2;
		$row -> title = $this -> _rootLabel;
		$row -> save();
		return $row;
	}
	
	public function updateTree() {
		$table = $this -> info('name');
		$sql = "update $table set level = (select count(parent.title) from (select * from $table ) as parent where $table.pleft BETWEEN parent.pleft and parent.pright)-1";
		$this -> getAdapter() -> query($sql);
	}

	public function getNodeParent($parent_id =NULL) {
		// if($parent_id == NULL){
		// return $this->getRoot();
		// }
		$row = $this -> find($parent_id) -> current();
		if(!is_object($row)) {
			$row = $this -> getRoot();
		}
		return $row;
	}

	/**
	 * @return Ynjobposting_Model_Node
	 */
	public function getNode($node_id, $check_root =true) {
		$row = $this -> find($node_id) -> current();
		if(!$row && $check_root) {
			$row = $this -> getRoot();
		}
		return $row;
	}

	/**
	 *  Get Root
	 * @return Ynjobposting_Model_Node
	 */
	public function getRoot() {
		$row = $this -> fetchRow($this -> select() -> where('pleft=1'));
		if(!$row) {
			$row = $this -> _insertRoot();
		}
		if(!is_object($row)) {
			throw new Exception(sprintf("can not insert root node: %s"), $e -> getMessage());
		}
		return $row;
	}

	/**
	 * get node after with correct condition
	 * @param  $parent_id
	 * @return Ynjobposting_Model_Node|NULL
	 */
	public function getNodeBefore($parent_id, $data) {
		if($parent_id == NULL) {
			return $this -> fetchRow($this -> select() -> where('title < ?', @$data['title']) -> where('parent_id is NULL') -> order('title DESC'));
		} else {
			return $this -> fetchRow($this -> select() -> where('title < ?', @$data['title']) -> where('parent_id= ?', $parent_id) -> order('title DESC'));
		}

	}

	/**
	 * get node after with correct condition
	 * @param  int    $parent_id
	 * @return Ynjobposting_Model_Node|NULL
	 */
	public function getNodeAfter($parent_id, $data) {
		if($parent_id == NULL) {
			return $this -> fetchRow($this -> select() -> where('title < ?', @$data['title']) -> where('parent_id is NULL') -> order('title ASC'));
		} else {
			return $this -> fetchRow($this -> select() -> where('title > ?', @$data['title']) -> where('parent_id= ?', $parent_id) -> order('title ASC'));
		}

	}

	/**
	 * get node random
	 * return Ynjobposting_Model_Node
	 */
	public function getNodeRandom() {

		$row = $this -> fetchRow($this -> select() -> order('rand()'));

		if(!is_object($row)) {
			return $this -> getRoot();
		}

		return $row;
	}

	/**
	 * new node with supply data will be added append to $node
	 * @param   Ynjobposting_Model_Node  $node
	 * @param   Array                $data
	 * @return  Ynjobposting_Model_Node
	 * @throw Exception
	 */
	public function insertAfter(Ynjobposting_Model_Node $node, $data) {
		if($node -> pleft == 1) {
			throw new Exception("can not insert after root");
		}
		$newNode = $this -> fetchNew();
		$newNode -> setFromArray($data);
		$anchor = $node -> pright;
		$newNode -> pleft = $anchor + 1;
		$newNode -> pright = $anchor + 2;
		$newNode -> parent_id = $node -> parent_id;
		$db = $this -> getAdapter();
		$tableName = $this -> info('name');

		try {
			$db -> beginTransaction();
			$db -> update($tableName, array('pright' => new Zend_Db_Expr('pright+2'), ), array('pright > ?' => $anchor));
			$db -> update($tableName, array('pleft' => new Zend_Db_Expr('pleft+2'), ), array('pleft > ?' => $anchor));
			$newNode -> save();
			$this -> updateTree();
			$optionId = Engine_Api::_()->getApi('core', 'ynjobposting')->typeCreate($newNode->title);
			$newNode->option_id = $optionId;
			$newNode->save();
			$db -> commit();
			return $newNode;
		} catch(Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}

	/**
	 * new node with supply data will be added append to $node
	 * @param   Ynjobposting_Model_Node  $node
	 * @param   Array                $data
	 * @return  Ynjobposting_Model_Node
	 * @throw Exception
	 */
	public function insertBefore(Ynjobposting_Model_Node $node, $data) {
		if($node -> pleft == 1) {
			throw new Exception("can not insert after root");
		}

		$newNode = $this -> fetchNew();
		$newNode -> setFromArray($data);
		$anchor = $node -> pleft;
		$newNode -> pleft = $anchor;
		$newNode -> pright = $anchor + 1;
		$newNode -> parent_id = $node -> parent_id;
		$db = $this -> getAdapter();
		$tableName = $this -> info('name');

		try {
			$db -> beginTransaction();
			$db -> update($tableName, array('pright' => new Zend_Db_Expr('pright+2'), ), array('pright > ?' => $anchor));
			$db -> update($tableName, array('pleft' => new Zend_Db_Expr('pleft+2'), ), array('pleft > ?' => $anchor - 1));
			$newNode -> save();
			$this -> updateTree();
			$db -> commit();
		} catch(Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}

	/**
	 * new node with supply data will be added append to $node
	 * @param   Ynjobposting_Model_Node  $node
	 * @param   Array                $data
	 * @return  Ynjobposting_Model_Node
	 * @throw Exception
	 */
	public function appendChild(Ynjobposting_Model_Node $node, $data) {

		$newNode = $this -> fetchNew();
		$newNode -> setFromArray($data);
		$anchor = $node -> pright;
		$newNode -> pleft = $anchor;
		$newNode -> pright = $anchor + 1;

		$newNode -> parent_id = $node -> getIdentity();
		$db = $this -> getAdapter();
		$tableName = $this -> info('name');

		try {
			$db -> beginTransaction();
			$db -> update($tableName, array('pright' => new Zend_Db_Expr('pright+2'), ), array('pright > ?' => $anchor - 1));
			$db -> update($tableName, array('pleft' => new Zend_Db_Expr('pleft+2'), ), array('pleft > ?' => $anchor));
			$newNode -> save();
			$this -> updateTree();
			$db -> commit();
		} catch(Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}

	/**
	 * new node with supply data will be added append to $node
	 * @param   Ynjobposting_Model_Node  $node
	 * @param   Array                $data
	 * @return  Ynjobposting_Model_Node
	 * @throw Exception
	 */
	public function prependChild(Ynjobposting_Model_Node $node, $data) {
		$newNode = $this -> fetchNew();
		$newNode -> setFromArray($data);
		$anchor = $node -> pleft;
		$newNode -> pleft = $anchor + 1;
		$newNode -> pright = $anchor + 2;
		$newNode -> parent_id = $node -> getIdentity();
		$db = $this -> getAdapter();
		$tableName = $this -> info('name');

		try {
			$db -> beginTransaction();
			$db -> update($tableName, array('pright' => new Zend_Db_Expr('pright+2'), ), array('pright > ?' => $anchor));
			$db -> update($tableName, array('pleft' => new Zend_Db_Expr('pleft+2'), ), array('pleft > ?' => $anchor));
			$newNode -> save();
			$this -> updateTree();
			$optionId = Engine_Api::_()->getApi('core', 'ynjobposting')->typeCreate($newNode->title);
			$newNode->option_id = $optionId;
			$newNode->save();
			$db -> commit();
			return $newNode;
		} catch(Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}

	/**
	 * new node with supply data will be added append to $node
	 * @param   Ynjobposting_Model_Node  $node
	 * @param   Array                $data
	 * @return  Ynjobposting_Model_Node
	 * @throw Exception
	 */
	public function addChild(Ynjobposting_Model_Node $node, $data) {
		if(!is_object($node)) {
			$node = $this -> getRoot();
		}

		$leftNode = $this -> getNodeBefore($node -> getIdentity(), $data['title']);

		if(!is_object($leftNode)) {
			return $this -> prependChild($node, $data);
		}

		return $this -> insertAfter($leftNode, $data);
	}

	/**
	 * new node with supply data will be added append to $node
	 * @param   Ynjobposting_Model_Node  $node
	 * @throw Exception
	 */
	public function deleteNode(Ynjobposting_Model_Node $node, $node_id = NULL) {
		$anchor = $node -> pleft;
		$range = $node -> pright - $node -> pleft + 1;
		$db = $this -> getAdapter();
		$tableName = $this -> info('name');

		try {
			$db -> beginTransaction();
			$db -> delete($tableName, array('pright <= ?' => $node -> pright, 'pleft >= ?' => $node -> pleft));
			$db -> update($tableName, array('pright' => new Zend_Db_Expr('pright-' . $range), ), array('pright > ?' => $anchor));
			$db -> update($tableName, array('pleft' => new Zend_Db_Expr('pleft-' . $range), ), array('pleft > ?' => $anchor));
			$this -> updateTree();
			$db -> commit();
		} catch(Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}

	public function getAscendant($node, $included_own =true) {

	}

	/**
	 * get descendant node.
	 */
	public function getDescendent($node, $include_own =true) {
		if(!$node instanceof Ynjobposting_Model_Node) {
			$node = $this -> getNode($node);
		}
		$tableName = $this -> info('name');
		$sql = "select * from $tableName where pright <= {$node -> pright} and pleft >= {$node -> pleft}";
		return (array)$this -> getAdapter() -> fetchCol($sql);

	}

	/**
	 * @param  string  $prefix
	 * @param  string  $root_label
	 * @param  bool    $include_root
	 * @param  int     $max_level
	 * @return array   [id, title]
	 */
	public function getMultiOptions($prefix ='', $root_label ='', $include_root =  false, $max_level =4) {
		$result = array();
		$rows = $this -> fetchAll($this -> select()->where('pleft > 1') -> order('pleft'));
		$result[""] =  $root_label;
		foreach($rows as $row) {
			$result[$row -> getIdentity()] = str_repeat($prefix, $row -> level -1) . ' ' . $row -> shortTitle();
		}
		return $result;
	}

   /**
	 * @param  string  $prefix
	 * @param  string  $root_label
	 * @param  bool    $include_root
	 * @param  int     $max_level
	 * @return array   [id, title]
	 */
	public function getDeleteOptions($exclude_id,$prefix = '..') {
		$node =  $this->getNode($exclude_id);
		$result = array();
		$select =  $this -> select()->where('pleft < ?', $node->pleft)->orWhere('pright > ?', $node->pright) -> order('pleft');;
		$rows = $this -> fetchAll($select);		
		foreach($rows as $row) {
			if($row->pleft==1){continue;}
			$result[$row -> getIdentity()] = str_repeat($prefix, $row -> level-1) . ' ' . $row -> shortTitle();
		}
		return $result;
	}
		
	public function getRelationTableName() {
		return $this -> _relationTableName;
	}

	/**
	 * update relation ignore any relation task
	 */
	public function updateRelation($item, $new_ids, $include_parent =false) {
		return;
		$ids = (array)$ids;
		$item_id = (int)$item -> getIdentity();
		$ids = array_unique($ids);
		$rel = $this -> getRelationTableName();
		$db = $this -> getAdapter();
		$key = 'industry_id'; //$this -> _primary;

		$old_ids = (array)$db -> fetchCol("select $key from $rel where item_id =  $item_id");

		if($delete_ids = array_diff($old_ids, $new_ids)) {
			$delete_ids = implode(',', $delete_ids);
			$sql = "delete from $rel where item_id =  $item_id and $key in ($delete_ids)";
			$db -> query($sql);
		}

		$new_ids = array_diff($new_ids, $old_ids);
		foreach($new_ids as $id) {
			$sql = "insert ignore into $rel($key, item_id) values($id,$item_id);";
			$db -> query($sql);
		}
	}

	public function getRelation($item) {
		$item_id = $item -> getIdentity();
		$rel = $this -> getRelationTableName();
		$db = $this -> getAdapter();
		$key = 'industry_id'; //$this -> _primary;
		$sql = "select $key from $rel where item_id =  $item_id";
		if($ids = $db -> fetchCol($sql)) {
			$select = $this -> select() -> where($key . ' IN (?) ', $ids);
			return $this -> fetchAll($select);
		}
		return array();
	}

}
