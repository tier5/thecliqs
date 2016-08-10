<?php 
class Ynlistings_Model_Review extends Core_Model_Item_Abstract {
	protected $_parent_type = 'ynlistings_listing';
    protected $_owner_type = 'user';
    protected $_type = 'ynlistings_review';
    protected $_searchTriggers = false;
	
    function isViewable() {
        return $this->authorization()->isAllowed(null, 'view'); 
    }
    
    function isEditable() {
        $listing = $this->getParent();
        if ($listing) {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            if ($listing->isOwner($viewer)) {
                return true;
            }
        }
        return $this->authorization()->isAllowed(null, 'edit'); 
    }
    
    function isDeletable() {
        $listing = $this->getParent();
        if ($listing) {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            if ($listing->isOwner($viewer)) {
                return true;
            }
        }
        return $this->authorization()->isAllowed(null, 'delete'); 
    }
}