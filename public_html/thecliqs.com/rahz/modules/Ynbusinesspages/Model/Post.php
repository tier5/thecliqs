<?php

class Ynbusinesspages_Model_Post extends Core_Model_Item_Abstract {

    protected $_parent_type = 'ynbusinesspages_topic';
    protected $_type = 'ynbusinesspages_post';
    protected $_owner_type = 'user';

    public function isSearchable() {
        $business = $this -> getParentBusiness();
        if (!($business instanceof Core_Model_Item_Abstract)) {
            return false;
        }
        return $business -> isSearchable();
    }

    public function getHref($params = array()) {
        $params = array_merge(array('route' => 'ynbusinesspages_extended', 'controller' => 'topic', 'action' => 'view', 'business_id' => $this -> business_id, 'topic_id' => $this -> getParentTopic() -> getIdentity(), 'post_id' => $this -> getIdentity(), ), $params);
        $route = @$params['route'];
        unset($params['route']);
        return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, true);
    }

    public function getDescription() {
        // Remove bbcode
        $desc = strip_tags($this -> body);
        $desc = preg_replace('/\[[^\[\]]+?\]/', '', $desc);
        return Engine_String::substr($desc, 0, 255);
    }

    public function getPostIndex() {
        $table = $this -> getTable();
        $select = new Zend_Db_Select($table -> getAdapter());
        $select -> from($table -> info('name'), new Zend_Db_Expr('COUNT(post_id) as count')) -> where('topic_id = ?', $this -> topic_id) -> where('post_id < ?', $this -> getIdentity()) -> order('post_id ASC');

        $data = $select -> query() -> fetch();

        return (int)$data['count'];
    }

    public function getParentBusiness() {
        return Engine_Api::_() -> getItem('ynbusinesspages_business', $this -> business_id);
    }

    public function getParentTopic() {
        return Engine_Api::_() -> getItem('ynbusinesspages_topic', $this -> topic_id);
    }

    public function getPoster() {
        return Engine_Api::_() -> getItem('user', $this -> user_id);
    }

    public function getAuthorizationItem() {
        return $this -> getParent('ynbusinesspages_business');
    }

    // Internal hooks

    protected function _insert() {
        if (!$this -> business_id) {
            throw new Exception('Cannot create post without business_id');
        }

        if (!$this -> topic_id) {
            throw new Exception('Cannot create post without topic_id');
        }

        parent::_insert();
    }

    protected function _postInsert() {
        if ($this -> _disableHooks)
            return;

        // Update topic
        $table = Engine_Api::_() -> getDbtable('topics', 'ynbusinesspages');
        $select = $table -> select() -> where('topic_id = ?', $this -> topic_id) -> limit(1);
        $topic = $table -> fetchRow($select);

        $topic -> lastpost_id = $this -> post_id;
        $topic -> lastposter_id = $this -> user_id;
        $topic -> modified_date = date('Y-m-d H:i:s');
        $topic -> post_count++;
        $topic -> save();

        parent::_postInsert();
    }

    protected function _delete() {
        // Update topic
        $table = Engine_Api::_() -> getDbtable('topics', 'ynbusinesspages');
        $select = $table -> select() -> where('topic_id = ?', $this -> topic_id) -> limit(1);
        $topic = $table -> fetchRow($select);
        $topic -> post_count--;

        if ($topic -> post_count == 0) {
            $topic -> delete();
        } else {
            $topic -> save();
        }
    }

    public function canEdit($user) {
        return $this -> getParent() -> getParent() -> authorization() -> isAllowed($user, 'edit') || $this -> isOwner($user);
    }

}
