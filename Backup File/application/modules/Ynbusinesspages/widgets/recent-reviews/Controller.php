<?php
class Ynbusinesspages_Widget_RecentReviewsController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
		$reviewTbl = Engine_Api::_()->getItemTable('ynbusinesspages_review');
		$this -> view -> paginator = $paginator = $reviewTbl -> getReviewsPaginator();
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 8));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		if (!$paginator -> getTotalItemCount())
		{
			$this -> setNoRender();
		}
    }
}
