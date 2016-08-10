<?php
class Ynbusinesspages_Widget_BusinessProfileWikisController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
	public function indexAction() 
	{
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
	    //check auth for view business
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        if (!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynwiki_page') || !Engine_Api::_() -> hasModuleBootstrap('ynwiki')) {
            return $this -> setNoRender();
        }
		$this->getElement()->removeDecorator('Title');
		$params = array();
	    $params['parent_type'] = $business->getType();
	    $params['parent_id'] = $business->getIdentity();
	
	    $this->view->paginator = $paginator = Engine_Api::_()->ynwiki ()->getPagesPaginator($params);
		// Set item count per page and current page number
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 5));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		
		$this -> view -> canAdd = $canAdd = $business -> isAllowed('wiki_create');
		
	    if($paginator->getTotalItemCount() <= 0 && !$canAdd){
	      return $this->setNoRender();
	    }
		// Add count to title if configured
	    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
	      $this->_childCount = $paginator->getTotalItemCount();
	    }
	}
	public function getChildCount() {
        return $this->_childCount;
    }
}