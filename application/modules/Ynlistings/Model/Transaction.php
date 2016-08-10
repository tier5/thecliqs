<?php
class Ynlistings_Model_Transaction extends Core_Model_Item_Abstract {
    protected $_searchTriggers = false;	
    public function getListing() {
        $listing = Engine_Api::_()->getItem('ynlistings_listing', $this->listing_id);
        if ($listing) return $listing;
    }
}