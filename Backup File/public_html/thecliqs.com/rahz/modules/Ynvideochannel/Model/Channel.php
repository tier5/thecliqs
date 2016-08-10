<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Model_Channel extends Core_Model_Item_Abstract
{
    protected $_owner_type = 'user';
    protected $_type = 'ynvideochannel_channel';

    /**
     * @param array $params
     * @return string
     */
    public function getHref($params = array())
    {
        $params = array_merge(array(
            'route' => 'ynvideochannel_channel_detail',
            'reset' => true,
            'channel_id' => $this -> getIdentity(),
            'slug' => $this -> getSlug(),
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
    }

    /**
     * @param $photo
     * @return $this
     * @throws Engine_Image_Exception
     * @throws User_Model_Exception
     */
    public function setPhoto($photo)
    {
        if ($photo instanceof Zend_Form_Element_File)
        {
            $file = $photo -> getFileName();
            $name = basename($file);
        }
        else if( $photo instanceof Storage_Model_File ) {
            $file = $photo->temporary();
            $name = $photo->name;
        }
        else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $name = $photo['name'];
        }
        else if (is_string($photo)) {
            $headers = get_headers($photo);
            // youtube image url is not valid
            if (substr($headers[0], 9, 3) != '200') {
                return $this;
            }
            $pathInfo = @pathinfo($photo);
            $parts = explode('?', preg_replace("/#!/", "?", $pathInfo['extension']));
            $ext = $parts[0];
            $photo_parsed = @parse_url($photo);
            if ($ext && $photo_parsed) {
                $file = APPLICATION_PATH . '/temporary/ynvideochannel_channel' . md5($photo) . '.' . $ext;
                file_put_contents($file, file_get_contents($photo));
                $name = basename($file);

            } else
                throw new User_Model_Exception('can not get get thumbnail image from youtube channel');
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'ynvideochannel_channel',
            'parent_id' => $this -> getIdentity(),
            'user_id' => $this -> owner_id
        );

        // Save
        $storage = Engine_Api::_() -> storage();
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
        $image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image -> resize(640, 360) -> write($path . '/m_' . $name) -> destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image-> resize(420, 236) -> write($path . '/p_' . $name) -> destroy();

        // Store
        $iMain = $storage -> create($path . '/m_' . $name, $params);
        $iProfile = $storage -> create($path . '/p_' . $name, $params);

        $iMain -> bridge($iProfile, 'thumb.profile');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        //@unlink($file);

        // Update row
        $this -> modified_date = date('Y-m-d H:i:s');
        $this -> photo_id = $iMain -> file_id;
        $this -> save();

        return $this;
    }

    /**
     * @param $photo
     * @return $this
     * @throws Engine_Image_Exception
     * @throws User_Model_Exception
     */
    public function setCoverPhoto($photo)
    {
        if ($photo instanceof Zend_Form_Element_File)
        {
            $file = $photo -> getFileName();
            $name = basename($file);
        }
        else if( $photo instanceof Storage_Model_File ) {
            $file = $photo->temporary();
            $name = $photo->name;
        }
        else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $name = $photo['name'];
        }
        else if (is_string($photo)) {
            $headers = get_headers($photo);
            // youtube image url is not valid
            if (substr($headers[0], 9, 3) != '200') {
                return $this;
            }
            $pathInfo = @pathinfo($photo);
            $parts = explode('?', preg_replace("/#!/", "?", $pathInfo['extension']));
            $ext = $parts[0];
            $photo_parsed = @parse_url($photo);
            if ($ext && $photo_parsed) {
                $file = APPLICATION_PATH . '/temporary/ynvideochannel_channel' . md5($photo) . '.' . $ext;
                file_put_contents($file, file_get_contents($photo));
                $name = basename($file);

            } else
                throw new User_Model_Exception('can not get get thumbnail image from youtube channel');
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'ynvideochannel_channel',
            'parent_id' => $this -> getIdentity(),
            'user_id' => $this -> owner_id
        );

        // Save
        $storage = Engine_Api::_() -> storage();
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
        $image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image -> resize(854, 480) -> write($path . '/m_' . $name) -> destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image -> resize(640, 360) -> write($path . '/p_' . $name) -> destroy();

        // Store
        $iMain = $storage -> create($path . '/m_' . $name, $params);
        $iProfile = $storage -> create($path . '/p_' . $name, $params);

        $iMain -> bridge($iProfile, 'thumb.profile');

        // Remove temp files
        @unlink($path . '/m_' . $name);
        @unlink($path . '/p_' . $name);
        //@unlink($file);

        // Update row
        $this -> modified_date = date('Y-m-d H:i:s');
        $this -> cover_id = $iMain -> file_id;
        $this -> save();
        return $this;
    }

    /**
     * @throws Zend_Db_Table_Row_Exception
     */

    protected function _delete()
    {
        if( $this->_disableHooks ) return;

        // TODO delete all subscribes belong to this channel
        // update channel id = 0 for all videos belong to this channel
        $table = Engine_Api::_()->getItemTable('ynvideochannel_video');
        $table -> update(array('channel_id' => 0), "channel_id = ". $this -> getIdentity());

        parent::_delete();
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     **/
    public function comments()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }


    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     **/
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

    /**
     * @param $order
     */
    public function updateVideosOrder($order) {
        Engine_Api::_()->getDbTable('videos', 'ynvideochannel')->updateVideosOrder($this->getIdentity(), $order);
    }

    /**
     * @param $deleted
     */
    public function deleteVideos($deleted) {
        return Engine_Api::_()->getDbTable('videos', 'ynvideochannel')->deleteVideos($this->getIdentity(), $deleted);
    }

    /**
     * @return mixed
     */
    public function getVideosSelect() {
        $videoTbl = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');
        $videoTblName = $videoTbl->info('name');

        $select = $videoTbl->select()->setIntegrityCheck(false)
            ->from($videoTbl)
            ->where("$videoTblName.channel_id = ?", $this->getIdentity())
            ->order("$videoTblName.order ASC");
        return $select;
    }

    /**
     * @param int $limit
     * @return mixed
     */

    public function getVideos($limit = 0) {

        $videoTbl = Engine_Api::_()->getDbTable('videos', 'ynvideochannel');
        $select = $this->getVideosSelect();
        // short list to show in playlist listing
        if ($limit)
            $select->limit($limit);
        return $videoTbl->fetchAll($select);
    }

    /**
     *
     */
    public function isAutoUpdate()
    {
        return $this -> auto_update?true:false;
    }

    /**
     * Gets a url to the current photo representing this item. Return null if none
     * set
     *
     * @param string The photo type (null -> main, thumb, icon, etc);
     * @return string The photo url
     */
    public function getPhotoUrl($type = null)
    {
        if( empty($this->photo_id) ) {
            return "application/modules/Ynvideochannel/externals/images/noimg_channel.jpg";
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type);
        if( !$file ) {
            return null;
        }

        return $file->map();
    }

    /**
     * Gets a url to the current cover representing this item. Return null if none
     * set
     *
     * @param string The cover type (null -> main, thumb, icon, etc);
     * @return string The cover url
     */
    public function getCoverUrl($type = null)
    {
        if( empty($this->cover_id) ) {
            return "application/modules/Ynvideochannel/externals/images/noimg_cover.jpg";
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->cover_id, $type);
        if( !$file ) {
            return null;
        }

        return $file->map();
    }

    public function isSubscribed($user_id)
    {
        $subscribeTable = Engine_Api::_() -> getDbTable('subscribes', 'ynvideochannel');
        $select = $subscribeTable->select()->where('channel_id = ?', $this->channel_id)->where('user_id =?', $user_id)->limit(1);
        return $subscribeTable -> fetchRow($select)?true:false;
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

    public function getSubscribers()
    {
        $subscribeTbl = Engine_Api::_()->getDbTable('subscribes', 'ynvideochannel');
        $subscriberIds = $subscribeTbl->select() -> from($subscribeTbl, 'user_id')->where('channel_id = ?', $this -> getIdentity()) -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
        if(count($subscriberIds) > 0) {
            $userTbl = Engine_Api::_() -> getItemTable('user');
            $select = $userTbl->select()->where('user_id IN (?)', $subscriberIds)->order('displayname');
            return $userTbl -> fetchAll($select)? $userTbl -> fetchAll($select):null;
        }
        return null;
    }

    public function getRichContent()
    {
        return Zend_Registry::get('Zend_View') -> partial('_channel_feed.tpl', 'ynvideochannel', array('item' => $this));
    }

    public function getMediaType()
    {
        return 'channel';
    }
}
