<?php

/**
 * class Ynrestapi_Api_Activity
 */
class Ynrestapi_Api_Activity extends Ynrestapi_Api_Base
{
    /**
     * Constuctor
     */
    public function __construct()
    {
        $this->module = 'activity';
        $this->mainItemType = 'activity_action';
    }

    /**
     * Get Update
     *
     * @param  $params
     * @return mixed
     */
    public function getUpdates($params)
    {
        self::requireScope('activities');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $notificationCount = (int) Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);

        $data = array(
            'notification_count' => $notificationCount,
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Share an item by re-posting it with a message.
     *
     * @param  $params
     * @return mixed
     */
    public function postShare($params)
    {
        self::requireScope('activities');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['item_type'])) {
            self::setParamError('item_type', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (!isset($params['item_id'])) {
            self::setParamError('item_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        if (self::isError()) {
            return false;
        }

        $type = $params['item_type'];
        $id = $params['item_id'];

        if ($type == 'activity_action') {
            if (!($action = Engine_Api::_()->getItem($type, $id))) {
                self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.'));
                return false;
            }

            $canShareAction = false;
            if ($action->getTypeInfo()->shareable) {
                if ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment())) {
                    $type = $attachment->item->getType();
                    $id = $attachment->item->getIdentity();
                    $canShareAction = true;
                } elseif ($action->getTypeInfo()->shareable == 2) {
                    $type = $action->subject_type;
                    $id = $action->subject_id;
                    $canShareAction = true;
                } elseif ($action->getTypeInfo()->shareable == 3) {
                    $type = $action->object_type;
                    $id = $action->object_id;
                    $canShareAction = true;
                } elseif ($action->getTypeInfo()->shareable == 4) {
                    $type = $action->getType();
                    $id = $action->getIdentity();
                    $canShareAction = true;
                }
            }

            if (!$canShareAction) {
                self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
                return false;
            }
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $attachment = Engine_Api::_()->getItem($type, $id);

        if (!$attachment) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.'));
            return false;
        }

        // hide facebook and twitter option if not logged in
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        if (!$facebookTable->isConnected()) {
            $params['post_to_facebook'] = false;
        }

        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        if (!$twitterTable->isConnected()) {
            $params['post_to_twitter'] = false;
        }

        // Process

        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            $valueBody = isset($params['body']) ? $params['body'] : '';
            // Apply filters
            $filters = array(
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            );
            foreach ($filters as $filter) {
                $valueBody = $filter->filter($valueBody);
            }
            // Get body
            $body = $valueBody;
            // Set Params for Attachment
            $activityParams = array(
                'type' => '<a href="' . $attachment->getHref() . '">' . $attachment->getMediaType() . '</a>',
            );

            // Add activity
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            //$action = $api->addActivity($viewer, $viewer, 'post_self', $body);
            $action = $api->addActivity($viewer, $attachment->getOwner(), 'share', $body, $activityParams);
            if ($action) {
                $api->attachActivity($action, $attachment);
            }
            $db->commit();

            // Notifications
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            // Add notification for owner of activity (if user and not viewer)
            if ($action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity()) {
                $notifyApi->addNotification($attachment->getOwner(), $viewer, $action, 'shared', array(
                    'label' => $attachment->getMediaType(),
                ));
            }

            // Preprocess attachment parameters
            $publishMessage = html_entity_decode($valueBody);
            $publishUrl = null;
            $publishName = null;
            $publishDesc = null;
            $publishPicUrl = null;
            // Add attachment
            if ($attachment) {
                $publishUrl = $attachment->getHref();
                $publishName = $attachment->getTitle();
                $publishDesc = $attachment->getDescription();
                if (empty($publishName)) {
                    $publishName = ucwords($attachment->getShortType());
                }
                if (($tmpPicUrl = $attachment->getPhotoUrl())) {
                    $publishPicUrl = $tmpPicUrl;
                }
                // prevents OAuthException: (#100) FBCDN image is not allowed in stream
                if ($publishPicUrl &&
                    preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
                    $publishPicUrl = null;
                }
            } else {
                $publishUrl = $action->getHref();
            }
            // Check to ensure proto/host
            if ($publishUrl &&
                false === stripos($publishUrl, 'http://') &&
                false === stripos($publishUrl, 'https://')) {
                $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
            }
            if ($publishPicUrl &&
                false === stripos($publishPicUrl, 'http://') &&
                false === stripos($publishPicUrl, 'https://')) {
                $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
            }
            // Add site title
            if ($publishName) {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                    . ': ' . $publishName;
            } else {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
            }

            // Publish to facebook, if checked & enabled
            if ($this->_getParam('post_to_facebook', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
                try {

                    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
                    $facebookApi = $facebook = $facebookTable->getApi();
                    $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

                    if ($fb_uid &&
                        $fb_uid->facebook_uid &&
                        $facebookApi &&
                        $facebookApi->getUser() &&
                        $facebookApi->getUser() == $fb_uid->facebook_uid) {
                        $fb_data = array(
                            'message' => $publishMessage,
                        );
                        if ($publishUrl) {
                            $fb_data['link'] = $publishUrl;
                        }
                        if ($publishName) {
                            $fb_data['name'] = $publishName;
                        }
                        if ($publishDesc) {
                            $fb_data['description'] = $publishDesc;
                        }
                        if ($publishPicUrl) {
                            $fb_data['picture'] = $publishPicUrl;
                        }
                        $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
                    }
                } catch (Exception $e) {
                    // Silence
                }
            } // end Facebook

            // Publish to twitter, if checked & enabled
            if ($this->_getParam('post_to_twitter', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable) {
                try {
                    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                    if ($twitterTable->isConnected()) {

                        // Get attachment info
                        $title = $attachment->getTitle();
                        $url = $attachment->getHref();
                        $picUrl = $attachment->getPhotoUrl();

                        // Check stuff
                        if ($url && false === stripos($url, 'http://')) {
                            $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
                        }
                        if ($picUrl && false === stripos($picUrl, 'http://')) {
                            $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
                        }

                        // Try to keep full message
                        // @todo url shortener?
                        $message = html_entity_decode($valueBody);
                        if (strlen($message) + strlen($title) + strlen($url) + strlen($picUrl) + 9 <= 140) {
                            if ($title) {
                                $message .= ' - ' . $title;
                            }
                            if ($url) {
                                $message .= ' - ' . $url;
                            }
                            if ($picUrl) {
                                $message .= ' - ' . $picUrl;
                            }
                        } else if (strlen($message) + strlen($title) + strlen($url) + 6 <= 140) {
                            if ($title) {
                                $message .= ' - ' . $title;
                            }
                            if ($url) {
                                $message .= ' - ' . $url;
                            }
                        } else {
                            if (strlen($title) > 24) {
                                $title = Engine_String::substr($title, 0, 21) . '...';
                            }
                            // Sigh truncate I guess
                            if (strlen($message) + strlen($title) + strlen($url) + 9 > 140) {
                                $message = Engine_String::substr($message, 0, 140 - (strlen($title) + strlen($url) + 9)) - 3 . '...';
                            }
                            if ($title) {
                                $message .= ' - ' . $title;
                            }
                            if ($url) {
                                $message .= ' - ' . $url;
                            }
                        }

                        $twitter = $twitterTable->getApi();
                        $twitter->statuses->update($message);
                    }
                } catch (Exception $e) {
                    // Silence
                }
            }

            // Publish to janrain
            if ( //$this->_getParam('post_to_janrain', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
                try {
                    $session = new Zend_Session_Namespace('JanrainActivity');
                    $session->unsetAll();

                    $session->message = $publishMessage;
                    $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
                    $session->name = $publishName;
                    $session->desc = $publishDesc;
                    $session->picture = $publishPicUrl;

                } catch (Exception $e) {
                    // Silence
                }
            }

        } catch (Exception $e) {
            $db->rollBack();
            throw $e; // This should be caught by error handler
        }

        // If we're here, we're done
        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Success!'),
            'id' => $action->getIdentity(),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Post new activity
     *
     * @param  $params
     * @return mixed
     */
    public function post($params)
    {
        self::requireScope('activities');

        // Make sure user exists
        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Get subject if necessary
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = null;
        $subjectType = isset($params['subject_type']) ? $params['subject_type'] : null;
        $subjectId = isset($params['subject_id']) ? (int) $params['subject_id'] : null;
        if (!empty($subjectType) && !empty($subjectId)) {
            $subject = Engine_Api::_()->getItem($subjectType, $subjectId);
        }

        // Use viewer as subject if no subject
        if (null === $subject) {
            $subject = $viewer;
        }

        // Check auth
        if (!$subject->authorization()->isAllowed($viewer, 'comment')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Check if form is valid
        if ((!isset($params['body']) || $params['body'] === '') && empty($params['attachment'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid data'));
            return false;
        }

        // set up action variable
        $action = null;

        // Process
        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();

        try {
            $valueBody = $params['body'];
            // Apply filters
            $filters = array(
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            );
            foreach ($filters as $filter) {
                $valueBody = $filter->filter($valueBody);
            }
            // Get body
            $body = $valueBody;
            $body = preg_replace('/<br[^<>]*>/', "\n", $body);
            // Try attachment getting stuff
            $attachment = null;
            $attachmentData = $params['attachment'];
            if (!empty($attachmentData) && !empty($attachmentData['type'])) {
                $type = $attachmentData['type'];
                $config = null;
                foreach (Zend_Registry::get('Engine_Manifest') as $data) {
                    if (!empty($data['composer'][$type])) {
                        $config = $data['composer'][$type];
                    }
                }
                if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
                    $config = null;
                }
                if ($config) {
                    $attachmentData['actionBody'] = $body;
                    $plugin = Engine_Api::_()->loadClass($config['plugin']);
                    $method = 'onAttach' . ucfirst($type);
                    $attachment = $plugin->$method($attachmentData);
                }
            }

            // Is double encoded because of design mode
            //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
            //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
            //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');

            // Special case: status
            if (!$attachment && $viewer->isSelf($subject)) {
                if ($body != '') {
                    $viewer->status = $body;
                    $viewer->status_date = date('Y-m-d H:i:s');
                    $viewer->save();

                    $viewer->status()->setStatus($body);
                }

                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'status', $body);

            } else {
                // General post

                $type = 'post';
                if ($viewer->isSelf($subject)) {
                    $type = 'post_self';
                }

                // Add notification for <del>owner</del> user
                $subjectOwner = $subject->getOwner();

                if (!$viewer->isSelf($subject) &&
                    $subject instanceof User_Model_User) {
                    $notificationType = 'post_' . $subject->getType();
                    Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
                        'url1' => $subject->getHref(),
                    ));
                }

                // Add activity
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, $type, $body);

                // Try to attach if necessary
                if ($action && $attachment) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
                }
            }

            // Preprocess attachment parameters
            $publishMessage = html_entity_decode($valueBody);
            $publishUrl = null;
            $publishName = null;
            $publishDesc = null;
            $publishPicUrl = null;
            // Add attachment
            if ($attachment) {
                $publishUrl = $attachment->getHref();
                $publishName = $attachment->getTitle();
                $publishDesc = $attachment->getDescription();
                if (empty($publishName)) {
                    $publishName = ucwords($attachment->getShortType());
                }
                if (($tmpPicUrl = $attachment->getPhotoUrl())) {
                    $publishPicUrl = $tmpPicUrl;
                }
                // prevents OAuthException: (#100) FBCDN image is not allowed in stream
                if ($publishPicUrl &&
                    preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
                    $publishPicUrl = null;
                }
            } else {
                $publishUrl = !$action ? null : $action->getHref();
            }
            // Check to ensure proto/host
            if ($publishUrl &&
                false === stripos($publishUrl, 'http://') &&
                false === stripos($publishUrl, 'https://')) {
                $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
            }
            if ($publishPicUrl &&
                false === stripos($publishPicUrl, 'http://') &&
                false === stripos($publishPicUrl, 'https://')) {
                $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
            }
            // Add site title
            if ($publishName) {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                    . ': ' . $publishName;
            } else {
                $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
            }

            // Publish to facebook, if checked & enabled
            if ($this->_getParam('post_to_facebook', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
                try {

                    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
                    $facebook = $facebookApi = $facebookTable->getApi();
                    $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

                    if ($fb_uid &&
                        $fb_uid->facebook_uid &&
                        $facebookApi &&
                        $facebookApi->getUser() &&
                        $facebookApi->getUser() == $fb_uid->facebook_uid) {
                        $fb_data = array(
                            'message' => $publishMessage,
                        );
                        if ($publishUrl) {
                            $fb_data['link'] = $publishUrl;
                        }
                        if ($publishName) {
                            $fb_data['name'] = $publishName;
                        }
                        if ($publishDesc) {
                            $fb_data['description'] = $publishDesc;
                        }
                        if ($publishPicUrl) {
                            $fb_data['picture'] = $publishPicUrl;
                        }
                        $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
                    }
                } catch (Exception $e) {
                    // Silence
                }
            } // end Facebook

            // Publish to twitter, if checked & enabled
            if ($this->_getParam('post_to_twitter', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable) {
                try {
                    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                    if ($twitterTable->isConnected()) {
                        // @todo truncation?
                        // @todo attachment
                        $twitter = $twitterTable->getApi();
                        $twitter->statuses->update($publishMessage);
                    }
                } catch (Exception $e) {
                    // Silence
                }
            }

            // Publish to janrain
            if ( //$this->_getParam('post_to_janrain', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
                try {
                    $session = new Zend_Session_Namespace('JanrainActivity');
                    $session->unsetAll();

                    $session->message = $publishMessage;
                    $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
                    $session->name = $publishName;
                    $session->desc = $publishDesc;
                    $session->picture = $publishPicUrl;

                } catch (Exception $e) {
                    // Silence
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e; // This should be caught by error handler
        }

        // If we're here, we're done
        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Success!'),
            'id' => $action->getIdentity(),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Delete an activity
     *
     * @param  $params
     * @return mixed
     */
    public function delete($params)
    {
        self::requireScope('activities');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $action_id = (int) $params['id'];

        $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($action_id);
        if (!$action) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('This activity item does not exist.'));
            return false;
        }

        // Both the author and the person being written about get to delete the action_id
        if ($activity_moderate ||
            ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || // owner of profile being commented on
            ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id)) // commenter
        {
            // Delete action item and all comments/likes
            $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
            $db->beginTransaction();
            try {
                $action->deleteItem();
                $db->commit();

                $data = array(
                    'message' => Zend_Registry::get('Zend_Translate')->_('This activity item has been removed.'),
                );

                self::setSuccess(200, $data);
                return true;
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        } else {
            // neither the item owner, nor the item subject.  Denied!
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }
    }

    /**
     * Get activities
     *
     * @param  $params
     * @return mixed
     */
    public function get($params)
    {
        self::requireScope('activities');

        $subjectType = isset($params['subject_type']) ? $params['subject_type'] : null;
        $subjectId = isset($params['subject_id']) ? $params['subject_id'] : null;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 10;
        $fields = $this->_getFields($params, 'listing');

        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = null;
        if (!empty($subjectType) && !empty($subjectId)) {
            // Get subject
            $subject = Engine_Api::_()->getItem($subjectType, $subjectId);
            if (!$subject || !$subject->getIdentity()) {
                self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Subject not found.'));
                return false;
            }
            if (!$subject->authorization()->isAllowed($viewer, 'view')) {
                self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
                return false;
            }
        }

        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');

        // Get some options
        $length = isset($params['limit']) ? (int) $params['limit'] : Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15);
        $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);

        $action_id = isset($params['id']) ? (int) $params['id'] : null;

        if ($length > 50) {
            $length = 50;
        }

        // Get config options for activity
        $config = array(
            'action_id' => $action_id,
            'max_id' => isset($params['maxid']) ? (int) $params['maxid'] : null,
            'min_id' => isset($params['minid']) ? (int) $params['minid'] : null,
            'limit' => (int) $length,
        );

        // Pre-process feed items
        $selectCount = 0;
        $nextid = null;
        $firstid = null;
        $tmpConfig = $config;
        $activity = array();
        $endOfFeed = false;

        $friendRequests = array();
        $itemActionCounts = array();
        $enabledModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

        do {
            // Get current batch
            $actions = null;

            // Where the Activity Feed is Fetched
            if (!empty($subject)) {
                $actions = $actionTable->getActivityAbout($subject, $viewer, $tmpConfig);
            } else {
                $actions = $actionTable->getActivity($viewer, $tmpConfig);
            }
            $selectCount++;

            // Are we at the end?
            if (count($actions) < $length || count($actions) <= 0) {
                $endOfFeed = true;
            }

            // Pre-process
            if (count($actions) > 0) {
                foreach ($actions as $action) {
                    // get next id
                    if (null === $nextid || $action->action_id <= $nextid) {
                        $nextid = $action->action_id - 1;
                    }
                    // get first id
                    if (null === $firstid || $action->action_id > $firstid) {
                        $firstid = $action->action_id;
                    }
                    // skip disabled actions
                    if (!$action->getTypeInfo() || !$action->getTypeInfo()->enabled) {
                        continue;
                    }

                    // skip items with missing items
                    if (!$action->getSubject() || !$action->getSubject()->getIdentity()) {
                        continue;
                    }

                    if (!$action->getObject() || !$action->getObject()->getIdentity()) {
                        continue;
                    }

                    // track/remove users who do too much (but only in the main feed)
                    if (empty($subject)) {
                        $actionSubject = $action->getSubject();
                        $actionObject = $action->getObject();
                        if (!isset($itemActionCounts[$actionSubject->getGuid()])) {
                            $itemActionCounts[$actionSubject->getGuid()] = 1;
                        } else if ($itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit) {
                            continue;
                        } else {
                            $itemActionCounts[$actionSubject->getGuid()]++;
                        }
                    }
                    // remove duplicate friend requests
                    if ($action->type == 'friends') {
                        $id = $action->subject_id . '_' . $action->object_id;
                        $rev_id = $action->object_id . '_' . $action->subject_id;
                        if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
                            continue;
                        } else {
                            $friendRequests[] = $id;
                            $friendRequests[] = $rev_id;
                        }
                    }

                    // remove items with disabled module attachments
                    try {
                        $attachments = $action->getAttachments();
                    } catch (Exception $e) {
                        // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
                        continue;
                    }

                    // add to list
                    if (count($activity) < $length) {
                        $activity[] = $action;
                        if (count($activity) == $length) {
                            break;
                        }
                    }
                }
            }

            // Set next tmp max_id
            if ($nextid) {
                $tmpConfig['max_id'] = $nextid;
            }
            if (!empty($tmpConfig['action_id'])) {
                $actions = array();
            }
        } while (count($activity) < $length && $selectCount <= 3 && !$endOfFeed);

        $count = count($activity);
        $items = array();

        if ($count) {
            $helperActivityLoop = new Ynrestapi_Helper_ActivityLoop();
            $activity = $helperActivityLoop->activityLoop($activity, array(
                'action_id' => $action_id,
            ));

            $actions = $activity['actions'];
            unset($activity['actions']);
            $exportParams = $activity;

            $items = Ynrestapi_Helper_Meta::exportAll($actions, $fields, $exportParams);
        }

        if ($action_id) {
            if (!$count) {
                self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('The action you are looking for does not exist.'));
                return false;
            }

            $data = $items[0];
        } else {
            $data = array(
                'count' => $count,
                'next_id' => $nextid,
                'first_id' => $firstid,
                'end_of_feed' => $endOfFeed,
                'items' => $items,
            );
        }

        self::setSuccess(200, $data);
        return true;
    }
}
