<?php
class Ynbusinesspages_Model_DbTable_Categorymaps extends Engine_Db_Table {
	protected $_name = 'ynbusinesspages_category_business_maps';
	protected $_rowClass = 'Ynbusinesspages_Model_Categorymap';
	
	public function checkExistCategoryByBusiness($category_id, $business_id)
	{
		$select = $this -> select() -> where('category_id = ?', $category_id) -> where('business_id =?', $business_id) -> limit(1);
		return $this -> fetchRow($select);
	}
	
	public function getMainCategoryByBusinessId($business_id)
	{
		$select = $this -> select() -> where('business_id = ?', $business_id) -> where('main = 1');
		return $this -> fetchRow($select);
	}
	
	public function getSubCategoryByBusinessId($business_id)
	{
		$select = $this -> select() -> where('business_id = ?', $business_id) -> where('main = 0');
		return $this -> fetchAll($select);
	}
	
	public function getCategoriesByBusinessId($business_id)
	{
		$select = $this -> select() -> where('business_id = ?', $business_id);
		return $this -> fetchAll($select);
	}
	
	public function deleteBusinessesByCategoryId($category_id)
	{
		$select = $this -> select() -> where('category_id = ?', $category_id);
		$rows = $this -> fetchAll($select);
		foreach($rows as $row)
		{
			$row -> delete();
		}
	}
	
	public function deleteCategoriesByBusinessId($business_id)
	{
		$select = $this -> select() -> where('business_id = ?', $business_id);
		$rows =  $this -> fetchAll($select);
		foreach($rows as $row)
		{
			$row -> delete();
		}
	}
}
