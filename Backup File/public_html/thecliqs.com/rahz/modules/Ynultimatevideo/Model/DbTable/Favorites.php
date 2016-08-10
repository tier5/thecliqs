<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Model_DbTable_Favorites extends Engine_Db_Table {

    protected $_rowClass = 'Ynultimatevideo_Model_Favorite';
    protected $_name = 'ynultimatevideo_favorites';

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
                "user_id = $userId"
            ));
            if ($row) return true;
        }

        return false;
    }
}