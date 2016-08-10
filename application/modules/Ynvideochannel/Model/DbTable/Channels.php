<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Model_DbTable_Channels extends Engine_Db_Table
{
    protected $_name = 'ynvideochannel_channels';
    protected $_rowClass = "Ynvideochannel_Model_Channel";

    public function getChannelsPaginator($params = array())
    {
        return Zend_Paginator::factory($this->getChannelsSelect($params));
    }

    public function getChannelsSelect($params = array()) {

        $table = Engine_Api::_()->getDbtable('channels', 'ynvideochannel');
        $rName = $table->info('name');

        $categoryTbl = Engine_Api::_() -> getItemTable('ynvideochannel_category');
        $select = $table->select()->from($table->info('name'))->setIntegrityCheck(false);
        // order
        $order = 'creation_date';
        if (!empty($params['order']))
        {
            $order = $params['order'];
        }

        switch ($order) {
            case 'most_liked' :
                $select->order("$rName.like_count DESC");
                break;
            case 'most_commented' :
                $select->order("$rName.comment_count DESC");
                break;
            case 'most_subscribed' :
                $select->order("$rName.subscriber_count DESC");
                break;
            case 'featured' :
                $select->order("$rName.is_featured DESC");
                break;
            case 'creation_date' :
                $select->order("$rName.creation_date DESC");
        }

        if (!empty($params['keyword'])) {
            $searchTable = Engine_Api::_()->getDbtable('search', 'core');
            $sName = $searchTable->info('name');
            $select
                ->joinRight($sName, $sName . '.id=' . $rName . '.channel_id', null)
                ->where($sName . '.type = ?', 'ynvideochannel_channel')
                ->where($sName . '.title LIKE ?', "%{$params['keyword']}%");
        }
        if (!empty($params['title'])) {
            $select->where("$rName.title LIKE ?", "%{$params['title']}%");
        }

        if (!empty($params['search']) && is_numeric($params['search'])) {
            $select->where($rName . '.search = ?', $params['search']);
        }
        if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
            $select->where($rName . '.owner_id = ?', $params['user_id']);
        }
        if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
            $select->where($rName . '.owner_id = ?', $params['user_id']->getIdentity());
        }

        if (array_key_exists('category', $params) && is_numeric($params['category'])) {
            $categorySelect = $categoryTbl->select()->where('option_id = ?', $params['category']);
            $category = $categoryTbl->fetchRow($categorySelect);
            if ($category) {
                $tree = array();
                $node = $categoryTbl -> getNode($category->getIdentity());
                $categoryTbl -> appendChildToTree($node, $tree);
                $categories = array();
                foreach ($tree as $node) {
                    array_push($categories, $node->category_id);
                }
                $select->where($rName.'.category_id IN (?)', $categories);
            }
        }

        if (!empty($params['channelIds']) && is_array($params['channelIds']) && count($params['channelIds']) > 0) {
            $select->where('channel_id in (?)', $params['channelIds']);
        }

        if (isset($params['featured']) && is_numeric($params['featured'])) {
            $select->where('is_featured = ?', $params['featured']);
        }

        if (isset($params['of_day']) && is_numeric($params['of_day'])) {
            $select->where('of_day = ?', $params['of_day']);
        }

        if (isset($params['subscribed']) && $params['subscribed'] && !empty($params['subscribed_user_id']) ) {
            $subscribeTable = Engine_Api::_()->getDbTable('subscribes', 'ynvideochannel');
            $subscribeTableName = $subscribeTable->info('name');
            $select ->join($subscribeTableName, $subscribeTableName . ".channel_id = " . $rName . ".channel_id")
                    ->where("$subscribeTableName.user_id = ?", $params['subscribed_user_id']);
        }

        //Owner in Admin Search
        if (!empty($params['owner'])) {
            $key = stripslashes($params['owner']);
            $select->setIntegrityCheck(false)
                ->join('engine4_users as u1', "u1.user_id = $rName.owner_id", '')
                ->where("u1.displayname LIKE ?", "%$key%");
        }

        if (!empty($params['fieldOrder'])) {
            if ($params['fieldOrder'] == 'owner') {
                $select->setIntegrityCheck(false)
                    ->join('engine4_users as u2', "u2.user_id = $rName.owner_id", '')
                    ->order("u2.displayname {$params['order']}");
            } else {
                $select->order("{$params['fieldOrder']} {$params['order']}");
            }
        }
        return $select;
    }

    public function getAllChildrenChannelsByCategory($node)
    {
        $return_arr = array();
        $list_categories = array();
        Engine_Api::_()->getItemTable('ynvideochannel_category') -> appendChildToTree($node, $list_categories);
        foreach($list_categories as $category)
        {
            $select = $this->select()->where('category_id = ?', $category -> category_id);
            $cur_arr = $this->fetchAll($select);
            if(count($cur_arr) > 0)
            {
                $return_arr[] = $cur_arr;
            }
        }
        return $return_arr;
    }
}