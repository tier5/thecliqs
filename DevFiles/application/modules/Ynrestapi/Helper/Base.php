<?php

class Ynrestapi_Helper_Base
{
    /**
     * @var mixed
     */
    protected static $viewer;

    /**
     * @var mixed
     */
    protected $view;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var mixed
     */
    protected $options;

    /**
     * @var mixed
     */
    protected $params;

    /**
     * Constructor
     *
     * @param $entry
     * @param $options
     * @param $params
     */
    public function __construct($entry, $options, $params)
    {
        $this->entry = $entry;
        $this->options = $options;
        $this->params = $params;

        $this->view = Zend_Registry::get('Zend_View');
    }

    protected function itemPhoto($item, $type = 'thumb.profile')
    {
        return Ynrestapi_Helper_Utils::prepareUrl(Ynrestapi_Helper_ItemPhoto::getInstance()->itemPhoto($item, $type));
    }

    /**
     * @param $key
     */
    public function getParam($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * @return Ynrestapi_Api_Base
     */
    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('base', 'ynrestapi');
    }

    /**
     * @param  $sType
     * @param  $iId
     * @return mixed
     */
    public function getWorkingItem($sType, $iId)
    {
        return $this->getYnrestapiApi()->getWorkingItem($sType, $iId);
    }

    /**
     * @param  $sType
     * @return mixed
     */
    public function getWorkingType($sType)
    {
        return $this->getYnrestapiApi()->getWorkingType($sType);
    }

    /**
     * @param  $item_type
     * @return mixed
     */
    public function getWorkingItemTable($item_type)
    {
        return $this->getYnrestapiApi()->getWorkingItemTable($item_type);
    }

    /**
     * @param  $api
     * @param  $module
     * @return mixed
     */
    public function getWorkingApi($api, $module = null)
    {
        return $this->getYnrestapiApi()->getWorkingApi($api, $module);
    }

    /**
     * @param  $module
     * @return mixed
     */
    public function getWorkingModule($module = null)
    {
        return $this->getYnrestapiApi()->getWorkingModule($module);
    }

    /**
     * @param  $table
     * @param  $module
     * @return mixed
     */
    public function getWorkingTable($table, $module = null)
    {
        return $this->getYnrestapiApi()->getWorkingTable($table, $module);
    }

    public static function viewer()
    {
        return self::getViewer();
    }

    public static function getViewer()
    {
        if (null == self::$viewer) {
            self::$viewer = Engine_Api::_()->user()->getViewer();
        }
        return self::$viewer;
    }

    /**
     * @return mixed
     */
    public static function getViewerId()
    {
        $viewer = self::getViewer();
        $id = 0;
        if ($viewer) {
            $id = $viewer->getIdentity();
        }
        return $id;
    }

    /**
     * @param $viewer
     */
    public static function setViewer($viewer)
    {
        self::$viewer = $viewer;
    }

