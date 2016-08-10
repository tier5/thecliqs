<?php
class Ynbusinesspages_Widget_BusinessProfileFollowersController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
	public function indexAction() 
	{
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
	    //check auth for view business
		$business = Engine_Api::_() -> core() -> getSubject();
        if (!$business -> isViewable()) {
            return $this -> setNoRender();
        }
		$followTable = Engine_Api::_() -> getDbTable('follows', 'ynbusinesspages');
		$usersFollow = $followTable -> getUsersFollow($business -> getIdentity());
		
		if(!isset($usersFollow) && count($usersFollow) == 0)
		{
			return $this->setNoRender();
		}
		
		$this->view->paginator = $paginator = Zend_Paginator::factory($usersFollow);
		 // Set item count per page and current page number
	    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 15));
	    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	    // Do not render if nothing to show and cannot upload
	    if( $paginator->getTotalItemCount() <= 0) 
	    {
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