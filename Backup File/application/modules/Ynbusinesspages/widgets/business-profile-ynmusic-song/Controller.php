<?php
class Ynbusinesspages_Widget_BusinessProfileYnmusicSongController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
	public function indexAction() {
        // Don't render if blog item not available
        if( !Engine_Api::_()->hasModuleBootstrap('ynmusic')) {
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
        if(!$business->isViewable() || !$business -> getPackage() -> checkAvailableModule('ynmusic_song')) {
            return $this->setNoRender();
        }
    
        //check auth create
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> canCreate = $canCreate = $business -> isAllowed('music_create');

        //Get search condition
        $params = array();
        $params['business_id'] = $business -> getIdentity();
        $params['ItemTable'] = 'ynmusic_song';
        $params['browse_by'] = 'recently_created';
		$params['type'] = 'song';
        //Get paginator
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getYnmusicPaginator($params);
        $itemCountPerPage = $this -> _getParam('itemCountPerPage', 8);
        if (!$itemCountPerPage) {
            $itemCountPerPage = 8;
        }
        $paginator -> setItemCountPerPage($itemCountPerPage);
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
        
        // Do not render if nothing to show and cannot create
        if ($paginator -> getTotalItemCount() <= 0 && !$canCreate) {
            return $this -> setNoRender();
        }
        
        // Add count to title if configured
        if( $this->_getParam('titleCount', false)) {
           $this->_childCount = $paginator -> getTotalItemCount();   
        }
        
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
		$this->view->formValues = array();
  }
	public function getChildCount() {
        return $this->_childCount;
    }
}