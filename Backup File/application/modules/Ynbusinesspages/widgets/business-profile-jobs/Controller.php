<?php
class Ynbusinesspages_Widget_BusinessProfileJobsController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
	public function indexAction() {
        // Don't render if job posting item not available
        if( !Engine_Api::_()->hasModuleBootstrap('ynjobposting') ) {
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
        if(!$business->isViewable() || !$business -> getPackage() -> checkAvailableModule('ynjobposting_job')) {
            return $this->setNoRender();
        }
    
        //check auth import
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> canImport = $canImport = $business -> isAllowed('job_import');

        //Get search condition
        $params = array();
        $params['business_id'] = $business -> getIdentity();
        $params['ItemTable'] = 'ynjobposting_job';
        $params['status'] = $this -> _getParam('status', 'all');
        //Get paginator
        $this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getJobsPaginator($params);
        $itemCountPerPage = $this -> _getParam('itemCountPerPage', 10);
        if (!$itemCountPerPage) {
            $itemCountPerPage = 10;
        }
        $paginator -> setItemCountPerPage($itemCountPerPage);
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
        
        // Do not render if nothing to show and cannot create
        if ($paginator -> getTotalItemCount() <= 0 && !$canImport) {
            return $this -> setNoRender();
        }
        
        // Add count to title if configured
        if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
           $this->_childCount = $paginator->getTotalItemCount();    
        }
        
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
  }
	public function getChildCount() {
        return $this->_childCount;
    }
}