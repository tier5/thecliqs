<?php
class Ynbusinesspages_Model_DbTable_Creators extends Engine_Db_Table {
    protected $_rowClass = 'Ynbusinesspages_Model_Creator';
    
    public function getCreators() {
        $creators = array();
        foreach ($this->fetchAll() as $creator) {
            array_push($creators, $creator->user_id);
        }
        return $creators;
    }
    
    public function checkIsCreator($user) {
        if (is_null($user)) {
            $user = Engine_Api::_()->user()->getViewer();
        }
        $creators = $this->getCreators();
        return in_array($user->getIdentity(), $creators);
    }
}