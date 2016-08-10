<?php
class Ynlistings_Widget_ListingVideosController extends Engine_Content_Widget_Abstract{
	protected $_childCount;
    public function indexAction(){
     // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    // Get subject and check auth
    $this->view->listing = $subject = Engine_Api::_()->core()->getSubject('ynlistings_listing');

	// Get paginator
    $mappingTable = Engine_Api::_()->getItemTable('ynlistings_mapping');
	$params['listing_id'] = $subject -> getIdentity();
    $this->view->paginator = $paginator = $mappingTable->getWidgetVideosPaginator($params);
    
    // Set item count per page and current page number
    $paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 6));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    
    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    
    $this->view->canUpload = $canUpload = $subject->canUploadVideos();
  }
	 
  public function getChildCount()
  {
    return $this->_childCount;
  }
}
?>
