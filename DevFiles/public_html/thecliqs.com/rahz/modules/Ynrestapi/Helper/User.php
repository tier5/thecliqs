<?php

class Ynrestapi_Helper_User extends Ynrestapi_Helper_Base
{
    /**
     * @var mixed
     */
    private $_cachedUserFields = null;

    public function getYnrestapiApi()
    {
        return Engine_Api::_()->getApi('user', 'ynrestapi');
    }

    public function field_id()
    {
        $this->data['id'] = $this->entry->getIdentity();
    }

    public function field_email()
    {
        $this->data['email'] = $this->entry->email;
    }

    public function field_username()
    {
        $this->data['username'] = $this->entry->username;
    }

    public function field_timezone()
    {
        $this->data['timezone'] = $this->entry->timezone;
    }

    public function field_timezone_label()
    {
        $this->data['timezone_label'] = $this->getYnrestapiApi()->getTimezoneLabel($this->entry->timezone);
    }

    public function field_locale()
    {
        $this->data['locale'] = $this->entry->locale;
    }

    public function field_locale_label()
    {
        $this->data['locale_label'] = $this->getYnrestapiApi()->getLocaleLabel($this->entry->locale);
    }

    /**
     * @return null
     */
    public function field_total_photo()
    {
        $table = $this->getWorkingTable('photos', 'album');

        if (!$table) {
            $this->data['total_photo'] = 0;
            return;
        }

        $albumIds = $this->_getAlbumsCanView($this->entry);
        $albumIds[] = 0;

        $select = $table->select()
            ->where('owner_type = ?', $this->entry->getType())
            ->where('owner_id = ?', $this->entry->getIdentity())
            ->where('album_id IN (?)', $albumIds)
            ->order('photo_id desc');

        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($limit);

        $total = $paginator->getTotalItemCount();

        $this->data['total_photo'] = $total;
    }

    /**
     * @param  $owner
     * @return mixed
     */
    private function _getAlbumsCanView($owner = null)
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $albumTable = $this->getWorkingTable('albums', 'album');
        $photoTable = $this->getWorkingTable('photos', 'album');

        $select = $albumTable->select();

        if ($owner) {
            $select->where('owner_id=?', $owner->getIdentity());
            $select->where('owner_type=?', $owner->getType());
        }

        $albumIds = array();

        foreach ($albumTable->fetchAll($select) as $album) {
            $bCanView = Engine_Api::_()->authorization()->isAllowed($album, $viewer, 'view');
            if ($bCanView) {
                $albumIds[] = $album->getIdentity();
            }
        }

