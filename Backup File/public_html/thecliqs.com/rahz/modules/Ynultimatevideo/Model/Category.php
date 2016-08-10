<?php

class Ynultimatevideo_Model_Category extends Ynultimatevideo_Model_Node {

    protected $_searchTriggers = false;
    protected $_parent_type = 'user';

    protected $_owner_type = 'user';

    protected $_type = 'ynultimatevideo_category';

    public function getParent($category_id)
    {
        $item = Engine_Api::_()->getItem('ynultimatevideo_category', $category_id);
        $parent_item = Engine_Api::_()->getItem('ynultimatevideo_category', $item->parent_id);
        return $parent_item;
    }

    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'ynultimatevideo_general',
            'controller' => 'index',
            'action' => 'list',
            'category' => $this->option_id,
        ), $params);

        if (!empty($params['type']) && $params['type'] == 'playlist') {
            $param['route'] = 'ynultimatevideo_playlist';
        }

        $route = $params['route'];
        unset($params['route']);
        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, true);
    }

    public function getTable() {
        if(is_null($this -> _table)) {
            $this -> _table = Engine_Api::_() -> getDbtable('categories', 'ynultimatevideo');
        }
        return $this -> _table;
    }

    public function checkHasVideo()
    {
        $table = Engine_Api::_() -> getItemTable('ynultimatevideo_video');
        $select = $table -> select() -> where('category_id = ?', $this->getIdentity()) -> limit(1);
        $row = $table -> fetchRow($select);
        if($row)
            return true;
        else {
            return false;
        }
    }

    public function getMoveCategoriesByLevel($level)
    {
        $table = Engine_Api::_() -> getDbtable('categories', 'ynultimatevideo');
        $select = $table -> select()
            -> where('category_id <>  ?', 1) // not default
            -> where('category_id <>  ?', $this->getIdentity())// not itseft
            -> where('level = ?', $level);
        $result = $table -> fetchAll($select);
        return $result;
    }

    public function setTitle($newTitle) {
        $this -> title = $newTitle;
        $this -> save();
        return $this;
    }

    public function shortTitle() {
        return strlen($this -> title) > 20 ? (substr($this -> title, 0, 17) . '...') : $this -> title;
    }

    public function getUsedCount() {
        $table = Engine_Api::_() -> getDbTable('deals', 'groupbuy');
        $rName = $table -> info('name');
        $ids =  $this->getDescendent(true);
        $select = $table -> select() -> from($rName) -> where($rName . '.category_id in (?)', $ids) -> where('is_delete = 0');
        $row = $table -> fetchAll($select);
        $total = count($row);
        return $total;
    }

    public function getChildList() {
        $table = Engine_Api::_()->getItemTable('ynultimatevideo_category');
        $select = $table->select();
        $select->where('parent_id = ?', $this->getIdentity());
        $childList = $table->fetchAll($select);
        return $childList;
    }

    public function getNumOfVideos() {
        $table = Engine_Api::_()->getItemTable('ynultimatevideo_video');
        $select = $table->select();
        $select
            ->where('category_id = ?', $this->getIdentity())
            ->where('status = ?', 'open')
            ->where('approved_status = ?', 'approved')
            ->where('search = ?', 1);
        $childList = $table->fetchAll($select);
        return count($childList);
    }
}
