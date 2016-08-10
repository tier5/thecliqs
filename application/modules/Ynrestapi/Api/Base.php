<?php

/**
 * class Ynrestapi_Api_Base
 */
class Ynrestapi_Api_Base extends Core_Api_Abstract
{
    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $mainItemType;

    /**
     * @var array
     */
    protected $availablePrivacyOptions = array(
        'everyone' => 'Everyone',
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me',
    );

    /**
     * @var array
     */
    static $pageNameMap = array(
        'advalbum_album' => 'album_album_view',
        'album' => 'album_album_view',
        'advalbum_photo' => 'album_photo_view',
        'photo' => 'album_photo_view',
        'blog' => 'blog_index_view',
        'ynblog' => 'ynblog_index_view',
        'music_playlist' => 'music_playlist_view',
        'mp3music_album' => 'mp3music_album_album',
        'mp3music_playlist' => 'mp3music_playlist_playlist',
        'classified' => 'classified_index_view',
        'poll' => 'poll_poll_view',
        'video' => 'video_index_view',
        'ynvideo' => 'ynvideo_index_view',
    );

    /**
     * @var array
     */
    static $activityPageMap = array(
        'activity' => 'user_index_home',
        'ynfeed' => 'user_index_home',
        'user' => 'user_profile_index',
        'event' => 'event_profile_index',
        'ynevent' => 'ynevent_profile_index',
        'group' => 'group_profile_index',
        'advgroup' => 'advgroup_profile_index',
        'ynlistings_listing' => 'ynlistings_index_view',
        'ynbusinesspages_business' => 'ynbusinesspages_profile_index',
        'ynjobposting_company' => 'ynjobposting_company_detail',
    );

    /**
     * @var array
     */
    static $workingTypes = array();

    /**
     * @var array
     */
    protected $categoryAssoc = array();

    /**
     * @var Ynrestapi_Service_Response
     */
    private static $_response;

    /**
     * Constuctor
     */
    public function __construct()
    {
        $this->module = 'core';
        $this->mainItemType = 'user';
    }

    public function getViewer()
    {
        return Engine_Api::_()->user()->getViewer();
    }

    /**
     * @param $sType
     * @param $iId
     */
    public function getWorkingItem($sType, $iId)
    {
        return Engine_Api::_()->getItem($this->getWorkingType($sType), $iId);
    }

    /**
     * @param $sType
     */
    public function getWorkingItemTable($sType)
    {
        return Engine_Api::_()->getItemTable($this->getWorkingType($sType));
    }

    /**
     * get working item type.
     */
    public function getWorkingType($type)
    {
        if (isset(self::$workingTypes[$type])) {
            return self::$workingTypes[$type];
        }

        if (Engine_Api::_()->hasItemType($type)) {
            return self::$workingTypes[$type] = $type;
        }

        $parts = explode('_', $type, 2);
        if (count($parts) > 1) {
            $module = $parts[0];
            $track = $parts[1];
        } else {
            $module = $type;
            $track = $type;
        }

        $module = $this->getWorkingModule($module);
        return self::$workingTypes[$type] = $module . '_' . $track;
    }

    /**
     * @param  $type
     * @return mixed
     */
    public function getActivityType($type)
    {
        $parts = explode('_', $type, 2);

        if (count($parts) > 1) {
            return $this->getWorkingModule($module) . '_' . $parts[1];
        } else {
            return $type;
        }
    }

    /**
     * @param $module
     */
    public function getWorkingModule($module = null)
    {
        return Ynrestapi_Helper_Meta::getWorkingModule($module ? $module : $this->module);
    }

    /**
     * @param $api
     * @param $module
     */
    public function getWorkingApi($api = 'core', $module = null)
    {
        $module = $this->getWorkingModule($module);
        return Engine_Api::_()->getApi($api, $module);
    }

    /**
     * @param $table
     * @param $module
     */
    public function getWorkingTable($table, $module = null)
    {
        $module = $this->getWorkingModule($module);
        return Engine_Api::_()->getDbTable($table, $module);
    }

    /**
     * @param $id
     */
    public function getCategoryName($id)
    {
        $assoc = $this->loadCategoryAssoc();
        return isset($assoc[$id]) ? $assoc[$id] : '';
    }

