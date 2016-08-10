<?php
class Ynbusinesspages_Widget_BusinessesYouMayLikeController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer -> getIdentity())
		{
			$this -> setNoRender();
		}
		$likeTbl = Engine_Api::_() -> getDbtable('likes', 'core');
		$select = $likeTbl -> select() -> from ($likeTbl->info('name'), 'resource_id')
		->where ("poster_type = ? ", 'user')
		->where ("poster_id = ? ", $viewer -> getIdentity())
		->where ("resource_type = ? ", 'ynbusinesspages_business');
		$resourceIds = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

		$membershipTbl = Engine_Api::_()->getDbTable('membership', 'ynbusinesspages');
		$resourceIds2 = $membershipTbl->getMembershipsOfIds($viewer);
		$resourceIds = array_unique(array_merge($resourceIds, $resourceIds2));
		
		$mapTbl = Engine_Api::_()->getDbTable('categorymaps', 'ynbusinesspages');
		$select = $mapTbl -> select() -> from($mapTbl -> info('name'), 'category_id');
		if (count($resourceIds))
		{
			$select -> where("business_id IN (?) ", $resourceIds); 
		}
		else 
		{
			$select -> where ("0 = 1");
		}
		$categoryIds = array_unique($select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN));
		
		$businessTbl = Engine_Api::_()->getItemTable('ynbusinesspages_business');
		$select = $businessTbl -> getBusinessesSelect(array('categories' => $categoryIds));
		if (count($resourceIds))
		{
			$select -> where("`business`.business_id NOT IN (?)", $resourceIds);
		}
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 8));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		if (!$paginator -> getTotalItemCount())
		{
			$this -> setNoRender();
		}
    }
}
