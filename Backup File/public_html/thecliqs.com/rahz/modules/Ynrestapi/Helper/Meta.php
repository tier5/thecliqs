<?php

class Ynrestapi_Helper_Meta
{
    /**
     * @var array
     */
    protected $metas = array();

    /**
     * @var array
     */
    static $modules = array(
        // for supports 3rd modules
        // ex: 'activity' => array('3rdactivity', 'activity'),
    );

    /**
     * @var mixed
     */
    static $inst;

    /**
     * @var array
     */
    static $workingModules = array();

    /**
     * Constuctor
     */
    public function __construct()
    {
        $this->supportActivity();
        $this->supportAlbum();
        $this->supportBlog();
        $this->supportCore();
        $this->supportClassified();
        $this->supportEvent();
        $this->supportForum();
        $this->supportGroup();
        $this->supportMessage();
        $this->supportMusic();
        // $this->supportNetwork();
        // $this->supportPoll();
        // $this->supportUltimatenews();
        $this->supportUser();
        $this->supportVideo();
        // $this->supportYnbusinesspages();
        // $this->supportYnJobPosting();
        // $this->supportYnlisting();
        // $this->supportYnresume();
    }

    /**
     * @param  $key
     * @return mixed
     */
    private static function _getWorkingModule($key)
    {
        $engine = Engine_Api::_();
        foreach (self::$modules[$key] as $module) {
            if ($engine->hasModuleBootstrap($module)) {
                return $module;
            }
        }

        return $key;
    }

    /**
     * @param $key
     */
    public static function getWorkingModule($key)
    {
        if (!isset(self::$workingModules[$key])) {
            self::$workingModules[$key] = self::_getWorkingModule($key);
        }
        return self::$workingModules[$key];
    }

    public static function getInstance()
    {
        if (null == self::$inst) {
            self::$inst = new self;
        }
        return self::$inst;
    }

    /**
     * @param $entry
     * @param $opts
     * @param array    $params
     */
    public function getModelHelper($entry, $opts = null, $params = array())
    {
        $type = $entry->getType();

        $options = $this->getMeta('model', $type);

        if ($opts) {
            $options = array_merge($options, $opts);
        }

        $def = @$options['def'] ? $options['def'] : 'Ynrestapi_Helper_Base';

        return new $def($entry, $options, $params);
    }

    /**
     * @param $category
     * @param $key
     * @param $specs
     */
    public function addMeta($category, $key, $specs)
    {
        if (is_array($key)) {
            foreach ($key as $k) {
                $this->metas[$category][$k] = $specs;
            }
        } else {
            $this->metas[$category][$key] = $specs;
        }

    }

    /**
     * @param  $category
     * @param  $key
     * @return mixed
     */
    public function getMeta($category, $key)
    {
        return $this->metas[$category][$key];
    }