    /**
     * @return mixed
     */
    public function loadCategoryAssoc()
    {
        if ($this->categoryAssoc) {
            return $this->categoryAssoc;
        }

        $table = $this->getWorkingTable('categories', $this->module);

        $named = array_pop(array_intersect(array(
            'category_name',
            'title',
        ), array_values($table->info('cols'))));

        $select = $table->select()->order($named);

        foreach ($table->fetchAll($select) as $cate) {
            $this->categoryAssoc[$cate->getIdentity()] = $cate->{$named};
        }

        return $this->categoryAssoc;
    }

    /**
     * get get category options
     */
    public function categories()
    {
        foreach ($this->loadCategoryAssoc() as $id => $title) {
            $options[] = array(
                'id' => intval($id),
                'title' => $title,
            );
        }

        return $options;
    }

    /**
     * @param  $entry
     * @param  $list
     * @param  $action
     * @return mixed
     */
    public function getPrivacyValue($entry, $list, $action)
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $type = $this->getWorkingType($entry->getType());
        $availableLabels = $this->availablePrivacyOptions;
        $auth = Engine_Api::_()->authorization()->context;

        if (!$viewer || !$viewer->getIdentity()) {
            return array();
        }

        // Element: auth_view
        $options = (array) Engine_Api::_()
            ->authorization()
            ->getAdapter('levels')
            ->getAllowed($type, $viewer, $list);

        $options = array_reverse(array_intersect_key($availableLabels, array_flip($options)));
        $result = array();

        foreach ($options as $role => $label) {
            if ($auth->isAllowed($entry, $role, $action)) {
                $result = array(
                    'id' => $role,
                    'title' => Zend_Registry::get('Zend_Translate')->_($label),
                );
            }
        }

        /**
         * init if empty values.
         */
        if (empty($result) && count($options)) {
            foreach ($options as $role => $label) {
                $result = array(
                    'id' => $role,
                    'title' => Zend_Registry::get('Zend_Translate')->_($label),
                );
            }
        }

