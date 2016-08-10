<?php
class Ynresume_Model_Badge extends Core_Model_Item_Abstract {
    protected $_type = 'ynresume_badge';
    protected $_searchTriggers = false;
    
    public function getTitle() {
        return $this->title;
    }
}