    public function supportNetwork()
    {
        $this->addMeta('model', array(
            'network',
        ), array(
            'def' => 'Ynrestapi_Helper_Network',
            'simple_img' => 'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
    }

    public function supportBlog()
    {
        $this->addMeta('model', array(
            'blog',
        ), array(
            'def' => 'Ynrestapi_Helper_Blog',
            'simple_img' => 'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
    }

    protected function supportCore()
    {
        $this->addMeta('model', array(
            'activity_like',
            'core_like',
        ), array(
            'def' => 'Ynrestapi_Helper_Like',
            'simple_img' => 'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));

        $this->addMeta('model', array(
            'activity_comment',
            'core_comment',
        ), array(
            'def' => 'Ynrestapi_Helper_Comment'));

        $this->addMeta('model', array(
            'core_link',
            'link',
        ), array(
            'def' => 'Ynrestapi_Helper_Link',
            'simple_img' => 'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
    }

    public function supportPoll()
    {
        $this->addMeta('model', array(
            'poll',
        ), array(
            'def' => 'Ynrestapi_Helper_Poll',
            'simple_img' => 'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
                'thumb.icon' => '/application/modules/User/externals/images/nophoto_user_thumb_icon.png',
                'thumb.normal' => '/application/modules/User/externals/images/nophoto_user_thumb_profile.png',
                'thumb.profile' => '/application/modules/User/externals/images/nophoto_user_thumb_profile.png',
            ),
        ));
    }

    public function supportForum()
    {
        $this->addMeta('model', array(
            'forum',
        ), array(
            'def' => 'Ynrestapi_Helper_Forum_Forum',
            'simple_img' => 'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(

            ),
        ));

        $this->addMeta('model', array(
            'forum_topic',
        ), array(
            'def' => 'Ynrestapi_Helper_Forum_Topic',
            'simple_img' => 'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));

        $this->addMeta('model', array(
            'forum_post',
        ), array(
            'def' => 'Ynrestapi_Helper_Forum_Post',
            'simple_img' => 'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));

    }

    public function supportUser()
    {
        $this->addMeta('model', array(
            'user',
        ), array(
            'def' => 'Ynrestapi_Helper_User',
            'simple_img' => 'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
                '' => '/application/modules/Ynrestapi/externals/images/avatar-default.png',
                'thumb.icon' => '/application/modules/Ynrestapi/externals/images/avatar-default.png',
                'thumb.normal' => '/application/modules/Ynrestapi/externals/images/avatar-default.png',
                'thumb.profile' => '/application/modules/Ynrestapi/externals/images/avatar-default.png',
            ),
        ));
    }

    public function supportVideo()
    {
        $this->addMeta('model', array(
            'video',
        ), array(
            'def' => 'Ynrestapi_Helper_Video',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynrestapi/externals/images/video-default.png',
                'thumb.icon' => '/application/modules/Ynrestapi/externals/images/video-default.png',
                'thumb.normal' => '/application/modules/Ynrestapi/externals/images/video-default.png',
            ),
        ));
    }

    public function supportEvent()
    {
        $this->addMeta('model', array(
            'event',
        ), array(
            'def' => 'Ynrestapi_Helper_Event_Event',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynrestapi/externals/images/event-default.png',
                'thumb.icon' => '/application/modules/Ynrestapi/externals/images/event-default.png',
                'thumb.normal' => '/application/modules/Ynrestapi/externals/images/event-default.png',
            ),
        ));

        $this->addMeta('model', array(
            'event_photo',
        ), array(
            'def' => 'Ynrestapi_Helper_Event_Photo',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynrestapi/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynrestapi/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynrestapi/externals/images/image-default.png',
            ),
        ));
    }

    public function supportMusic()
    {
        $this->addMeta('model', array(
            'music_playlist',
        ), array(
            'def' => 'Ynrestapi_Helper_Music_Playlist',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynrestapi/externals/images/music-default.png',
                'thumb.icon' => '/application/modules/Ynrestapi/externals/images/music-default.png',
                'thumb.normal' => '/application/modules/Ynrestapi/externals/images/music-default.png',
            ),
        ));

        $this->addMeta('model', array(
            'music_playlist_song',
        ), array(
            'def' => 'Ynrestapi_Helper_Music_PlaylistSong',
            'simple_img' => 'thumb.icon',
            'no_imgs' => array(
                '' => '/application/modules/Ynrestapi/externals/images/music-default.png',
                'thumb.icon' => '/application/modules/Ynrestapi/externals/images/music-default.png',
                'thumb.normal' => '/application/modules/Ynrestapi/externals/images/music-default.png',
            ),
        ));
    }

    public function supportActivity()
    {
        $this->addMeta('model', array(
            'activity_action',
        ), array(
            'def' => 'Ynrestapi_Helper_Activity',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
                'thumb.icon' => '/application/modules/Ynrestapi/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynrestapi/externals/images/image-default.png',
            ),
        ));

        $this->addMeta('model', array(
            'activity_notification',
        ), array(
            'def' => 'Ynrestapi_Helper_Notification',
            'simple_img' => 'thumb.icon', // fetch icon when return to simple list
            'no_imgs' => array(
            ),
        ));
    }

    public function supportAlbum()
    {
        $this->addMeta('model', array(
            'album',
        ), array(
            'def' => 'Ynrestapi_Helper_Album_Album',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynrestapi/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynrestapi/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynrestapi/externals/images/image-default.png',
            ),
        ));

        $this->addMeta('model', array(
            'album_photo',
        ), array(
            'def' => 'Ynrestapi_Helper_Album_Photo',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynrestapi/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynrestapi/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynrestapi/externals/images/image-default.png',
            ),
        ));
    }

    public function supportGroup()
    {
        $this->addMeta('model', array(
            'group',
        ), array(
            'def' => 'Ynrestapi_Helper_Group_Group',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Group/externals/images/nophoto_group_thumb_icon.png',
                'thumb.icon' => '/application/modules/Group/externals/images/nophoto_group_thumb_icon.png',
                'thumb.normal' => '/application/modules/Group/externals/images/nophoto_group_thumb_normal.png',
                'thumb.profile' => '/application/modules/Group/externals/images/nophoto_group_thumb_profile.png',
            ),
        ));

        $this->addMeta('model', array(
            'group_photo',
        ), array(
            'def' => 'Ynrestapi_Helper_Group_Photo',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynrestapi/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynrestapi/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynrestapi/externals/images/image-default.png',
            ),
        ));
    }

