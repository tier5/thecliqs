<?php
class Ynbusinesspages_Widget_BusinessProfileListingsController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
	public function indexAction() {
        // Don't render if classified item not available
        if( !Engine_Api::_()->hasModuleBootstrap('ynlistings') ) {
            return $this->setNoRender();
        }
    
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !Engine_Api::_()->core()->hasSubject() ) {
            return $this->setNoRender();
        }
        
        // Just remove the title decorator
        $this->getElement()->removeDecorator('Title');
        
        // Get subject and check auth
        $this->view->business = $business = Engine_Api::_()->core()->getSubject('ynbusinesspages_business');
        if(!$business->isViewable() || !$business -> getPackage() -> checkAvailableModule('ynlistings_listing')) {
            return $this->setNoRender();
        }
    
        //check auth create
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $canCreate = $business -> isAllowed('listing_create');
        $this -> view -> canCreate = $canCreate;

        //Get search condition
        $params = array();
        $params['business_id'] = $business -> getIdentity();
        $params['ItemTable'] = 'ynlistings_listing';
        $params['order'] = $this -> _getParam('order', 'recent');
        //Get paginator
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getListingsPaginator($params);
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