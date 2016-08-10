<?php
class Ynlistings_Model_DbTable_Categories extends Ynlistings_Model_DbTable_Nodes {
	protected $_rowClass = 'Ynlistings_Model_Category';
	protected $_serializedColumns = array('themes');
	
	public function getCategoryByOptionId($option_id)
	{
		$select = $this->select();
		$select -> where('option_id = ?', $option_id);
		$select -> limit(1);
		$item  = $this->fetchRow($select);
		return $item;
	}
	
	public function getFirstCategory()
	{
		$select = $this->select();
		$select -> order('category_id ASC');
		$select -> limit(2);
		$select -> where('category_id <> 1');
		$item  = $this->fetchRow($select);
		return $item;
	}
	
	public function deleteNode(Ynlistings_Model_Node $node, $node_id = NULL) {
		/*
		$result = $node -> getDescendent(true);
				$db = $this -> getAdapter();
				$sql = 'update engine4_ynlistings_deals set category_id =  '.$node_id.' where category_id in (' . implode(',', $result) . ',0)';
				$db -> query($sql);*/
		
		parent::deleteNode($node);
	}

    public function getCategories() {
        $table = Engine_Api::_() -> getDbTable('categories', 'ynlistings');
        $tree = array();
        $node = $table -> getNode(1);
        $this->appendChildToTree($node, $tree);
        return $tree;
    }
    
    public function appendChildToTree($node, &$tree) {
        array_push($tree, $node);
        $children = $node->getChilren();
        foreach ($children as $child_node) {
            $this->appendChildToTree($child_node, $tree);
        }
    }
}
