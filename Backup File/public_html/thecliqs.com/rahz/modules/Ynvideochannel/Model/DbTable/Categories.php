<?php
class Ynvideochannel_Model_DbTable_Categories extends Ynvideochannel_Model_DbTable_Nodes {
    protected $_rowClass = 'Ynvideochannel_Model_Category';

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

    public function deleteNode(Ynvideochannel_Model_Node $node, $node_id = NULL) {
        parent::deleteNode($node);
    }

    public function getCategories($showAllCates = 1) {
        $table = Engine_Api::_() -> getDbTable('categories', 'Ynvideochannel');
        $tree = array();
        $node = $table -> getNode(1);
        $this->appendChildToTree($node, $tree);
        if (!$showAllCates) {
            unset($tree[0]);
        }
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