    public function supportClassified()
    {
        $this->addMeta('model', array(
            'classified'
        ), array(
            'def' => 'Ynrestapi_Helper_Classified_Classified',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Classified/externals/images/nophoto_classified_thumb_profile.png',
                'thumb.icon' => '/application/modules/Classified/externals/images/nophoto_classified_thumb_normal.png',
                'thumb.normal' => '/application/modules/Classified/externals/images/nophoto_classified_thumb_normal.png',
                'thumb.profile' => '/application/modules/Classified/externals/images/nophoto_classified_thumb_profile.png',
            ),
        ));

        $this->addMeta('model', array(
            'classified_photo',
        ), array(
            'def' => 'Ynrestapi_Helper_Classified_Photo',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
                '' => '/application/modules/Ynrestapi/externals/images/image-default.png',
                'thumb.icon' => '/application/modules/Ynrestapi/externals/images/image-default.png',
                'thumb.normal' => '/application/modules/Ynrestapi/externals/images/image-default.png',
            ),
        ));
    }

    public function supportMessage()
    {
        $this->addMeta('model', array(
            'messages_message',
        ), array(
            'def' => 'Ynrestapi_Helper_Message_Message',
            'simple_img' => 'thumb.normal',
            'no_imgs' => array(
            ),
        ));

        $this->addMeta('model', array(
            'messages_conversation',
        ), array(
            'def' => 'Ynrestapi_Helper_Message_Conversation',
            'simple_img' => 'thumb.icon',
            'no_imgs' => array(
            ),
        ));
    }

    /**
     * @param  $select
     * @param  $iPage
     * @param  $iLimit
     * @param  $fields
     * @param  array     $params
     * @return mixed
     */
    public static function exportByPage($select, $iPage, $iLimit, $fields, $params = array())
    {
        if (!$select) {
            return array();
        } else if ($select instanceof Zend_Paginator) {
            $paginator = $select;
        } else {
            $paginator = Zend_Paginator::factory($select);
        }

        $paginator->setCurrentPageNumber($iPage);

        $paginator->setItemCountPerPage($iLimit);

        if ($iPage < 1) {
            $iPage = 1;
        }

        if (!$iLimit) {
            $iLimit = 10;
        }

        if ($iPage > $paginator->count()) {
            return array();
        }

        $return = array();

        foreach ($paginator as $entry) {
            $return[] = self::getInstance()->getModelHelper($entry, array(), $params)->toArray($fields);
        }

        return $return;
    }

    /**
     * @param $entry
     * @param $fields
     * @param array     $params
     */
    public static function exportOne($entry, $fields, $params = array())
    {
        return self::getInstance()->getModelHelper($entry, array(), $params)->toArray($fields);
    }

    /**
     * @param  $select
     * @param  $fields
     * @param  array     $params
     * @return mixed
     */
    public static function exportAll($select, $fields, $params = array())
    {
        if (!$select) {
            return array();
        } else
        if ($select instanceof Zend_Paginator) {
            $paginator = $select;
        } else {
            $paginator = Zend_Paginator::factory($select);
            $paginator->setItemCountPerPage(1000);
        }

        $return = array();

        foreach ($paginator as $entry) {
            try {
                $return[] = Ynrestapi_Helper_Meta::getInstance()->getModelHelper($entry, array(), $params)->toArray($fields);
            } catch (Exception $e) {

            }

        }

        return $return;
    }
}
