<?php

/**
 * class Ynrestapi_Api_User
 */
class Ynrestapi_Api_User extends Ynrestapi_Api_Base
{
    /**
     * Constuctor
     */
    public function __construct()
    {
        $this->module = 'user';
        $this->mainItemType = 'user';
    }

    /**
     * Change password
     *
     * @param  $params
     * @return mixed
     */
    public function postPassword($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        if (empty($params['old_password'])) {
            self::setParamError('old_password', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        }

        $oStringLength = new Zend_Validate_StringLength(6, 32);

        if (empty($params['password'])) {
            self::setParamError('password', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
        } elseif (!$oStringLength->isValid($params['password'])) {
            self::setParamError('password', Zend_Registry::get('Zend_Translate')->_('Please enter at least 6 characters and no more than 32 characters long'), 400);
        }

        if (self::isError()) {
            return false;
        }

        // Process form
        $userTable = Engine_Api::_()->getItemTable('user');
        $db = $userTable->getAdapter();

        // Check old password
        $salt = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret', 'staticSalt');
        $select = $userTable->select()
            ->from($userTable, new Zend_Db_Expr('TRUE'))
            ->where('user_id = ?', $user->getIdentity())
            ->where('password = ?', new Zend_Db_Expr(sprintf('MD5(CONCAT(%s, %s, salt))', $db->quote($salt), $db->quote($params['old_password']))))
            ->limit(1)
        ;
        $valid = $select
            ->query()
            ->fetchColumn()
        ;

        if (!$valid) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Old password did not match'));
            return false;
        }

        // Save
        $db->beginTransaction();

        try {
            $user->setFromArray(array(
                'password' => $params['password'],
            ));
            $user->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Settings were successfully saved.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Update notification settings
     *
     * @param  $params
     * @return mixed
     */
    public function postSettingsNotification($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        // Build the different notification types
        $modules = Engine_Api::_()->getDbtable('modules', 'core')->getModulesAssoc();
        $notificationTypes = Engine_Api::_()->getDbtable('notificationTypes', 'activity')->getNotificationTypes();
        $notificationSettings = Engine_Api::_()->getDbtable('notificationSettings', 'activity')->getEnabledNotifications($user);

        $notificationTypesAssoc = array();
        $notificationSettingsAssoc = array();
        foreach ($notificationTypes as $type) {
            if (in_array($type->module, array('core', 'activity', 'fields', 'authorization', 'messages', 'user'))) {
                $elementName = 'general';
                $category = 'General';
            } else if (isset($modules[$type->module])) {
                $elementName = preg_replace('/[^a-zA-Z0-9]+/', '-', $type->module);
                $category = $modules[$type->module]->title;
            } else {
                $elementName = 'misc';
                $category = 'Misc';
            }

            $notificationTypesAssoc[$elementName]['category'] = $category;
            $notificationTypesAssoc[$elementName]['types'][$type->type] = 'ACTIVITY_TYPE_' . strtoupper($type->type);

            if (in_array($type->type, $notificationSettings)) {
                $notificationSettingsAssoc[$elementName][] = $type->type;
            }
        }

        ksort($notificationTypesAssoc);

        $notificationTypesAssoc = array_filter(array_merge(array(
            'general' => array(),
            'misc' => array(),
        ), $notificationTypesAssoc));

        // Process
        $values = array();
        foreach ($params as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            $aValue = array_map('trim', explode(',', $value));
            if (empty($aValue)) {
                continue;
            }

            foreach ($aValue as $skey => $svalue) {
                if (!isset($notificationTypesAssoc[$key]['types'][$svalue])) {
                    continue;
                }
                $values[] = $svalue;
            }
        }

        // Set notification setting
        Engine_Api::_()->getDbtable('notificationSettings', 'activity')
            ->setEnabledNotifications($user, $values);

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get notification settings
     *
     * @param  $params
     * @return mixed
     */
    public function getSettingsNotification($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        // Build the different notification types
        $modules = Engine_Api::_()->getDbtable('modules', 'core')->getModulesAssoc();
        $notificationTypes = Engine_Api::_()->getDbtable('notificationTypes', 'activity')->getNotificationTypes();
        $notificationSettings = Engine_Api::_()->getDbtable('notificationSettings', 'activity')->getEnabledNotifications($user);

        $notificationTypesAssoc = array();
        $notificationSettingsAssoc = array();
        foreach ($notificationTypes as $type) {
            if (in_array($type->module, array('core', 'activity', 'fields', 'authorization', 'messages', 'user'))) {
                $elementName = 'general';
                $category = 'General';
            } else if (isset($modules[$type->module])) {
                $elementName = preg_replace('/[^a-zA-Z0-9]+/', '-', $type->module);
                $category = $modules[$type->module]->title;
            } else {
                $elementName = 'misc';
                $category = 'Misc';
            }

            $notificationTypesAssoc[$elementName]['category'] = $category;
            $notificationTypesAssoc[$elementName]['types'][$type->type] = 'ACTIVITY_TYPE_' . strtoupper($type->type);

            if (in_array($type->type, $notificationSettings)) {
                $notificationSettingsAssoc[$elementName][] = $type->type;
            }
        }

        ksort($notificationTypesAssoc);

        $notificationTypesAssoc = array_filter(array_merge(array(
            'general' => array(),
            'misc' => array(),
        ), $notificationTypesAssoc));

        $data = array();

        foreach ($notificationTypesAssoc as $elementName => $info) {
            $options = array();
            foreach ($info['types'] as $key => $value) {
                $options[] = array(
                    'id' => $key,
                    'title' => Zend_Registry::get('Zend_Translate')->_($value),
                );
            }

            $data[] = array(
                'id' => $elementName,
                'title' => $info['category'],
                'options' => $options,
                'value' => (array) @$notificationSettingsAssoc[$elementName],
            );
        }

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Leave a network
     *
     * @param  $params
     * @return mixed
     */
    public function postNetworksLeave($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $network = Engine_Api::_()->getItem('network', $params['id']);
        if (null === $network) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Network not found'));
            return false;
        } else if ($network->assignment != 0) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Network not found'));
            return false;
        } else {
            try {
                $network->membership()->removeMember($viewer);
            } catch (Core_Model_Exception $e) {
                self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
                return false;
            } catch (Exception $e) {
                throw $e;
            }
        }

        self::setSuccess(200);
        return true;
    }

    /**
     * Join a network
     *
     * @param  $params
     * @return mixed
     */
    public function postNetworksJoin($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $network = Engine_Api::_()->getItem('network', $params['id']);
        if (null === $network) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Network not found'));
            return false;
        } else if ($network->assignment != 0) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Network not found'));
            return false;
        } else {
            try {
                $network->membership()->addMember($viewer)
                    ->setUserApproved($viewer)
                    ->setResourceApproved($viewer);

                if (!$network->hide) {
                    // Activity feed item
                    Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $network, 'network_join');
                }
            } catch (Core_Model_Exception $e) {
                self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
                return false;
            } catch (Exception $e) {
                throw $e;
            }
        }

        self::setSuccess(200);
        return true;
    }

