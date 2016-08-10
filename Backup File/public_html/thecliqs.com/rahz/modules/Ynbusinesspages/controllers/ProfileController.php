<?php
class Ynbusinesspages_ProfileController extends Core_Controller_Action_Standard
{
  	public function init()
  	{
  		$viewer = Engine_Api::_() -> user() -> getViewer();
  		$subject = null;
  		if( !Engine_Api::_()->core()->hasSubject() )
  		{
  			$id = $this->_getParam('id');
  			if( null !== $id )
  			{
  				$subject = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
				if (!$subject || $subject->deleted) {
					return $this->_helper->requireSubject()->forward();
				}
				if ($subject->deleted)
				//check auth view if this business has owner
				if(!$subject -> is_claimed)
				{
	  				if( !$subject->isAllowed('view', $viewer) )
	  				{
	  					return $this -> _helper -> requireAuth() -> forward();
	  				}
				}
  				if( $subject && $subject->getIdentity() )
  				{
  					Engine_Api::_()->core()->setSubject($subject);
  				}
  				else 
  				{
	  				return;
  				}
  			}
  		}
  		$this->_helper->requireSubject('ynbusinesspages_business');
  	}
  
	public function indexAction()
	{
	    if(!Engine_Api::_()->core()->hasSubject()) 
		{
	    	return $this->_helper->requireSubject()->forward();
		}	
	    $subject = Engine_Api::_()->core()->getSubject();
		if (!$subject || $subject->deleted) {
			return $this->_helper->requireSubject()->forward();
		}
        // Check authorization to view business.
        if (!$subject->isViewable()) {
            return $this -> _helper -> requireAuth() -> forward();
        }
	    $viewer = Engine_Api::_()->user()->getViewer();
		if($subject -> status != 'published')
		{
			if(!$subject -> is_claimed)
			{
				if(!$viewer -> isAdmin() && !$viewer -> isSelf($subject -> getOwner()))
				{
					return $this -> _helper -> requireAuth() -> forward();
				}
			}
		}
		$subject -> view_count += 1;
		$subject -> save();
	    // Render
	    $this->_helper->content
	        ->setNoRender()
	        ->setEnabled()
	        ;
  	}
}
