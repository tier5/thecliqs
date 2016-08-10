<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Model_DbTable_Videos extends Engine_Db_Table
{
    protected $_name = 'ynvideochannel_videos';
    protected $_rowClass = "Ynvideochannel_Model_Video";

    public function getVideosPaginator($params = array())
    {
        return Zend_Paginator::factory($this->getVideosSelect($params));
    }

    public function getVideosSelect($params = array()) {

        $rName = $this->info('name');

        $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tmName = $tmTable->info('name');

        $categoryTbl = Engine_Api::_() -> getItemTable('ynvideochannel_category');

        $select = $this->select()->from($rName)->setIntegrityCheck(false);

        // order
        $order = 'creation_date';
        if (!empty($params['order']))
        {
            $order = $params['order'];
        }

        switch ($order) {
            case 'popular' :
            case 'most_viewed' :
                $select->order("$rName.view_count DESC");
                break;
            case 'most_favorited' :
                $select->order("$rName.favorite_count DESC");
                break;
            case 'rating' :
                $select->order("$rName.rating DESC'");
                break;
            case 'most_liked' :
                $select->order("$rName.like_count DESC");
                break;
            case 'most_commented' :
                $select->order("$rName.comment_count DESC");
                break;
            case 'featured' :
                $select->order("$rName.is_featured DESC");
                break;
            case 'rand' :
                $select->order("RAND() ASC");
                break;
            case 'creation_date' :
                $select->order("$rName.creation_date DESC");
        }

        if (!empty($params['keyword'])) {
            $searchTable = Engine_Api::_()->getDbtable('search', 'core');
            $sName = $searchTable->info('name');
            $select
                ->joinRight($sName, $sName . '.id=' . $rName . '.video_id', null)
                ->where($sName . '.type = ?', 'ynvideochannel_video')
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
        if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
            $select->where($rName . '.owner_id = ?', $params['user_id']);
        }
        if (!empty($params['category_id'])) {
            $select->where($rName . '.category_id = ?', $params['category_id']);
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

        if (!empty($params['tag'])) {
            $select->joinLeft($tmName, "$tmName.resource_id = $rName.video_id", NULL)
                ->where($tmName . '.resource_type = ?', 'ynvideochannel_video')
                ->where($tmName . '.tag_id = ?', $params['tag']);
        }

        if (!empty($params['videoIds']) && is_array($params['videoIds']) && count($params['videoIds']) > 0) {
            $select->where('video_id in (?)', $params['videoIds']);
        }

        if (isset($params['featured']) && is_numeric($params['featured'])) {
            $select->where('is_featured = ?', $params['featured']);
        }

        if (isset($params['favorites']) && $params['favorites'] && !empty($params['favorite_user_id']) ) {
            $favoriteTable = Engine_Api::_()->getDbTable('favorites', 'ynvideochannel');
            $favoriteTableName = $favoriteTable->info('name');
            $select -> join($favoriteTableName, $favoriteTableName . ".video_id = " . $rName . ".video_id")
                    -> where("$favoriteTableName.user_id = ?", $params['favorite_user_id'])
                    -> where("$favoriteTableName.favorited = 1");
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

        if (!empty($params['parent_type'])) {
            $select->where('parent_type = ?', $params['parent_type']);
        }

        if (!empty($params['parent_id'])) {
            $select->where('parent_id = ?', $params['parent_id']);
        }
        return $select;
    }

    public function getAllChildrenVideosByCategory($node)
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

    public function updateVideosOrder($channel_id, $videos) {
        foreach ($videos as $order => $video_id) {
            if ($video_id) {
                $where = array (
                    $this->getAdapter()->quoteInto('channel_id = ?', $channel_id),
                    $this->getAdapter()->quoteInto('video_id = ?', $video_id)
                );
                $data = array ('order' => $order);
                $this->update($data, $where);
            }
        }
    }

    public function deleteVideos($channel_id, $videos) {
        $vCount = 0;
        foreach ($videos as $video_id) {
            if ($video_id) {
                $where = array (
                    $this->getAdapter()->quoteInto('channel_id = ?', $channel_id),
                    $this->getAdapter()->quoteInto('video_id = ?', $video_id)
                );
                if($this->delete($where))
                {
                    $vCount++;
                };

            }
        }
        return $vCount;
    }
}