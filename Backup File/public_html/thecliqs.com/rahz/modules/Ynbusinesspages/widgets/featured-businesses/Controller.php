<?php
class Ynbusinesspages_Widget_FeaturedBusinessesController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
		$limit = $this -> _getParam('max_business', 4);
		$params = array(
	    	'featured' => '1',
			'order' => 'rand()',
			'direction' => ' ',
	    );

	    $businessTbl = Engine_Api::_()->getItemTable('ynbusinesspages_business');
	    $paginator = $businessTbl -> getBusinessesPaginator($params);
	    $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', $limit));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		$currentItemCount = $paginator -> getTotalItemCount();

		if ($currentItemCount == 0)
		{
			$this -> setNoRender();
		}

		$this -> view -> businesses_count = ($limit > $currentItemCount)?$currentItemCount:$limit;
		$this -> view -> businesses = $paginator;
	}
}
