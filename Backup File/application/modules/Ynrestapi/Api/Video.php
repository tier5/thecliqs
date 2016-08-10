<?php

/**
 * class Ynrestapi_Api_Video
 */
class Ynrestapi_Api_Video extends Ynrestapi_Api_Base
{
    /**
     * @var array
     */
    protected $availablePrivacies = array(
        'everyone' => 'Everyone',
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me',
    );

    /**
     * @param $params
     */
    public function getEmbed($params)
    {
        self::requireScope('videos');

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        $video = Engine_Api::_()->getItem('video', $params['id']);
        if (!$video || !$video->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Video not found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($video);

        // Check if embedding is allowed
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        } else if (isset($video->allow_embed) && !$video->allow_embed) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Get embed code
        $data = array(
            'code' => $video->getEmbedCode(),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postRate($params)
    {
        self::requireScope('videos');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['video_id'])) {
            self::setParamError('video_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (empty($params['rating'])) {
            self::setParamError('rating', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === filter_var($params['rating'], FILTER_VALIDATE_INT) || $params['rating'] < 1 || $params['rating'] > 5) {
            self::setParamError('rating');
        }

        if (self::isError()) {
            return false;
        }

        $video = Engine_Api::_()->getItem('video', $params['video_id']);

        if (!($video instanceof Core_Model_Item_Abstract) || !$video->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Video not found.'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();
        $rating = $params['rating'];
        $video_id = $params['video_id'];

        if (Engine_Api::_()->video()->checkRated($video_id, $user_id)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You already rated'));
            return false;
        }

        $table = Engine_Api::_()->getDbtable('ratings', 'video');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try
        {
            Engine_Api::_()->video()->setRating($video_id, $user_id, $rating);

            $video = Engine_Api::_()->getItem('video', $video_id);
            $video->rating = Engine_Api::_()->video()->getRating($video->getIdentity());
            $video->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $total = Engine_Api::_()->video()->ratingCount($video->getIdentity());

        $data = array(
            'total' => $total,
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function delete($params)
    {
        self::requireScope('videos');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $video = Engine_Api::_()->getItem('video', $params['id']);

        if (!($video instanceof Core_Model_Item_Abstract) || !$video->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Video doesn\'t exists or not authorized to delete'));
            return false;
        }

        if (!$this->requireAuthIsValid($video, null, 'delete')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $db = $video->getTable()->getAdapter();
        $db->beginTransaction();

        try
        {
            Engine_Api::_()->getApi('core', 'video')->deleteVideo($video);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Video has been deleted.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param $params
     * @param $return
     */
    public function getSupportTypes($params, $return = false)
    {
        self::requireScope('videos');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        //YouTube, Vimeo
        $video_options = array();
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey')) {
            $video_options[1] = 'YouTube';
        }
        $video_options[2] = 'Vimeo';

        //My Computer
        $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'upload');
        $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
        if (!empty($ffmpeg_path) && $allowed_upload) {
            $video_options[3] = 'Upload';
        }

        if ($return) {
            return $video_options;
        }

        $data = array();
        foreach ($video_options as $key => $value) {
            $data[] = array(
                'id' => $key,
                'title' => $value,
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get Comment Options
     *
     * @param $params
     */
    public function getCommentOptions($params, $return = false)
    {
        self::requireScope('videos');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_comment');
        $commentOptions = array_intersect_key($this->availablePrivacies, array_flip($commentOptions));

        if ($return) {
            return $commentOptions;
        }

        $data = array();
        foreach ($commentOptions as $key => $value) {
            $data[] = array(
                'id' => $key,
                'title' => Zend_Registry::get('Zend_Translate')->_($value),
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get View Options
     *
     * @param $params
     */
    public function getViewOptions($params, $return = false)
    {
        self::requireScope('videos');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_view');
        $viewOptions = array_intersect_key($this->availablePrivacies, array_flip($viewOptions));

        if ($return) {
            return $viewOptions;
        }

        $data = array();
        foreach ($viewOptions as $key => $value) {
            $data[] = array(
                'id' => $key,
                'title' => Zend_Registry::get('Zend_Translate')->_($value),
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postItem($params)
    {
        self::requireScope('videos');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $video = Engine_Api::_()->getItem('video', $params['id']);

        if (!($video instanceof Core_Model_Item_Abstract) || !$video->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Video not found'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($video);

        if ($viewer->getIdentity() != $video->owner_id && !$this->requireAuthIsValid($video, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if (!isset($params['title'])) {
            self::setParamError('title', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
        $db->beginTransaction();
        try {
            if (!($values = $this->_getPostItemValues($params))) {
                return false;
            }

            $video->setFromArray($values);
            $video->save();

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if ($values['auth_view']) {
                $auth_view = $values['auth_view'];
            } else {
                $auth_view = 'everyone';
            }

            $viewMax = array_search($auth_view, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
            }

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if ($values['auth_comment']) {
                $auth_comment = $values['auth_comment'];
            } else {
                $auth_comment = 'everyone';
            }

            $commentMax = array_search($auth_comment, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
            }

            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $video->tags()->setTagMaps($viewer, $tags);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($video) as $action) {
                $actionTable->resetActivityBindings($action);
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        self::setSuccess(200);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function post($params)
    {
        if (isset($params['id'])) {
            return $this->postItem($params);
        }

        self::requireScope('videos');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!$this->requireAuthIsValid('video', null, 'create')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        if (!isset($params['title'])) {
            self::setParamError('title', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        // Upload video
        if (isset($_FILES['Filedata']) && !empty($_FILES['Filedata']['name'])) {
            if (!($id = $this->_uploadVideo())) {
                return false;
            }
        }

        // set up data needed to check quota
        $viewer = Engine_Api::_()->user()->getViewer();
        $values['user_id'] = $viewer->getIdentity();
        $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);

        $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
        $current_count = $paginator->getTotalItemCount();
        if (($current_count >= $quota) && !empty($quota)) {
            // return error message
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first.'));
            return false;
        }

        // Process
        if (!($values = $this->_getPostValues($params))) {
            return false;
        }

        $values['owner_id'] = $viewer->getIdentity();

        $insert_action = false;

        $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
        $db->beginTransaction();

        try {
            // Create video
            $table = Engine_Api::_()->getDbtable('videos', 'video');
            if ($values['type'] == 3) {
                if (!isset($id)) {
                    self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('No file'));
                    return false;
                }
                $video = Engine_Api::_()->getItem('video', $id);
            } else {
                $video = $table->createRow();
            }

            $video->setFromArray($values);
            $video->save();

            // Now try to create thumbnail
            $thumbnail = $this->handleThumbnail($video->type, $video->code);
            $ext = ltrim(strrchr($thumbnail, '.'), '.');
            $thumbnail_parsed = @parse_url($thumbnail);

            if (@GetImageSize($thumbnail)) {
                $valid_thumb = true;
            } else {
                $valid_thumb = false;
            }

            if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

                $src_fh = fopen($thumbnail, 'r');
                $tmp_fh = fopen($tmp_file, 'w');
                stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

                $image = Engine_Image::factory();
                $image->open($tmp_file)
                    ->resize(120, 240)
                    ->write($thumb_file)
                    ->destroy();

                try {
                    $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                        'parent_type' => $video->getType(),
                        'parent_id' => $video->getIdentity(),
                    ));

                    // Remove temp file
                    @unlink($thumb_file);
                    @unlink($tmp_file);
                } catch (Exception $e) {

                }
                $information = $this->handleInformation($video->type, $video->code);

                $video->duration = $information['duration'];
                if (!$video->description) {
                    $video->description = $information['description'];
                }
                $video->photo_id = $thumbFileRow->file_id;
                $video->status = 1;
                $video->save();

                // Insert new action item
                $insert_action = true;
            }

            if ($values['ignore'] == true) {
                $video->status = 1;
                $video->save();
                $insert_action = true;
            }

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_()->authorization()->context;
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if (isset($values['auth_view'])) {
                $auth_view = $values['auth_view'];
            } else {
                $auth_view = 'everyone';
            }

            $viewMax = array_search($auth_view, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'view', ($i <= $viewMax));
            }

            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            if (isset($values['auth_comment'])) {
                $auth_comment = $values['auth_comment'];
            } else {
                $auth_comment = 'everyone';
            }

            $commentMax = array_search($auth_comment, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'comment', ($i <= $commentMax));
            }

            // Add tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $video->tags()->addTagMaps($viewer, $tags);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $db->beginTransaction();
        try {
            if ($insert_action) {
                $owner = $video->getOwner();
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $video, 'video_new');
                if ($action != null) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $video);
                }
            }

            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($video) as $action) {
                $actionTable->resetActivityBindings($action);
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'video_id' => $video->getIdentity(),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    private function _getPostValues($params)
    {
        if (!($values = $this->_getPostItemValues($params))) {
            return false;
        }

        $rotationOptions = array(
            0 => '',
            90 => '90°',
            180 => '180°',
            270 => '270°',
        );

        if (isset($params['rotation'])) {
            if (!array_key_exists($params['rotation'], $rotationOptions)) {
                self::setParamError('rotation');
                return false;
            }
            $values['rotation'] = $params['rotation'];
        } else {
            $values['rotation'] = 0;
        }

        $types = array(
            1 => 'youtube',
            2 => 'vimeo',
            3 => 'upload',
        );

        if (!isset($params['type'])) {
            self::setParamError('type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }
        if (false === ($type = array_search($params['type'], $types))) {
            self::setParamError('type');
            return false;
        }
        $supportTypes = $this->getSupportTypes(null, true);
        if (!array_key_exists($type, $supportTypes)) {
            self::setParamError('type');
            return false;
        }
        $values['type'] = $type;

        if (1 == $type || 2 == $type) {
            if (!isset($params['url'])) {
                self::setParamError('url', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
                return false;
            }
            $values['code'] = $this->extractCode($params['url'], $type);
            if (1 == $type) {
                $valid = $this->checkYouTube($values['code']);
            } elseif (2 == $type) {
                $valid = $this->checkVimeo($values['code']);
            }
            if (!$valid) {
                self::setParamError('url', 400, 'invalid_parameter', Zend_Registry::get('Zend_Translate')->_('We could not find a video there - please check the URL and try again.'));
                return false;
            }
            $values['url'] = $params['url'];
        }

        return $values;
    }

    /**
     * @param  $params
     * @return mixed
     */
    private function _getPostItemValues($params)
    {
        $values = array();

        $titleFilter = new Zend_Filter();
        $titleFilter->addFilter(new Zend_Filter_StripTags());
        $titleFilter->addFilter(new Engine_Filter_Censor());
        $titleFilter->addFilter(new Engine_Filter_StringLength(array('max' => '100')));
        $values['title'] = $titleFilter->filter($params['title']);

        $tagsFilter = new Zend_Filter();
        $tagsFilter->addFilter(new Engine_Filter_Censor());
        $tagsFilter->addFilter(new Engine_Filter_HtmlSpecialChars());
        $values['tags'] = $tagsFilter->filter($params['tags']);

        $descriptionFilter = new Zend_Filter();
        $descriptionFilter->addFilter(new Zend_Filter_StripTags());
        $descriptionFilter->addFilter(new Engine_Filter_Censor());
        $descriptionFilter->addFilter(new Engine_Filter_EnableLinks());
        $values['description'] = $descriptionFilter->filter($params['description']);

        if (isset($params['category_id'])) {
            $categoryOptions = $this->getCategories(null, true);
            if (!array_key_exists($params['category_id'], $categoryOptions)) {
                self::setParamError('category_id');
                return false;
            }
            $values['category_id'] = $params['category_id'];
        } else {
            $values['category_id'] = 0;
        }

        if (isset($params['allow_search'])) {
            if ('0' !== strval($params['allow_search']) && '1' !== strval($params['allow_search'])) {
                self::setParamError('allow_search');
                return false;
            }
            $values['search'] = $params['allow_search'];
        } else {
            $values['search'] = 1;
        }

        $values['auth_view'] = isset($params['auth_view']) ? $params['auth_view'] : '';
        $values['auth_comment'] = isset($params['auth_comment']) ? $params['auth_comment'] : '';

        return $values;
    }

    /**
     * @return null
     */
    private function _uploadVideo()
    {
        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $values = array();

        if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
            return false;
        }

        $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
        if (in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions)) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
            return false;
        }

        $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
        $db->beginTransaction();

        try
        {
            $viewer = Engine_Api::_()->user()->getViewer();
            $values['owner_id'] = $viewer->getIdentity();

            $params = array(
                'owner_type' => 'user',
                'owner_id' => $viewer->getIdentity(),
            );
            $video = Engine_Api::_()->video()->createVideo($params, $_FILES['Filedata'], $values);

            // sets up title and owner_id now just incase members switch page as soon as upload is completed
            $video->title = $_FILES['Filedata']['name'];
            $video->owner_id = $viewer->getIdentity();
            $video->save();

            $db->commit();
            return $video->video_id;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * @param  $params
     * @return null
     */
    public function getItem($params)
    {
        self::requireScope('videos');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $video = Engine_Api::_()->getItem('video', $params['id']);

        if (!($video instanceof Core_Model_Item_Abstract) || !$video->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Video not found'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($video);
        $viewer = Engine_Api::_()->user()->getViewer();
        $exportParams = array();

        // if this is sending a message id, the user is being directed from a coversation
        // check if member is part of the conversation
        $message_id = $params['conversation_id'];
        $message_view = false;
        if ($message_id) {
            $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
            if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) {
                $message_view = true;
            }
        }
        $exportParams['message_view'] = $message_view;
        if (!$message_view && !$this->requireAuthIsValid($video, null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $exportParams['videoTags'] = $video->tags()->getTagMaps();

        // Check if edit/delete is allowed
        $exportParams['can_edit'] = $can_edit = $this->requireAuthIsValid($video, null, 'edit');
        $exportParams['can_delete'] = $can_delete = $this->requireAuthIsValid($video, null, 'delete');

        // check if embedding is allowed
        $can_embed = true;
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('video.embeds', 1)) {
            $can_embed = false;
        } else if (isset($video->allow_embed) && !$video->allow_embed) {
            $can_embed = false;
        }
        $exportParams['can_embed'] = $can_embed;

        // increment count
        $embedded = '';
        if ($video->status == 1) {
            if (!$video->isOwner($viewer)) {
                $video->view_count++;
                $video->save();
            }
            $embedded = $video->getRichContent(true);
        }

        if ($video->type == 3 && $video->status == 1) {
            if (!empty($video->file_id)) {
                $storage_file = Engine_Api::_()->getItem('storage_file', $video->file_id);
                if ($storage_file) {
                    $exportParams['video_location'] = $storage_file->map();
                    $exportParams['video_extension'] = $storage_file->extension;
                }
            }
        }

        $exportParams['viewer_id'] = $viewer->getIdentity();
        $exportParams['rating_count'] = Engine_Api::_()->video()->ratingCount($video->getIdentity());
        $exportParams['video'] = $video;
        $exportParams['rated'] = Engine_Api::_()->video()->checkRated($video->getIdentity(), $viewer->getIdentity());
        $exportParams['videoEmbedded'] = $embedded;
        if ($video->category_id) {
            $exportParams['category'] = Engine_Api::_()->video()->getCategory($video->category_id);
        }

        $fields = $this->_getFields($params, 'detail');
        $data = Ynrestapi_Helper_Meta::exportOne($video, $fields, $exportParams);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param $params
     */
    public function getCategories($params, $return = false)
    {
        self::requireScope('videos');

        $categories = Engine_Api::_()->video()->getCategories();
        $categoryOptions = array();
        foreach ($categories as $category) {
            $categoryOptions[$category->category_id] = $category->category_name;
        }

        if ($return) {
            return $categoryOptions;
        }

        $data = array(
            // 0 => array(
            //     'id' => 0,
            //     'title' => Zend_Registry::get('Zend_Translate')->_('All Categories'),
            // ),
        );

        foreach ($categoryOptions as $key => $value) {
            $data[] = array(
                'id' => $key,
                'title' => Zend_Registry::get('Zend_Translate')->_($value),
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return null
     */
    public function getMy($params)
    {
        self::requireScope('videos');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $values = array(
            'text' => '',
            'orderby' => 'creation_date',
            'category' => 0,
        );

        if (isset($params['keywords'])) {
            $values['text'] = $params['keywords'];
        }

        $orderBy = array(
            'creation_date' => 'Most Recent',
            'view_count' => 'Most Viewed',
            'rating' => 'Highest Rated',
        );

        if (isset($params['sort'])) {
            if (!array_key_exists($params['sort'], $orderBy)) {
                self::setParamError('sort');
                return false;
            } else {
                $values['orderby'] = $params['sort'];
            }
        }

        if (isset($params['category_id'])) {
            $categoryTable = Engine_Api::_()->getDbTable('categories', 'video');
            if (!($category = $categoryTable->fetchRow($categoryTable->select()->where('category_id = ?', $params['category_id'])))) {
                self::setParamError('category_id');
                return false;
            }
            $values['category'] = $params['category_id'];
        }

        $values['user_id'] = $viewer->getIdentity();

        $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);

        $items_count = isset($params['limit']) ? (int) $params['limit'] : Engine_Api::_()->getApi('settings', 'core')->getSetting('video.page', 10);
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($page);

        $fields = $this->_getFields($params, 'manage');

        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param $params
     */
    public function get($params)
    {
        if (isset($params['id'])) {
            return $this->getItem($params);
        }

        self::requireScope('videos');

        // Prepare
        $viewer = Engine_Api::_()->user()->getViewer();

        $values = array(
            'text' => '',
            'orderby' => 'creation_date',
            'category' => 0,
            // 'tag' => ''
        );

        if (isset($params['keywords'])) {
            $values['text'] = $params['keywords'];
        }

        $orderBy = array(
            'creation_date' => 'Most Recent',
            'view_count' => 'Most Viewed',
            'rating' => 'Highest Rated',
        );

        if (!empty($params['sort'])) {
            if (!array_key_exists($params['sort'], $orderBy)) {
                self::setParamError('sort');
                return false;
            } else {
                $values['orderby'] = $params['sort'];
            }
        }

        if (!empty($params['category_id'])) {
            $categoryTable = Engine_Api::_()->getDbTable('categories', 'video');
            if (!($category = $categoryTable->fetchRow($categoryTable->select()->where('category_id = ?', $params['category_id'])))) {
                self::setParamError('category_id');
                return false;
            }
            $values['category'] = $params['category_id'];
        }

        $values['status'] = 1;
        $values['search'] = 1;

        // check to see if request is for specific user's listings
        if (isset($params['user_id'])) {
            $values['user_id'] = $user_id;
        }

        // Get videos
        $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);
        $items_count = isset($params['limit']) ? (int) $params['limit'] : Engine_Api::_()->getApi('settings', 'core')->getSetting('video.page', 12);
        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $paginator->setItemCountPerPage($items_count);
        $paginator->setCurrentPageNumber($page);

        $fields = $this->_getFields($params, 'listing');

        $data = Ynrestapi_Helper_Meta::exportAll($paginator, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Add a video for attach
     *
     * @param  $params
     * @return mixed
     */
    public function postComposeUpload($params)
    {
        self::requireScope('videos');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Validate params
        if (empty($params['uri'])) {
            self::setParamError('uri', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (empty($params['type'])) {
            self::setParamError('type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (false === ($video_type = $this->_getTypeId($params['type']))) {
            self::setParamError('type', Zend_Registry::get('Zend_Translate')->_('Invalid video type'), 400);
        }

        if (self::isError()) {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $video_title = $params['title'];
        $video_url = $params['uri'];
        $composer_type = !empty($params['c_type']) ? $params['c_type'] : 'wall';

        // extract code
        //$code = $this->extractCode("http://www.youtube.com/watch?v=5osJ8-NttnU&feature=popt00us08", $video_type);
        //$code = parse_url("http://vimeo.com/3945157/asd243", PHP_URL_PATH);

        $code = $this->extractCode($video_url, $video_type);
        // check if code is valid
        // check which API should be used
        if ($video_type == 1) {
            $valid = $this->checkYouTube($code);
        }
        if ($video_type == 2) {
            $valid = $this->checkVimeo($code);
        }

        // check to make sure the user has not met their quota of # of allowed video uploads
        // set up data needed to check quota
        $values['user_id'] = $viewer->getIdentity();
        $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);
        $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
        $current_count = $paginator->getTotalItemCount();

        if (($current_count >= $quota) && !empty($quota)) {
            // return error message
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first.'));
            return false;
        } else if ($valid) {
            $db = Engine_Api::_()->getDbtable('videos', 'video')->getAdapter();
            $db->beginTransaction();

            try
            {
                $information = $this->handleInformation($video_type, $code);

                // create video
                $table = Engine_Api::_()->getDbtable('videos', 'video');
                $video = $table->createRow();
                $video->title = $information['title'];
                $video->description = $information['description'];
                $video->duration = $information['duration'];
                $video->owner_id = $viewer->getIdentity();
                $video->code = $code;
                $video->type = $video_type;
                $video->save();

                // Now try to create thumbnail
                $thumbnail = $this->handleThumbnail($video->type, $video->code);
                $ext = ltrim(strrchr($thumbnail, '.'), '.');
                $thumbnail_parsed = @parse_url($thumbnail);

                $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
                $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

                $src_fh = fopen($thumbnail, 'r');
                $tmp_fh = fopen($tmp_file, 'w');
                stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

                $image = Engine_Image::factory();
                $image->open($tmp_file)
                    ->resize(120, 240)
                    ->write($thumb_file)
                    ->destroy();

                $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                    'parent_type' => $video->getType(),
                    'parent_id' => $video->getIdentity(),
                ));

                // If video is from the composer, keep it hidden until the post is complete
                if ($composer_type) {
                    $video->search = 0;
                }

                $video->photo_id = $thumbFileRow->file_id;
                $video->status = 1;
                $video->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            // make the video public
            if ($composer_type === 'wall') {
                // CREATE AUTH STUFF HERE
                $auth = Engine_Api::_()->authorization()->context;
                $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
                foreach ($roles as $i => $role) {
                    $auth->setAllowed($video, $role, 'view', ($i <= $roles));
                    $auth->setAllowed($video, $role, 'comment', ($i <= $roles));
                }
            }

            $data = array(
                'message' => Zend_Registry::get('Zend_Translate')->_('Video posted successfully'),
                'video_id' => $video->video_id,
                'photo_id' => $video->photo_id,
                'title' => $video->title,
                'description' => $video->description,
                'src' => Ynrestapi_Helper_Utils::prepareUrl($video->getPhotoUrl()),
            );

            self::setSuccess(200, $data);
            return true;
        } else {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('We could not find a video there - please check the URL and try again.'));
            return false;
        }
    }

    /**
     * @param  $url
     * @param  $type
     * @return mixed
     */
    public function extractCode($url, $type)
    {
        switch ($type) {
            //youtube
            case '1':
                // change new youtube URL to old one
                $new_code = @pathinfo($url);
                $url = preg_replace('/#!/', '?', $url);

                // get v variable from the url
                $arr = array();
                $arr = @parse_url($url);
                if ($arr['host'] === 'youtu.be') {
                    $data = explode('?', $new_code['basename']);
                    $code = $data[0];
                } else {
                    $parameters = $arr['query'];
                    parse_str($parameters, $data);
                    $code = $data['v'];
                    if ($code == '') {
                        $code = $new_code['basename'];
                    }
                }
                return $code;
            //vimeo
            case '2':
                // get the first variable after slash
                $code = @pathinfo($url);
                return $code['basename'];
        }
    }

    /**
     * YouTube Functions
     *
     * @param $code
     */
    public function checkYouTube($code)
    {
        $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
        if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key)) {
            return false;
        }

        $data = Zend_Json::decode($data);
        if (empty($data['items'])) {
            return false;
        }

        return true;
    }

    /**
     * Vimeo Functions
     *
     * @param $code
     */
    public function checkVimeo($code)
    {
        //http://www.vimeo.com/api/docs/simple-api
        //http://vimeo.com/api/v2/video
        $data = @simplexml_load_file('http://vimeo.com/api/v2/video/' . $code . '.xml');
        $id = count($data->video->id);
        if ($id == 0) {
            return false;
        }

        return true;
    }

    /**
     * handles thumbnails
     *
     * @param  $type
     * @param  $code
     * @return mixed
     */
    public function handleThumbnail($type, $code = null)
    {
        switch ($type) {
            //youtube
            case '1':
                //https://i.ytimg.com/vi/Y75eFjjgAEc/default.jpg
                return "https://i.ytimg.com/vi/$code/default.jpg";
            //vimeo
            case '2':
                //thumbnail_medium
                $data = simplexml_load_file('http://vimeo.com/api/v2/video/' . $code . '.xml');
                $thumbnail = $data->video->thumbnail_medium;
                return $thumbnail;
        }
    }

    /**
     * @param  $type
     * @param  $code
     * @return mixed
     */
    public function handleInformation($type, $code)
    {
        switch ($type) {
            //youtube
            case '1':
                $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
                $data = file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' . $code . '&key=' . $key);
                if (empty($data)) {
                    return;
                }
                $data = Zend_Json::decode($data);
                $information = array();
                $youtube_video = $data['items'][0];
                $information['title'] = $youtube_video['snippet']['title'];
                $information['description'] = $youtube_video['snippet']['description'];
                $information['duration'] = Engine_Date::convertISO8601IntoSeconds($youtube_video['contentDetails']['duration']);
                return $information;
            //vimeo
            case '2':
                //thumbnail_medium
                $data = simplexml_load_file('http://vimeo.com/api/v2/video/' . $code . '.xml');
                $thumbnail = $data->video->thumbnail_medium;
                $information = array();
                $information['title'] = $data->video->title;
                $information['description'] = $data->video->description;
                $information['duration'] = $data->video->duration;
                //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
                return $information;
        }
    }

    /**
     * @param $typeLabel
     */
    private function _getTypeId($typeLabel)
    {
        $types = array(
            1 => 'youtube',
            2 => 'vimeo',
        );

        return array_search(strtolower($typeLabel), $types);
    }
}
