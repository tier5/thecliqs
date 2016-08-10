<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Model_Video extends Core_Model_Item_Abstract
{
    protected $_owner_type = 'user';
    protected $_type = 'ynvideochannel_video';

    /**
     * @param array $params
     * @return string
     * @throws User_Model_Exception
     */
    public function getHref($params = array())
    {
        $params = array_merge(array(
            'route' => 'ynvideochannel_video_detail',
            'reset' => true,
            'video_id' => $this -> getIdentity(),
            'slug' => $this -> getSlug(),
        ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
    }

    /**
     * @param bool $view
     * @param array $params
     * @return string
     * @throws Zend_Exception
     */
    public function getRichContent()
    {
        return Zend_Registry::get('Zend_View') -> partial('_video_feed.tpl', 'ynvideochannel', array('item' => $this));
    }

    /**
     * @param $photo
     * @param bool $cronJob
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
                $file = APPLICATION_PATH . '/temporary/ynvideochannel_' . md5($photo) . '.' . $ext;
                file_put_contents($file, file_get_contents($photo));
                $name = basename($file);

            } else
                throw new User_Model_Exception('can not get get thumbnail image from youtube video');
        } else {
            throw new User_Model_Exception('invalid argument passed to setPhoto');
        }

        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => 'ynvideochannel_video',
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

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image -> resize(640, 360) -> write($path . '/in_' . $name) -> destroy();

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
        $this -> modified_date = date('Y-m-d H:i:s');
        $this -> photo_id = $iMain -> file_id;
        $this -> save();

        return $this;
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

    /**
     * Gets a proxy object for the tags handler
     *
     * @return Engine_ProxyObject
     **/
    public function tags()
    {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
    }

    /**
     * @throws Zend_Db_Table_Row_Exception
     */

    protected function _delete()
    {
        // update video_count of channel
        if($this->channel_id != 0) {
            $channel = Engine_Api::_()->getItem('ynvideochannel_channel', $this->channel_id);
            $channel->video_count--;
            $channel->save();
        }
        // update video_count of playlist
        $playlistvideoTbl = Engine_Api::_()->getDbTable('playlistvideos', 'ynvideochannel');
        $playlistvideoTblName = $playlistvideoTbl->info('name');
        $playlistTbl = Engine_Api::_()->getDbTable('playlists', 'ynvideochannel');
        $playlistTblName = $playlistTbl->info('name');
        $select = $playlistTbl->select()->setIntegrityCheck(false)
            ->from($playlistTbl)
            ->join($playlistvideoTblName, "$playlistvideoTblName.playlist_id = $playlistTblName.playlist_id")
            ->where("$playlistvideoTblName.video_id = ?", $this->getIdentity());
        $playlists = $playlistTbl->fetchAll($select);
        if($playlists)
        {
            foreach ($playlists as $playslist)
            {
                $playslist->video_count--;
                $playslist->save();
            }
        }
        if( $this->_disableHooks ) return;
        Engine_Api::_() -> getDbtable('favorites', 'ynvideochannel') -> delete(array('video_id = ?' => $this -> video_id));
        Engine_Api::_() -> getDbtable('ratings', 'ynvideochannel') -> delete(array('video_id = ?' => $this -> video_id));
        parent::_delete();
    }

    public function getChannel()
    {
        return Engine_Api::_()->getItem('ynvideochannel_channel', $this -> channel_id);
    }

    public function getCategory() {
        $category = Engine_Api::_()->getItem('ynvideochannel_category', $this->category_id);
        if ($category) {
            return $category;
        }
    }

    public function getVideoIframe($width = 560, $height = 315, $autoplay = 1, $allowFullscreen = 1, $related = 1)
    {
        $setting = Engine_Api::_()->getApi('settings', 'core');
        $autoplayStr = $setting->getSetting('ynvideochannel.auto.play', $autoplay) ? 'autoplay=1&' : '';
        $allowFullscreenStr = $setting->getSetting('ynvideochannel.full.screen', $allowFullscreen) ? 'allowfullscreen' : '';
        $allowFullscreenBtnStr = !$setting->getSetting('ynvideochannel.full.screen', $allowFullscreen) ? 'fs=0&' : '';
        $relatedStr = !$setting->getSetting('ynvideochannel.related.videos', $related) ? 'rel=0&' : '';
        return '<iframe width="'.$width.'" height="'.$height.'" src="//www.youtube.com/embed/'.$this -> code.'?'.$relatedStr.$autoplayStr.$allowFullscreenBtnStr.'" frameborder="0" '.$allowFullscreenStr.'></iframe>';
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

    public function getEmbedCode(array $options = null)
    {
        $options = array_merge(array(
            'height' => '315',
            'width' => '560',
        ), (array)$options);
        $view = Zend_Registry::get('Zend_View');
        $url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                'module' => 'ynvideochannel',
                'controller' => 'video',
                'action' => 'external',
                'video_id' => $this -> getIdentity(),

            ), 'default', true) . '?format=frame';
        return '<iframe ' . 'src="' . $view -> escape($url) . '" ' . 'width="' . sprintf("%d", $options['width']) . '" ' . 'height="' . sprintf("%d", $options['height']) . '" ' . 'style="overflow:hidden;"' . '>' . '</iframe>';
    }

    public function getPlayerDOM()
    {
        $video_id = $this->getIdentity();
        $code = $this->code;
        $videoDom = '<video id="player_' . $video_id . '" class="ynvideochannel-player" data-type="1" width="764" height="492">
                <source type="video/youtube" src="http://www.youtube.com/watch?v='. $code .'" />
            </video>';
        return $videoDom;
    }

    public function getRated($user_id)
    {
        $table = Engine_Api::_() -> getDbTable('ratings', 'ynvideochannel');
        $select = $table -> select() -> where('video_id = ?', $this -> getIdentity()) -> where('user_id = ?', $user_id) -> limit(1);
        return $table -> fetchRow($select);
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
            return "application/modules/Ynvideochannel/externals/images/noimg_video.jpg";
        }

        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->photo_id, $type);
        if( !$file ) {
            return null;
        }

        return $file->map();
    }

}
