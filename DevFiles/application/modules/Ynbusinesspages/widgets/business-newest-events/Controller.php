<?php
class Ynbusinesspages_Widget_BusinessNewestEventsController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() {
		
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			
			return $this -> setNoRender();
		}
		//check auth for view business
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		if (!$business -> isViewable()) {
			
			return $this -> setNoRender();
		}
		// Don't render if event item not available
		if (!Engine_Api::_() -> hasItemType('event') || !$business -> getPackage() -> checkAvailableModule('event')) {
			return $this -> setNoRender();
		}

		// Get paginator
		$params = array(
			'business_id' => $business -> getIdentity()
		);
		$this -> view -> paginator = $paginator = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages') -> getEventsPaginator($params);
        $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 1));
		// Do not render if nothing to show and cannot upload
		if ($paginator -> getTotalItemCount() <= 0) {
			return $this -> setNoRender();
		}
	}
}