    /**
     * Get network settings
     *
     * @param $params
     */
    public function getNetworks($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $fields = array(
            'available_networks',
            'current_networks',
        );

        $data = Ynrestapi_Helper_Meta::exportOne($viewer, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Update privacy settings
     *
     * @param  $params
     * @return mixed
     */
    public function postSettingsPrivacy($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();
        $values = array();

        if (isset($params['privacy_search'])) {
            if (!Engine_Api::_()->getDbtable('permissions', 'authorization')->isAllowed($user, $user, 'search')) {
                // self::setError(Zend_Registry::get('Zend_Translate')->_('Forbidden'), 403);
            } elseif ('0' !== strval($params['privacy_search']) && '1' !== strval($params['privacy_search'])) {
                self::setParamError('privacy_search');
            } else {
                $values['search'] = $params['privacy_search'];
            }
        }

        $oHelper = new Ynrestapi_Helper_User($user, null, null);

        if (isset($params['privacy_view'])) {
            $view_options = $oHelper->field_privacy_view_options(true);
            if (!array_key_exists($params['privacy_view'], $view_options)) {
                self::setParamError('privacy_view');
            } else {
                $privacy_value = $params['privacy_view'];
            }
        }

        if (isset($params['privacy_comment'])) {
            $comment_options = $oHelper->field_privacy_comment_options(true);
            if (!array_key_exists($params['privacy_comment'], $comment_options)) {
                self::setParamError('privacy_comment');
            } else {
                $comment_value = $params['privacy_comment'];
            }
        }

        if (isset($params['privacy_activity'])) {
            $activity_options = $oHelper->field_privacy_activity_options(true);
            $privacyActivity = array_map('trim', explode(',', $params['privacy_activity']));
            foreach ($privacyActivity as $key => $value) {
                if (!array_key_exists($value, $activity_options)) {
                    self::setParamError('privacy_activity');
                    break;
                }
            }
        }

        if (self::isError()) {
            return false;
        }

        $roles = array('owner', 'member', 'network', 'registered', 'everyone');
        $auth = Engine_Api::_()->authorization()->context;

        if ($privacy_value) {
            // Process member profile viewing privacy
            $privacy_max_role = array_search($privacy_value, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($user, $role, 'view', ($i <= $privacy_max_role));
            }
        }

        if ($comment_value) {
            // Process member profile commenting privacy
            $comment_max_role = array_search($comment_value, $roles);
            foreach ($roles as $i => $role) {
                $auth->setAllowed($user, $role, 'comment', ($i <= $comment_max_role));
            }
        }

        $user->setFromArray($values)->save();

        // Update notification settings
        if (!empty($privacyActivity)) {
            $publishTypes = $privacyActivity;
            $publishTypes[] = 'signup';
            $publishTypes[] = 'post';
            $publishTypes[] = 'status';
            Engine_Api::_()->getDbtable('actionSettings', 'activity')->setEnabledActions($user, (array) $publishTypes);
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get privacy settings
     *
     * @param $params
     */
    public function getSettingsPrivacy($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $fields = $this->_getFields($params, 'settings_privacy');

        $viewer = Engine_Api::_()->user()->getViewer();

        $data = Ynrestapi_Helper_Meta::exportOne($viewer, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Unblock a blocked member
     *
     * @param $params
     */
    public function deleteBlock($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->authorization()->isAllowed('user', $viewer, 'block')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Get id of friend to add
        $user_id = $params['id'];
        if (!$user_id) {
            self::setParamError('id', Zend_Registry::get('Zend_Translate')->_('No member specified'), 400);
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('block', 'user')->getAdapter();
        $db->beginTransaction();

        try {
            $user = Engine_Api::_()->getItem('user', $user_id);

            $viewer->removeBlock($user);

            $db->commit();

            $data = array(
                'message' => Zend_Registry::get('Zend_Translate')->_('Member unblocked'),
            );

            self::setSuccess(200, $data);
            return true;
        } catch (Exception $e) {
            $db->rollBack();

            throw $e;
        }
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postBlock($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        } elseif (false === filter_var($params['id'], FILTER_VALIDATE_INT)) {
            self::setParamError('id');
            return false;
        }

        // Get id of friend to add
        $user_id = $params['id'];
        $user = Engine_Api::_()->user()->getUser($user_id);
        if (!$user || !$user->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('User not found.'));
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('block', 'user')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $viewer->addBlock($user);
            if ($user->membership()->isMember($viewer, null)) {
                $user->membership()->removeMember($viewer);
            }

            try {
                // Set the requests as handled
                $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
                if ($notification) {
                    $notification->mitigated = true;
                    $notification->read = 1;
                    $notification->save();
                }
                $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
                if ($notification) {
                    $notification->mitigated = true;
                    $notification->read = 1;
                    $notification->save();
                }
            } catch (Exception $e) {
                throw $e;
            }

            $db->commit();

            $data = array(
                'message' => Zend_Registry::get('Zend_Translate')->_('Member blocked'),
            );

            self::setSuccess(200, $data);
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Get blocked members
     *
     * @param $params
     */
    public function getBlock($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        $blockedUsers = array();

        if (Engine_Api::_()->authorization()->isAllowed('user', $user, 'block')) {
            foreach ($user->getBlockedUsers() as $blocked_user_id) {
                $blockedUser = Engine_Api::_()->user()->getUser($blocked_user_id);
                $blockedUsers[] = Ynrestapi_Helper_Meta::exportOne($blockedUser, array('listing'));
            }
        } else {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        self::setSuccess(200, $blockedUsers);
        return true;
    }

    /**
     * Update settings general
     *
     * @param  $params
     * @return mixed
     */
    public function postSettingsGeneral($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $user = Engine_Api::_()->user()->getViewer();

        if (isset($params['email'])) {
            $email = $params['email'];
            $oEmailAddress = new Zend_Validate_EmailAddress();
            $oEmailAddress->getHostnameValidator()->setValidateTld(false);

            // Check NotEmpty, EmailAddress
            if (empty($email) || !$oEmailAddress->isValid($email)) {
                self::setParamError('email', Zend_Registry::get('Zend_Translate')->_('Please enter a valid email address.'), 400);
            } elseif ($email != $user->email) {
                $oDb_NoRecordExists = new Zend_Validate_Db_NoRecordExists(Engine_Db_Table::getTablePrefix() . 'users', 'email', array('field' => 'user_id', 'value' => $user->getIdentity()));
                $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');

                // Check Db_NoRecordExists
                if (!$oDb_NoRecordExists->isValid($email)) {
                    self::setParamError('email', Zend_Registry::get('Zend_Translate')->_('Someone has already registered this email address, please use another one.', 'recordFound'), 400);
                }
                // Check email against banned list if necessary
                elseif ($bannedEmailsTable->isEmailBanned($email)) {
                    self::setParamError('email', Zend_Registry::get('Zend_Translate')->_('This email address is not available, please use another one.'), 400);
                }
            }
        }

        if (isset($params['username'])) {
            $username = $params['username'];
            $oAlnum = new Zend_Validate_Alnum();
            $oStringLength = new Zend_Validate_StringLength(4, 64);
            $oRegex = new Zend_Validate_Regex('/^[a-z][a-z0-9]*$/i');

            // Check NotEmpty
            if (empty($username)) {
                self::setParamError('username', Zend_Registry::get('Zend_Translate')->_('Please enter a valid profile address.'), 400);
            }
            // Check Alnum
            elseif (!$oAlnum->isValid($username)) {
                self::setParamError('username', Zend_Registry::get('Zend_Translate')->_('Profile addresses must be alphanumeric.'), 400);
            }
            // Check StringLength
            elseif (!$oStringLength->isValid($username)) {
                self::setParamError('username', Zend_Registry::get('Zend_Translate')->_(implode('. ', $oStringLength->getMessages())), 400);
            }
            // Check Regex
            elseif (!$oRegex->isValid($username)) {
                self::setParamError('username', Zend_Registry::get('Zend_Translate')->_('Profile addresses must start with a letter.'), 400);
            } elseif ($username != $user->username) {
                $oDb_NoRecordExists = new Zend_Validate_Db_NoRecordExists(Engine_Db_Table::getTablePrefix() . 'users', 'username', array('field' => 'user_id', 'value' => $user->getIdentity()));
                $bannedUsernamesTable = Engine_Api::_()->getDbtable('BannedUsernames', 'core');

                // Check Db_NoRecordExists
                if (!$oDb_NoRecordExists->isValid($username)) {
                    self::setParamError('username', Zend_Registry::get('Zend_Translate')->_('Someone has already picked this profile address, please use another one.'), 400);
                }

                // Check username against banned list if necessary
                if ($bannedUsernamesTable->isUsernameBanned($username)) {
                    self::setParamError('username', Zend_Registry::get('Zend_Translate')->_('This profile address is not available, please use another one.'), 400);
                }
            }
        }

        if (self::isError()) {
            return false;
        }

        // Set values for user object
        $user->setFromArray($params);

        // If username is changed
        $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
        $user->setDisplayName($aliasValues);

        $user->save();

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Settings saved.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Get settings general
     *
     * @param $params
     */
    public function getSettingsGeneral($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $fields = $this->_getFields($params, 'settings_general');

        $viewer = Engine_Api::_()->user()->getViewer();

        $data = Ynrestapi_Helper_Meta::exportOne($viewer, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Cancel friend request or follow request
     *
     * @param  $params
     * @return mixed
     */
    public function postFriendsCancel($params)
    {
        self::requireScope('friends');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Get viewer and other user
        $viewer = Engine_Api::_()->user()->getViewer();
        if (null == ($user_id = $params['user_id']) ||
            null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('No member specified'));
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
        $db->beginTransaction();

        try {
            $user->membership()->removeMember($viewer);

            // Set the requests as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                ->getNotificationBySubjectAndType($user, $viewer, 'friend_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                ->getNotificationBySubjectAndType($user, $viewer, 'friend_follow_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }

            $db->commit();

            $data = array(
                'message' => Zend_Registry::get('Zend_Translate')->_('Your friend request has been cancelled.'),
            );

            self::setSuccess(200, $data);
            return true;
        } catch (Core_Model_Exception $e) {
            $db->rollBack();
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Reject friend request or ignore follow request
     *
     * @param  $params
     * @return mixed
     */
    public function postFriendsIgnore($params)
    {
        self::requireScope('friends');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Get viewer and other user
        $viewer = Engine_Api::_()->user()->getViewer();
        if (null == ($user_id = $params['user_id']) ||
            null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('No member specified'));
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer->membership()->removeMember($user);

            // Set the request as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }

            $db->commit();

            if ($user->membership()->isReciprocal()) {
                $message = Zend_Registry::get('Zend_Translate')->_('You ignored a friend request from %s');
            } else {
                $message = Zend_Registry::get('Zend_Translate')->_('You ignored %s\'s request to follow you');
            }

            $message = sprintf($message, $user->__toString());

            $data = array(
                'message' => $message,
            );

            self::setSuccess(200, $data);
            return true;
        } catch (Core_Model_Exception $e) {
            $db->rollBack();
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Confirm friend request or approve follow request
     *
     * @param  $params
     * @return mixed
     */
    public function postFriendsConfirm($params)
    {
        self::requireScope('friends');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Get viewer and other user
        $viewer = Engine_Api::_()->user()->getViewer();
        if (null == ($user_id = $params['user_id']) ||
            null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('No member specified'));
            return false;
        }

        $friendship = $viewer->membership()->getRow($user);
        if ($friendship->active) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Already friends'));
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
        $db->beginTransaction();

        try {
            $viewer->membership()->setResourceApproved($user);

            // Add activity
            if (!$user->membership()->isReciprocal()) {
                Engine_Api::_()->getDbtable('actions', 'activity')
                    ->addActivity($user, $viewer, 'friends_follow', '{item:$subject} is now following {item:$object}.');
            } else {
                Engine_Api::_()->getDbtable('actions', 'activity')
                    ->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
                Engine_Api::_()->getDbtable('actions', 'activity')
                    ->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
            }

            // Add notification
            if (!$user->membership()->isReciprocal()) {
                Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->addNotification($user, $viewer, $user, 'friend_follow_accepted');
            } else {
                Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->addNotification($user, $viewer, $user, 'friend_accepted');
            }

            // Set the requests as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }

            // Increment friends counter
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.friendships');

            $db->commit();

            $message = Zend_Registry::get('Zend_Translate')->_('You are now friends with %s');
            $message = sprintf($message, $user->__toString());

            $data = array(
                'message' => $message,
            );

            self::setSuccess(200, $data);
            return true;
        } catch (Core_Model_Exception $e) {
            $db->rollBack();
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Remove friend or unfollow member from logged in user
     *
     * @param  $params
     * @return mixed
     */
    public function deleteFriends($params)
    {
        self::requireScope('friends');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Get viewer and other user
        $viewer = Engine_Api::_()->user()->getViewer();
        if (null == ($user_id = $params['user_id']) ||
            null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('No member specified'));
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
        $db->beginTransaction();

        try {
            $user->membership()->removeMember($viewer);

            // Remove from lists?
            // @todo make sure this works with one-way friendships
            $user->lists()->removeFriendFromLists($viewer);
            $viewer->lists()->removeFriendFromLists($user);

            // Set the requests as handled
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                ->getNotificationBySubjectAndType($user, $viewer, 'friend_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }
            $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
            if ($notification) {
                $notification->mitigated = true;
                $notification->read = 1;
                $notification->save();
            }

            $db->commit();

            $message = Zend_Registry::get('Zend_Translate')->_('This person has been removed from your friends.');

            $data = array(
                'message' => $message,
            );

            self::setSuccess(200, $data);
            return true;
        } catch (Core_Model_Exception $e) {
            $db->rollBack();
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Send friend request or follow request from logged in user
     *
     * @param  $params
     * @return mixed
     */
    public function postFriends($params)
    {
        self::requireScope('friends');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        // Get viewer and other user
        $viewer = Engine_Api::_()->user()->getViewer();
        if (null == ($user_id = $params['user_id']) ||
            null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('No member specified'));
            return false;
        }

        // check that user is not trying to befriend 'self'
        if ($viewer->isSelf($user)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You cannot befriend yourself.'));
            return false;
        }

        // check that user is already friends with the member
        if ($user->membership()->isMember($viewer)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('You are already friends with this member.'));
            return false;
        }

        // check that user has not blocked the member
        if ($viewer->isBlocked($user)) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Friendship request was not sent because you blocked this member.'));
            return false;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
        $db->beginTransaction();

        try {

            // send request
            $user->membership()
                ->addMember($viewer)
                ->setUserApproved($viewer);

            if (!$viewer->membership()->isUserApprovalRequired() && !$viewer->membership()->isReciprocal()) {
                // if one way friendship and verification not required

                // Add activity
                Engine_Api::_()->getDbtable('actions', 'activity')
                    ->addActivity($viewer, $user, 'friends_follow', '{item:$subject} is now following {item:$object}.');

                // Add notification
                Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->addNotification($user, $viewer, $viewer, 'friend_follow');

                $message = Zend_Registry::get('Zend_Translate')->_('You are now following this member.');

            } else if (!$viewer->membership()->isUserApprovalRequired() && $viewer->membership()->isReciprocal()) {
                // if two way friendship and verification not required

                // Add activity
                Engine_Api::_()->getDbtable('actions', 'activity')
                    ->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
                Engine_Api::_()->getDbtable('actions', 'activity')
                    ->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');

                // Add notification
                Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->addNotification($user, $viewer, $user, 'friend_accepted');

                $message = Zend_Registry::get('Zend_Translate')->_('You are now friends with this member.');

            } else if (!$user->membership()->isReciprocal()) {
                // if one way friendship and verification required

                // Add notification
                Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->addNotification($user, $viewer, $user, 'friend_follow_request');

                $message = Zend_Registry::get('Zend_Translate')->_('Your friend request has been sent.');

            } else if ($user->membership()->isReciprocal()) {
                // if two way friendship and verification required

                // Add notification
                Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->addNotification($user, $viewer, $user, 'friend_request');

                $message = Zend_Registry::get('Zend_Translate')->_('Your friend request has been sent.');
            }

            $db->commit();

            $data = array(
                'message' => $message,
            );

            self::setSuccess(200, $data);
            return true;
        } catch (Core_Model_Exception $e) {
            $db->rollBack();
            self::setExceptionError($e, 500, 'exception_error', $e->getMessage());
            return false;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Get friend list of an user
     *
     * @param  $params
     * @return mixed
     */
    public function getFriends($params)
    {
        self::requireScope('friends');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['id'])) {
            $id = Engine_Api::_()->user()->getViewer()->getIdentity();
        } elseif (!is_numeric($params['id'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('User identity is invalid'));
            return false;
        } else {
            $id = $params['id'];
            $user = Engine_Api::_()->user()->getUser($id);
            if (!$user->getIdentity()) {
                self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('User not found'));
                return false;
            }
        }

        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 10;
        $fields = $this->_getFields($params, 'listing');

        $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
        $membershipName = $membershipTable->info('name');

        $select = $membershipTable->select()
            ->from(array('m1' => $membershipName))
            ->setIntegrityCheck(false)
            ->where('`m1`.`user_id` = ?', $id)->where('`m1`.`active` = ?', 1);

        $friends = Zend_Paginator::factory($select);
        $friends->setCurrentPageNumber($page);
        $friends->setItemCountPerPage($limit);

        if ($page > $friends->count()) {
            $data = array();

            self::setSuccess(200, $data);
            return true;
        }

        // Get stuff
        $ids = array();
        foreach ($friends as $friend) {
            $ids[] = $friend->resource_id;
        }

        // Get the items
        $items = array();
        $appMeta = Ynrestapi_Helper_Meta::getInstance();

        foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $entry) {
            $items[] = $appMeta->getModelHelper($entry)->toArray($fields);
        }

        self::setSuccess(200, $items);
        return true;
    }

    /**
     * @param $params
     */
    public function postSignup($params)
    {
        self::requireScope('basic');

        $scheduledTasks = array(
            '_validateFormAccount',
            '_validateFormFields',
            '_validateFormPhoto',
            '_processFormAccount',
            '_processFormFields',
            '_processFormPhoto',
        );

        $registry = array();

        foreach ($scheduledTasks as $task) {
            if (!$this->{$task}($params, $registry)) {
                return false;
            }
        }

        self::setSuccess(200);
        return true;
    }

    /**
     * @param $params
     * @param $registry
     */
    private function _validateFormAccount($params, &$registry)
    {
        $registry['form'] = $form = new User_Form_Signup_Account();
        $form->removeElement('name');
        $form->removeElement('passconf');
        $form->removeElement('terms');
        if ($form->captcha) {
            $form->removeElement('captcha');
        }
        $fieldEmail = $form->getEmailElementFieldName();
        $params[$fieldEmail] = $params['email'];
        unset($params['email']);

        $form->populate($params);

        if (!$form->isValid($form->getValues())) {
            $messages = $form->getMessages();
            foreach ($messages as $key => $value) {
                $field = ($key == $fieldEmail) ? 'email' : $key;
                self::setParamError($field, 400, 'invalid_parameter', implode("\n", $value));
            }
            return false;
        }

        $accountSession = new Zend_Session_Namespace('User_Plugin_Signup_Account');
        $accountSession->data = $form->getValues();

        return true;
    }

    /**
     * @param $params
     * @param $registry
     */
    private function _processFormAccount($params, &$registry)
    {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $random = ($settings->getSetting('user.signup.random', 0) == 1);
        $emailadmin = ($settings->getSetting('user.signup.adminemail', 0) == 1);
        if ($emailadmin) {
            // the signup notification is emailed to the first SuperAdmin by default
            $users_table = Engine_Api::_()->getDbtable('users', 'user');
            $users_select = $users_table->select()
                ->where('level_id = ?', 1)
                ->where('enabled >= ?', 1);
            $super_admin = $users_table->fetchRow($users_select);
        }

        $accountSession = new Zend_Session_Namespace('User_Plugin_Signup_Account');
        $data = $accountSession->data;

        // Add email and code to invite session if available
        $inviteSession = new Zend_Session_Namespace('invite');
        if (isset($data['email'])) {
            $inviteSession->signup_email = $data['email'];
        }
        if (isset($data['code'])) {
            $inviteSession->signup_code = $data['code'];
        }

        if ($random) {
            $data['password'] = Engine_Api::_()->user()->randomPass(10);
        }

        if (isset($data['language'])) {
            $data['locale'] = $data['language'];
        }

        // Create user
        // Note: you must assign this to the registry before calling save or it
        // will not be available to the plugin in the hook
        $registry['user'] = $user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
        $user->setFromArray($data);
        $user->save();

        Engine_Api::_()->user()->setViewer($user);

        // Increment signup counter
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');

        if ($user->verified && $user->enabled) {
            // Create activity for them
            Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'signup');
            // Set user as logged in if not have to verify email
            Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
        }

        $mailType = null;
        $mailParams = array(
            'host' => $_SERVER['HTTP_HOST'],
            'email' => $user->email,
            'date' => time(),
            'recipient_title' => $user->getTitle(),
            'recipient_link' => $user->getHref(),
            'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
            'object_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        );

        // Add password to email if necessary
        if ($random) {
            $mailParams['password'] = $data['password'];
        }

        // Mail stuff
        switch ($settings->getSetting('user.signup.verifyemail', 0)) {
            case 0:
                // only override admin setting if random passwords are being created
                if ($random) {
                    $mailType = 'core_welcome_password';
                }
                if ($emailadmin) {
                    $mailAdminType = 'notify_admin_user_signup';
                    $siteTimezone = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.timezone', 'America/Los_Angeles');
                    $date = new DateTime('now', new DateTimeZone($siteTimezone));
                    $mailAdminParams = array(
                        'host' => $_SERVER['HTTP_HOST'],
                        'email' => $user->email,
                        'date' => $date->format('F j, Y, g:i a'),
                        'recipient_title' => $super_admin->displayname,
                        'object_title' => $user->displayname,
                        'object_link' => $user->getHref(),
                    );
                }
                break;

            case 1:
                // send welcome email
                $mailType = ($random ? 'core_welcome_password' : 'core_welcome');
                if ($emailadmin) {
                    $mailAdminType = 'notify_admin_user_signup';
                    $siteTimezone = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.timezone', 'America/Los_Angeles');
                    $date = new DateTime('now', new DateTimeZone($siteTimezone));
                    $mailAdminParams = array(
                        'host' => $_SERVER['HTTP_HOST'],
                        'email' => $user->email,
                        'date' => $date->format('F j, Y, g:i a'),
                        'recipient_title' => $super_admin->displayname,
                        'object_title' => $user->getTitle(),
                        'object_link' => $user->getHref(),
                    );
                }
                break;

            case 2:
                // verify email before enabling account
                $verify_table = Engine_Api::_()->getDbtable('verify', 'user');
                $verify_row = $verify_table->createRow();
                $verify_row->user_id = $user->getIdentity();
                $verify_row->code = md5($user->email
                    . $user->creation_date
                    . $settings->getSetting('core.secret', 'staticSalt')
                    . (string) rand(1000000, 9999999));
                $verify_row->date = $user->creation_date;
                $verify_row->save();

                $mailType = ($random ? 'core_verification_password' : 'core_verification');

                $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                    'action' => 'verify',
                    'email' => $user->email,
                    'verify' => $verify_row->code,
                ), 'user_signup', true);

                if ($emailadmin) {
                    $mailAdminType = 'notify_admin_user_signup';

                    $mailAdminParams = array(
                        'host' => $_SERVER['HTTP_HOST'],
                        'email' => $user->email,
                        'date' => date('F j, Y, g:i a'),
                        'recipient_title' => $super_admin->displayname,
                        'object_title' => $user->getTitle(),
                        'object_link' => $user->getHref(),
                    );
                }
                break;

            default:
                // do nothing
                break;
        }

        if (!empty($mailType)) {
            $registry['mailParams'] = $mailParams;
            $registry['mailType'] = $mailType;
            // Moved to User_Plugin_Signup_Fields
            // Engine_Api::_()->getApi('mail', 'core')->sendSystem(
            //   $user,
            //   $mailType,
            //   $mailParams
            // );
        }

        if (!empty($mailAdminType)) {
            $registry['mailAdminParams'] = $mailAdminParams;
            $registry['mailAdminType'] = $mailAdminType;
            // Moved to User_Plugin_Signup_Fields
            // Engine_Api::_()->getApi('mail', 'core')->sendSystem(
            //   $user,
            //   $mailType,
            //   $mailParams
            // );
        }

        // Attempt to connect facebook
        if (!empty($params['provider']) && $params['provider'] == 'facebook' && !empty($params['uid'])) {
            try {
                $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
                $facebook = $facebookTable->getApi();
                $settings = Engine_Api::_()->getDbtable('settings', 'core');
                if ($facebook && $settings->core_facebook_enable) {
                    $facebookTable->insert(array(
                        'user_id' => $user->getIdentity(),
                        'facebook_uid' => $params['uid'],
                        'access_token' => '', // @todo get value
                        //'code' => $code,
                        'expires' => 0, // @todo make sure this is correct
                    ));
                }
            } catch (Exception $e) {
                // Silence
                if ('development' == APPLICATION_ENV) {
                    echo $e;
                }
            }
        }

        // Attempt to connect twitter
        if (!empty($params['provider']) && $params['provider'] == 'twitter' && !empty($params['uid'])) {
            try {
                $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                $twitter = $twitterTable->getApi();
                $twitterOauth = $twitterTable->getOauth();
                $settings = Engine_Api::_()->getDbtable('settings', 'core');
                if ($twitter && $twitterOauth && $settings->core_twitter_enable) {
                    $accountInfo = $twitter->account->verify_credentials();
                    $twitterTable->insert(array(
                        'user_id' => $user->getIdentity(),
                        'twitter_uid' => $params['uid'],
                        'twitter_token' => '', // @todo get value
                        'twitter_secret' => '', // @todo get value
                    ));
                }
            } catch (Exception $e) {
                // Silence?
                if ('development' == APPLICATION_ENV) {
                    echo $e;
                }
            }
        }

        // Attempt to connect janrain
        if (!empty($params['provider']) && $params['provider'] == 'janrain' && !empty($params['uid'])) {
            // not supported
        }

        return true;
    }

    /**
     * @param $params
     * @param $registry
     */
    private function _validateFormFields($params, &$registry)
    {
        $formArgs = array();

        // Preload profile type field stuff
        $profileTypeField = $this->_getProfileTypeField();
        if ($profileTypeField) {
            $accountSession = new Zend_Session_Namespace('User_Plugin_Signup_Account');
            $profileTypeValue = @$accountSession->data['profile_type'];
            if ($profileTypeValue) {
                $formArgs = array(
                    'topLevelId' => $profileTypeField->field_id,
                    'topLevelValue' => $profileTypeValue,
                );
            } else {
                $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
                if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                    $profileTypeField = $topStructure[0]->getChild();
                    $options = $profileTypeField->getOptions();
                    if (count($options) == 1) {
                        $formArgs = array(
                            'topLevelId' => $profileTypeField->field_id,
                            'topLevelValue' => $options[0]->option_id,
                        );
                    }
                }
            }
        }

        // Create form
        $registry['form'] = $form = new User_Form_Signup_Fields($formArgs);
        $fieldMaps = array();

        // Prepare values
        if (!empty($params['gender']) && !($params['gender'] = $this->getGenderOptionId($params['gender']))) {
            self::setParamError('gender');
            return false;
        }

        if (!empty($params['birthdate'])) {
            $dateParts = explode('-', $params['birthdate']);
            if (count($dateParts) !== 3 || !checkdate($dateParts[1], $dateParts[2], $dateParts[0])) {
                self::setParamError('birthdate');
                return false;
            }
        }

        // Set values
        foreach ($form->getElements() as $key => $el) {
            $parts = explode('_', $key);
            if (count($parts) !== 3) {
                continue;
            }
            list($parent_id, $option_id, $field_id) = $parts;
            $field = Engine_Api::_()->fields()->getField($field_id, 'user', true);
            $fieldMaps[$key] = $field->type;
            if (isset($params[$field->type])) {
                $el->setValue($params[$field->type]);
            }
        }

        $values = $form->getValues();

        if (false !== ($dateField = array_search('birthdate', $fieldMaps)) && isset($dateParts)) {
            $values[$dateField] = array(
                'day' => $dateParts[2],
                'month' => $dateParts[1],
                'year' => $dateParts[0],
            );
        }

        if (!$form->isValid($values)) {
            $messages = $form->getMessages();
            foreach ($messages as $key => $value) {
                $param = isset($fieldMaps[$key]) ? $fieldMaps[$key] : $key;
                self::setParamError($param, 400, 'invalid_parameter', implode("\n", $value));
            }
            return false;
        }

        $fieldsSession = new Zend_Session_Namespace('User_Plugin_Signup_Fields');
        $fieldsSession->data = $form->getProcessedValues();
        return true;
    }

    /**
     * @return mixed
     */
    private function _getProfileTypeField()
    {
        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            return $topStructure[0]->getChild();
        }
        return null;
    }

    /**
     * @param $params
     * @param $registry
     */
    private function _processFormFields($params, &$registry)
    {
        $user = $registry['user'];

        // Preload profile type field stuff
        $profileTypeField = $this->_getProfileTypeField();
        if ($profileTypeField) {
            $accountSession = new Zend_Session_Namespace('User_Plugin_Signup_Account');
            $profileTypeValue = @$accountSession->data['profile_type'];
            if ($profileTypeValue) {
                $values = Engine_Api::_()->fields()->getFieldsValues($user);
                $valueRow = $values->createRow();
                $valueRow->field_id = $profileTypeField->field_id;
                $valueRow->item_id = $user->getIdentity();
                $valueRow->value = $profileTypeValue;
                $valueRow->save();
            } else {
                $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
                if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                    $profileTypeField = $topStructure[0]->getChild();
                    $options = $profileTypeField->getOptions();
                    if (count($options) == 1) {
                        $values = Engine_Api::_()->fields()->getFieldsValues($user);
                        $valueRow = $values->createRow();
                        $valueRow->field_id = $profileTypeField->field_id;
                        $valueRow->item_id = $user->getIdentity();
                        $valueRow->value = $options[0]->option_id;
                        $valueRow->save();
                    }
                }
            }
        }

        // Save them values
        $form = $registry['form']->setItem($user);
        $form->setProcessedValues($fieldsSession->data);
        $form->saveValues();

        $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
        $user->setDisplayName($aliasValues);
        $user->save();

        // Send Welcome E-mail
        if (isset($this->_registry->mailType) && $this->_registry->mailType) {
            $mailType = $this->_registry->mailType;
            $mailParams = $this->_registry->mailParams;
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                $user,
                $mailType,
                $mailParams
            );
        }

        // Send Notify Admin E-mail
        if (isset($this->_registry->mailAdminType) && $this->_registry->mailAdminType) {
            $mailAdminType = $this->_registry->mailAdminType;
            $mailAdminParams = $this->_registry->mailAdminParams;
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                $user,
                $mailAdminType,
                $mailAdminParams
            );
        }

        return true;
    }

    /**
     * @param $params
     * @param $registry
     */
    private function _validateFormPhoto($params, &$registry)
    {
        $photoIsRequired = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.photo');
        if ($photoIsRequired && !isset($params['photo_url']) && !isset($_FILES['Filedata'])) {
            self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Profile photo is required.'));
            return false;
        }

        return true;
    }

    /**
     * @param $params
     * @param $registry
     */
    private function _processFormPhoto($params, &$registry)
    {
        $user = $registry['user'];

        if (isset($params['photo_url']) && $params['photo_url'] != '') {
            $filepath = $this->_saveTmpFileFromUrl($params['photo_url']);
            $user->setPhoto($filepath);
            @unlink($filepath);

            if ($this->_isValidPhotoCropData($params)) {
                $this->_cropPhoto($user, $params);
            }
        } elseif (isset($_FILES['Filedata'])) {
            $user->setPhoto($_FILES['Filedata']);

            if ($this->_isValidPhotoCropData($params)) {
                $this->_cropPhoto($user, $params);
            }
        }

        return true;
    }

    /**
     * @param $user
     * @param $uid
     */
    public static function updateAgentForFacebook($user, $uid)
    {
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebookTable->delete('facebook_uid=' . $uid);
        $facebookAgent = $facebookTable->insert(array(
            'user_id' => $user->getIdentity(),
            'facebook_uid' => $uid,
            'access_token' => '',
            'code' => '',
            'expires' => 0,
        ));

        //WHEN YNSOCIAL-CONNECT EXISTED
        if (Engine_Api::_()->hasModuleBootstrap('social-connect')) {
            // cleanup old data
            Engine_Api::_()->getDbtable('agents', 'socialconnect')->delete('identity=' . $uid);
            Engine_Api::_()->getDbtable('accounts', 'socialconnect')->delete('identity=' . $uid);

            $api = Engine_Api::_()->getApi('Core', 'SocialConnect');
            $api->createAccount($user->getIdentity(), $uid, 'facebook', array());
        }
    }

    /**
     * @param $user
     * @param $uid
     */
    public static function updateAgentForTwitter($user, $uid)
    {
        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $twitterTable->delete('twitter_uid=' . $uid);
        $twitterAgent = $twitterTable->insert(array(
            'user_id' => $user->getIdentity(),
            'twitter_uid' => $uid,
            'twitter_token' => '',
            'twitter_secret' => '',
        ));

        //WHEN YNSOCIAL-CONNECT EXISTED
        if (Engine_Api::_()->hasModuleBootstrap('social-connect')) {
            // cleanup old data
            Engine_Api::_()->getDbtable('agents', 'socialconnect')->delete('identity=' . $uid);
            Engine_Api::_()->getDbtable('accounts', 'socialconnect')->delete('identity=' . $uid);

            $api = Engine_Api::_()->getApi('Core', 'SocialConnect');
            $api->createAccount($user->getIdentity(), $uid, 'twitter', array());
        }
    }

    /**
     * @param  $url
     * @return mixed
     */
    private function _saveTmpFileFromUrl($url)
    {
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $qpos = strpos($ext, '?');
        if ($qpos !== false) {
            $ext = substr($ext, 0, $qpos);
        }
        if (empty($ext)) {
            $ext = 'png';
        }

        $filename = 'tmpfile_' . time() . '.' . $ext;
        $filepath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . $filename;
        $url = html_entity_decode($url, ENT_QUOTES, 'UTF-8');

        $ch = curl_init($url);
        $fp = fopen($filepath, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return $filepath;
    }

    /**
     * Get item(s)
     *
     * @param  array   $params
     * @return array
     */
    public function get($params)
    {
        self::requireScope('basic');

        $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
        if (!$require_check) {
            if (!$this->isViewer()) {
                self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
                return false;
            }
        }

        if (isset($params['id'])) {
            return $this->getItem($params);
        }

        $page = isset($params['page']) ? (int) $params['page'] : 1;
        $limit = isset($params['limit']) ? (int) $params['limit'] : 10;
        $keywords = isset($params['keywords']) ? $params['keywords'] : '';
        $genderLabel = isset($params['gender_label']) ? (int) $params['gender_label'] : '';
        $ageMin = isset($params['age_min']) ? (int) $params['age_min'] : 0;
        $ageMax = isset($params['age_max']) ? (int) $params['age_max'] : 0;
        $hasPhoto = isset($params['has_photo']) ? (int) $params['has_photo'] : 0;
        $isOnline = isset($params['is_online']) ? (int) $params['is_online'] : 0;

        $fields = $this->_getFields($params, 'listing');

        $options = array();

        if (!empty($genderLabel) && !($options['gender'] = $this->getGenderOptionId($genderLabel))) {
            self::setParamError('gender_label');
            return false;
        }

        if ($ageMin) {
            $options['birthdate']['min'] = $ageMin;
        }

        if ($ageMax) {
            $options['birthdate']['max'] = $ageMax;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        // Get table info
        $table = Engine_Api::_()->getItemTable('user');
        $userTableName = $table->info('name');

        $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
        $searchTableName = $searchTable->info('name');

        // Contruct query
        $select = $table->select()
            ->from($userTableName)
            ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
            ->where("{$userTableName}.search = ?", 1)
            ->where("{$userTableName}.enabled = ?", 1);

        $searchDefault = true;

        // Build the photo and is online part of query
        if (isset($hasPhoto) && $hasPhoto) {
            $select->where($userTableName . '.photo_id != ?', '0');
            $searchDefault = false;
        }

        if (isset($isOnline) && $isOnline) {
            $select
                ->joinRight('engine4_user_online', "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
                ->group('engine4_user_online.user_id')
                ->where($userTableName . '.user_id != ?', '0');

            $searchDefault = false;
        }

        // Add displayname
        if (!empty($keywords)) {
            $db = $table->getAdapter();
            $likeSearch = $db->quote("%{$keywords}%");
            $equalSearch = $db->quote($keywords);

            $select->where("(`{$userTableName}`.`username` LIKE {$likeSearch} or `{$userTableName}`.`displayname` LIKE {$likeSearch} or `{$userTableName}`.`email`={$equalSearch})");
            $searchDefault = false;
        }

        // Build search part of query
        $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
        foreach ($searchParts as $k => $v) {
            $select->where("`{$searchTableName}`.{$k}", $v);

            if (isset($v) && $v != '') {
                $searchDefault = false;
            }
        }

        if ($searchDefault) {
            $select->order("{$userTableName}.lastlogin_date DESC");
        } else {
            $select->order("{$userTableName}.displayname ASC");
        }

        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);

        if ($paginator->count() < $page) {
            $data = array();

            self::setSuccess(200, $data);
            return true;
        }

        // Get the items
        $result = array();
        $appMeta = Ynrestapi_Helper_Meta::getInstance();

        foreach ($paginator as $entry) {
            $result[] = $appMeta->getModelHelper($entry)->toArray($fields);
        }

        self::setSuccess(200, $result);
        return true;
    }

    /**
     * Get authorized user info
     *
     * @param  $params
     * @return mixed
     */
    public function getMe($params)
    {
        self::requireScope('basic');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $params['id'] = Engine_Api::_()->user()->getViewer()->getIdentity();

        return $this->getItem($params);
    }

    /**
     * Get user info
     *
     * @param  array   $params
     * @return array
     */
    public function getItem($params)
    {
        self::requireScope('basic');

        $id = isset($params['id']) ? (int) $params['id'] : null;

        if (empty($id)) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $subject = Engine_Api::_()->user()->getUser($id);

        if (!$subject->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Member not found'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($subject);

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->requireAuthIsValid($subject, $viewer, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // check public settings
        $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_profile;
        if (!$require_check && !$this->isViewer()) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Check enabled
        if (!$subject->enabled && !$viewer->isAdmin()) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Check block
        if ($viewer->isBlockedBy($subject) && !$viewer->isAdmin()) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Increment view count
        if (!$subject->isSelf($viewer)) {
            $subject->view_count++;
            $subject->save();
        }

        $fields = $this->_getFields($params, 'detail');

        $data = Ynrestapi_Helper_Meta::exportOne($subject, $fields);

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Update authorized user profile
     *
     * @param  $params
     * @return mixed
     */
    public function postMeInfo($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $params['id'] = Engine_Api::_()->user()->getViewer()->getIdentity();

        return $this->postInfo($params);
    }

    /**
     * Update user profile
     *
     * @todo validate required fields
     * @param  $params
     * @return mixed
     */
    public function postInfo($params)
    {
        self::requireScope('settings');

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $id = $params['id'];
        $subject = Engine_Api::_()->getItem('user', $id);
        Engine_Api::_()->core()->setSubject($subject);

        if (!$this->requireAuthIsValid(null, null, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        $user = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();

        // General form w/o profile type
        $aliasedFields = $user->fields()->getFieldsObjectsByAlias();
        $topLevelId = 0;
        $topLevelValue = null;
        if (isset($aliasedFields['profile_type'])) {
            $aliasedFieldValue = $aliasedFields['profile_type']->getValue($user);
            $topLevelId = $aliasedFields['profile_type']->field_id;
            $topLevelValue = (is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null);
            if (!$topLevelId || !$topLevelValue) {
                $topLevelId = null;
                $topLevelValue = null;
            }
        }

        // Get form
        $form = new Fields_Form_Standard(array(
            'item' => Engine_Api::_()->core()->getSubject(),
            'topLevelId' => $topLevelId,
            'topLevelValue' => $topLevelValue,
            'hasPrivacy' => true,
            'privacyValues' => null,
        ));

        $fieldMaps = array();
        $privacies = array();
        $privacyOptions = Fields_Api_Core::getFieldPrivacyOptions();

        // Prepare values
        if (!empty($params['gender']) && !($params['gender'] = $this->getGenderOptionId($params['gender']))) {
            self::setParamError('gender');
            return false;
        }

        if (!empty($params['birthdate'])) {
            $dateParts = explode('-', $params['birthdate']);
            if (count($dateParts) !== 3 || !checkdate($dateParts[1], $dateParts[2], $dateParts[0])) {
                self::setParamError('birthdate');
                return false;
            }
        }

        // Set values
        foreach ($form->getElements() as $key => $el) {
            $parts = explode('_', $key);
            if (count($parts) !== 3) {
                continue;
            }
            list($parent_id, $option_id, $field_id) = $parts;
            $field = Engine_Api::_()->fields()->getField($field_id, 'user', true);
            $fieldMaps[$key] = $field->type;
            if (isset($params[$field->type])) {
                $el->setValue($params[$field->type]);
            }
            if (isset($params['privacy']) && isset($params['privacy'][$field->type])) {
                $privacy = $params['privacy'][$field->type];
                if (!array_key_exists($privacy, $privacyOptions)) {
                    self::setParamError('privacy[' . $field->type . ']');
                    return false;
                }
                $privacies[$key] = $privacy;
            }
        }

        $form->setPrivacyValues($privacies);
        $values = $form->getValues();

        if (false !== ($dateField = array_search('birthdate', $fieldMaps)) && isset($dateParts)) {
            $values[$dateField] = array(
                'day' => $dateParts[2],
                'month' => $dateParts[1],
                'year' => $dateParts[0],
            );
        }

        if (!$form->isValid($values)) {
            $messages = $form->getMessages();
            foreach ($messages as $key => $value) {
                $param = isset($fieldMaps[$key]) ? $fieldMaps[$key] : $key;
                self::setParamError($param, 400, 'invalid_parameter', implode("\n", $value));
            }
            return false;
        }

        $form->saveValues();

        // Update display name
        $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
        $user->setDisplayName($aliasValues);
        //$user->modified_date = date('Y-m-d H:i:s');
        $user->save();

        // update networks
        Engine_Api::_()->network()->recalculate($user);

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * @param  $params
     * @return mixed
     */
    public function postMeExternalPhoto($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (empty($params['photo_id'])) {
            self::setParamError('photo_id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $photo = Engine_Api::_()->getItem('album_photo', $params['photo_id']);
        if (!$photo || !$photo->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Not Found.'));
            return false;
        }

        $album = $photo->getParent();
        if (!$album || !$album->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Not Found.'));
            return false;
        }

        Engine_Api::_()->core()->setSubject($photo);

        $user = Engine_Api::_()->user()->getViewer();

        if (!$photo->authorization()->isAllowed(null, 'view')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        // Process
        $db = $user->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            // Get the owner of the photo
            $photoOwnerId = null;
            if (isset($photo->user_id)) {
                $photoOwnerId = $photo->user_id;
            } else if (isset($photo->owner_id) && (!isset($photo->owner_type) || $photo->owner_type == 'user')) {
                $photoOwnerId = $photo->owner_id;
            }

            // if it is from your own profile album do not make copies of the image
            if ($photo instanceof Album_Model_Photo &&
                ($photoParent = $photo->getParent()) instanceof Album_Model_Album &&
                $photoParent->owner_id == $photoOwnerId &&
                $photoParent->type == 'profile') {

                // ensure thumb.icon and thumb.profile exist
                $newStorageFile = Engine_Api::_()->getItem('storage_file', $photo->file_id);
                $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
                if ($photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.profile')) {
                    try {
                        $tmpFile = $newStorageFile->temporary();
                        $image = Engine_Image::factory();
                        $image->open($tmpFile)
                            ->resize(200, 400)
                            ->write($tmpFile)
                            ->destroy();
                        $iProfile = $filesTable->createFile($tmpFile, array(
                            'parent_type' => $user->getType(),
                            'parent_id' => $user->getIdentity(),
                            'user_id' => $user->getIdentity(),
                            'name' => basename($tmpFile),
                        ));
                        $newStorageFile->bridge($iProfile, 'thumb.profile');
                        @unlink($tmpFile);
                    } catch (Exception $e) {
                        throw $e;
                    }
                }
                if ($photo->file_id == $filesTable->lookupFile($photo->file_id, 'thumb.icon')) {
                    try {
                        $tmpFile = $newStorageFile->temporary();
                        $image = Engine_Image::factory();
                        $image->open($tmpFile);
                        $size = min($image->height, $image->width);
                        $x = ($image->width - $size) / 2;
                        $y = ($image->height - $size) / 2;
                        $image->resample($x, $y, $size, $size, 48, 48)
                            ->write($tmpFile)
                            ->destroy();
                        $iSquare = $filesTable->createFile($tmpFile, array(
                            'parent_type' => $user->getType(),
                            'parent_id' => $user->getIdentity(),
                            'user_id' => $user->getIdentity(),
                            'name' => basename($tmpFile),
                        ));
                        $newStorageFile->bridge($iSquare, 'thumb.icon');
                        @unlink($tmpFile);
                    } catch (Exception $e) {
                        throw $e;
                    }
                }

                // Set it
                $user->photo_id = $photo->file_id;
                $user->save();

                // Insert activity
                // @todo maybe it should read "changed their profile photo" ?
                $action = Engine_Api::_()->getDbtable('actions', 'activity')
                    ->addActivity($user, $user, 'profile_photo_update',
                        '{item:$subject} changed their profile photo.');
                if ($action) {
                    // We have to attach the user himself w/o album plugin
                    Engine_Api::_()->getDbtable('actions', 'activity')
                        ->attachActivity($action, $photo);
                }
            }

            // Otherwise copy to the profile album
            else {
                $user->setPhoto($photo);

                // Insert activity
                $action = Engine_Api::_()->getDbtable('actions', 'activity')
                    ->addActivity($user, $user, 'profile_photo_update',
                        '{item:$subject} added a new profile photo.');

                // Hooks to enable albums to work
                $newStorageFile = Engine_Api::_()->getItem('storage_file', $user->photo_id);
                $event = Engine_Hooks_Dispatcher::_()
                    ->callEvent('onUserProfilePhotoUpload', array(
                        'user' => $user,
                        'file' => $newStorageFile,
                    ));

                $attachment = $event->getResponse();
                if (!$attachment) {
                    $attachment = $newStorageFile;
                }

                if ($action) {
                    // We have to attach the user himself w/o album plugin
                    Engine_Api::_()->getDbtable('actions', 'activity')
                        ->attachActivity($action, $attachment);
                }
            }

            $db->commit();
        }

        // Otherwise it's probably a problem with the database or the storage system (just throw it)
         catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $data = array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Set as profile photo'),
        );

        self::setSuccess(200, $data);
        return true;
    }

    /**
     * Update authorized user photo
     *
     * @param  $params
     * @return mixed
     */
    public function postMePhoto($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        $params['id'] = Engine_Api::_()->user()->getViewer()->getIdentity();

        return $this->postPhoto($params);
    }

    /**
     * Update user photo
     *
     * @param  params
     * @return mixed
     */
    public function postPhoto($params)
    {
        self::requireScope('settings');

        if (!$this->isViewer()) {
            self::setError(401, 'unauthorized_user', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            return false;
        }

        if (!isset($params['id'])) {
            self::setParamError('id', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        $id = $params['id'];

        if (empty($id) || !is_numeric($id)) {
            self::setParamError('id', 400, 'invalid_parameter', Zend_Registry::get('Zend_Translate')->_('Invalid parameter'));
            return false;
        }

        $user = Engine_Api::_()->user()->getUser($id);

        if ($user->getIdentity() <= 0) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('User not found'));
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$user->authorization()->isAllowed($viewer, 'edit')) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Forbidden'));
            return false;
        }

        try {
            $db = $user->getTable()->getAdapter();
            $db->beginTransaction();

            $isUpdate = false;

            if (isset($_FILES['Filedata'])) {
                $isUpdate = true;
                $user->setPhoto($_FILES['Filedata']);
            }

            if ($this->_isValidPhotoCropData($params)) {
                $isUpdate = true;
                $this->_cropPhoto($user, $params);
            }

            if (!$isUpdate) {
                self::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing photo or cropping data'));
                return false;
            }

            $iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update', '{item:$subject} added a new profile photo.');

            if ($action) {
                $event = Engine_Hooks_Dispatcher::_()
                    ->callEvent('onUserProfilePhotoUpload', array(
                        'user' => $user,
                        'file' => $iMain,
                    ));

                $attachment = $event->getResponse();
                if (!$attachment) {
                    $attachment = $iMain;
                }

                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
            }

            $db->commit();
        } catch (Engine_Image_Adapter_Exception $e) {
            // If an exception occurred within the image adapter, it's probably an invalid image
            $db->rollBack();
            $form->addError(Zend_Registry::get('Zend_Translate')->_('The uploaded file is not supported or is corrupt.'));
        } catch (Exception $e) {
            // Otherwise it's probably a problem with the database or the storage system (just throw it)
            $db->rollBack();
            throw $e;
        }

        self::setSuccess(200);
        return true;
    }

    /**
     * @param $user
     * @param $params
     */
    private function _cropPhoto($user, $params)
    {
        $storage = Engine_Api::_()->storage();
        $iIcon = $storage->get($user->photo_id, 'thumb.icon');

        $x = (int) $params['crop_position_x'];
        $y = (int) $params['crop_position_y'];
        $w = (int) $params['crop_size'];
        $h = (int) $params['crop_size'];
        $iViewWidth = (int) $params['crop_photo_width'];
        $iViewHeight = (int) $params['crop_photo_height'];

        if ($iViewWidth >= 200) {
            // If the image width is over 200, we will use the profile photo to scale
            $iProfile = $storage->get($user->photo_id, 'thumb.profile');
            $fRatio = floatval($iViewWidth) / 200;
            $pName = $iProfile->getStorageService()->temporary($iProfile);
            $iName = dirname($pName) . '/nis_' . basename($pName);

            $x = $x / $fRatio;
            $y = $y / $fRatio;
            $w = $w / $fRatio;
            $h = $h / $fRatio;
        } else {
            // when the image width is smaller than 200, we will use the orginal file
            if (!is_null($_FILES['image']['tmp_name'])) {
                if (is_array($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
                    $pName = $_FILES['image']['tmp_name'];
                    $fileName = $_FILES['image']['name'];
                    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
                    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
                    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
                    $iName = $path . DIRECTORY_SEPARATOR . "nis_$base." . $extension;
                }
            } else {
                $iProfile = $storage->get($user->photo_id, 'thumb.profile');
                $pName = $iProfile->getStorageService()->temporary($iProfile);
                $iName = dirname($pName) . '/nis_' . basename($pName);
            }
        }

        $image = Engine_Image::factory();
        $image->open($pName)->resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48)->write($iName)->destroy();
        $iIcon->store($iName);

        // Remove temp files
        @unlink($iName);
    }

    /**
     * Verify photo cropping data
     */
    private function _isValidPhotoCropData($params)
    {
        return (isset($params['crop_position_x']) && is_numeric($params['crop_position_x'])
            && isset($params['crop_position_y']) && is_numeric($params['crop_position_y'])
            && isset($params['crop_size']) && is_numeric($params['crop_size'])
            && isset($params['crop_photo_width']) && is_numeric($params['crop_photo_width'])
            && isset($params['crop_photo_height']) && is_numeric($params['crop_photo_height']));
    }

    /**
     * Forgot password
     *
     * @param params
     */
    public function postForgotpassword($params)
    {
        self::requireScope('basic');

        $email = isset($params['email']) ? $params['email'] : '';

        if (empty($email)) {
            self::setParamError('email', 400, 'missing_parameter', Zend_Registry::get('Zend_Translate')->_('Missing parameter'));
            return false;
        }

        // Split email address up and disallow '..'
        if ((strpos($email, '..') !== false) || (!filter_var($email, FILTER_VALIDATE_EMAIL))) {
            self::setParamError('email', 400, 'invalid_parameter', Zend_Registry::get('Zend_Translate')->_('Invalid parameter'));
            return false;
        }

        // Check for existing user
        $user = Engine_Api::_()->getDbtable('users', 'user')->fetchRow(array('email = ?' => $email));
        if (!$user || !$user->getIdentity()) {
            self::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('No record found with this email'));
            return false;
        }

        // Check to make sure they're enabled
        if (!$user->enabled) {
            self::setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('This user account has not yet been verified or disabled by an admin.'));
            return false;
        }

        // Ok now we can do the fun stuff
        $forgotTable = Engine_Api::_()->getDbtable('forgot', 'user');
        $db = $forgotTable->getAdapter();
        $db->beginTransaction();

        try {
            // Delete any existing reset password codes
            $forgotTable->delete(array('user_id = ?' => $user->getIdentity()));

            // Create a new reset password code
            $code = base_convert(md5($user->salt . $user->email . $user->user_id . uniqid(time(), true)), 16, 36);
            $forgotTable->insert(array(
                'user_id' => $user->getIdentity(),
                'code' => $code,
                'creation_date' => date('Y-m-d H:i:s'),
            ));

            $view = Zend_Registry::get('Zend_View');
            // Send user an email
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'core_lostpassword', array(
                'host' => $_SERVER['HTTP_HOST'],
                'email' => $user->email,
                'date' => time(),
                'recipient_title' => $user->getTitle(),
                'recipient_link' => $user->getHref(),
                'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
                'object_link' => $view->url(array(
                    'module' => 'user',
                    'controller' => 'auth',
                    'action' => 'reset',
                    'code' => $code,
                    'uid' => $user->getIdentity(),
                )),
                'queue' => false,
            ));

            $db->commit();

            self::setSuccess(200);
            return true;
        } catch (Exception $e) {
            $db->rollBack();

            throw $e;
        }
    }
}
