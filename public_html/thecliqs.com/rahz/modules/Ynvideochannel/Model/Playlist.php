<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Model_Playlist extends Core_Model_Item_Abstract
{
    /**
     * @param array $params
     * @return string
     */
    public function getHref($params = array())
    {
        $params = array_merge(array(
            'route' => 'ynvideochannel_playlist_detail',
            'reset' => true,
            'playlist_id' => $this->getIdentity(),
            'slug' => $this->getSlug(),
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
    }

    public function getRichContent($view = false, $params = array())
    {
        return Zend_Registry::get('Zend_View')->partial('_playlist_feed.tpl', 'ynvideochannel', array('item' => $this));
    }

    /**
     * @throws Zend_Db_Table_Row_Exception
     */

    protected function _delete()
    {
        if ($this->_disableHooks) return;

        $table = Engine_Api::_()->getDbtable('playlistvideos', 'ynvideochannel');
        $select = $table->select()->where('playlist_id = ?', $this->getIdentity());
        foreach ($table->fetchAll($select) as $playlistVideo) {
            $playlistVideo->delete();
        }

        parent::_delete();
    }

    public function removeVideoFromPlaylist($videoId)
    {
        $playlistId = $this->getIdentity();
        $playlistVideo = Engine_Api::_()->getDbTable('playlistvideos', 'ynvideochannel')->fetchRow(array(
            "video_id = $videoId",
            "playlist_id = $playlistId"
        ));
        if ($playlistVideo) {
            if ($playlistVideo->delete()) {
                if ($this->video_count > 0) {
                    $this->video_count = new Zend_Db_Expr('video_count - 1');
                    $this->save();
                }
                return true;
            }
        }
        return false;
    }

    function isViewable()
    {
        return $this->authorization()->isAllowed(null, 'view');
    }

    function isEditable()
    {
        return $this->authorization()->isAllowed(null, 'edit');
    }

    function isDeletable()
    {
        return $this->authorization()->isAllowed(null, 'delete');
    }

    public function getVideosSelect()
    {
        $videoTbl = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');
        $videoTblName = $videoTbl->info('name');
        $playlistVideoTbl = Engine_Api::_()->getDbTable('playlistvideos', 'ynvideochannel');
        $playlistVideoTblName = $playlistVideoTbl->info('name');

        $select = $videoTbl->select()->setIntegrityCheck(false)
            ->from($videoTbl)
            ->join($playlistVideoTblName, "$playlistVideoTblName.video_id = $videoTblName.video_id")
            ->where("$playlistVideoTblName.playlist_id = ?", $this->getIdentity())
            ->order("$playlistVideoTblName.video_order ASC");
        return $select;
    }

    public function getLastVideoPhoto()
    {
        $videoTbl = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');
        $videoTblName = $videoTbl->info('name');
        $select = $this->getVideosSelect();
        $select->order("$videoTblName.video_id DESC")->limit(1);
        $lastVideo = $videoTbl->fetchRow($select);
        if ($lastVideo) {
            return $lastVideo->getPhotoUrl('thumb.normal');
        } else return "application/modules/Ynvideochannel/externals/images/noimg_playlist.jpg";
    }

    public function getVideoCount()
    {
        $table = Engine_Api::_()->getDBTable('playlistvideos', 'ynvideochannel');
        $name = $table->info('name');
        $select = $table->select()
            ->from($name, 'COUNT(*) AS count')
            ->where("playlist_id = $this->playlist_id");
        return $select->query()->fetchColumn(0);
    }

    public function getVideos($limit = 0)
    {
        $videoTbl = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');
        $select = $this->getVideosSelect();
        // short list to show in playlist listing
        if ($limit)
            $select->limit($limit);
        return $videoTbl->fetchAll($select);
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }

    public function getCategory() {
        $category = Engine_Api::_()->getItem('ynvideochannel_category', $this->category_id);
        if ($category) {
            return $category;
        }
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
            'parent_type' => $this->getType(),
            'user_id' => $this->owner_id
        );

        // Save
        $storage = Engine_Api::_()->storage();
        $angle = 0;
        if (function_exists('exif_read_data'))
        {
            $exif = exif_read_data($file);
            if (!empty($exif['Orientation']))
            {
                switch($exif['Orientation'])
                {
                    case 8 :
                        $angle = 90;
                        break;
                    case 3 :
                        $angle = 180;
                        break;
                    case 6 :
                        $angle = -90;
                        break;
                }
            }
        }

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image->resize(854, 480)->write($path . '/m_' . $name)->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image->resize(640, 360)->write($path . '/p_' . $name)->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image->resize(640, 360)->write($path . '/in_' . $name)->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);
        $iProfile = $storage->create($path . '/p_' . $name, $params);
        $iIconNormal = $storage->create($path . '/in_' . $name, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
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

    public function getPhotoUrl($type = null) {
        if( empty($this->photo_id) ) {
            return $this->getLastVideoPhoto();
        } else {
            return parent::getPhotoUrl($type);
        }
    }

    public function updateVideosOrder($order) {
        Engine_Api::_()->getDbTable('playlistvideos', 'ynvideochannel')->updateVideosOrder($this->getIdentity(), $order);
    }

    public function deleteVideos($deleted) {
        Engine_Api::_()->getDbTable('playlistvideos', 'ynvideochannel')->deleteVideos($this->getIdentity(), $deleted);
    }

    public function getMediaType()
    {
        return 'playlist';
    }
}
