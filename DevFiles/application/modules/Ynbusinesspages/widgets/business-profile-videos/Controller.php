<?php
class Ynbusinesspages_Widget_BusinessProfileVideosController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
	public function indexAction() 
	{
		 // Don't render this if not authorized
	    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
		
	    if(!Engine_Api::_()->hasItemType('video'))
	    {
	      return $this->setNorender();
	    }
	    // Get subject and check auth
	    $this->view->business = $subject = Engine_Api::_()->core()->getSubject('ynbusinesspages_business');
		
	    if (!$subject -> isViewable() || !$subject -> getPackage() -> checkAvailableModule('video')) {
            return $this -> setNoRender();
        }
	    $params = array();
	    $params['orderby'] = 'creation_date';
		$params['business_id'] = $subject -> getIdentity();
	    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages') -> getVideosPaginator($params);
		
		 // Set item count per page and current page number
	    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
	    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	
	    $this -> view -> canCreate = $subject -> isAllowed('video_create');
	    $this->getElement()->removeDecorator('Title');
		// Add count to title if configured
	    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
	      $this->_childCount = $paginator->getTotalItemCount();
	    }
	}
	public function getChildCount() {
        return $this->_childCount;
    }
}