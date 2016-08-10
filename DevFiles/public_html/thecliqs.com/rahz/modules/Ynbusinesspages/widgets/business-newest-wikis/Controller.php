<?php
class Ynbusinesspages_Widget_BusinessNewestWikisController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
		if (!Engine_Api::_() -> core() -> hasSubject()) 
		{
			return $this -> setNoRender();
		}
	    //check auth for view business
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        if (!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('ynwiki_page') || !Engine_Api::_() -> hasModuleBootstrap('ynwiki')) 
        {
            return $this -> setNoRender();
        }
		
		$params = array();
	    $params['parent_type'] = $business->getType();
	    $params['parent_id'] = $business->getIdentity();
	
	    $this->view->paginator = $paginator = Engine_Api::_()->ynwiki ()->getPagesPaginator($params);
		// Set item count per page and current page number
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 1));
		
	    if($paginator->getTotalItemCount() <= 0)
	    {
	      return $this->setNoRender();
	    }
	}
}