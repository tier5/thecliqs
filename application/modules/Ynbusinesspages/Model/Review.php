<?php 
class Ynbusinesspages_Model_Review extends Core_Model_Item_Abstract {
	protected $_parent_type = 'ynbusinesspages_business';
    protected $_owner_type = 'user';
    protected $_type = 'ynbusinesspages_review';
    protected $_searchTriggers = false;
	
	
	public function getDescription()
	{
		return $this -> body;
	}
	
    function isViewable() {
        return true; 
    }
    
    function isEditable() {
        $business = $this->getParent();
        if ($business) {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            if ($business->isOwner($viewer)) {
                return true;
            }
        }
        return false; 
    }
    
    function isDeletable() {
        $business = $this->getParent();
        if ($business) {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            if ($business->isOwner($viewer)) {
                return true;
            }
        }
        return false; 
    }
    
    function getTitle() {
        return $this->title;
    }
    
    function getCreationTime() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $date = new Zend_Date(strtotime($this->creation_date));
        $date->setTimezone($timezone);
        return $date;
    }
    
    function getCreator() {
        return Engine_Api::_()->user()->getUser($this->user_id);
    }
    
    public function getBusiness()
    {
    	if (!$this->business_id)
    	{
    		return null;
    	}
    	return Engine_Api::_()-> getItem('ynbusinesspages_business', $this->business_id);
    }
    
	public function renderRating() {
        $view = Zend_Registry::get('Zend_View');
        return $view -> partial('render-rating.tpl', 'ynbusinesspages', array('rating' => $this->rate_number, 'li' => false ));
    }
}