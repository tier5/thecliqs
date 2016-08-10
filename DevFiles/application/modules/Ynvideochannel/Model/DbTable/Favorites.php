<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Model_DbTable_Favorites extends Engine_Db_Table {

    protected $_rowClass = 'Ynvideochannel_Model_Favorite';
    protected $_name = 'ynvideochannel_favorites';

    /**
    * @param $videoId
    * @param $userId
    * @return bool
    * determine if the video is in user's favorite list
     */
    public function isAdded($videoId, $userId) {

        if (isset($userId) && isset($videoId)) {
            $row = $this -> fetchRow(array(
                "video_id = $videoId",
                "user_id = $userId",
                "favorited = 1"
            ));
            if ($row) return true;
        }

        return false;
    }

    /**
     * @param $videoId
     * @param $userId
     * @return bool
     */
    public function addVideoToFavorite($video, $user)
    {
        $videoId = $video -> getIdentity();
        $userId = $user -> getIdentity();
        $favorite = $this -> fetchRow(array(
            "video_id = $videoId",
            "user_id = $userId"
        ));
        if (!$favorite) {
            $favorite = $this->createRow();
            $favorite->video_id = $videoId;
            $favorite->user_id = $userId;
            $favorite -> save();

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($favorite, 'registered', 'view', true);
            $auth->setAllowed($favorite, 'registered', 'comment', true);

            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $action = $actionTable->addActivity($user, $favorite, 'ynvideochannel_add_favorite');

            if ($action != null) {
                $actionTable->attachActivity($action, $video);
            }

            foreach ($actionTable->getActionsByObject($favorite) as $action) {
                $actionTable->resetActivityBindings($action);
            }
            if($user -> getIdentity() != $video -> getOwner() -> getIdentity()) {
                $notifyApi->addNotification($video->getOwner(), $user, $video, 'ynvideochannel_favorite_video');
            }

        }
        $favorite -> favorited = 1;
        $favorite -> save();

        $video = Engine_Api::_() -> getItem('ynvideochannel_video', $videoId);
        $video -> favorite_count = new Zend_Db_Expr('favorite_count + 1');
        $video -> save();
        return $favorite;
    }

    /**
     * @param $videoId
     * @param $userId
     * @return bool
     */
    public function removeVideoFromFavorite($videoId, $userId)
    {
        $favorite = Engine_Api::_() -> getDbTable('favorites', 'ynvideochannel') -> fetchRow(array(
            "video_id = $videoId",
            "user_id = $userId"
        ));
        if ($favorite)
        {
            $video = Engine_Api::_() -> getItem('ynvideochannel_video', $videoId);
            $video -> favorite_count = new Zend_Db_Expr('favorite_count - 1');
            $video -> save();

            $favorite -> favorited = 0;
            return $favorite -> save();
        }
        return false;
    }
}