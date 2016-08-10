<?php
class Ynbusinesspages_Widget_BusinessProfileEventsController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
	public function indexAction() {
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		//check auth for view business
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		if (!$business -> isViewable()) {
			return $this -> setNoRender();
		}
		$this -> getElement() -> removeDecorator('Title');
		// Don't render if event item not available
		if (!Engine_Api::_() -> hasItemType('event') || !$business -> getPackage() -> checkAvailableModule('event')) {
			return $this -> setNoRender();
		}

		// Get paginator
		$this -> view -> paginator = $paginator = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages') -> getEventsPaginator(array('business_id' => $business -> getIdentity()));
		$this -> view -> canAdd = $canAdd = $business -> isAllowed('event_create');

		// Set item count per page and current page number
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 5));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

		// Do not render if nothing to show and cannot upload
		if ($paginator -> getTotalItemCount() <= 0 && !$canAdd) {
			return $this -> setNoRender();
		}

		// Add count to title if configured
		if ($this -> _getParam('titleCount', false) && $paginator -> getTotalItemCount() > 0) {
			$this -> _childCount = $paginator -> getTotalItemCount();
		}
	}

	public function getChildCount() {
		return $this -> _childCount;
	}

}
