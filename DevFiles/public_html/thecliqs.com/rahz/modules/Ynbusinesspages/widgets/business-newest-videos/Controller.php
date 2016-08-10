<?php
class Ynbusinesspages_Widget_BusinessNewestVideosController extends Engine_Content_Widget_Abstract 
{
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
		$params['limit'] = $this->_getParam('itemCountPerPage', 1);
	    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages') -> getVideosPaginator($params);
	    // Do not render if nothing to show
        if(!$paginator -> getTotalItemCount()) 
        {
            return $this->setNoRender();
        }
    }
}