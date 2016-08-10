<?php

class Ynjobposting_Model_Package extends Core_Model_Item_Abstract {
    protected $_searchTriggers = false;
    
    public function getTitle() {
        return $this->title;
    }
    
    public function isViewable() {
        return $this->authorization()->isAllowed(null, 'view');
    }
}
