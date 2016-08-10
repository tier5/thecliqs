<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Model_Playlistvideo extends Core_Model_Item_Abstract {
    protected function _postDelete() {
        parent::_postDelete();
        $playlist = Engine_Api::_()->getItem('ynvideochannel_playlist', $this->playlist_id);
        if ($playlist && isset($playlist->video_count) && $playlist->video_count > 0) {
            $playlist->video_count = new Zend_Db_Expr('video_count - 1');
            $playlist->save();
        }
    }
}