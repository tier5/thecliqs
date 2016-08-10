<?php
class Ynbusinesspages_Widget_BusinessProfileRelatedBusinessesController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
	    if (!Engine_Api::_() -> core() -> hasSubject()) {
            return $this -> setNoRender();
        }
        //check auth for view business
        $business = Engine_Api::_() -> core() -> getSubject();
        if (!$business -> isViewable()) 
        {
            return $this -> setNoRender();
        }
		$mapTbl = Engine_Api::_()->getDbTable('categorymaps', 'ynbusinesspages');
        $select = $mapTbl -> select() -> from($mapTbl -> info('name'), 'category_id');
        $select -> where("business_id = ? ", $business -> getIdentity()); 
        $categoryIds = array_unique($select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN));
        
        $businessTbl = Engine_Api::_()->getItemTable('ynbusinesspages_business');
        $select = $businessTbl -> getBusinessesSelect(array('categories' => $categoryIds));
        $select -> where("`business`.`business_id` <> ?", $business -> getIdentity());
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 8));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		if (!$paginator -> getTotalItemCount())
		{
			$this -> setNoRender();
		}
    }
}
