<?php
class Ynlistings_Widget_ListingDiscussionsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    // Get subject
    $this->view->listing = $listing = Engine_Api::_()->core()->getSubject('ynlistings_listing');
    $this->view->canPost = $listing->CanDiscuss();
    
    // Get paginator
    $params = array();
    $params['listing_id'] =  $listing->getIdentity();
    $params['closed'] = 0;
    $params['order'] = 'modified_date';
	
    $this->view->paginator = $paginator = Engine_Api::_() -> getItemTable('ynlistings_topic')
                                                          -> getTopicsPaginator($params);
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show and not viewer
    if(!$viewer->getIdentity()) {
      return $this->setNoRender();
    }
	
    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

    if($paginator->getTotalItemCount() > $this->_getParam('itemCountPerPage', 5)){
      $this->view->viewMore = true;
    }
    else $this->view->viewMore = false;
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}