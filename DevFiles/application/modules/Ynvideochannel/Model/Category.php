<?php
class Ynvideochannel_Model_Category extends Ynvideochannel_Model_Node {

    protected $_searchTriggers = false;
    protected $_parent_type = 'user';

    protected $_owner_type = 'user';

    protected $_type = 'ynvideochannel_category';

    public function getCategoryParent($category_id)
    {
        $item = Engine_Api::_()->getItem('ynvideochannel_category', $category_id);
        $parent_item = Engine_Api::_()->getItem('ynvideochannel_category', $item->parent_id);
        return $parent_item;
    }

    public function getHref($params = array()) {

        $params = array_merge(array(
            'route' => 'ynvideochannel_general',
            'controller' => 'index',
            'action' => 'browse-videos',
            'category' => $this->option_id,
        ), $params);

        if (!empty($params['type'])) {
            $params['action'] = "browse-".$params['type'];
        }

        if (!empty($params['actionName']) && !empty($params['type'])) {
            $actionName = $params['actionName'];
            if(strpos($actionName, 'manage') !== false || $actionName == 'favorites' || $actionName == 'subscriptions')
            {
                $params['action'] = $actionName;
            }
        }
        $route = $params['route'];
        unset($params['route']);
        unset($params['type']);
        unset($params['actionName']);
        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, true);
    }

    public function getTable() {
        if(is_null($this -> _table)) {
            $this -> _table = Engine_Api::_() -> getDbtable('categories', 'Ynvideochannel');
        }
        return $this -> _table;
    }

    public function checkHasVideo()
    {
        $table = Engine_Api::_() -> getItemTable('ynvideochannel_video');
        $select = $table -> select() -> where('category_id = ?', $this->getIdentity()) -> limit(1);
        $row = $table -> fetchRow($select);
        if($row)
            return true;
        else {
            return false;
        }
    }

    public function checkHasChannel()
    {
        $channelTable = Engine_Api::_() -> getItemTable('ynvideochannel_channel');
        $select = $channelTable -> select() -> where('category_id = ?', $this->getIdentity()) -> limit(1);
        return $channelTable -> fetchRow($select)?true:false;
    }

    public function checkHasPlaylist()
    {
        $playlistTable = Engine_Api::_() -> getItemTable('ynvideochannel_playlist');
        $select = $playlistTable -> select() -> where('category_id = ?', $this->getIdentity()) -> limit(1);
        return $playlistTable -> fetchRow($select)?true:false;
    }

    public function getMoveCategoriesByLevel($level)
    {
        $table = Engine_Api::_() -> getDbtable('categories', 'Ynvideochannel');
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

    public function getChildList() {
        $table = Engine_Api::_()->getItemTable('ynvideochannel_category');
        $select = $table->select();
        $select->where('parent_id = ?', $this->getIdentity());
        $childList = $table->fetchAll($select);
        return $childList;
    }

    public function getNumOfVideos() {
        $table = Engine_Api::_()->getItemTable('ynvideochannel_video');
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