        return $result;
    }

    /**
     * etc: [{id: everyone, title: "everyone"}, ... ]
     * @return array[{id: int, title: label}] // options
     */
    public function getPrivacyOptions($itemType, $action)
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $type = $this->getWorkingType($itemType);
        $availableLabels = $this->availablePrivacyOptions;

        if (!$viewer || !$viewer->getIdentity()) {
            return array();
        }

        // Element: auth_view
        $options = (array) Engine_Api::_()
            ->authorization()
            ->getAdapter('levels')
            ->getAllowed($type, $viewer, $action);

        $options = array_intersect_key($availableLabels, array_flip($options));
        $result = array();
        foreach ($options as $key => $title) {
            $result[] = array(
                'id' => $key,
                'title' => Zend_Registry::get('Zend_Translate')->_($title),
            );
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function view_options()
    {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_view');
    }

    /**
     * @return mixed
     */
    public function photoOptions()
    {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_photo');
    }

    /**
     * @return mixed
     */
    public function eventOptions()
    {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_event');
    }

    /**
     * @return mixed
     */
    public function comment_options()
    {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_comment');
    }

    /**
     * @return mixed
     */
    public function inviteOptions()
    {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_invite');
    }

    /**
     * form add
     */
    public function formadd($aData)
    {
        $response = array(
            'view_options' => $this->view_options(),
            'comment_options' => $this->comment_options(),
            'category_options' => $this->categories(),
        );

        return $response;
    }

    /**
     * @param  $itemType
     * @return mixed
     */
    public function getCommentOptions($itemType)
    {
        $options = array(
            'can_view_comments' => 0,
        );

        if (!isset($itemType) || !Engine_Api::_()->hasModuleBootstrap('yncomment')) {
            return $options;
        }

        // core.comments, yncomment.comments
        $pageName = self::$pageNameMap[$this->getWorkingModule($itemType)];

        if (!$pageName) {
            // return if there is no map page
            return $options;
        }

        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $contentTable = Engine_Api::_()->getDbTable('content', 'core');
        $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $pageName));
        // get content row of yncomment content

        if (!$pageObject) {
            // return if there is no map page
            return $options;
        }

        $contentRow = $contentTable->fetchRow(array(
            'page_id = ?' => $pageObject->page_id,
            'name IN (?)' => array('core.comments', 'yncomment.comments'),
        ));

        if (!$contentRow) {
            // return if there is no map page
            return $options;
        }

        $options['can_view_comments'] = 1;

        return $options;
    }

    /**
     * enabled int,
     * )
     * @param  $itemType
     * @return array(
     */

    public function getAdvancedCommentOptions($itemType)
    {
        // return enable for now
        $options = array(
            'is_enabled' => 0,
        );

        if (!isset($itemType) || !Engine_Api::_()->hasModuleBootstrap('yncomment')) {
            return $options;
        }

        if ($itemType == 'activity_action') {
            // get adv comment setting for activity action
            $modulesTable = Engine_Api::_()->getDbtable('modules', 'yncomment');
            $modules = $modulesTable->fetchRow(array('resource_type = ?' => 'ynfeed'));
            $val = $modules->toArray();
        } else {
            // get comment setting for other item type
            // $pageName = self::$pageNameMap[$itemType];
            $pageName = self::$pageNameMap[$this->getWorkingModule($itemType)];

            if (!$pageName) {
                // return if there is no map page
                return $options;
            }
            $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
            $contentTable = Engine_Api::_()->getDbTable('content', 'core');
            $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $pageName));
            // get content row of yncomment content

            if (!$pageObject) {
                // return if there is no map page
                return $options;
            }

            $contentRow = $contentTable->fetchRow(array(
                'page_id = ?' => $pageObject->page_id,
                'name = ?' => 'yncomment.comments',
            ));

            if (!$contentRow) {
                // return if there is no map page
                return $options;
            }

            $val = $contentRow->toArray();
        }
        if ($val['params']) {

            // return corresponding data
            if (is_array($val['params'])) {
                $aParams = $val['params'];
            } else {
                $aParams = Zend_Json_Decoder::decode($val['params']);
            }

            // enable setting for activity feed, enable if content present for other item
            $enabled = isset($aParams['enabled']) ? intval($aParams['enabled']) : 1;
            $taggingContent = !empty($aParams['taggingContent']) ? $aParams['taggingContent'] : array();
            $showComposerOptions = !empty($aParams['showComposerOptions']) ? $aParams['showComposerOptions'] : array();
            // feed always show reply (called show as nested)
            // showAsNested = 1 : show replys on both comment and reply, 0 not show
            $showAsNested = isset($aParams['showAsNested']) ? intval($aParams['showAsNested']) : 1;
            // showAsLike : 1 like only, 0 : both like and disklike
            $showAsLike = isset($aParams['showAsLike']) ? intval($aParams['showAsLike']) : 1;
            $showDislikeUsers = isset($aParams['showDislikeUsers']) ? intval($aParams['showDislikeUsers']) : 0;
            $commentsorder = isset($aParams['commentsorder']) ? intval($aParams['commentsorder']) : 0;
            $showLikeWithoutIconInReplies = isset($aParams['showLikeWithoutIconInReplies']) ? intval($aParams['showLikeWithoutIconInReplies']) : 0;

            // parse options
            $options['is_enabled'] = $enabled;
            $options['tagging_content'] = $taggingContent;
            $options['attachment_types'] = $showComposerOptions;
            $options['can_reply'] = $showAsNested;
            $options['can_dislike'] = $showAsLike ? 0 : 1;
            $options['can_view_user_disliked'] = $showDislikeUsers;
            $options['comments_order'] = $commentsorder;
            $options['show_as_like'] = $showAsLike;
            $options['show_like_without_icon_in_replies'] = $showLikeWithoutIconInReplies;
        }

        return $options;
    }

    /**
     * Check if Advanced feed widget is added to the item's activity page
     * @param  null   $itemType
     * @return bool
     */
    public function hasAdvancedFeed($itemType = null)
    {
        if (!$itemType) {
            $itemType = 'activity';
        }

        // false if Advanced feed module is not installed and enabled, obviously
        if (!Engine_Api::_()->hasModuleBootstrap('ynfeed')) {
            return false;
        }

        // get page name from map table between object type and page name
        // this requires default page's names is not changed
        $pageName = self::$activityPageMap[$this->getWorkingModule($itemType)];

        // there is no map page implemented such as new module added
        if (!$pageName) {
            return false;
        }

        // get core page and core content table
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $contentTable = Engine_Api::_()->getDbTable('content', 'core');

        // get the page object
        $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $pageName));

        // cannot find page
        if (!$pageObject) {
            return false;
        }

        // is ynfeed.feed added in this page ?
        $contentRow = $contentTable->fetchRow(array(
            'page_id = ?' => $pageObject->page_id,
            'name = ?' => 'ynfeed.feed',
        ));

        if ($contentRow) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $timezone
     */
    public function getTimezoneLabel($timezone)
    {
        $timezones = array(
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

        return isset($timezones[$timezone]) ? $timezones[$timezone] : $timezone;
    }

    /**
     * @param  $localeKey
     * @return mixed
     */
    public function getLocaleLabel($localeKey)
    {
        if (empty($localeKey) || $localeKey == 'auto') {
            return 'Automatic';
        }

        // Init default locale
        $locale = Zend_Registry::get('Locale');

        $localeMultiKeys = array_merge(
            array_keys(Zend_Locale::getLocaleList())
        );

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
                } else {
                    $localeMultiOptions[$key] = $languages[$language];
                }
            }
        }

        return isset($localeMultiOptions[$localeKey]) ? $localeMultiOptions[$localeKey] : $localeKey;
    }

    /**
     * @return mixed
     */
    public function getGenderOptions()
    {
        $gender_options = array();

        $fieldTable = Engine_Api::_()->fields()->getTable('user', 'meta');
        $optionTable = Engine_Api::_()->fields()->getTable('user', 'options');

        $genderField = $fieldTable->fetchRow($fieldTable->select()->where('type=?', 'gender')->limit(1));

        if ($genderField) {
            $gender_select = $optionTable->select()->where('field_id=?', $genderField->field_id);

            foreach ($optionTable->fetchAll($gender_select) as $entry) {
                $gender_options[] = array('key' => $entry->option_id, 'val' => $entry->label);
            };
        }

        return $gender_options;
    }

    /**
     * @param  $genderLabel
     * @return mixed
     */
    public function getGenderOptionId($genderLabel)
    {
        $fieldTable = Engine_Api::_()->fields()->getTable('user', 'meta');
        $optionTable = Engine_Api::_()->fields()->getTable('user', 'options');

        $genderField = $fieldTable->fetchRow($fieldTable->select()->where('type=?', 'gender')->limit(1));

        if ($genderField) {
            $gender_select = $optionTable->select()->where('field_id=?', $genderField->field_id);

            foreach ($optionTable->fetchAll($gender_select) as $entry) {
                if (strcasecmp($genderLabel, $entry->label) == 0) {
                    return $entry->option_id;
                }
            };
        }

        return false;
    }

    public function isViewer()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }

        return true;
    }

    /**
     * @param  $requiredType
     * @return mixed
     */
    public function requireSubjectIsValid($requiredType = null)
    {
        try
        {
            $subject = Engine_Api::_()->core()->getSubject();
        } catch (Exception $e) {
            $subject = null;
        }

        $ret = true;

        if (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity()) {
            $ret = false;
        } else if (null !== $requiredType && $subject->getType() != $requiredType) {
            $ret = false;
        }

        return $ret;
    }

    /**
     * @param  $resource
     * @param  null        $role
     * @param  null        $action
     * @return mixed
     */
    public function requireAuthIsValid($resource = null, $role = null, $action = null)
    {
        if (is_null($role)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($viewer->getIdentity()) {
                $role = $viewer;
            }
        }
        $ret = Engine_Api::_()->authorization()->isAllowed(
            $resource,
            $role,
            str_replace('-', '.', $action)
        );

        return $ret;
    }

    /**
     * Set access scope
     *
     * @param $scope
     */
    public static function requireScope($scope)
    {
        Ynrestapi_Service_Oauth_Server::verifyResourceRequest($scope);
    }

    /**
     * Get request
     *
     * @return object
     */
    protected function _getRequest()
    {
        return Zend_Controller_Front::getInstance()->getRequest();
    }

    /**
     * Get request param
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    protected function _getParam($key, $default = null)
    {
        $request = $this->_getRequest();
        if (null !== $request && null !== ($value = $this->_getRequest()->getParam($key))) {
            return $value;
        }

        return $default;
    }

    /**
     * Get request params
     *
     * @return array
     */
    protected function _getAllParams()
    {
        $params = array();

        if (null !== ($request = $this->_getRequest())) {
            $params = array_merge($request->getParams(), $params);
        }

        return $params;
    }

    /**
     * Get fields
     *
     * @param  string|array $default
     * @return array
     */
    protected function _getFields($params, $default = null, $fields_param = 'fields')
    {
        $requestFields = isset($params[$fields_param]) ? $params[$fields_param] : '';

        if (!empty($requestFields)) {
            return explode(',', $requestFields);
        }

        if (!empty($default)) {
            if (is_array($default)) {
                return $default;
            } else {
                return explode(',', $default);
            }
        }

        return array();
    }

    /**
     * Set parameter error
     *
     * @param $name
     * @param $statusCode
     * @param $error
     * @param $errorDescription
     * @param null                $errorUri
     * @param null                $dataDebug
     */
    public static function setParamError($name, $statusCode = 400, $error = 'invalid_parameter', $errorDescription = null, $errorUri = null, $dataDebug = null)
    {
        if (null == $errorDescription) {
            $errorDescription = Zend_Registry::get('Zend_Translate')->_('Invalid parameter');
        }

        $response = self::getResponse();

        $errorParams = $response->getParameter('error_params', array());
        $errorParams[] = array(
            'name' => $name,
            'message' => $errorDescription,
            'error_text' => $error,
            'error_code' => $statusCode,
        );

        $response->setError(400, 'invalid_parameters', Zend_Registry::get('Zend_Translate')->_('Invalid parameter(s)'), $errorUri, $dataDebug);
        $response->setParameter('error_params', $errorParams);
    }

    /**
     * Set exception error
     *
     * @param $e
     * @param $statusCode
     * @param $error
     * @param $errorDescription
     * @param null                $errorUri
     * @param null                $dataDebug
     */
    public static function setExceptionError($e, $statusCode = 500, $error = 'exception_error', $errorDescription = null, $errorUri = null, $dataDebug = null)
    {
        if (null == $errorDescription) {
            $errorDescription = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
        }

        if (null == $dataDebug) {
            $dataDebug = array(
                'message' => $e->getMessage(),
                'error_code' => Engine_Api::getErrorCode(),
            );
        }

        self::setError($statusCode, $error, $errorDescription, $errorUri, $dataDebug);
    }

    /**
     * Set error
     *
     * @param $statusCode
     * @param $error
     * @param $errorDescription
     * @param null                $errorUri
     * @param null                $dataDebug
     */
    public static function setError($statusCode, $error, $errorDescription = null, $errorUri = null, $dataDebug = null)
    {
        $response = self::getResponse();
        $response->setError($statusCode, $error, $errorDescription, $errorUri, $dataDebug);
    }

    /**
     * @param $messages
     * @param $fieldMaps
     * @param $parentField
     */
    public static function setFormErrors($messages, $fieldMaps = array(), $parentField = '')
    {
        foreach ($messages as $key => $value) {
            if ('fields' == $key) {
                $subMessages = $value;
                $subFieldMaps = array();
                if (isset($fieldMaps[$key]) && is_array($fieldMaps[$key])) {
                    $subFieldMaps = $fieldMaps[$key];
                }
                self::setFormErrors($subMessages, $subFieldMaps, 'fields');
                continue;
            } elseif (false !== ($k = array_search($key, $fieldMaps))) {
                $field = $k;
            } else {
                $field = $key;
            }

            if (!empty($parentField)) {
                $field = $parentField . '[' . $field . ']';
            }

            self::setParamError($field, 400, 'invalid_parameter', implode("\n", $value));
        }
    }

    /**
     * Is error
     *
     * @return boolean
     */
    public static function isError()
    {
        $response = self::getResponse();
        return $response->isClientError() || $response->isServerError();
    }

    /**
     * Set success data
     *
     * @param $statusCode
     * @param $data
     * @param $dataDebug
     */
    public static function setSuccess($statusCode, $data, $dataDebug = null)
    {
        $response = self::getResponse();
        $response->setSuccess($statusCode, $data, $dataDebug);
    }

    /**
     * Get response
     *
     * @return Ynrestapi_Service_Response
     */
    public static function getResponse()
    {
        if (null == self::$_response) {
            self::$_response = new Ynrestapi_Service_Response();
        }

        return self::$_response;
    }

    /**
     * Send response
     *
     * @param $format
     */
    public static function sendResponse($format = 'json')
    {
        $response = self::getResponse();
        $response->send($format);
    }
}
