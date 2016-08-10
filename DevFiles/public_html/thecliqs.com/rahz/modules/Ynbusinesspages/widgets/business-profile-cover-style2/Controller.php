<?php
class Ynbusinesspages_Widget_BusinessProfileCoverStyle2Controller extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
		// Don't render this if not authorized
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		if (!$business -> isViewable()) 
		{
			return $this -> setNoRender();
		}
		if ($business->theme != 'theme2')
		{
			return $this -> setNoRender();
		}
		$followTable = Engine_Api::_() -> getDbTable('follows', 'ynbusinesspages');
		$row = $followTable -> getFollowBusiness($business -> getIdentity(), $viewer -> getIdentity());
		$this -> view -> follow = $row ? 1 : 0;
		$favouriteTable = Engine_Api::_() -> getDbTable('favourites', 'ynbusinesspages');
		$row = $favouriteTable -> getFavouriteBusiness($business -> getIdentity(), $viewer -> getIdentity());
		$this -> view -> favourite = $row ? 1 : 0;
		
		$coverTbl = Engine_Api::_()->getDbTable('covers', 'ynbusinesspages');
		$this -> view -> covers = $covers = $coverTbl -> getCoverByBusiness($business);
		
		$this->view->location = $location = $business -> getMainLocationObject();
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$table = Engine_Api::_()->getItemTable('ynbusinesspages_review');
		$select = $table->select();
		$select
            ->where('business_id = ?', $business->getIdentity())
            ->where('user_id = ?', $viewer->getIdentity());  
		$this->view->my_review = $my_review = $table->fetchRow($select);
		$this->view->can_review = $can_review = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')
		-> setAuthParams('ynbusinesspages_business', null, 'rate') 
		-> checkRequire();
	}
}