    /**
     * @param  array   $fields
     * @return mixed
     */
    public function toArray($fields = array('id', 'title'))
    {
        $this->data = array();
        foreach ($fields as $field) {
            if (method_exists($this, $method = 'field_' . $field)) {
                $this->$method();
            }
        }
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function toSimple()
    {
        return $this->toArray(array('simple'));
    }

    public function getExtra()
    {
        return array();
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->getIdentity();
    }

    public function field_type()
    {
        $this->data['type'] = $this->entry->getType();
    }

    public function field_title()
    {
        $this->data['title'] = $this->entry->getTitle();
    }

    public function field_description()
    {
        $this->data['description'] = $this->entry->getDescription();
    }

    public function field_timestamp()
    {
        if (isset($this->entry->creation_date)) {
            $this->data['timestamp'] = strtotime($this->entry->creation_date);
        } else if ($this->entry->date) {
            $this->data['timestamp'] = strtotime($this->entry->date);
        }
    }

    public function field_creation_date()
    {
        $this->data['creation_date'] = $this->entry->creation_date;
    }

    /**
     * @param $type
     */
    public function getNoImg($type)
    {
        if (@$this->options['no_imgs'][$type]) {
            return Ynrestapi_Helper_Utils::prepareUrl($this->options['no_imgs'][$type]);
        }
    }

    /**
     * @param $type
     */
    public function getImgUrl($type = '')
    {
        $url = $this->entry->getPhotoUrl($type);
        return ($url ? Ynrestapi_Helper_Utils::prepareUrl($url) : $this->getNoImg($type));
    }

    /**
     * @param $return
     * @return mixed
     */
    public function field_img_icon($return = false)
    {
        $img = $this->getImgUrl('thumb.icon');
        if ($return) {
            return $img;
        }
        $this->data['img_icon'] = $img;
    }

    /**
     * @param $return
     * @return mixed
     */
    public function field_img_normal($return = false)
    {
        $img = $this->getImgUrl('thumb.normal');
        if ($return) {
            return $img;
        }
        $this->data['img_normal'] = $img;
    }

    /**
     * @param $return
     * @return mixed
     */
    public function field_img_profile($return = false)
    {
        $img = $this->getImgUrl('thumb.profile');
        if ($return) {
            return $img;
        }
        $this->data['img_profile'] = $img;
    }

    /**
     * @param $return
     * @return mixed
     */
    public function field_img($return = false)
    {
        $img = $this->getImgUrl();
        if ($return) {
            return $img;
        }
        $this->data['img'] = $img;
    }

    public function field_imgs()
    {
        $this->data['imgs'] = array(
            'icon' => $this->field_img_icon(true),
            'normal' => $this->field_img_normal(true),
            'profile' => $this->field_img_profile(true),
            'original' => $this->field_img(true),
        );
    }

    public function field_total_like()
    {
        if (method_exists($this->entry, 'likes')) {
            $this->data['total_like'] = intval($this->entry->likes()->getLikeCount());
        }
    }

    public function field_is_liked()
    {
        if (method_exists($this->entry, 'likes')) {
            $this->data['is_liked'] = $this->entry->likes()->isLike($this->getViewer()) ? true : false;
        }
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_can_like($return = false)
    {
        $can_like = false;
        if (!isset($this->data['can_comment'])) {
            $bCanComment = (Engine_Api::_()->authorization()->isAllowed($this->entry, null, 'comment')) ? true : false;
            $can_like = $bCanComment;
        }

        if ($return) {
            return $can_like;
        }

        $this->data['can_like'] = $can_like;
    }

    public function field_is_disliked()
    {
        $this->data['is_disliked'] = 0;
        $viewer = self::getViewer();
        if (Engine_Api::_()->hasModuleBootstrap('yncomment')) {
            $this->data['is_disliked'] = (Engine_Api::_()->getDbtable('dislikes', 'yncomment')->isDislike($this->entry, $viewer)) ? true : false;
        }
    }

    public function field_total_dislike()
    {
        $this->data['total_dislike'] = 0;
        if (Engine_Api::_()->hasModuleBootstrap('yncomment')) {
            $this->data['total_dislike'] = Engine_Api::_()->getDbtable('dislikes', 'yncomment')->getDislikeCount($this->entry);
        }
    }

    public function field_user_disliked()
    {
        $this->data['user_disliked'] = Engine_Api::_()->getApi('like', 'ynrestapi')->getUserDislike($this->entry);
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_can_dislike($return = false)
    {
        $action = $this->entry;
        $itemType = $action->getType();
        $advancedCommentOptions = $this->getYnrestapiApi()->getAdvancedCommentOptions($itemType);
        $canComment = (Engine_Api::_()->authorization()->isAllowed($this->entry, null, 'comment')) ? true : false;
        $comment_options = $this->getYnrestapiApi()->getCommentOptions($this->entry->getType());
        $can_dislike = ($comment_options['can_view_comments'] && $canComment && $advancedCommentOptions['can_dislike']) ? true : false;

        if ($return) {
            return $can_dislike;
        }

        $this->data['can_dislike'] = $can_dislike;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_can_view_user_disliked($return = false)
    {
        $itemType = $this->entry->getType();
        $advancedCommentOptions = $this->getYnrestapiApi()->getAdvancedCommentOptions($itemType);
        $can_view_user_disliked = ($advancedCommentOptions['is_enabled'] && $advancedCommentOptions['can_view_user_disliked']) ? true : false;

        if ($return) {
            return $can_view_user_disliked;
        }

        $this->data['can_view_user_disliked'] = $can_view_user_disliked;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_can_comment($return = false)
    {
        $can_comment = false;
        if (!isset($this->data['can_comment'])) {
            $bCanComment = (Engine_Api::_()->authorization()->isAllowed($this->entry, null, 'comment')) ? true : false;
            $can_comment = $bCanComment;
        }

        if ($return) {
            return $can_comment;
        }

        $this->data['can_comment'] = $can_comment;
    }

    public function field_total_comment()
    {
        if (method_exists($this->entry, 'comments')) {
            $comments = $this->entry->comments();
            $table = $comments->getReceiver();
            $select = $comments->getCommentSelect();
            //get all comment if advanced comment is not enabled
            $advancedCommentOptions = $this->getYnrestapiApi()->getAdvancedCommentOptions($this->entry->getType());
            if ($advancedCommentOptions['is_enabled']) {
                $select->where('parent_comment_id = ?', 0);
            }
            $allComments = $table->fetchAll($select);
            $this->data['total_comment'] = count($allComments);
        }
    }

    /**
     *
     */
    public function field_advanced_comment_options()
    {
        $action = $this->entry;
        $actionType = $action->getType();
        // return nothing for comment item
        if (in_array($actionType, array('activity_comment', 'core_comment'))) {
            return false;
        } else {
            $itemType = $actionType;
            $this->data['advanced_comment_options'] = $this->getYnrestapiApi()->getAdvancedCommentOptions($itemType);
        }
        $viewer = self::getViewer();
        // check for yn comment module and if setting is on
    }

    public function field_total_view()
    {
        if (isset($this->entry->view_count)) {
            $this->data['total_view'] = intval($this->entry->view_count);
        } else {
            $this->data['total_view'] = 0;
        }
    }

    public function field_href()
    {
        $this->data['href'] = Ynrestapi_Helper_Utils::prepareUrl($this->entry->getHref());
    }

    /**
     * @return null
     */
    public function field_rating()
    {
        if (!isset($this->entry->rating)) {
            return;
        }

        $this->data['rating'] = $this->entry->rating;
    }

    /**
     * @return null
     */
    public function field_total_rating()
    {
        if (!isset($this->entry->rating)) {
            return;
        }

        $id = $this->entry->getIdentity();
        $coreApi = $this->getWorkingApi('core', $this->module);
        if (!method_exists($coreApi, 'ratingCount')) {
            return;
        }

        $ratingCount = $coreApi->ratingCount($id);
        $this->data['total_rating'] = $ratingCount;
    }

    /**
     * @return null
     */
    public function field_is_rated()
    {
        if (!isset($this->entry->rating)) {
            return;
        }

        $id = $this->entry->getIdentity();
        $iViewerId = $this->getViewerId();
        $coreApi = $this->getWorkingApi('core', $this->module);
        if (!method_exists($coreApi, 'checkRated')) {
            return;
        }

        $rated = 0;
        if ($iViewerId) {
            $rated = $coreApi->checkRated($id, $iViewerId) ? true : false;
        }
        $this->data['is_rated'] = $rated;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_can_view($return = false)
    {
        $can_view = $this->entry->authorization()->isAllowed($this->getViewer(), 'view') ? true : false;

        if ($return) {
            return $can_view;
        }

        $this->data['can_view'] = $can_view;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_can_edit($return = false)
    {
        $can_edit = (Engine_Api::_()->authorization()->isAllowed($this->entry, null, 'edit')) ? true : false;

        if ($return) {
            return $can_edit;
        }

        $this->data['can_edit'] = $can_edit;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_can_delete($return = false)
    {
        $can_delete = (Engine_Api::_()->authorization()->isAllowed($this->entry, null, 'edit')) ? true : false;

        if ($return) {
            return $can_delete;
        }

        $this->data['can_delete'] = $can_delete;
    }

    public function field_total_photo()
    {
        $table = $this->getWorkingTable('photos', 'album');
        $this->data['total_photo'] = (int) $table->select()
            ->from($table, new Zend_Db_Expr('COUNT(photo_id)'))
            ->where('owner_type = ?', $this->entry->getType())
            ->where('owner_id = ?', $this->entry->getIdentity())
            ->where('album_id > 0')
            ->limit(1)
            ->query()
            ->fetchColumn();
    }

    /**
     * @return null
     */
    public function field_photos()
    {
        $limit = defined('LIMIT_FIELD_PHOTOS') ? LIMIT_FIELD_PHOTOS : 3;
        $engine = Engine_Api::_();
        $table = $this->getWorkingTable('photos', 'album');
        if (!$table) {
            $this->data['photos'] = array();
            return;
        }
        $select = $table->select()
            ->where('owner_type = ?', $this->entry->getType())
            ->where('owner_id = ?', $this->entry->getIdentity())
            ->where('album_id > 0');
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($limit);
        $items = array();
        $appMeta = Ynrestapi_Helper_Meta::getInstance();
        $fields = array('simple');
        foreach ($paginator as $item) {
            $items[] = $appMeta->getModelHelper($item)->toArray($fields);
        }
        $this->data['photos'] = $items;
    }

    /**
     * @return null
     */
    public function field_total_event()
    {
        $table = $this->getWorkingTable('events', 'event');
        if (!$table) {
            $this->data['total_event'] = 0;
            return;
        }
        $select = $table->select()
            ->where('parent_type = ?', $this->entry->getType())
            ->where('parent_id = ?', $this->entry->getIdentity());
        $paginator = Zend_Paginator::factory($select);
        $total = $paginator->getTotalItemCount();
        $this->data['total_event'] = $total;
    }

    /**
     * @return null
     */
    public function field_events()
    {
        $limit = defined('LIMIT_FIELD_EVENTS') ? LIMIT_FIELD_EVENTS : 3;
        $engine = Engine_Api::_();
        $table = $this->getWorkingTable('events', 'event');
        if (!$table) {
            $this->data['events'] = array();
            return;
        }
        $select = $table->select()
            ->where('parent_type = ?', $this->entry->getType())
            ->where('parent_id = ?', $this->entry->getIdentity());
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($limit);
        $items = array();
        $appMeta = Ynrestapi_Helper_Meta::getInstance();
        $fields = array('id', 'title', 'img_icon', 'type');
        foreach ($paginator as $item) {
            $items[] = $appMeta->getModelHelper($item)->toArray($fields);
        }
        $this->data['events'] = $items;
    }

    /**
     * @return null
     */
    public function field_total_album()
    {
        $table = $this->getWorkingTable('albums', 'album');
        if (!$table) {
            $this->data['total_album'] = 0;
            return;
        }
        $select = $table->select()
            ->where('owner_type = ?', $this->entry->getType())
            ->where('owner_id = ?', $this->entry->getIdentity());
        $paginator = Zend_Paginator::factory($select);
        $total = $paginator->getTotalItemCount();
        $this->data['total_album'] = $total;
    }

    /**
     * @return null
     */
    public function field_albums()
    {
        $limit = defined('LIMIT_FIELD_ALBUMS') ? LIMIT_FIELD_ALBUMS : 3;
        // Get paginator
        $engine = Engine_Api::_();
        $table = $this->getWorkingTable('albums', 'album');
        if (!$table) {
            $this->data['albums'] = array();
            return;
        }
        $select = $table->select()
            ->where('owner_type = ?', $this->entry->getType())
            ->where('owner_id = ?', $this->entry->getIdentity());
        $paginator = Zend_Paginator::factory($select);
        // Set item count per page and current page number
        $paginator->setItemCountPerPage($iLimit);
        $paginator->setCurrentPageNumber(1);
        $items = array();
        $appMeta = Ynrestapi_Helper_Meta::getInstance();
        $fields = array('simple');
        foreach ($paginator as $item) {
            $items[] = $appMeta->getModelHelper($item)->toArray($fields);
        }
        $this->data['albums'] = $items;
    }

    /**
     * @return null
     */
    public function field_total_blog()
    {
        $table = $this->getWorkingTable('posts', 'blog');
        if (!$table) {
            $this->data['total_blog'] = 0;
            return;
        }
        $select = $table->select()
            ->where('owner_type = ?', $this->entry->getType())
            ->where('owner_id = ?', $this->entry->getIdentity())
            ->where('is_approved = 1');
        $paginator = Zend_Paginator::factory($select);
        $total = $paginator->getTotalItemCount();
        $this->data['total_blog'] = $total;
    }

    /**
     * @return null
     */
    public function field_blogs()
    {
        $limit = defined('LIMIT_FIELD_BLOGS') ? LIMIT_FIELD_BLOGS : 3;
        $engine = Engine_Api::_();
        $table = $this->getWorkingTable('posts', 'blog');
        if (!$table) {
            $this->data['blogs'] = array();
            return;
        }
        $select = $table->select()
            ->where('owner_type = ?', $this->entry->getType())
            ->where('owner_id = ?', $this->entry->getIdentity())
            ->where('is_approved = 1');
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($limit);
        $items = array();
        $appMeta = Ynrestapi_Helper_Meta::getInstance();
        $fields = array('simple');
        foreach ($paginator as $item) {
            $items[] = $appMeta->getModelHelper($item)->toArray($fields);
        }
        $this->data['blogs'] = $items;
    }

    public function field_owner($return = false)
    {
        $owner = $this->entry->getOwner();
        $data = Ynrestapi_Helper_Meta::getInstance()->getModelHelper($owner)->toArray(array('simple'));
        if ($return) {
            return $data;
        }
        $this->data['owner'] = $data;
    }

    /**
     * @return mixed
     */
    public function field_parent()
    {
        $object = $this->entry->getParent();
        if (!$object) {
            return $this->data['parent'] = array();
        }
        return $this->data['parent'] = Ynrestapi_Helper_Meta::getInstance()->getModelHelper($object)->toSimple();
    }

    /**
     * @return mixed
     */
    public function field_object()
    {
        $object = $this->entry->getObject();
        if (!$object) {
            return $this->data['object'] = array();
        }
        return $this->data['object'] = Ynrestapi_Helper_Meta::getInstance()->getModelHelper($object)->toSimple();
    }

    /**
     * @return mixed
     */
    public function field_subject()
    {
        $subject = $this->entry->getSubject();
        if (!$subject) {
            return $this->data['subject'] = array();
        }
        return $this->data['subject'] = Ynrestapi_Helper_Meta::getInstance()->getModelHelper($subject)->toSimple();
    }

    public function field_tags()
    {
        $tags = $this->entry->tags()->getTagMaps();
        $items = array();
        foreach ($tags as $tag) {
            $items[] = $tag->getTag()->text;
        }
        $this->data['tags'] = $items;
    }

    /**
     * for edit data
     */
    public function field_model()
    {
        if ($this->entry) {
            $model = $this->entry->toArray();
            $model['id'] = $this->entry->getIdentity();
        }
        $this->data['model'] = $model;
    }

    public function field_view_options()
    {
        $this->data['view_options'] = $this->getYnrestapiApi()->view_options();
    }

    public function field_auth()
    {
        $classified = $this->entry;
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array(
            'owner',
            'owner_member',
            'owner_member_member',
            'owner_network',
            'registered',
            'everyone',
        );
        foreach ($roles as $role) {
            if (1 === $auth->isAllowed($classified, $role, 'view')) {
                $sViewPrivacy = $role;
            }
            if (1 === $auth->isAllowed($classified, $role, 'comment')) {
                $sCommentPrivacy = $role;
            }
        }
        $this->data['auth']['view'] = $sViewPrivacy;
        $this->data['auth']['comment'] = $sCommentPrivacy;
    }

    /**
     * @predecated
     */
    public function field_auth2()
    {
        $auth = Engine_Api::_()->authorization()->context;
        $view = 'everyone';
        $comment = 'everyone';
        $roles = array(
            'owner',
            'officer',
            'member',
            'registered',
            'everyone',
        );
        foreach ($roles as $role) {
            if (1 === $auth->isAllowed($this->entry, $role, 'view')) {
                $view = $role;
            }
            if (1 === $auth->isAllowed($this->entry, $role, 'comment')) {
                $comment = $role;
            }
        }
        $this->data['auth']['view'] = $view;
        $this->data['auth']['comment'] = $comment;
    }

    public function field_comment_options()
    {
        $this->data['comment_options'] = $this->getYnrestapiApi()->comment_options();
    }

    public function field_total_member()
    {
        $total = 0;
        if (isset($this->entry->member_count)) {
            $total = $this->entry->member_count;
        }
        $this->data['total_member'] = $total;
    }

    public function field_members()
    {
        $total = 0;
        $limit = 20;
        $index = 0;
        $members = array();
        if (isset($this->entry->member_count)) {
            $total = $this->entry->member_count;
        }
        $appMeta = Ynrestapi_Helper_Meta::getInstance();
        if ($total) {
            $members = $this->entry->membership()->getMembers();
            foreach ($members as $member) {
                if ($index >= $limit) {
                    break;
                }
                ++$index;
                $members[] = $appMeta->getModelHelper($member)->toArray($fields = array('simple'));
            }
        }
        $this->data['members'] = $members;
    }

    public function field_category_id()
    {
        if (isset($this->entry->category_id)) {
            $this->data['category_id'] = $this->entry->category_id;
        }
    }

    public function field_category()
    {
        if (isset($this->entry->category_id)) {
            $this->data['category'] = $this->getYnrestapiApi()->getCategoryName($this->entry->category_id);
        }
    }

    public function field_category_options()
    {
        $this->data['category_options'] = $this->getYnrestapiApi()->categories();
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_can_share($return = false)
    {
        $can_share = true;

        if ($return) {
            return $can_share;
        }

        $this->data['can_share'] = $can_share;
    }

    /**
     * Permissions of viewer on entry
     */
    public function field_permissions()
    {
        $this->field_can_comment();
        $this->field_can_delete();
        $this->field_can_dislike();
        $this->field_can_edit();
        $this->field_can_like();
        $this->field_can_share();
        $this->field_can_view();
        $this->field_can_view_user_disliked();
    }

    public function field_stats()
    {
        $this->field_is_disliked();
        $this->field_is_liked();
        $this->field_total_comment();
        $this->field_total_dislike();
        $this->field_total_like();
        $this->field_total_view();
    }

    public function field_as_attachment()
    {
        $this->data['id'] = $this->entry->getIdentity();
        $this->field_description();
        $this->field_href();
        $this->field_id();
        $this->field_img();
        $this->field_img_normal();
        $this->field_title();
        $this->field_type();
    }

    public function field_simple()
    {
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_imgs();
    }

    /**
     * @param  $method_name
     * @param  $args
     * @return mixed
     */
    public function __call($method_name, $args)
    {
        if (method_exists($this->entry, $method_name)) {
            return $this->entry->{$method_name}();
        }
    }
}