        return $albumIds;
    }

    /**
     * @return null
     */
    public function field_photos()
    {
        $limit = defined('LIMIT_FIELD_PHOTOS') ? LIMIT_FIELD_PHOTOS : 3;
        $table = $this->getWorkingTable('photos', 'album');

        if (!$table) {
            $this->data['total_photo'] = 0;
            $this->data['photos'] = array();
            return;
        }

        $albumIds = $this->_getAlbumsCanView($this->entry);
        $albumIds[] = 0;

        $select = $table->select()
            ->where('owner_type = ?', $this->entry->getType())
            ->where('owner_id = ?', $this->entry->getIdentity())
            ->where('album_id IN (?)', $albumIds)
            ->order('photo_id desc');

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
    public function field_can_send_message()
    {
        $canSendMessage = false;

        $viewer = $this->getViewer();
        $user = $this->entry;
        $levelAuth = Engine_Api::_()->authorization()->getAdapter('levels');

        if (!$viewer || !$viewer->getIdentity()) {
            return $this->data['can_send_message'] = $canSendMessage;
        }

        /**
         * action belong to owner only.
         */
        if ($viewer->getGuid() == $user->getGuid()) {
            return $this->data['can_send_message'] = $canSendMessage;
        }

        $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');

        /**
         * check can send message permission.
         */
        if ($permission) {
            $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
            if ($messageAuth == 'none') {
                // do not allow send message feature.
                $canSendMessage = false;
            } elseif ($messageAuth == 'friends') {
                // Get data
                $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

                if (!$direction) {
                    $friendship_status = $viewer->membership()->getRow($user);
                } else {
                    $friendship_status = $user->membership()->getRow($viewer);
                }

                if ($friendship_status && $friendship_status->active) {
                    $canSendMessage = true;
                }
            } else {
                // everyone can send message
                $canSendMessage = true;
            }
        }

        $this->data['can_send_message'] = $canSendMessage;
    }

    /**
     * @return null
     */
    public function field_friend_status()
    {
        $viewer = $this->getViewer();

        if (!$viewer || !$viewer->getIdentity()) {
            return;
        }

        $row = $viewer->membership()->getRow($this->entry);

        if (null == $row) {
            $friendStatus = 'not_friend';
        } elseif ($row->user_approved == 0) {
            $friendStatus = 'is_sent_request';
        } elseif ($row->resource_approved == 0) {
            $friendStatus = 'is_sent_request_by';
        } else {
            $friendStatus = 'is_friend';
        }

        $this->data['friend_status'] = $friendStatus;
    }

    public function field_total_mutual_friend()
    {
        $user = $this->entry;
        $viewer = $this->getViewer();

        $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
        $friendsName = $friendsTable->info('name');

        // Mututal friends/following mode
        $sql = "SELECT `user_id` FROM `{$friendsName}` WHERE (`active`= 1 and `resource_id`={$user->getIdentity()})
            and `user_id` in (select `resource_id` from `engine4_user_membership` where (`user_id`={$viewer->getIdentity()} and `active`= 1))";
        $friends = $friendsTable->getAdapter()->fetchcol($sql);

        $this->data['total_mutual_friend'] = count($friends);
    }

    public function field_total_friend()
    {
        $user_id = $this->entry->getIdentity();

        $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
        $membershipName = $membershipTable->info('name');
        $select = $membershipTable->select()->from($membershipTable, new Zend_Db_Expr('COUNT(resource_id)'));

        $this->data['total_friend'] = (int) $select
            ->where("{$membershipName}.user_id = ?", $user_id)
            ->where('active = 1')
            ->limit(1)
            ->query()
            ->fetchColumn();
    }

    public function field_block_status()
    {
        $viewer = $this->getViewer();
        $user = $this->entry;

        $blockStatus = 'no_block';

        if ($user->isBlockedBy($viewer)) {
            $blockStatus = 'is_blocked';
        } elseif ($viewer->isBlockedBy($user) && !$viewer->isAdmin()) {
            $blockStatus = 'is_blocked_by';
        }

        $this->data['block_status'] = $blockStatus;
    }

    /**
     * @return array
     */
    public function _getUserFields()
    {
        $result = array();

        $oViewer = $this->getViewer();
        $user_id = $this->entry->getIdentity();
        $oUser = Engine_Api::_()->user()->getUser($user_id);

        // Load fields view helpers
        $view = Zend_Registry::get('Zend_View');
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

        // Values
        $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($oUser);

        // Calculate viewer-subject relationship
        $usePrivacy = ($oUser instanceof User_Model_User);
        if ($usePrivacy) {
            $relationship = 'everyone';
            if ($oViewer && $oViewer->getIdentity()) {
                if ($oViewer->getIdentity() == $oUser->getIdentity()) {
                    $relationship = 'self';
                } else
                if ($oViewer->membership()->isMember($oUser, true)) {
                    $relationship = 'friends';
                } else {
                    $relationship = 'registered';
                }
            }
        }

        $show_hidden = $oViewer->getIdentity() ? ($oUser->getOwner()->isSelf($oViewer) || 'admin' === Engine_Api::_()->getItem('authorization_level', $oViewer->level_id)->type) : false;

        foreach ($fieldStructure as $index => $map) {
            $field = $map->getChild();
            $value = $field->getValue($oUser);
            if (!$field || $field->type == 'profile_type') {
                continue;
            }

            if (!$field->display && !$show_hidden) {
                continue;
            }

            $isHidden = !$field->display;

            // Get first value object for reference
            $firstValue = $value;
            if (is_array($value)) {
                $firstValue = $value[0];
            }

            // Evaluate privacy
            if ($usePrivacy && !empty($firstValue->privacy) && $relationship != 'self') {
                if ($firstValue->privacy == 'self' && $relationship != 'self') {
                    $isHidden = true;
                    //continue;
                } else
                if ($firstValue->privacy == 'friends' && ($relationship != 'friends' && $relationship != 'self')) {
                    $isHidden = true;
                    //continue;
                } else
                if ($firstValue->privacy == 'registered' && $relationship == 'everyone') {
                    $isHidden = true;
                    //continue;
                }
            }

            if ((!$isHidden || $show_hidden) && $firstValue) {
                $result[$field->type] = $firstValue->value;
            }
        }

        return $result;
    }

    /**
     * @param $type
     */
    private function _getUserField($type)
    {
        if (empty($this->_cachedUserFields)) {
            $this->_cachedUserFields = $this->_getUserFields();
        }

        return isset($this->_cachedUserFields[$type]) ? $this->_cachedUserFields[$type] : '';
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_first_name($return = false)
    {
        $result = $this->_getUserField('first_name');

        if ($return) {
            return $result;
        }

        $this->data['first_name'] = $result;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_last_name($return = false)
    {
        $result = $this->_getUserField('last_name');

        if ($return) {
            return $result;
        }

        $this->data['last_name'] = $result;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_birthdate($return = false)
    {
        $result = $this->_getUserField('birthdate');

        if ($return) {
            return $result;
        }

        $this->data['birthdate'] = $result;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_about_me($return = false)
    {
        $result = $this->_getUserField('about_me');

        if ($return) {
            return $result;
        }

        $this->data['about_me'] = $result;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_facebook($return = false)
    {
        $result = $this->_getUserField('facebook');

        if ($return) {
            return $result;
        }

        $this->data['facebook'] = $result;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_twitter($return = false)
    {
        $result = $this->_getUserField('twitter');

        if ($return) {
            return $result;
        }

        $this->data['twitter'] = $result;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_aim($return = false)
    {
        $result = $this->_getUserField('aim');

        if ($return) {
            return $result;
        }

        $this->data['aim'] = $result;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_website($return = false)
    {
        $result = $this->_getUserField('website');

        if ($return) {
            return $result;
        }

        $this->data['website'] = $result;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_gender($return = false)
    {
        $result = (int) $this->_getUserField('gender');

        if ($return) {
            return $result;
        }

        $this->data['gender'] = $result;
    }

    public function field_gender_label()
    {
        $genderOptions = $this->getYnrestapiApi()->getGenderOptions();
        $gender = $this->field_gender(true);
        $genderLabel = '';

        foreach ($genderOptions as $option) {
            if ($option['key'] == $gender) {
                $genderLabel = $option['val'];
            }
        }

        $this->data['gender_label'] = $genderLabel;
    }

    /**
     * @param $iLimit
     */
    public function field_friends($iLimit = 3)
    {
        $oUser = $this->entry;
        $oViewer = $this->getViewer();

        $membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
        $membershipName = $membershipTable->info('name');

        $select = $membershipTable->select()
            ->from(array('m1' => $membershipName), array('resource_id'))
            ->setIntegrityCheck(false);

        $select->where('`m1`.`user_id` = ?', $oUser->getIdentity())->where('`m1`.`active` = ?', 1);

        $paginator = Zend_Paginator::factory($select);

        $select->limit($iLimit);

        $ids = array();

        foreach ($membershipTable->fetchAll($select) as $entry) {
            $ids[] = $entry->resource_id;
        }

        // Get the items
        $items = array();

        $fields = array('simple');

        $table = Engine_Api::_()->getItemTable('user');

        foreach ($table->find($ids) as $entry) {
            $items[] = Ynrestapi_Helper_Meta::getInstance()->getModelHelper($entry)->toArray($fields);
        }

        $this->data['friends'] = $items;
    }

    public function field_subscription_status()
    {
        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');

        $this->data['subscription_status'] = $subscriptionsTable->check($this->entry) ? 1 : 0;
    }

    public function field_timezone_options()
    {
        $timezone = array(
            'US/Pacific' => '(UTC-8) Pacific Time (US & Canada)',
            'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
            'US/Central' => '(UTC-6) Central Time (US & Canada)',
            'US/Eastern' => '(UTC-5) Eastern Time (US & Canada)',
            'America/Halifax' => '(UTC-4)  Atlantic Time (Canada)',
            'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
            'Pacific/Honolulu' => '(UTC-10) Hawaii (US)',
            'Pacific/Samoa' => '(UTC-11) Midway Island, Samoa',
            'Etc/GMT-12' => '(UTC-12) Eniwetok, Kwajalein',
            'Canada/Newfoundland' => '(UTC-3:30) Canada/Newfoundland',
            'America/Buenos_Aires' => '(UTC-3) Brasilia, Buenos Aires, Georgetown',
            'Atlantic/South_Georgia' => '(UTC-2) Mid-Atlantic',
            'Atlantic/Azores' => '(UTC-1) Azores, Cape Verde Is.',
            'Europe/London' => 'Greenwich Mean Time (Lisbon, London)',
            'Europe/Berlin' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid',
            'Europe/Athens' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe',
            'Europe/Moscow' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow',
            'Iran' => '(UTC+3:30) Tehran',
            'Asia/Dubai' => '(UTC+4) Abu Dhabi, Kazan, Muscat',
            'Asia/Kabul' => '(UTC+4:30) Kabul',
            'Asia/Yekaterinburg' => '(UTC+5) Islamabad, Karachi, Tashkent',
            'Asia/Calcutta' => '(UTC+5:30) Bombay, Calcutta, New Delhi',
            'Asia/Katmandu' => '(UTC+5:45) Nepal',
            'Asia/Omsk' => '(UTC+6) Almaty, Dhaka',
            'India/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
            'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
            'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
            'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
            'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
            'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
            'Asia/Magadan' => '(UTC+11) Magadan, Soloman Is., New Caledonia',
            'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
        );

        $timezoneOptions = array();
        foreach ($timezone as $key => $value) {
            $timezoneOptions[] = array(
                'id' => $key,
                'title' => $value,
            );
        }

        $this->data['timezone_options'] = $timezoneOptions;
    }

    public function field_locale_options()
    {
        $locale = Zend_Registry::get('Locale');

        $localeMultiKeys = array_merge(
            array_keys(Zend_Locale::getLocaleList())
        );
        $localeMultiOptions = array();
        $languages = Zend_Locale::getTranslationList('language', $locale);
        $territories = Zend_Locale::getTranslationList('territory', $locale);
        foreach ($localeMultiKeys as $key) {
            if (!empty($languages[$key])) {
                $localeMultiOptions[$key] = $languages[$key];
            } else {
                $locale = new Zend_Locale($key);
                $region = $locale->getRegion();
                $language = $locale->getLanguage();
                if ((!empty($languages[$language]) && (!empty($territories[$region])))) {
                    $localeMultiOptions[$key] = $languages[$language] . ' (' . $territories[$region] . ')';
                }
            }
        }
        $localeMultiOptions = array_merge(array('auto' => '[Automatic]'), $localeMultiOptions);

        $localeOptions = array();
        foreach ($localeMultiOptions as $key => $value) {
            $localeOptions[] = array(
                'id' => $key,
                'title' => $value,
            );
        }

        $this->data['locale_options'] = $localeOptions;
    }

    /**
     * @return null
     */
    public function field_privacy_search()
    {
        $user = $this->entry;

        if (!Engine_Api::_()->getDbtable('permissions', 'authorization')->isAllowed($user, $user, 'search')) {
            return;
        }

        $this->data['privacy_search'] = $this->entry->search;
    }

    public function field_privacy_view()
    {
        $privacyView = '';

        $roles = array('owner', 'member', 'network', 'registered', 'everyone');
        $auth = Engine_Api::_()->authorization()->context;
        $user = $this->entry;

        foreach ($roles as $role) {
            if (1 === $auth->isAllowed($user, $role, 'view')) {
                $privacyView = $role;
            }
        }

        $this->data['privacy_view'] = $privacyView;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_privacy_view_options($return = false)
    {
        $user = $this->entry;

        $availableLabels = array(
            'owner' => 'Only Me',
            'member' => 'Only My Friends',
            'network' => 'Friends & Networks',
            'registered' => 'All Registered Members',
            'everyone' => 'Everyone',
        );

        // Init profile view
        $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_view');
        $view_options = array_intersect_key($availableLabels, array_flip($view_options));

        if ($return) {
            return $view_options;
        }

        $viewOptions = array();
        foreach ($view_options as $key => $value) {
            $viewOptions[] = array(
                'id' => $key,
                'title' => $value,
            );
        }

        $this->data['privacy_view_options'] = $viewOptions;
    }

    public function field_privacy_comment()
    {
        $privacyComment = '';

        $roles = array('owner', 'member', 'network', 'registered', 'everyone');
        $auth = Engine_Api::_()->authorization()->context;
        $user = $this->entry;

        foreach ($roles as $role) {
            if (1 === $auth->isAllowed($user, $role, 'comment')) {
                $privacyComment = $role;
            }
        }

        $this->data['privacy_comment'] = $privacyComment;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_privacy_comment_options($return = false)
    {
        $user = $this->entry;

        $availableLabelsComment = array(
            'owner' => 'Only Me',
            'member' => 'Only My Friends',
            'network' => 'Friends & Networks',
            'registered' => 'All Registered Members',
        );

        // Init profile comment
        $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_comment');
        $comment_options = array_intersect_key($availableLabelsComment, array_flip($comment_options));

        if ($return) {
            return $comment_options;
        }

        $commentOptions = array();
        foreach ($comment_options as $key => $value) {
            $commentOptions[] = array(
                'id' => $key,
                'title' => $value,
            );
        }

        $this->data['privacy_comment_options'] = $commentOptions;
    }

    public function field_privacy_activity()
    {
        $user = $this->entry;

        $actionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getEnabledActionTypesAssoc();
        unset($actionTypes['signup']);
        unset($actionTypes['postself']);
        unset($actionTypes['post']);
        unset($actionTypes['status']);
        $actionTypesEnabled = Engine_Api::_()->getDbtable('actionSettings', 'activity')->getEnabledActions($user);

        $this->data['privacy_activity'] = $actionTypesEnabled;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_privacy_activity_options($return = false)
    {
        $actionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getEnabledActionTypesAssoc();
        unset($actionTypes['signup']);
        unset($actionTypes['postself']);
        unset($actionTypes['post']);
        unset($actionTypes['status']);

        if ($return) {
            return $actionTypes;
        }

        $activityOptions = array();
        foreach ($actionTypes as $key => $value) {
            $activityOptions[] = array(
                'id' => $key,
                'title' => $value,
            );
        }

        $this->data['privacy_activity_options'] = $activityOptions;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_available_networks($return = false)
    {
        $viewer = $this->viewer();
        $available_networks = Network_Model_Network::getUserNetworks($viewer);

        if ($return) {
            return $available_networks;
        }

        $availableNetworks = array();
        foreach ($available_networks as $network) {
            $availableNetworks[] = array(
                'id' => $network['id'],
                'title' => $network['title'],
            );
        }

        $this->data['available_networks'] = $availableNetworks;
    }

    /**
     * @param  $return
     * @return mixed
     */
    public function field_current_networks($return = false)
    {
        $viewer = $this->viewer();
        $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($viewer)
            ->order('engine4_network_networks.title ASC');
        $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);

        if ($return) {
            return $networks;
        }

        $currentNetworks = array();
        foreach ($networks as $network) {
            $currentNetworks[] = array(
                'id' => $network->network_id,
                'title' => $network->title,
                'total_member' => $network->membership()->getMemberCount(),
                'can_leave' => ($network->assignment == 0),
            );
        }

        $this->data['current_networks'] = $currentNetworks;
    }

    public function field_status()
    {
        $this->data['status'] = !empty($this->entry->status) ? $this->entry->status : '';
    }

    public function field_listing()
    {
        $this->field_id();
        $this->field_title();
    }

    public function field_detail()
    {
        $this->field_id();
        $this->field_title();
        $this->field_imgs();
    }

    public function field_settings_general()
    {
        $this->field_email();
        $this->field_username();
        $this->field_timezone();
        $this->field_timezone_options();
        $this->field_locale();
        $this->field_locale_options();
    }

    public function field_settings_privacy()
    {
        $this->field_privacy_search();
        $this->field_privacy_view();
        $this->field_privacy_view_options();
        $this->field_privacy_comment();
        $this->field_privacy_comment_options();
        $this->field_privacy_activity();
        $this->field_privacy_activity_options();
    }
}
