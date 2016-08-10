<?php
class Ynbusinesspages_Widget_MostDiscussedBusinessesController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
		$params = array(
	    	'order' => 'topic_count',
	    	'direction' => 'DESC'
	    );
	    $businessTbl = Engine_Api::_()->getItemTable('ynbusinesspages_business');
	    $this -> view -> paginator = $paginator = $businessTbl -> getBusinessesPaginator($params);
	    $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 8));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		if (!$paginator -> getTotalItemCount())
		{
			$this -> setNoRender();
		}
    }
}
