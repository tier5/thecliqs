<?php

class Ynbusinesspages_Model_Proxy extends Engine_Db_Table_Row {

    /**
     * 
     * 1. delete content where page_id =  $this->page_id
     * 2. copy content structure from default to current page id.s 
     */
    public function resetDefault() {
        // delete content page where page_id = $this->page_id
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');
        $contentTable->delete(array('page_id = ?' => $this->page_id));
        
        //copy content structure
        Engine_Api::_()->getApi('layout', 'ynbusinesspages')->resetPageProxy($this->page_id, $this->page_name);
    }

    /**
     * Implement delete core_content where page_id = $this->page_id;
     */
    public function delete() {
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');
        $contentTable->delete(array('page_id = ?' => $this->page_id));        
        // excute delete this object
        parent::delete();
    }

}
