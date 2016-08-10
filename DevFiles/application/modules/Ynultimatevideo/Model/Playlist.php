<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_Model_Playlist extends Core_Model_Item_Abstract {

    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'ynultimatevideo_playlist',
            'reset' => true,
            'id' => $this->getIdentity(),
            'slug' => $this->getSlug(),
            'action' => 'view'
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
    }

    function isViewable() {
        return $this->authorization()->isAllowed(null, 'view');
    }

    function isEditable() {
        return $this->authorization()->isAllowed(null, 'edit');
    }

    function isDeletable() {
        return $this->authorization()->isAllowed(null, 'delete');
    }

    public function setPhoto($photo) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $name = basename($file);
        }
        else if( $photo instanceof Storage_Model_File ) {
            $file = $photo->temporary();
            $name = $photo->name;
        }
        else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $name = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $name = basename($file);
        } else {
            throw new Event_Model_Exception('invalid argument passed to setPhoto');
        }
        if ($this->photo_id) {
            $this->removeOldPhoto();
        }
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_id' => $this->getIdentity(),
            'parent_type' => 'ynultimatevideo_playlist',
            'user_id' => $this -> user_id
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(854, 480) -> write($path . '/m_' . $name) -> destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(640, 360) -> write($path . '/p_' . $name) -> destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(420, 236) -> write($path . '/in_' . $name) -> destroy();

        // Store
        $iMain = $storage -> create($path . '/m_' . $name, $params);
        $iProfile = $storage -> create($path . '/p_' . $name, $params);
        $iIconNormal = $storage -> create($path . '/in_' . $name, $params);

        $iMain -> bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path.'/in_'.$name);
        @unlink($file);

        // Update row
        $this->modified_date = date('Y-m-d H:i:s');
        $this->photo_id = $iMain->getIdentity();
        $this->save();

        return $this;
    }

    protected function removeOldPhoto() {
        if ($this->photo_id) {
            $item = Engine_Api::_()->storage()->get($this->photo_id);

            $table = Engine_Api::_()->getItemTable('storage_file');
            $select = $table->select()
                ->where('parent_type = ?', $this->getType())
                ->where('parent_id = ?', $this->getIdentity());

            foreach ($table->fetchAll($select) as $file) {
                try {
                    $file->delete();
                } catch (Exception $e) {
                    if (!($e instanceof Engine_Exception)) {
                        $log = Zend_Registry::get('Zend_Log');
                        $log->log($e->__toString(), Zend_Log::WARN);
                    }
                }
            }
        }
    }

    protected function _postDelete() {
        parent::_postDelete();

        // Remove all association videos to this playlist
        $table = Engine_Api::_()->getDbtable('playlistassoc', 'ynultimatevideo');
        $select = $table->select()->where('playlist_id = ?', $this->getIdentity());

        foreach ($table->fetchAll($select) as $playlistAssoc) {
            $playlistAssoc->delete();
        }
    }

    public function addVideoToPlaylist($video) {
        $playlistAssocTbl = Engine_Api::_()->getDbTable('playlistassoc', 'ynultimatevideo');

        $row = $playlistAssocTbl->fetchRow(array("video_id = {$video->getIdentity()}", "playlist_id = {$this->getIdentity()}"));
        if (!$row) {
            $playlistAssoc = $playlistAssocTbl->createRow();
            $playlistAssoc->video_id = $video->getIdentity();
            $playlistAssoc->playlist_id = $this->getIdentity();
            $playlistAssoc->creation_date = date('Y-m-d H:i:s');
            $playlistAssoc->save();

            $this->video_count = new Zend_Db_Expr('video_count + 1');
            $this->save();

            return $playlistAssoc;
        } else {
            throw new Ynultimatevideo_Model_ExistedException();
        }
    }

    public function removeVideoFromPlaylist($video) {
        $playlistAssocTbl = Engine_Api::_()->getDbTable('playlistassoc', 'ynultimatevideo');

        $row = $playlistAssocTbl->fetchRow(array("video_id = {$video->getIdentity()}", "playlist_id = {$this->getIdentity()}"));
        if ($row) {
            if ($row->delete()) {

                $this->video_count = new Zend_Db_Expr('video_count - 1');
                $this->save();

                return true;
            }
        }
        return false;
    }

    public function getVideosSelect() {
        $videoTbl = Engine_Api::_()->getDbTable('videos', 'ynultimatevideo');
        $videoTblName = $videoTbl->info('name');
        $playlistAssocTbl = Engine_Api::_()->getDbTable('playlistassoc', 'ynultimatevideo');
        $playlistAssocTblName = $playlistAssocTbl->info('name');

        $select = $videoTbl->select()->setIntegrityCheck(false)
            ->from($videoTbl)
            ->join($playlistAssocTblName, "$playlistAssocTblName.video_id = $videoTblName.video_id")
            ->where("$playlistAssocTblName.playlist_id = ?", $this->getIdentity())
            ->order("$playlistAssocTblName.video_order ASC");
//            ->where("$videoTblName.search = 1")
//            ->where("$videoTblName.status = 1");
        return $select;
    }

    public function getVideos($limit = 0) {

        $videoTbl = Engine_Api::_()->getDbTable('videos', 'ynultimatevideo');
        $select = $this->getVideosSelect();
        // short list to show in playlist listing
        if ($limit)
            $select->limit($limit);
        return $videoTbl->fetchAll($select);
    }

    public function getLastVideoPhoto() {
        $videoTbl = Engine_Api::_()->getDbTable('videos', 'ynultimatevideo');
        $videoTblName = $videoTbl->info('name');
        $select = $this->getVideosSelect();
        $select -> order("$videoTblName.video_id DESC") -> limit(1);
        $lastVideo = $videoTbl -> fetchRow($select);
        if ($lastVideo) {
            return $lastVideo -> getPhotoUrl('thumb.normal');
        }
        else return '';
    }

    public function getVideoCount(){
        $table = Engine_Api::_()->getDBTable('playlistassoc', 'ynultimatevideo');
        $name = $table->info('name');
        $select = $table->select()
            ->from($name, 'COUNT(*) AS count')
            ->where("playlist_id = $this->playlist_id");
        return $select->query()->fetchColumn(0);
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    public function canAddVideos() {
        return true;
    }

    public function getCategory() {
        $params['type'] = 'playlist';
        $view = Zend_Registry::get('Zend_View');
        $category = '';
        $categoryId = $this->category_id;
        if (!$categoryId) return $category;

        $categoryItem = Engine_Api::_()->getItem('ynultimatevideo_category', $categoryId);
        if ($categoryItem) {
            $category = $view->htmlLink($categoryItem->getHref($params), $categoryItem->getTitle());
        }
        return $category;
    }

    public function getRichContent($view = false, $params = array()) {
        $zend_View = Zend_Registry::get('Zend_View');
        // $view == false means that this rich content is requested from the activity feed
        if($view == false){
            return $zend_View -> partial('_playlist_feed.tpl', 'ynultimatevideo', array('item' => $this));
        }
    }


    public function updateVideosOrder($order) {
        Engine_Api::_()->getDbTable('playlistassoc', 'ynultimatevideo')->updateVideosOrder($this->getIdentity(), $order);
    }

    public function deleteVideos($deleted) {
        Engine_Api::_()->getDbTable('playlistassoc', 'ynultimatevideo')->deleteVideos($this->getIdentity(), $deleted);
    }

    public function getPhotoUrl($type = null) {
        if( empty($this->photo_id) ) {
            return $this->getLastVideoPhoto();
        } else {
            return parent::getPhotoUrl($type);
        }
    }
}