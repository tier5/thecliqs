<?php
class Ynbusinesspages_Widget_BusinessProfileMusicsController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
    public function indexAction() {
   		$music_enable = Engine_Api::_() -> hasModuleBootstrap('music');
		
		if (!$music_enable) {
			$this -> setNoRender();
		}
        
        if( !Engine_Api::_()->core()->hasSubject() ) {
            return $this->setNoRender();
        }
        
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		
        if (!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('music_playlist')) {
            return $this -> setNoRender();
        }
        
        // Just remove the title decorator
        $this->getElement()->removeDecorator('Title');
        
		//check auth create
		$this -> view -> canCreate = $canCreate = $business -> isAllowed('music_create');

		//Get search condition
		$params = array();
		$params['business_id'] = $business -> getIdentity();
		$params['order'] = $this -> _getParam('order', 'recent');
		$params['ItemTable'] = 'music_playlist';
		//Get Album paginator
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getAlbumsPaginator($params);
		$itemCountPerPage = $this -> _getParam('itemCountPerPage', 10);
        if (!$itemCountPerPage) {
            $itemCountPerPage = 10;
        }
        $paginator -> setItemCountPerPage($itemCountPerPage);
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		
        // Do not render if nothing to show and cannot create
        if ($paginator -> getTotalItemCount() <= 0 && !$canCreate) {
            return $this -> setNoRender();
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