<?php
class Ynresume_Model_DbTable_Industries extends Ynresume_Model_DbTable_Nodes {
	protected $_rowClass = 'Ynresume_Model_Industry';
	
	public function getIndustryByOptionId($option_id)
	{
		$select = $this->select();
		$select -> where('option_id = ?', $option_id);
		$select -> limit(1);
		$item  = $this->fetchRow($select);
		return $item;
	}
	
	public function getFirstIndustry()
	{
		$select = $this->select();
		$select -> order('industry_id ASC');
		$select -> limit(2);
		$select -> where('industry_id <> 1');
		$item  = $this->fetchRow($select);
		return $item;
	}
	
	public function deleteNode(Ynresume_Model_Node $node, $node_id = NULL) {
		
		parent::deleteNode($node);
	}

    public function getIndustries() {
        $table = Engine_Api::_() -> getDbTable('industries', 'ynresume');
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
	
	public function getAllIndustries()
	{
		$select = $this -> select() -> order('title') -> where('industry_id <> 1');
		return $this -> fetchAll($select);
	}
	
	public function getIndustriesAssoc()
	{
		$industries = $this -> getIndustries();
		unset($industries[0]);
		$arr = array(
			'0' => Zend_Registry::get("Zend_Translate")->_('All')
		);
		if(count($industries)) 
		{
			foreach ($industries as $item)
			{
				$arr[$item['industry_id']] = str_repeat("-- ", $item['level'] - 1) . $item['title'];
			}
		}
		return $arr;
	}
}
