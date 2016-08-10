<?php

class Ynfeed_IndexController extends Core_Controller_Action_Standard {
    protected static $_baseUrl;
    public static function getBaseUrl() {
        if (self::$_baseUrl == NULL) {
            $request = Zend_Controller_Front::getInstance() -> getRequest();
            self::$_baseUrl = sprintf('%s://%s', $request -> getScheme(), $request -> getHttpHost());
        }
        return self::$_baseUrl;
    }

    public function postAction() {
        // Make sure user exists
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        // Get subject if necessary
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = null;
        $subject_guid = $this -> _getParam('subject', null);
        if ($subject_guid) {
            $subject = Engine_Api::_() -> getItemByGuid($subject_guid);
        }
        // Use viewer as subject if no subject
        if (null === $subject) {
            $subject = $viewer;
        }
        // Make form
        $form = $this -> view -> form = new Activity_Form_Post();

        // Check auth
        if (!$subject -> authorization() -> isAllowed($viewer, 'comment')) {
            return $this -> _helper -> requireAuth() -> forward();
        }

        // Check if post
        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Not post');
            return;
        }

        // Check if form is valid
        $postData = $this -> getRequest() -> getPost();
        $body = @$postData['body'];
        $privacies = array(
                        'general' => $postData['SPRI_GE'],
                        'friend_list' => $postData['SPRI_FL'],
                        'network' => $postData['SPRI_NE'],
                        'group' => $postData['SPRI_GR'],
                        'friend' => $postData['SPRI_FR']
                        );
        $arrTags = array();
        $arrHashTags = array();
        $url = $this -> getBaseUrl() . $this -> view -> baseUrl();
        if (isset($postData['body_html']) && $postData['body_html'] != '') 
        {
            $body = $postData['body_html'];
            $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
            
            // replace all element A
            $pattern = '/<a[^>]+href="(#tags@\w+@\d+@)">(.*?)<\/a>/mi';
            $body = preg_replace($pattern, 'ynfeedOTA href=ynfeed_ldquo$1ynfeed_ldquoynfeedCA$2ynfeedCTA', $body);
            $pattern = '/<a[^>]+href="(#hashtags@[^\s"]+@)">(.*?)<\/a>/mi';
            preg_match_all($pattern, $body, $matches);
            $body = preg_replace($pattern, 'ynfeedOTA href=ynfeed_ldquo$1ynfeed_ldquoynfeedCA$2ynfeedCTA', $body);
            
            // Tags
            $pattern = '/#tags@\w+@\d+@/';
            preg_match_all($pattern, $body, $matches);
            $matches = $matches[0];

            foreach ($matches as $match) {
                $pattern2 = '/#tags@(\w+)@(\d+)@/';
                preg_match_all($pattern2, $match, $temp_matches);
                $type = $temp_matches[1][0];
                $type = substr($type, 3);
                $item_id = $temp_matches[2][0];

                $arrTags[] = array('item_type' => $type, 'item_id' => $item_id);

                $item = Engine_Api::_() -> getItem($type, $item_id);
                $href = "";
                if ($item) {
                    $href = $item -> getHref() . sprintf('ynfeed_ldquo ng-url=ynfeed_ldquo#/app/%s/%s', $item->getType(), $item->getIdentity());
                }
                $body = str_replace($match, $href, $body);
            }

            // Hashtags
            $pattern = '/#hashtags@([^\s"]+)@/';
            preg_match_all($pattern, $body, $matches);
            $matches = $matches[0];
            foreach ($matches as $match) {
                $pattern2 = '/#hashtags@([^\s"]+)@/';
                preg_match_all($pattern2, $match, $temp_matches);
                $hashtag = $temp_matches[1][0];

                $arrHashTags[] = $hashtag;

                $href = 'javascript:ynfeedFilter(ynfeed_lsquohashtagynfeed_lsquo,ynfeed_lsquo'. $hashtag .'ynfeed_lsquo)ynfeed_ldquo ng-click=ynfeed_ldquofilterHashTag(ynfeed_lsquo'.$hashtag.'ynfeed_lsquo)';
                $body = str_replace($match, $href, $body);
            }
        }

        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        
        $body = preg_replace('/<br[^<>]*>/', "\n", $body);
        $postData['body'] = $body;

        if (!$form -> isValid($postData)) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
            return;
        }

        // Check one more thing
        if ($form -> body -> getValue() === '' && $form -> getValue('attachment_type') === '') {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
            return;
        }

        // set up action variable
        $action = null;

        // Process
        $db = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getAdapter();
        $db -> beginTransaction();

        try {
            // Try attachment getting stuff
            $attachment = null;
            $attachmentData = $this -> getRequest() -> getParam('attachment');
            $filter = new Zend_Filter();
            $filter -> addFilter(new Engine_Filter_Censor());
            $filter -> addFilter(new Engine_Filter_HtmlSpecialChars());
             // detect links and add link automatically
            if(empty($attachmentData) && Engine_Api::_()->authorization()->isAllowed('core_link', null, 'create'))
            {
                $body_decode = html_entity_decode($body);
                $body_decode = html_entity_decode($body_decode);
                $body_decode = html_entity_decode($body_decode);
                $regex = '/http(s)?:\/\/([^" ]*)/mi';
                preg_match_all($regex, $body_decode, $matches);
                if (count($matches) > 0) 
                {
                    $link = $matches[0][0];
                    if ($link) 
                    {
                        $info = parse_url($link);
                        try
                        {
                          $client = new Zend_Http_Client($link, array(
                            'maxredirects' => 2,
                            'timeout'      => 30,
                          ));
                    
                          // Try to mimic the requesting user's UA
                          $client->setHeaders(array(
                            'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                            'X-Powered-By' => 'Zend Framework'
                          ));
                          $response = $client->request();
                    
                          // Get content-type
                          list($contentType) = explode(';', $response->getHeader('content-type'));
                          $this->view->contentType = $contentType;
                    
                          // Handling based on content-type
                          switch( strtolower($contentType) ) 
                          {
                            // Images
                            case 'image/gif':
                            case 'image/jpeg':
                            case 'image/jpg':
                            case 'image/tif': // Might not work
                            case 'image/xbm':
                            case 'image/xpm':
                            case 'image/png':
                            case 'image/bmp': // Might not work
                              $attachmentData = $this->_previewImage($link, $response);
                              break;
                    
                            // HTML
                            case '':
                            case 'text/html':
                              $attachmentData = $this->_previewHtml($link, $response);
                              break;
                    
                            // Plain text
                            case 'text/plain':
                              $attachmentData = $this->_previewText($link, $response);
                              break;
                          }
                        }
                        catch( Exception $e )
                        {
                          throw $e;
                        }
                        if($attachmentData)
                        {
                            $attachmentData['uri'] = $link;
                            if (Engine_Api::_() -> core() -> hasSubject()) {
                                $subject = Engine_Api::_() -> core() -> getSubject();
                                if ($subject -> getType() != 'user') 
                                {
                                    $attachmentData['parent_type'] = $subject -> getType();
                                    $attachmentData['parent_id'] = $subject -> getIdentity();
                                }
                            }
                            if (!empty($attachmentData['title'])) {
                                $attachmentData['title'] = $filter -> filter($attachmentData['title']);
                            }
                            if (!empty($attachmentData['description'])) {
                                $attachmentData['description'] = $attachmentData['description'];
                            }
                            else {
                                $attachmentData['description'] = $attachmentData['title'];
                            }
                            $attachmentData['type'] = 'link';
                        }
                    }
                }
            }
            if (!empty($attachmentData) && !empty($attachmentData['type'])) 
            {
                $type = $attachmentData['type'];
                $config = null;
                foreach (Zend_Registry::get('Engine_Manifest') as $data) {
                    if (!empty($data['composer'][$type])) {
                        $config = $data['composer'][$type];
                    }
                }
                if (!empty($config['auth']) && !Engine_Api::_() -> authorization() -> isAllowed($config['auth'][0], null, $config['auth'][1])) {
                    $config = null;
                }
                if ($config) {
                    $plugin = Engine_Api::_() -> loadClass($config['plugin']);
                    $method = 'onAttach' . ucfirst($type);
                    $attachment = $plugin -> $method($attachmentData);
                }
            }
            $body = $form -> getValue('body');
            
            // fix SE enable link issue
            $body = str_replace('&amp;quot;"', '"', $body);
            $body = str_replace('&quot;</a>', '</a>"', $body);
            
            // Support tag and hastag
            $body = str_replace('ynfeedOTA', '<a', $body);
            $body = str_replace('ynfeedCA', '>', $body);
            $body = str_replace('ynfeedCTA', '</a>', $body);
            $body = str_replace('ynfeed_ldquo', '"', $body);
            $body = str_replace('ynfeed_lsquo', '\'', $body);
            
            $body = str_replace('../', '', $body);
            
            $baseUrl = $this -> view -> baseUrl();

            foreach (Engine_Api::_() -> ynfeed() -> getEmoticons() as $emoticon) {
                $body = str_replace($emoticon -> text, "<img title = '{$this-> view -> translate(ucwords($emoticon -> title))}' src='{$baseUrl}/application/modules/Ynfeed/externals/images/emoticons/{$emoticon -> image}'/>", $body);
            }

            // Special case: status
            if (!$attachment && $viewer -> isSelf($subject)) {
                if ($body != '') {
                    $viewer -> status = $body;
                    $viewer -> status_date = date('Y-m-d H:i:s');
                    $viewer -> save();
                    $viewer -> status() -> setStatus($body);
                }

                $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> addActivity($viewer, $subject, 'status', $body, array('privacies' => $privacies));

            } else {// General post

                $type = 'post';
                if ($viewer -> isSelf($subject)) {
                    $type = 'post_self';
                }

                // Add notification for <del>owner</del> user
                $subjectOwner = $subject -> getOwner();

                if (!$viewer -> isSelf($subject) && $subject instanceof User_Model_User) {
                    $notificationType = 'post_' . $subject -> getType();
                    Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($subjectOwner, $viewer, $subject, $notificationType, array('url1' => $subject -> getHref(), ));
                }

                // Add activity
                $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> addActivity($viewer, $subject, $type, $body, array('privacies' => $privacies));

                // Try to attach if necessary
                if ($action && $attachment) {
                    Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> attachActivity($action, $attachment);
                }
            }

            // Preprocess attachment parameters
            $publishMessage = html_entity_decode($form -> getValue('body'));
            $publishUrl = null;
            $publishName = null;
            $publishDesc = null;
            $publishPicUrl = null;
            // Add attachment
            if ($attachment) {
                $publishUrl = $attachment -> getHref();
                $publishName = $attachment -> getTitle();
                $publishDesc = $attachment -> getDescription();
                if (empty($publishName)) {
                    $publishName = ucwords($attachment -> getShortType());
                }
                if (($tmpPicUrl = $attachment -> getPhotoUrl())) {
                    $publishPicUrl = $tmpPicUrl;
                }
                // prevents OAuthException: (#100) FBCDN image is not allowed in stream
                if ($publishPicUrl && preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
                    $publishPicUrl = null;
                }
            } else {
                $publishUrl = !$action ? null : $action -> getHref();
            }
            // Check to ensure proto/host
            if ($publishUrl && false === stripos($publishUrl, 'http://') && false === stripos($publishUrl, 'https://')) {
                $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
            }
            if ($publishPicUrl && false === stripos($publishPicUrl, 'http://') && false === stripos($publishPicUrl, 'https://')) {
                $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
            }
            // Add site title
            if ($publishName) {
                $publishName = Engine_Api::_() -> getApi('settings', 'core') -> core_general_site_title . ": " . $publishName;
            } else {
                $publishName = Engine_Api::_() -> getApi('settings', 'core') -> core_general_site_title;
            }

            // Publish to facebook, if checked & enabled
            if ($this -> _getParam('post_to_facebook', false) && 'publish' == Engine_Api::_() -> getApi('settings', 'core') -> core_facebook_enable) {
                try {

                    $facebookTable = Engine_Api::_() -> getDbtable('facebook', 'user');
                    $facebook = $facebookApi = $facebookTable -> getApi();
                    $fb_uid = $facebookTable -> find($viewer -> getIdentity()) -> current();

                    if ($fb_uid && $fb_uid -> facebook_uid && $facebookApi && $facebookApi -> getUser() && $facebookApi -> getUser() == $fb_uid -> facebook_uid) {
                        $fb_data = array('message' => $publishMessage, );
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
                        $res = $facebookApi -> api('/me/feed', 'POST', $fb_data);
                    }
                } catch( Exception $e ) {
                    // Silence
                }
            }// end Facebook

            // Publish to twitter, if checked & enabled
            if ($this -> _getParam('post_to_twitter', false) && 'publish' == Engine_Api::_() -> getApi('settings', 'core') -> core_twitter_enable) {
                try {
                    $twitterTable = Engine_Api::_() -> getDbtable('twitter', 'user');
                    if ($twitterTable -> isConnected()) {
                        // @todo truncation?
                        // @todo attachment
                        $twitter = $twitterTable -> getApi();
                        $twitter -> statuses -> update($publishMessage);
                    }
                } catch( Exception $e ) {
                    // Silence
                }
            }

            // Publish to janrain
            if ('publish' == Engine_Api::_() -> getApi('settings', 'core') -> core_janrain_enable) {
                try {
                    $session = new Zend_Session_Namespace('JanrainActivity');
                    $session -> unsetAll();

                    $session -> message = $publishMessage;
                    $session -> url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
                    $session -> name = $publishName;
                    $session -> desc = $publishDesc;
                    $session -> picture = $publishPicUrl;

                } catch( Exception $e ) {
                    // Silence
                }
            }

            $db -> commit();
        } catch( Exception $e ) {
            $db -> rollBack();
            throw $e;
            // This should be caught by error handler
        }
        $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');

        // save add friend
        $str_friends = $this -> getRequest() -> getParam('friendValues', '');
        $arr_friends = array();
        if ($str_friends) {
            $tagfriend_table = Engine_Api::_() -> getDbtable('tagfriends', 'ynfeed');
            $arr_friends = explode(',', $str_friends);
            foreach ($arr_friends as $friendId) {
                $tagfriend = $tagfriend_table -> createRow();
                $tagfriend -> user_id = $viewer -> getIdentity();
                $tagfriend -> action_id = $action -> getIdentity();
                $tagfriend -> friend_id = $friendId;
                $tagfriend -> save();

                // send notitcation to user tagged
                $obj_item = Engine_Api::_() -> getItem('user', $friendId);
                if (!$viewer -> isSelf($obj_item)) {
                    $notifyApi -> addNotification($obj_item, $viewer, $action, 'ynfeed_tag');
                }
            }
        }

        // save tags
        $tag_table = Engine_Api::_() -> getDbtable('tags', 'ynfeed');
        foreach ($arrTags as $item) {
            $tag = $tag_table -> createRow();
            $tag -> user_id = $viewer -> getIdentity();
            $tag -> action_id = $action -> getIdentity();
            $tag -> item_type = $item['item_type'];
            $tag -> item_id = $item['item_id'];
            $tag -> save();

            // send notitcation to user tagged
            $obj_item = Engine_Api::_() -> getItem($item['item_type'], $item['item_id']);
            if ($item['item_type'] == 'user' && !$viewer -> isSelf($obj_item) && !in_array($item['item_id'], $arr_friends)) {
                $notifyApi -> addNotification($obj_item, $viewer, $action, 'ynfeed_tag');
            }
        }

        // save hash tags
        $hashtag_table = Engine_Api::_() -> getDbtable('hashtags', 'ynfeed');
        foreach ($arrHashTags as $item) {
            $hashtag = $hashtag_table -> createRow();
            $hashtag -> user_id = $viewer -> getIdentity();
            $hashtag -> action_id = $action -> getIdentity();
            $hashtag -> action_type = $action -> type;
            $hashtag -> hashtag = $item;
            $hashtag -> save();
        }
        // checkin
        if ($this -> getRequest() -> getParam('checkin_lat') && $this -> getRequest() -> getParam('checkin_long') && $this -> getRequest() -> getParam('checkinValue')) 
        {
            if ($action) 
            {
                $map_table = Engine_Api::_() -> getDbTable("maps", "ynfeed");
                $map = $map_table -> createRow();
                $map -> title = $this -> getRequest() -> getParam('checkinValue');
                $map -> latitude = $this -> getRequest() -> getParam('checkin_lat');
                $map -> longitude = $this -> getRequest() -> getParam('checkin_long');
                $map -> user_id = $viewer -> getIdentity();
                $map -> action_id = $action -> getIdentity();
                $map -> business_id = 0;
                $map -> save();
                
                if(!$attachment)
                {
                    // CREATE AUTH STUFF HERE
                    $roles = array(
                        'owner',
                        'owner_member',
                        'owner_member_member',
                        'owner_network',
                        'registered',
                        'everyone'
                    );
                    $auth = Engine_Api::_() -> authorization() -> context;
                    $viewMax = array_search('everyone', $roles);
            
                    foreach ($roles as $i => $role)
                    {
                        $auth -> setAllowed($map, $role, 'view', ($i <= $viewMax));
                    }
                
                    Engine_Api::_()->getDbtable('actions', 'ynfeed') -> attachActivity($action, $map);
                }
            }
        }

        // Add business to post
        elseif(Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages'))
        {
            if($this -> getRequest() -> getParam('businessValues', 0))
            {
                $business_id = $this -> getRequest() -> getParam('businessValues', 0);
                $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id);
                if($business && $action)
                {
                    $main_location = $business -> getMainLocationObject();
                    $map_table = Engine_Api::_() -> getDbTable("maps", "ynfeed");
                    $map = $map_table -> createRow();
                    $map -> title = $main_location -> location;
                    $map -> latitude = $main_location->latitude;
                    $map -> longitude = $main_location -> longitude;
                    $map -> user_id = $viewer -> getIdentity();
                    $map -> action_id = $action -> getIdentity();
                    $map -> business_id = $business_id;
                    $map -> save();
                    
                    if(!$attachment)
                    {
                        // CREATE AUTH STUFF HERE
                        $roles = array(
                            'owner',
                            'owner_member',
                            'owner_member_member',
                            'owner_network',
                            'registered',
                            'everyone'
                        );
                        $auth = Engine_Api::_() -> authorization() -> context;
                        $viewMax = array_search('everyone', $roles);
                
                        foreach ($roles as $i => $role)
                        {
                            $auth -> setAllowed($map, $role, 'view', ($i <= $viewMax));
                        }
                    
                        Engine_Api::_()->getDbtable('actions', 'ynfeed') -> attachActivity($action, $map);
                    }
                }
            }
        }
        // If we're here, we're done
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Success!');
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(TRUE);
        
        $url = '';
        // check and support social publisher
        $session = new Zend_Session_Namespace('mobile');
        if(Engine_Api::_() -> hasModuleBootstrap('socialpublisher') && !$session -> mobile)
        {
            if ($action)
            {
                $api = Engine_Api::_() -> socialpublisher();
                $resource_type = $action -> getType();
                $resource_id = $action -> getIdentity();
                $enable_settings = $api -> getTypeSettings($resource_type);
                $module_settings = $api -> getUserTypeSettings($viewer -> getIdentity(), $resource_type);
    
                $is_popup = ($enable_settings['active'] && count($module_settings['providers']));
                // item privacy satisty
                if ($is_popup)
                {
                    switch ($module_settings['option'])
                    {
                        case Socialpublisher_Plugin_Constants::OPTION_ASK :
                            // open popup
                            $params = array(
                                'action' => 'share',
                                'resource_id' => $resource_id,
                                'resource_type' => $resource_type,
                            );
                            $url = Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'socialpublisher_general');
                            break;
                        case Socialpublisher_Plugin_Constants::OPTION_AUTO :
                            if (!empty($module_settings['providers']))
                            {
                                $providers = $module_settings['providers'];
                                foreach ($providers as $provider)
                                {
                                    $values = array(
                                        'service' => $provider,
                                        'user_id' => $viewer -> getIdentity()
                                    );
                                    $obj = Engine_Api::_() -> socialbridge() -> getInstance($provider);
                                    $token = $obj -> getToken($values);
                                    $default_status = $api -> getDefaultStatus(array(
                                        'viewer' => Engine_Api::_() -> user() -> getViewer(),
                                        'resource' => $action,
                                        'title' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.general.site.title', $this -> view -> translate('SocialEngine Site'))
                                    ));
                                    $photo_url = $api -> getPhotoUrl($action);
                                    $post_data = $api -> getPostData($provider, $action, $default_status, $photo_url);
                                    if (!empty($_SESSION['socialbridge_session'][$provider]))
                                    {
                                        try
                                        {
                                            $obj -> postActivity($post_data);
                                        }
                                        catch(Exception $e)
                                        {
                                        }
                                    }
                                    else
                                    {
                                        $_SESSION['socialbridge_session'][$provider]['access_token'] = $token -> access_token;
                                        $_SESSION['socialbridge_session'][$provider]['secret_token'] = $token -> secret_token;
                                        $_SESSION['socialbridge_session'][$provider]['owner_id'] = $token -> uid;
                                        try
                                        {
                                            $obj -> postActivity($post_data);
                                        }
                                        catch(Exception $e)
                                        {
                                        }
                                    }
                                }
                            }
                            break;
                        case Socialpublisher_Plugin_Constants::OPTION_NOT_ASK :
                            break;
                        default :
                            break;
                    }
                }
            }
        }
        echo Zend_Json::encode(array('url' => $url, 'action_id' => $action -> getIdentity()));
    }

    public function editPostAction()
    {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $action_id = $this -> _getParam('action_id', 0);
        if (!$action_id) 
        {
            return $this -> _helper -> requireSubject -> forward();
        }
        $this -> view -> action = $action = Engine_Api::_() -> getItem('activity_action', $action_id);
        $activity_moderate = Engine_Api::_() -> getDbtable('permissions', 'authorization') -> getAllowed('user', $viewer -> level_id, 'activity');
        if (!$activity_moderate && !(
            ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) ||
            ('user' == $action->object_type && $viewer->getIdentity()  == $action->object_id)
              )) 
        {
            return $this -> _helper -> requireAuth -> forward();
        }
        $this -> view -> friendUsers = $friendUsers = Engine_Api::_() -> ynfeed() -> getViewerFriends($viewer);
        $subject = $action -> getObject();
        $this -> view -> subject = $subject;
        $this -> view -> subjectType = $subject -> getType();
        
        // Groups tagged
        $groupsTagged = Engine_Api::_() -> getDbTable('tags', 'ynfeed') -> getTagsByAction($action_id, 'group');
        $aGroupsTagged = array();
        foreach ($groupsTagged as $item) 
        {
            $aGroupsTagged[] = $item -> item_id;
        }
        
        // Friends tagged
        $usersTagged = Engine_Api::_() -> getDbTable('tags', 'ynfeed') -> getTagsByAction($action_id, 'user');
        $aUsersTagged = array();
        foreach ($usersTagged as $item) 
        {
            $aUsersTagged[] = $item -> item_id;
        }
        
        // Friends with
        $with = Engine_Api::_() -> getDbTable('tagfriends', 'ynfeed') -> getWithByAction($action_id);
        $sWithFriend = "";
        $aWithFriend = array();
        foreach($with as $item)
        {
            if($item -> friend_id)
            {
                $sWithFriend .= $item -> friend_id.',';
                $aWithFriend[] = $item -> friend_id;
            }
        }
        $this -> view -> sWithFriend = $sWithFriend;
        $this -> view -> aWithFriend = $aWithFriend;
        
        // Privacy
        $privacies = array();
        $sGeneral = $sFriendlist = $sNetwork = $sGroup = $sFriend = "";
        if(isset($action->params['privacies']))
        {
            $privacies = $action->params['privacies'];
        }
        $aGeneral = array();
        if(isset($privacies['general']))
        {
            $sGeneral = $privacies['general'];
            if($sGeneral)
            {
                $aGeneral = explode(',', $sGeneral);
            }
        }
        $this -> view -> sGeneral = $sGeneral;
        $this -> view -> aGeneral = $aGeneral;
        
        $aNetwork = array();
        if(isset($privacies['network']))
        {
            $sNetwork = $privacies['network'];
            if($sNetwork)
            {
                $aNetwork = explode(',', $sNetwork);
            }
        }
        $this -> view -> sNetwork = $sNetwork;
        $this -> view -> aNetwork = $aNetwork;
        
        $aFriendlist = array();
        if(isset($privacies['friend_list']))
        {
            $sFriendlist = $privacies['friend_list'];
            if($sFriendlist)
            {
                $aFriendlist = explode(',', $sFriendlist);
            }
        }
        $this -> view -> sFriendlist = $sFriendlist;
        $this -> view -> aFriendlist = $aFriendlist;
        
        $aFriend = array();
        if(isset($privacies['friend']))
        {
            $sFriend = $privacies['friend'];
            if($sFriend)
            {
                $aFriend = explode(',', $sFriend);
            }
        }
        $this -> view -> sFriend = $sFriend;
        $this -> view -> aFriend = $aFriend;
        
        $aGroup = array();
        if(isset($privacies['group']))
        {
            $sGroup = $privacies['group'];
            if($sGroup)
            {
                $aGroup = explode(',', $sGroup);
            }
        }
        $this -> view -> sGroup = $sGroup;
        $this -> view -> aGroup = $aGroup;
        
        // Map
        $map = Engine_Api::_() -> getDbTable('maps', 'ynfeed') -> getMapByAction($action_id);
        $mapTile = '';
        if($map)
        {
            $mapTile = $map -> title;
            $this -> view -> sLat = $map -> latitude;
            $this -> view -> sLong = $map -> longitude;
        }
        $this -> view -> map = $mapTile;
        $this -> view -> business_id = $map -> business_id;
        if (!$this -> getRequest() -> isPost()) 
        {
            // Add javascript
            $headScript = new Zend_View_Helper_HeadScript();
    
            $headScript -> appendFile('application/modules/Ynfeed/externals/scripts/core.js');
            $headScript -> appendFile('application/modules/Ynfeed/externals/scripts/yncomposer.js');
            $headScript -> appendFile('application/modules/Ynfeed/externals/scripts/ynfeed.js');
    
            if ($viewer -> getIdentity()) 
            {
                // Support tags
                $headScript -> appendFile('application/modules/Ynfeed/externals/scripts/yntag.js');
                $headScript -> appendFile('application/modules/Ynfeed/externals/scripts/ynaddfriend.js');
                $this -> view -> hasTag = true;
                
                // Checkin JS
                $headScript -> appendFile('https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places');
                $headScript -> appendFile('application/modules/Ynfeed/externals/scripts/yncheckin.js');
            }
            if($viewer && ($subject && $viewer -> isSelf($subject) 
              || !$subject
              || ($subject && in_array($subject -> getType(), array('event', 'group')))
              ))
            {
                // Add privacy
                $headScript -> appendFile('application/modules/Ynfeed/externals/scripts/ynaddprivacies.js');
                $this -> view -> hasPrivacy = true;
            }
            if(Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages'))
            {
                  $headScript -> appendFile('application/modules/Ynfeed/externals/scripts/ynaddbusiness.js');
            }
            $body =  trim(preg_replace('/[\r\n]/im', "\n", $action -> body));
            // convert emoticons
            $baseUrl = $this -> view -> baseUrl();
            $pattern = "#<img.*?src='{$baseUrl}/application/modules/Ynfeed/externals/images/emoticons/([^>]*)'/>#i";
            preg_match_all($pattern, $body, $matches);
            $matches = $matches[0];
            foreach ($matches as $match) 
            {
                preg_match_all($pattern, $match, $temp_matches);
                $emoticon_img = $temp_matches[1][0];
                $emoticon = Engine_Api::_() -> ynfeed() -> getEmoticonByImg($emoticon_img);
                $str_replace = "<img title = '{$this-> view -> translate(ucwords($emoticon -> title))}' src='{$baseUrl}/application/modules/Ynfeed/externals/images/emoticons/{$emoticon -> image}'/>";
                $body = str_replace($str_replace, $emoticon -> text, $body);
            }
            
            $pattern = '/<a\s+href=\"([^\"]*)\"\s+ng-url=\"\#\/app\/(\w+)\/(\d+)\">(.*)<\/a>/siU';
            $input_hidden = preg_replace($pattern, '#tags@tag$2@$3@;­$4#im', $body);
            $this -> view -> input_hidden = strip_tags($input_hidden);
            $body = preg_replace('#<a[^>]*?>([\#][^>]*)</a>#im', '$1', $body);
            $body = preg_replace('#<a[^>]*?>([^\#][^>]*)</a>#im', '­$1', $body);
            $this -> view -> body = $body;
        }
        else 
        {
            // Make form
            $form = new Activity_Form_Post();
    
            // Check auth
            if (!$subject -> authorization() -> isAllowed($viewer, 'comment')) 
            {
                return $this -> _helper -> requireAuth() -> forward();
            }
            // Check if form is valid
            $postData = $this -> getRequest() -> getPost();
    
            $body = @$postData['body'];
            $privacies = array(
                            'general' => $postData['SPRI_GE'],
                            'friend_list' => $postData['SPRI_FL'],
                            'network' => $postData['SPRI_NE'],
                            'group' => $postData['SPRI_GR'],
                            'friend' => $postData['SPRI_FR']
                            );
            $arrTags = array();
            $arrHashTags = array();
            $url = $this -> getBaseUrl() . $this -> view -> baseUrl();
            if (isset($postData['body_html']) && $postData['body_html'] != '') {
                $body = $postData['body_html'];
                $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
                
                 // replace all element A
                $pattern = '/<a[^>]+href="(#tags@\w+@\d+@)">(.*?)<\/a>/mi';
                $body = preg_replace($pattern, 'ynfeedOTA href=ynfeed_ldquo$1ynfeed_ldquoynfeedCA$2ynfeedCTA', $body);
                $pattern = '/<a[^>]+href="(#hashtags@[^\s"]+@)">(.*?)<\/a>/mi';
                preg_match_all($pattern, $body, $matches);
                $body = preg_replace($pattern, 'ynfeedOTA href=ynfeed_ldquo$1ynfeed_ldquoynfeedCA$2ynfeedCTA', $body);
    
                // Tags
                $pattern = '/#tags@\w+@\d+@/';
                preg_match_all($pattern, $body, $matches);
                $matches = $matches[0];
    
                foreach ($matches as $match) {
                    $pattern2 = '/#tags@(\w+)@(\d+)@/';
                    preg_match_all($pattern2, $match, $temp_matches);
                    $type = $temp_matches[1][0];
                    $type = substr($type, 3);
                    $item_id = $temp_matches[2][0];
                    $arrTags[] = array('item_type' => $type, 'item_id' => $item_id);
                    $item = Engine_Api::_() -> getItem($type, $item_id);
                    $href = "";
                    if ($item) 
                    {
                        $href = $item -> getHref() . sprintf('ynfeed_ldquo ng-url=ynfeed_ldquo#/app/%s/%s', $item->getType(), $item->getIdentity());
                    }
                    $body = str_replace($match, $href, $body);
                }
    
                // Hashtags
                $pattern = '/#hashtags@(\w+)@/';
                preg_match_all($pattern, $body, $matches);
                $matches = $matches[0];
                foreach ($matches as $match) {
                    $pattern2 = '/#hashtags@(\w+)/';
                    preg_match_all($pattern2, $match, $temp_matches);
                    $hashtag = $temp_matches[1][0];
                    $arrHashTags[] = $hashtag;
                    $href = 'javascript:ynfeedFilter(ynfeed_lsquohashtagynfeed_lsquo,ynfeed_lsquo'. $hashtag .'ynfeed_lsquo)ynfeed_ldquo ng-click=ynfeed_ldquofilterHashTag(ynfeed_lsquo'.$hashtag.'ynfeed_lsquo)';
                    $body = str_replace($match, $href, $body);
                }
            }
    
            $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
            $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
            $body = preg_replace('/<br[^<>]*>/', "\n", $body);
            $postData['body'] = $body;
    
            if (!$form -> isValid($postData)) {
                $this -> view -> status = false;
                $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
                return;
            }
    
            // Check one more thing
            if ($form -> body -> getValue() === '' && $form -> getValue('attachment_type') === '') {
                $this -> view -> status = false;
                $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
                return;
            }
    
            // Process
            $db = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getAdapter();
            $db -> beginTransaction();
    
            try {
                 $body = $form -> getValue('body');
            
                // fix SE enable link issue
                $body = str_replace('&amp;quot;"', '"', $body);
                $body = str_replace('&quot;</a>', '</a>"', $body);
                
                // Support tag and hastag
                $body = str_replace('ynfeedOTA', '<a', $body);
                $body = str_replace('ynfeedCA', '>', $body);
                $body = str_replace('ynfeedCTA', '</a>', $body);
                $body = str_replace('ynfeed_ldquo', '"', $body);
                $body = str_replace('ynfeed_lsquo', '\'', $body);
            
                $body = str_replace('../', '', $body);
                $baseUrl = $this -> view -> baseUrl();
    
                foreach (Engine_Api::_() -> ynfeed() -> getEmoticons() as $emoticon) {
                    $body = str_replace($emoticon -> text, "<img title = '{$this-> view -> translate(ucwords($emoticon -> title))}' src='{$baseUrl}/application/modules/Ynfeed/externals/images/emoticons/{$emoticon -> image}'/>", $body);
                }
    
                // Special case: status
                if ($viewer -> isSelf($subject)) 
                {
                    if ($body != '') {
                        $viewer -> status = $body;
                        $viewer -> status_date = date('Y-m-d H:i:s');
                        $viewer -> save();
    
                        $viewer -> status() -> setStatus($body);
                    }
    
                    $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> editActivity($action, $viewer, $subject, 'status', $body, array('privacies' => $privacies));
    
                } else {// General post
    
                    $type = 'post';
                    if ($viewer -> isSelf($subject)) {
                        $type = 'post_self';
                    }
    
                    // Add activity
                    $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> editActivity($action, $viewer, $subject, $type, $body, array('privacies' => $privacies));
                }
                $db -> commit();
            } catch( Exception $e ) {
                $db -> rollBack();
                throw $e;
                // This should be caught by error handler
            }
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            
            // save add friend
            $tagfriend_table = Engine_Api::_() -> getDbtable('tagfriends', 'ynfeed');
            $str_friends = $this -> getRequest() -> getParam('friendValues', '');
            $tagfriend_table -> deleteWithByAction($action_id);
            $arr_friends = array();
            if ($str_friends) {
                $arr_friends = explode(',', $str_friends);
                foreach ($arr_friends as $friendId) 
                {
                    if($friendId)
                    {
                        $tagfriend = $tagfriend_table -> createRow();
                        $tagfriend -> user_id = $viewer -> getIdentity();
                        $tagfriend -> action_id = $action -> getIdentity();
                        $tagfriend -> friend_id = $friendId;
                        $tagfriend -> save();
                        
                        if(in_array($friendId, $aWithFriend))
                        {
                            continue;
                        }
                        // send notitcation to user tagged
                        $obj_item = Engine_Api::_() -> getItem('user', $friendId);
                        if (!$viewer -> isSelf($obj_item)) {
                            $notifyApi -> addNotification($obj_item, $viewer, $action, 'ynfeed_tag');
                        }
                    }
                }
            }
    
            // save tags
            $tag_table = Engine_Api::_() -> getDbtable('tags', 'ynfeed');
            $tag_table -> deleteTagsByAction($action_id);
            foreach ($arrTags as $item) 
            {
                $tag = $tag_table -> createRow();
                $tag -> user_id = $viewer -> getIdentity();
                $tag -> action_id = $action -> getIdentity();
                $tag -> item_type = $item['item_type'];
                $tag -> item_id = $item['item_id'];
                $tag -> save();
                
                if($item['item_type'] == 'user' && in_array($item['item_id'], $aUsersTagged))
                {
                    continue;
                }
                else if($item['item_type'] == 'group' && in_array($item['item_id'], $aGroupsTagged))
                {
                    continue;
                }
                
                // send notitcation to user tagged
                $obj_item = Engine_Api::_() -> getItem($item['item_type'], $item['item_id']);
                if ($item['item_type'] == 'user' && !$viewer -> isSelf($obj_item) && !in_array($item['item_id'], $arr_friends)) {
                    $notifyApi -> addNotification($obj_item, $viewer, $action, 'ynfeed_tag');
                }
            }
    
            // save hash tags
            $hashtag_table = Engine_Api::_() -> getDbtable('hashtags', 'ynfeed');
            $hashtag_table -> deleteHashTagsByAction($action_id);
            foreach ($arrHashTags as $item) 
            {
                $hashtag = $hashtag_table -> createRow();
                $hashtag -> user_id = $viewer -> getIdentity();
                $hashtag -> action_id = $action -> getIdentity();
                $hashtag -> action_type = $action -> type;
                $hashtag -> hashtag = $item;
                $hashtag -> save();
            }
            // checkin
            $map_table = Engine_Api::_() -> getDbTable("maps", "ynfeed");
            $map = $map_table -> getMapByAction($action_id);
            if ($this -> getRequest() -> getParam('checkin_lat') && $this -> getRequest() -> getParam('checkin_long') && $this -> getRequest() -> getParam('checkinValue')) 
            {
                $isNewMap = false;
                if(!$map)
                {
                    $map = $map_table -> createRow();
                    $isNewMap = true;
                }
                $map -> title = $this -> getRequest() -> getParam('checkinValue');
                $map -> latitude = $this -> getRequest() -> getParam('checkin_lat');
                $map -> longitude = $this -> getRequest() -> getParam('checkin_long');
                $map -> user_id = $viewer -> getIdentity();
                $map -> action_id = $action -> getIdentity();
                $map -> business_id = 0;
                $map -> save();
                
                if(!$action -> attachment_count && $isNewMap)
                {
                    // CREATE AUTH STUFF HERE
                    $roles = array(
                        'owner',
                        'owner_member',
                        'owner_member_member',
                        'owner_network',
                        'registered',
                        'everyone'
                    );
                    $auth = Engine_Api::_() -> authorization() -> context;
                    $viewMax = array_search('everyone', $roles);
            
                    foreach ($roles as $i => $role)
                    {
                        $auth -> setAllowed($map, $role, 'view', ($i <= $viewMax));
                    }
                
                    Engine_Api::_()->getDbtable('actions', 'ynfeed') -> attachActivity($action, $map);
                }
            }
            elseif($this -> getRequest() -> getParam('businessValues', 0))
            {
                if(Engine_Api::_() -> hasModuleBootstrap('ynbusinesspages'))
                {
                    $business_id = $this -> getRequest() -> getParam('businessValues', 0);
                    $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id);
                    if($business)
                    {
                        $isNewMap = false;
                        if(!$map)
                        {
                            $map = $map_table -> createRow();
                            $isNewMap = true;
                        }
                        $main_location = $business -> getMainLocationObject();
                        $map -> title = $main_location -> location;
                        $map -> latitude = $main_location->latitude;
                        $map -> longitude = $main_location -> longitude;
                        $map -> user_id = $viewer -> getIdentity();
                        $map -> action_id = $action -> getIdentity();
                        $map -> business_id = $business_id;
                        $map -> save();
                        
                        if(!$action -> attachment_count && $isNewMap)
                        {
                            // CREATE AUTH STUFF HERE
                            $roles = array(
                                'owner',
                                'owner_member',
                                'owner_member_member',
                                'owner_network',
                                'registered',
                                'everyone'
                            );
                            $auth = Engine_Api::_() -> authorization() -> context;
                            $viewMax = array_search('everyone', $roles);
                    
                            foreach ($roles as $i => $role)
                            {
                                $auth -> setAllowed($map, $role, 'view', ($i <= $viewMax));
                            }
                        
                            Engine_Api::_()->getDbtable('actions', 'ynfeed') -> attachActivity($action, $map);
                        }
                    }
                }
            }
            elseif($map) 
            {
                foreach($action -> getAttachments() as $attch)
                {
                    if($attch -> meta-> type == 'ynfeed_map')
                    {
                        $attch -> meta -> delete();
                    }
                }
                $action -> attachment_count = 0;
                $action -> save();
                $map -> action_id = 0;
                $map -> save();
            }
            // If we're here, we're done
            $this -> view -> status = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Success!');
            $this -> _helper -> layout -> disableLayout();
            $this -> _helper -> viewRenderer -> setNoRender(TRUE);
            echo $action -> getIdentity();
        }
    }
    
    public function getUsersGroupsAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer -> getIdentity()) {
            $data = null;
        } else {
            $data = array();
            $table = Engine_Api::_() -> getItemTable('user');
            $table_name = $table -> info('name');
            $select = $viewer -> membership() -> getMembersObjectSelect();

            if (0 < ($limit = ( int )$this -> _getParam('limit', 5))) {
                $select -> limit($limit);
            }

            $str_users = $this -> _getParam('users', '');
            $arr_users = explode(',', $str_users);
            $str_groups = $this -> _getParam('groups', '');
            $arr_groups = explode(',', $str_groups);

            if (null !== ($text = $this -> _getParam('search', $this -> _getParam('value')))) {
                $select -> where("$table_name.displayname LIKE '%{$text}%' OR $table_name.username LIKE '%{$text}%'");
            }
            if ($arr_users) {
                $select -> where("$table_name.user_id NOT IN (?)", $arr_users);
            }
            if (strpos(strtolower($viewer -> getTitle()), strtolower($text)) !== FALSE && !in_array($viewer -> getIdentity(), $arr_users)) {
                $data[] = array('type' => 'user', 'id' => $viewer -> getIdentity(), 'guid' => $viewer -> getGuid(), 'label' => $viewer -> getTitle(), 'photo' => $this -> view -> itemPhoto($viewer, 'thumb.icon'), 'url' => $viewer -> getHref());
            }
            foreach ($table -> fetchAll ( $select ) as $friend) {
                $data[] = array('type' => 'user', 'id' => $friend -> getIdentity(), 'guid' => $friend -> getGuid(), 'label' => $friend -> getTitle(), 'photo' => $this -> view -> itemPhoto($friend, 'thumb.icon'), 'url' => $friend -> getHref());
            }

            $checkGroup = Engine_Api::_() -> getDbtable('modules', 'core') -> isModuleEnabled('group');
            $checkAdvGroup = Engine_Api::_() -> getDbtable('modules', 'core') -> isModuleEnabled('advgroup');
            if (($checkGroup) || ($checkAdvGroup)) {
                $groupTable = Engine_Api::_() -> getItemTable('group');
                $group_select = $groupTable -> select() -> where('title LIKE ?', '%' . $text . '%') -> where('search = 1');
                if ($arr_groups) {
                    $group_select -> where("group_id NOT IN (?)", $arr_groups);
                }
                if (0 < ($limit = ( int )$this -> _getParam('limit', 5))) 
                {
                    $group_select -> limit($limit);
                }
                $group_results = $groupTable -> fetchAll($group_select);
                foreach ($group_results as $result) {
                    $data[] = array('type' => 'group', 'id' => $result -> getIdentity(), 'guid' => $result -> getGuid(), 'label' => $result -> getTitle(), 'photo' => $this -> view -> itemPhoto($result, 'thumb.icon'), 'url' => $result -> getHref());
                }
            }
        }
        if ($this -> _getParam('sendNow', true)) {
            return $this -> _helper -> json($data);
        } else {
            $this -> _helper -> viewRenderer -> setNoRender(true);
            $data = Zend_Json::encode($data);
            $this -> getResponse() -> setBody($data);
        }
    }

    public function getBusinessesAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer -> getIdentity()) {
            $data = null;
        } else {
            $data = array();
            $checkBusiness = Engine_Api::_() -> getDbtable('modules', 'core') -> isModuleEnabled('ynbusinesspages');
            if ($checkBusiness) {
                $businessTable = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
                $select = $businessTable -> select();
                if (null !== ($text = $this -> _getParam('search', $this -> _getParam('value')))) 
                {
                    $select -> where('name LIKE ?', '%' . $text . '%');
                }
                $select -> where('approved = 1') -> where('deleted = 0') -> where('status = "published"');
                if (0 < ($limit = ( int )$this -> _getParam('limit', 5))) 
                {
                    $select -> limit($limit);
                }
                $business_results = $businessTable -> fetchAll($select);
                foreach ($business_results as $result) {
                    $data[] = array('type' => 'ynbusinesspages_business', 'id' => $result -> getIdentity(), 'guid' => $result -> getGuid(), 'label' => $result -> getTitle(), 'photo' => $this -> view -> itemPhoto($result, 'thumb.icon'), 'url' => $result -> getHref());
                }
            }
        }
        if ($this -> _getParam('sendNow', true)) {
            return $this -> _helper -> json($data);
        } else {
            $this -> _helper -> viewRenderer -> setNoRender(true);
            $data = Zend_Json::encode($data);
            $this -> getResponse() -> setBody($data);
        }
    }

    public function getFriendsAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer -> getIdentity()) {
            $data = null;
        } else {
            $data = array();
            $table = Engine_Api::_() -> getItemTable('user');
            $table_name = $table -> info('name');
            $select = $viewer -> membership() -> getMembersObjectSelect();

            if (0 < ($limit = ( int )$this -> _getParam('limit', 10))) {
                $select -> limit($limit);
            }

            $str_users = $this -> _getParam('users', '');
            $arr_users = explode(',', $str_users);

            if (null !== ($text = $this -> _getParam('search', $this -> _getParam('value')))) {
                $select -> where("$table_name.displayname LIKE '%{$text}%' OR $table_name.username LIKE '%{$text}%'");
            }
            if ($arr_users) {
                $select -> where("$table_name.user_id NOT IN (?)", $arr_users);
            }
            foreach ($table -> fetchAll ( $select ) as $friend) {
                $data[] = array('type' => 'user', 'id' => $friend -> getIdentity(), 'guid' => $friend -> getGuid(), 'label' => $friend -> getTitle(), 'photo' => $this -> view -> itemPhoto($friend, 'thumb.icon'), 'url' => $friend -> getHref());
            }

        }
        if ($this -> _getParam('sendNow', true)) {
            return $this -> _helper -> json($data);
        } else {
            $this -> _helper -> viewRenderer -> setNoRender(true);
            $data = Zend_Json::encode($data);
            $this -> getResponse() -> setBody($data);
        }
    }

    public function viewMapAction() {
        $map_id = $this -> _getParam('map_id', 0);
        $map = Engine_Api::_() -> getItem('ynfeed_map', $map_id);
        if (!$map) {
            return $this -> _helper -> requireSubject -> forward();
        }
        $this -> view -> map = $map;
        $this -> _helper -> layout -> disableLayout();
    }

    public function moreFriendAction() {
        $action_id = $this -> _getParam('action_id', 0);
        $friend_id = $this -> _getParam('friend_id', 0);
        if (!$action_id || !$friend_id) {
            return $this -> _helper -> requireSubject -> forward();
        }
        list($ids, $friends) = Engine_Api::_() -> ynfeed() -> getWithFriends($action_id, $friend_id);
        $this -> view -> friends = $friends;
    }

    public function removeTagAction() {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;
        $action_id = $this -> _getParam('action_id', 0);
        if (!$action_id) {
            return $this -> _forward('notfound', 'error', 'core');
        }
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $viewer_id = $viewer -> getIdentity();
        Engine_Api::_() -> ynfeed() -> removeTag($action_id, $viewer_id);
        // Success
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your tag has been removed.');

        $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getActionById($action_id);
        // Redirect if not json context
        $this -> view -> body = $this -> view -> ynfeed($action, array('noList' => true));
    }
    
    // remove link preview
    public function removePreviewAction() {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;
        $action_id = $this -> _getParam('action_id', 0);
        if (!$action_id) {
            return $this -> _forward('notfound', 'error', 'core');
        }
        $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getActionById($action_id);
        
        list($attachment) = $action->getAttachments();
        if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item && $attachment->item -> getType() == 'core_link')
        {
            $attachment -> meta -> delete();
            $attachment -> item -> delete();
            $action->attachment_count = 0;
            $action -> save();
        }
        
        // Success
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your link preview has been removed.');

        // Redirect if not json context
        $this -> view -> body = $this -> view -> ynfeed($action, array('noList' => true));
    }

    public function updateSaveFeedAction() {
        // Make sure user exists
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        // Collect params
        $action_id = $this -> _getParam('action_id');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getActionById($action_id);
        // Start transaction
        $table = Engine_Api::_() -> getDbtable('saveFeeds', 'ynfeed');
        $table -> setSaveFeeds($viewer, $action_id, $action -> type);
        // Success
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('This change has been saved.');

        // Redirect if not json context
        $this -> view -> body = $this -> view -> ynfeed($action, array('noList' => true));
    }
    
    public function updateNotificationAction() 
    {
        // Make sure user exists
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        // Collect params
        $action_id = $this -> _getParam('action_id');
        $value = $this -> _getParam('value', 0);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getActionById($action_id);
        // Start transaction
        $table = Engine_Api::_() -> getDbtable('optionFeeds', 'ynfeed');
        $table -> setOptionFeeds($viewer, $action_id, $action -> type, 'notification', $value);
        // Success
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('This change has been saved.');

        // Redirect if not json context
        $this -> view -> body = $this -> view -> ynfeed($action, array('noList' => true));
    }
    
    public function updateCommentAction() {
        // Make sure user exists
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        // Collect params
        $action_id = $this -> _getParam('action_id');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getActionById($action_id);
        // Start transaction
        $table = Engine_Api::_() -> getDbtable('optionFeeds', 'ynfeed');
        $table -> setOptionFeeds($viewer, $action_id, $action -> type, 'comment');
        // Success
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('This change has been saved.');

        // Redirect if not json context
        $this -> view -> body = $this -> view -> ynfeed($action, array('noList' => true));
    }
    
    public function updateLockAction() {
        // Make sure user exists
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        // Collect params
        $action_id = $this -> _getParam('action_id');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getActionById($action_id);
        // Start transaction
        $table = Engine_Api::_() -> getDbtable('optionFeeds', 'ynfeed');
        $table -> setOptionFeeds($viewer, $action_id, $action -> type, 'lock');
        // Success
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('This change has been saved.');

        // Redirect if not json context
        $this -> view -> body = $this -> view -> ynfeed($action, array('noList' => true));
    }

    public function hideItemAction() {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        $this -> view -> type = $type = $this -> _getParam('type', null);
        $this -> view -> id = $id = $this -> _getParam('id', null);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $viewer_id = $viewer -> getIdentity();
        if (empty($type) || empty($id))
            return;
        $this -> view -> status = true;
        Engine_Api::_() -> getDbtable('hide', 'ynfeed') -> insert(array('user_id' => $viewer_id, 'hide_resource_type' => $type, 'hide_resource_id' => $id));
    }

    public function unHideItemAction() {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $viewer_id = $viewer -> getIdentity();
        $hideTable = Engine_Api::_() -> getDbtable('hide', 'ynfeed');
        $type = $this -> _getParam('type', null);
        $id = $this -> _getParam('id', null);
        if (empty($type) || empty($id))
            return;
        $this -> view -> status = true;
        $hideTable -> delete(array('user_id = ?' => $viewer_id, 'hide_resource_type =? ' => $type, 'hide_resource_id =?' => $id));
    }

    public function addReportAction() {
        $subject = null;
        //GET THE VIEWER
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject_guid = $this -> _getParam('subject', null);
        if ($subject_guid) {
            $subject = Engine_Api::_() -> getItemByGuid($subject_guid);
        }

        $this -> view -> form = $form = new Core_Form_Report();
        $form -> populate($this -> _getAllParams());

        if (!$this -> getRequest() -> isPost()) {
            return;
        }
        if (!$form -> isValid($this -> getRequest() -> getPost())) {
            return;
        }

        // PROCESS
        $table = Engine_Api::_() -> getItemTable('core_report');
        $db = $table -> getAdapter();
        $db -> beginTransaction();

        try {
            $report = $table -> createRow();
            $report -> setFromArray(array_merge($form -> getValues(), array('subject_type' => $subject -> getType(), 'subject_id' => $subject -> getIdentity(), 'user_id' => $viewer -> getIdentity(), )));
            $report -> save();

            // Increment report count
            Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('core.reports');
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        return $this -> _forward('success', 'utility', 'core', array('messages' => $this -> view -> translate('Your report has been submitted.'), 'smoothboxClose' => true, 'parentRefresh' => false, ));
    }

    public function viewSharedAction() {
        $action_id = $this -> _getParam('id', 0);
        if (!$action_id) {
            return $this -> _forward('notfound', 'error', 'core');
        }
        $type = "";
        $id = 0;
        $action = Engine_Api::_() -> getItem('activity_action', $action_id);
        if(count($action->getAttachments()) > 0)
        {
            $atts = $action->getAttachments();
            $type = $atts[0] -> item -> getType();
            $id = $atts[0] -> item -> getIdentity();
        }
        $actions = Engine_Api::_() -> ynfeed() -> getShareds($action_id, $type, $id);
        // Add javascript
        $headScript = new Zend_View_Helper_HeadScript();
        $headScript -> appendFile('application/modules/Ynfeed/externals/scripts/core.js');
        $paginator = Zend_Paginator::factory($actions);
        $paginator -> setItemCountPerPage(10);
        $paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
        $this -> view -> actions = $paginator;
        $this -> view -> count = $paginator -> getTotalItemCount();
        $this -> view -> ajax = $this -> _getParam('ajax', 0);
        $this -> view -> action_id = $action_id;
    }

    public function editHideOptionsAction() {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $viewer_id = $viewer -> getIdentity();
        $hideTable = Engine_Api::_() -> getDbtable('hide', 'ynfeed');

        if (!$this -> getRequest() -> isPost()) 
        {
            $this -> view -> hideItems = $hideItems = $hideTable -> getHideItemByMember($viewer, array('not_activity_action' => 1));
            return;
        }
        $unhide_items = $_POST['unhide_items'];

        if (!empty($unhide_items)) {
            $unhide_items = explode(',', $unhide_items);
            foreach ($unhide_items as $value) {

                $resource = explode('-', $value);
                $hideTable -> delete(array('user_id = ?' => $viewer_id, 'hide_resource_type =? ' => $resource[0], 'hide_resource_id =?' => $resource[1]));
            }
        }
        return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
    }
    public function getGroupPrivaciesAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if (!$viewer -> getIdentity()) {
            $data = null;
        } else {
            $data = array();
            $text = $this -> _getParam('search', $this -> _getParam('value'));
            // Get General
            $subjectType = $this -> _getParam('subjectType', '');
            if($subjectType == "" or $subjectType == 'user')
            {
                if(!empty($text))
                {
                    if(strpos(strtolower($this -> view -> translate("Everyone")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'everyone', 
                            'label' => $this -> view -> translate("Everyone"), 
                            'photo' => '', 
                            'url' => '');
                    if(strpos(strtolower($this -> view -> translate("Friends & Networks")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'network', 
                            'label' => $this -> view -> translate("Friends & Networks"),  
                            'photo' => '', 
                            'url' => '');
                    if(strpos(strtolower($this -> view -> translate("Friends Only")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'member', 
                            'label' => $this -> view -> translate("Friends Only"), 
                            'photo' => '', 
                            'url' => '');
                    if(strpos(strtolower($this -> view -> translate("Only Me")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'owner', 
                            'label' => $this -> view -> translate("Only Me"), 
                            'photo' => '', 
                            'url' => '');
                }
                else 
                {
                    $data[] = array('type' => 'general', 
                        'id' => 'everyone', 
                        'label' => $this -> view -> translate("Everyone"), 
                        'photo' => '', 
                        'url' => '');
                    $data[] = array('type' => 'general', 
                        'id' => 'network', 
                        'label' => $this -> view -> translate("Friends & Networks"), 
                        'photo' => '', 
                        'url' => '');
                    $data[] = array('type' => 'general', 
                        'id' => 'member', 
                        'label' => $this -> view -> translate("Friends Only"), 
                        'photo' => '', 
                        'url' => '');
                    $data[] = array('type' => 'general', 
                        'id' => 'owner', 
                        'label' => $this -> view -> translate("Only Me"), 
                        'photo' => '', 
                        'url' => '');
                }
                
                // Get Networks
                $networkTable = Engine_Api::_()->getDbtable('networks', 'network');
                $ntable_name = $networkTable -> info('name');
                $select = Engine_Api::_()->getDbtable('membership', 'network') 
                    ->getMembershipsOfSelect($viewer) 
                    -> where("$ntable_name.title LIKE '%{$text}%'");
                foreach ($networkTable -> fetchAll ( $select ) as $network) {
                    $data[] = array('type' => 'network', 'id' => $network -> getIdentity(), 'guid' => '', 'label' => $network -> getTitle(), 'photo' => '', 'url' => '');
                }
                
                // Get Friend List
                $listTable = Engine_Api::_()->getItemTable('user_list');
                $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()) -> where("title LIKE '%{$text}%'") -> order('title ASC') -> limit(50));
                foreach ($lists as $list) 
                {
                    $data[] = array('type' => 'friendlist', 
                        'id' => $list -> list_id, 
                        'label' => $list -> getTitle(), 
                        'photo' => '', 
                        'url' => '');
                }
                
                // Get Friends
                $table = Engine_Api::_() -> getItemTable('user');
                $table_name = $table -> info('name');
                $select = $viewer -> membership() -> getMembersObjectSelect();
                $select -> limit(50) -> order("$table_name.displayname ASC");
                if(!empty($text)) 
                {
                    $select -> where("$table_name.displayname LIKE '%{$text}%' OR $table_name.username LIKE '%{$text}%'");
                }
                foreach ($table -> fetchAll ( $select ) as $friend) 
                {
                    if($friend -> getIdentity())
                    {
                        $data[] = array('type' => 'user', 'id' => $friend -> getIdentity(), 'guid' => $friend -> getGuid(), 'label' => $friend -> getTitle(), 'photo' => $this -> view -> itemPhoto($friend, 'thumb.icon'), 'url' => $friend -> getHref());
                    }
                }
                
                // Get groups
                $checkGroup = Engine_Api::_() -> getDbtable('modules', 'core') -> isModuleEnabled('group');
                $checkAdvGroup = Engine_Api::_() -> getDbtable('modules', 'core') -> isModuleEnabled('advgroup');
                if (($checkGroup) || ($checkAdvGroup)) 
                {
                    if($checkGroup)
                        $membership = Engine_Api::_()->getDbtable('membership', 'group');
                    else {
                        $membership = Engine_Api::_()->getDbtable('membership', 'advgroup');
                    }
                    $groupTable = Engine_Api::_() -> getItemTable('group');
                    $table_name = $groupTable -> info('name');
                    $group_select = $membership->getMembershipsOfSelect($viewer);
                    $group_select -> where("$table_name.title LIKE ?", '%' . $text . '%') -> where('search = 1') -> order('title ASC') -> limit(50);
                    $group_results = $groupTable -> fetchAll($group_select);
                    foreach ($group_results as $result) 
                    {
                        $data[] = array('type' => 'group', 'id' => $result -> getIdentity(), 'guid' => $result -> getGuid(), 'label' => $result -> getTitle(), 'photo' => $this -> view -> itemPhoto($result, 'thumb.icon'), 'url' => $result -> getHref());
                    }
                }
            }
            elseif($subjectType == 'group') 
            {
                if(!empty($text))
                {
                    if(strpos(strtolower($this -> view -> translate("Everyone")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'everyone', 
                            'label' => $this -> view -> translate("Everyone"), 
                            'photo' => '', 
                            'url' => '');
                    if(strpos(strtolower($this -> view -> translate("All Group Members")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'member', 
                            'label' => $this -> view -> translate("All Group Members"), 
                            'photo' => '', 
                            'url' => '');
                    if(strpos(strtolower($this -> view -> translate("Officers and Owner Only")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'officer', 
                            'label' => $this -> view -> translate("Officers and Owner Only"), 
                            'photo' => '', 
                            'url' => '');
                    if(strpos(strtolower($this -> view -> translate("Owner Only")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'owner', 
                            'label' => $this -> view -> translate("Owner Only"), 
                            'photo' => '', 
                            'url' => '');
                }
                else 
                {
                    $data[] = array('type' => 'general', 
                        'id' => 'everyone', 
                        'label' => $this -> view -> translate("Everyone"), 
                        'photo' => '', 
                        'url' => '');
                    $data[] = array('type' => 'general', 
                        'id' => 'member', 
                        'label' => $this -> view -> translate("All Group Members"), 
                        'photo' => '', 
                        'url' => '');
                    $data[] = array('type' => 'general', 
                        'id' => 'officer', 
                        'label' => $this -> view -> translate("Officers and Owner Only"), 
                        'photo' => '', 
                        'url' => '');
                    $data[] = array('type' => 'general', 
                        'id' => 'owner', 
                        'label' => $this -> view -> translate("Owner Only"), 
                        'photo' => '', 
                        'url' => '');
                }
            }
            elseif($subjectType == 'ynbusinesspages_business') 
            {
                if(!empty($text))
                {
                    if(strpos(strtolower($this -> view -> translate("Everyone")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'everyone', 
                            'label' => $this -> view -> translate("Everyone"), 
                            'photo' => '', 
                            'url' => '');
                    if(strpos(strtolower($this -> view -> translate("All Business Members")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'member', 
                            'label' => $this -> view -> translate("All Business Members"), 
                            'photo' => '', 
                            'url' => '');
                    if(strpos(strtolower($this -> view -> translate("Admins and Owner Only")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'admin', 
                            'label' => $this -> view -> translate("Admins and Owner Only"), 
                            'photo' => '', 
                            'url' => '');
                    if(strpos(strtolower($this -> view -> translate("Owner Only")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'owner', 
                            'label' => $this -> view -> translate("Owner Only"), 
                            'photo' => '', 
                            'url' => '');
                }
                else 
                {
                    $data[] = array('type' => 'general', 
                        'id' => 'everyone', 
                        'label' => $this -> view -> translate("Everyone"), 
                        'photo' => '', 
                        'url' => '');
                    $data[] = array('type' => 'general', 
                        'id' => 'member', 
                        'label' => $this -> view -> translate("All Business Members"), 
                        'photo' => '', 
                        'url' => '');
                    $data[] = array('type' => 'general', 
                        'id' => 'admin', 
                        'label' => $this -> view -> translate("Admins and Owner Only"), 
                        'photo' => '', 
                        'url' => '');
                    $data[] = array('type' => 'general', 
                        'id' => 'owner', 
                        'label' => $this -> view -> translate("Owner Only"), 
                        'photo' => '', 
                        'url' => '');
                }
            }
            elseif($subjectType == 'event')
            {
                if(!empty($text))
                {
                    if(strpos(strtolower($this -> view -> translate("Everyone")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'everyone', 
                            'label' => $this -> view -> translate("Everyone"), 
                            'photo' => '', 
                            'url' => '');
                    if(strpos(strtolower($this -> view -> translate("Event Guests Only")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'member', 
                            'label' => $this -> view -> translate("Event Guests Only"), 
                            'photo' => '', 
                            'url' => '');
                    if(strpos(strtolower($this -> view -> translate("Owner Only")), strtolower($text)) !== FALSE)
                        $data[] = array('type' => 'general', 
                            'id' => 'owner', 
                            'label' => $this -> view -> translate("Owner Only"), 
                            'photo' => '', 
                            'url' => '');
                }
                else
                {
                    $data[] = array('type' => 'general', 
                        'id' => 'everyone', 
                        'label' => $this -> view -> translate("Everyone"), 
                        'photo' => '', 
                        'url' => '');
                    $data[] = array('type' => 'general', 
                        'id' => 'member', 
                        'label' => $this -> view -> translate("Event Guests Only"), 
                        'photo' => '', 
                        'url' => '');
                    $data[] = array('type' => 'general', 
                        'id' => 'owner', 
                        'label' => $this -> view -> translate("Owner Only"), 
                        'photo' => '', 
                        'url' => '');
                }
            }
        }
        if ($this -> _getParam('sendNow', true)) {
            return $this -> _helper -> json($data);
        } else {
            $this -> _helper -> viewRenderer -> setNoRender(true);
            $data = Zend_Json::encode($data);
            $this -> getResponse() -> setBody($data);
        }
    }
    
    public function likeAction() {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
          return;
    
        // Collect params
        $action_id = $this->_getParam('action_id');
        $comment_id = $this->_getParam('comment_id');
        $viewer = Engine_Api::_()->user()->getViewer();

        // Start transaction
        $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
        $db->beginTransaction();
    
        try {
          $action = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getActionById($action_id);
    
          // Action
          if (!$comment_id) {
    
            if (Engine_Api::_()->ynfeed()->checkEnabledAdvancedComment() && $action->dislikes()->isDislike($viewer))
                $action->dislikes()->removeDislike($viewer);
               
            if($action && $action->getCommentObject() && !in_array($action->getCommentObject() -> getType(), array('core_link', 'ynfeed_map', 'advalbum_photo')))
            {
                 // Check authorization
                if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment'))
                    throw new Engine_Exception('This user is not allowed to like on this item.');
            }
    
            $action->likes()->addLike($viewer);
    
            // Add notification for owner of activity (if user and not viewer)
            if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) 
            {
                $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($actionOwner, $viewer, $action, 'liked', array(
                  'label' => 'post'
                ));
            }
            $hideReply = false;
          }
          // Comment
          else {
            $comment = $action->comments()->getComment($comment_id);
            $hideReply = false;
            $commentItem = $comment;
            
            if(isset($commentItem->parent_comment_id) && !empty($commentItem->parent_comment_id)) {
                $hideReply = true;
            }
            if (Engine_Api::_()->ynfeed()->checkEnabledAdvancedComment() 
                && Engine_Api::_()->getDbtable('dislikes', 'yncomment')->isDislike($commentItem, $viewer))
                Engine_Api::_()->getDbtable('dislikes', 'yncomment')->removeDislike($commentItem, $viewer);
            
            $comment->likes()->addLike($viewer);
    
            // @todo make sure notifications work right
            if ($comment->poster_id != $viewer->getIdentity() && $comment->getPoster()->getType() == 'user') {
              Engine_Api::_()->getDbtable('notifications', 'activity')
                      ->addNotification($comment->getPoster(), $viewer, $comment, 'liked', array(
                          'label' => 'comment'
              ));
            }
    
            // Add notification for owner of activity (if user and not viewer)
            if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
              $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
            }
          }
    
          //FEED LIKE NOTIFICATION WORK
          $object_type = $action->object_type;
          $object_id = $action->object_id;
    
          // Stats
          Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');
    
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
    
        // Success
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('You now like this action.');
    
        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
          $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
          $helper = 'ynfeed';
          $method = 'update';
          $show_all_comments = $this -> _getParam('show_all_comments');
          if(is_array($show_all_comments) && count($show_all_comments) > 1)
          {
             $show_all_comments = $show_all_comments[1];
          }
          $comment_like_box_show = $this->_getParam('comment_like_box_show');
          $onViewPage = $this -> _getParam('onViewPage');
          if ($onViewPage) {
            $show_all_comments = true;
          }
          $this->view->body = $this->view->$helper($action, array('noList' => true, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'hideReply' => $hideReply, 'ynfeed_comment_like_box_show' => $comment_like_box_show), $method, $show_all_comments);
        }
    }

    public function unlikeAction() {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
          return;
    
        // Collect params
        $action_id = $this->_getParam('action_id');
        $comment_id = $this->_getParam('comment_id');
        $viewer = Engine_Api::_()->user()->getViewer();
    
        // Start transaction
        $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
        $db->beginTransaction();
    
        try {
          $action = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getActionById($action_id);
    
          // Action
          if (!$comment_id) {
            if($action && $action->getCommentObject() && !in_array($action->getCommentObject() -> getType(), array('core_link', 'ynfeed_map', 'advalbum_photo')))
            {
                 // Check authorization
                if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment'))
                    throw new Engine_Exception('This user is not allowed to unlike on this item.');
            }
    
            if($action->likes()->isLike($viewer))
                $action->likes()->removeLike($viewer);
            
            if(Engine_Api::_()->ynfeed()->checkEnabledAdvancedComment() && !$action->dislikes()->isDislike($viewer))
                $action->dislikes()->addDislike($viewer);
                $hideReply = false;
            }
          // Comment
          else 
          {
                $comment = $action->comments()->getComment($comment_id);
                $hideReply = false;
                $commentItem = $comment;
                if(isset($commentItem->parent_comment_id) && !empty($commentItem->parent_comment_id)) {
                    $hideReply = true;
                }
                if($commentItem->likes()->isLike($viewer))
                $commentItem->likes()->removeLike($viewer);
                
                if (Engine_Api::_()-> ynfeed()->checkEnabledAdvancedComment() 
                && !Engine_Api::_()->getDbtable('dislikes', 'yncomment')->isDislike($commentItem, $viewer))
                        Engine_Api::_()->getDbtable('dislikes', 'yncomment')->addDislike($commentItem, $viewer);
          }
    
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
    
        // Success
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('You no longer like this action.');
    
        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
          $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
          $helper = 'ynfeed';
          $method = 'update';
          $show_all_comments = $this -> _getParam('show_all_comments');
          if(is_array($show_all_comments) && count($show_all_comments) > 1)
          {
             $show_all_comments = $show_all_comments[1];
          }
          $onViewPage = $this -> _getParam('onViewPage');
          $comment_like_box_show = $this->_getParam('comment_like_box_show');
          if ($onViewPage) {
            $show_all_comments = true;
          }
          $this->view->body = $this->view->$helper($action, array('noList' => true, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'hideReply' => $hideReply, 'ynfeed_comment_like_box_show' => $comment_like_box_show), $method, $show_all_comments);
        }
    }
    public function commentAction() 
    {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
            return;
    
        // Make form
        $this->view->form = $form = new Yncomment_Form_Feed_Comment();
        
        // Not post
        if (!$this->getRequest()->isPost()) 
        {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not a post');
            return;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $params = $this -> _getAllParams();
        $body = $params['body'];
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $params['body'] = $body;
        
        // Not valid
        if (!$form->isValid($params)) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
            return;
        }
        // Start transaction
        $db = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getAdapter();
        $db->beginTransaction();
    
        try {
            $viewer = Engine_Api::_()->user()->getViewer();
            $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
            $action = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getActionById($action_id);
            $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
            // Filter HTML
            $filter = new Zend_Filter();
            $filter -> addFilter(new Engine_Filter_Censor());
            $filter -> addFilter(new Engine_Filter_HtmlSpecialChars());
            $body = $form->getValue('body');
            // fix SE enable link issue
            $body = str_replace('&amp;quot;"', '"', $body);
            $body = str_replace('&quot;</a>', '</a>"', $body);
            
            $body = preg_replace('/<br[^<>]*>/', "\n", $body);
            
            if($action->getCommentObject() && !in_array($action->getCommentObject() -> getType(), array('core_link', 'ynfeed_map', 'advalbum_photo')))
            {
                 // Check authorization
                if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment'))
                    throw new Engine_Exception('This user is not allowed to comment on this item.');
            }
             // Add the comment
            $subject = $viewer;
            $row =  $action->comments()->addComment($subject, $body);
    
            // Notifications
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
    
            // Add notification for owner of activity (if user and not viewer)
            if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
                $notifyApi->addNotification($actionOwner, $subject, $action, 'commented', array(
                    'label' => 'post'
                ));
            }
    
            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            $commentedUserNotifications = array();
            foreach ($action->comments()->getAllCommentsUsers() as $notifyUser) {
                if ($notifyUser->getType() == 'user' && $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                    $commentedUserNotifications[] = $notifyUser->getIdentity();  
                    $notifyApi->addNotification($notifyUser, $subject, $action, 'commented_commented', array(
                        'label' => 'post'
                    ));
                }
            }
    
            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            foreach ($action->likes()->getAllLikesUsers() as $notifyUser) 
            {
                if ($notifyUser->getType() == 'user' && $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
                    // Don't send a notification if the user both commented and liked this
                    if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
                        continue;
                    $notifyApi->addNotification($notifyUser, $subject, $action, 'liked_commented', array(
                        'label' => 'post'
                     ));
                }
            }
    
            $attachment = null; 
            $attachmentPhotoValue = $this->_getParam('photo_id');
            $attachmentType =$this->_getParam('type');
            
            $linkEnabled = $this -> _getParam('linkEnabled');
            
            if ($attachmentPhotoValue && $attachmentType) 
            {
                if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
                    $attachment = Engine_Api::_()->getItem('advalbum_photo', $attachmentPhotoValue);
                else
                    $attachment = Engine_Api::_()->getItem('album_photo', $attachmentPhotoValue);
            }
            // check body exist link
            else if ($linkEnabled && Engine_Api::_()->authorization()->isAllowed('core_link', null, 'create')) 
            {
                $body_decode = html_entity_decode($body);
                $body_decode = html_entity_decode($body_decode);
                $body_decode = html_entity_decode($body_decode);
                $regex = '/http(s)?:\/\/([^" ]*)/mi';
                preg_match_all($regex, $body_decode, $matches);
                if (count($matches) > 0) 
                {
                    $link = $matches[0][0];
                    if ($link) 
                    {
                        $info = parse_url($link);
                        try
                        {
                          $client = new Zend_Http_Client($link, array(
                            'maxredirects' => 2,
                            'timeout'      => 30,
                          ));
                    
                          // Try to mimic the requesting user's UA
                          $client->setHeaders(array(
                            'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                            'X-Powered-By' => 'Zend Framework'
                          ));
                          $response = $client->request();
                    
                          // Get content-type
                          list($contentType) = explode(';', $response->getHeader('content-type'));
                          $this->view->contentType = $contentType;
                    
                          // Handling based on content-type
                          switch( strtolower($contentType) ) 
                          {
                            // Images
                            case 'image/gif':
                            case 'image/jpeg':
                            case 'image/jpg':
                            case 'image/tif': // Might not work
                            case 'image/xbm':
                            case 'image/xpm':
                            case 'image/png':
                            case 'image/bmp': // Might not work
                              $attachmentData = $this->_previewImage($link, $response);
                              break;
                    
                            // HTML
                            case '':
                            case 'text/html':
                              $attachmentData = $this->_previewHtml($link, $response);
                              break;
                    
                            // Plain text
                            case 'text/plain':
                              $attachmentData = $this->_previewText($link, $response);
                              break;
                          }
                        }
                        catch( Exception $e )
                        {
                          throw $e;
                        }
                        if($attachmentData)
                        {
                            $attachmentData['uri'] = $link;
                            if (Engine_Api::_() -> core() -> hasSubject()) {
                                $subject = Engine_Api::_() -> core() -> getSubject();
                                if ($subject -> getType() != 'user') 
                                {
                                    $attachmentData['parent_type'] = $subject -> getType();
                                    $attachmentData['parent_id'] = $subject -> getIdentity();
                                }
                            }
                            if (!empty($attachmentData['title'])) {
                                $attachmentData['title'] = $filter -> filter($attachmentData['title']);
                            }
                            if (!empty($attachmentData['description'])) {
                                $attachmentData['description'] = $filter -> filter($attachmentData['description']);
                            }
                            else {
                                $attachmentData['description'] = $attachmentData['title'];
                            }
                            $attachment = Engine_Api::_() -> getApi('links', 'core') -> createLink($viewer, $attachmentData);
                        }
                    }
                }
            }
            if (isset($row->attachment_type))
                $row->attachment_type = ( $attachment ? $attachment->getType() : '' );
            if (isset($row->attachment_id))
                $row->attachment_id = ( $attachment ? $attachment->getIdentity() : 0 );
            $row->save();
                
            $composerDatas = $this->getRequest()->getParam('composer', null);
            
            $tagsArray = array();
            parse_str($composerDatas['tag'], $tagsArray);
            if (!empty($tagsArray)) {
                $viewer = Engine_Api::_()->_()->user()->getViewer();
                $type_name = Zend_Registry::get('Zend_Translate')->translate('post');
                if (is_array($type_name)) {
                    $type_name = $type_name[0];
                } else {
                    $type_name = 'post';
                }
                $notificationAPi = Engine_Api::_()->getDbtable('notifications', 'activity');
     
                foreach ($tagsArray as $key => $tagStrValue) {
                    $tag = Engine_Api::_()->getItemByGuid($key);
                    // Don't send a notification if the user both commented and liked this
                        if (in_array($tag->getIdentity(), $commentedUserNotifications))
                           continue;
                        
                    if ($action && $tag && ($tag instanceof User_Model_User) && !$tag->isSelf($viewer)) {
                        $notificationAPi->addNotification($tag, $viewer, $action, 'tagged', array(
                            'object_type_name' => $type_name,
                            'label' => $type_name,
                        ));
                    } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) {
                        $subject_title = $viewer->getTitle();
                        $item_type = Zend_Registry::get('Zend_Translate')->translate($tag->getShortType());
                        $item_title = $tag->getTitle();
                        $owner = $tag->getOwner();
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                            $notificationAPi->addNotification($owner, $viewer, $action, 'yncomment_tagged', array(
                                'subject_title' => $subject_title,
                                'label' => $type_name,
                                'object_type_name' => $type_name,
                                'item_title' => $item_title,
                                'item_type' => $item_type
                            ));
                        }
                        if (($tag instanceof Group_Model_Group)) {
                            foreach ($tag->getOfficerList()->getAll() as $offices) {
                                $owner = Engine_Api::_()->getItem('user', $offices->child_id);
                                if ($action && $owner && ($owner instanceof User_Model_User) && !$owner->isSelf($viewer)) {
                                    $notificationAPi->addNotification($owner, $viewer, $action, 'yncomment_tagged', array(
                                        'subject_title' => $subject_title,
                                        'label' => $type_name,
                                        'object_type_name' => $type_name,
                                        'item_title' => $item_title,
                                        'item_type' => $item_type
                                    ));
                                }
                            }
                        }
                    }
                }
    
                if ($action) {
                    $data = array_merge((array) $action->params, array('tags' => $tagsArray));
                    $row->params = Zend_Json::encode($data);
                }
                $row->save();
            }        
          // Stats
          Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');
    
          $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    
        // Assign message for json
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment posted');
        $this->view->comment_id = $row->getIdentity();    
        // Redirect if not json
        if (null === $this->_getParam('format', null)) {
          $this->_redirect($form->return_url->getValue(), array('prependBase' => false));
        } else if ('json' === $this->_getParam('format', null)) {
          $helper = 'ynfeed';
          $method = 'update';
          $show_all_comments = $this->_getParam('show_all_comments');
          if(is_array($show_all_comments) && count($show_all_comments) > 1)
          {
              $show_all_comments = $show_all_comments[1];
          }
          $comment_like_box_show = $this->_getParam('comment_like_box_show');
          $onViewPage = $this->_getParam('onViewPage');
          
          if ($onViewPage) {
            $show_all_comments = true;
          }
          
          $this->view->body = $this->view->$helper($action, array('noList' => true, 'submitComment' => true, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'hideReply' => false, 'action_id' => $action_id, 'ynfeed_comment_like_box_show' => $comment_like_box_show), $method, $show_all_comments);
        }
    }
    /**
       * Handles HTTP request to get an activity feed item's comments and returns 
       * a Json as the response
       *
       * Uses the default route and can be accessed from
       *  - /activity/index/viewcomment
       *
       * @return void
       */
    public function viewcommentAction() 
    {
        // Collect params
        $action_id = $this->_getParam('action_id');
        $viewer = Engine_Api::_()->user()->getViewer();
    
        $action = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getActionById($action_id);
        $form = $this->view->form = new Activity_Form_Comment();
        $form->setActionIdentity($action_id);
    
        // Redirect if not json context
        if (null === $this->_getParam('format', null)) {
          $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_getParam('format', null)) {
          $helper = 'ynfeed';
          $this->view->body = $this->view->$helper($action, array('viewAllComments' => true, 'noList' => $this->_getParam('nolist', false), 'ynfeed_comment_like_box_show' => 1));
        }
    }
    
    // Delete comment or reply
    public function deleteAction() 
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');
    
        if (!$this->_helper->requireUser()->isValid())
          return;
    
        // Identify if it's an action_id or comment_id being deleted
        $comment_id = $this->_getParam('comment_id', null);
        $action_id = $this->_getParam('action_id', null);
    
        $action = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getActionById($action_id);
        if (!$action) 
        {
            // tell smoothbox to close
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot delete this item because it has been removed.');
            return;
        }
    
        $is_owner = false;
        if (Engine_Api::_()->core()->hasSubject()) 
        {
            $subject = Engine_Api::_()->core()->getSubject();
            switch ($subject->getType()) {
            case 'user':
              $is_owner = $viewer->isSelf($subject);
              break;
            default :
              $is_owner = $viewer->isSelf($subject->getOwner());
              break;
            }
        }
        if($comment_id)
        {
            $hideReply = false;
            $comment = $action->comments()->getComment($comment_id);
            if(isset($comment->parent_comment_id) && !empty($comment->parent_comment_id)) 
            {
                $hideReply = true;
            }
            // allow delete if profile/entry owner
            $db = Engine_Api::_()->getDbtable('comments', 'activity')->getAdapter();
            $db->beginTransaction();
            if ($activity_moderate || $is_owner ||
                  ('user' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
                  ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id)) 
            {
                try 
                {
                    $action -> removeReply($comment_id);
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollback();
                    $this->view->status = false;
                }
            } else {
                $this->view->message = Zend_Registry::get('Zend_Translate')->_('You do not have the privilege to delete this comment');
                return;
            }
        }
        // Success
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment has been deleted');
          
        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
              $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
              $helper = 'ynfeed';
              $method = 'update';
              $show_all_comments = $this -> _getParam('show_all_comments');
              if(is_array($show_all_comments) && count($show_all_comments) > 1)
              {
                 $show_all_comments = $show_all_comments[1];
              }
              $onViewPage = $this -> _getParam('onViewPage');
              if ($onViewPage) {
                $show_all_comments = true;
              }
              $this->view->body = $this->view->$helper($action, array('noList' => true, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'hideReply' => $hideReply, 'ynfeed_comment_like_box_show' => 1), $method, $show_all_comments);
        }
    }
    
    public function removeLinkAction() {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
          return;
    
        // Collect params
        $action_id = $this->_getParam('action_id');
        $comment_id = $this->_getParam('comment_id');
        $viewer = Engine_Api::_()->user()->getViewer();

        // Start transaction
        $db = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getAdapter();
        $db->beginTransaction();
    
        try {
          $action = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getActionById($action_id);
    
          if ($comment_id) 
          {
            $comment = $action->comments()->getComment($comment_id);
            $attachment = Engine_Api::_() -> getItem($comment -> attachment_type, $comment -> attachment_id);
            $attachment -> delete();
            $comment -> attachment_type = '';
            $comment -> attachment_id = 0;
            $comment -> save();
          }
    
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
    
        // Success
        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Remove link successfully.');
    
        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
          $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
          $helper = 'ynfeed';
          $method = 'update';
          $show_all_comments = $this -> _getParam('show_all_comments');
          if(is_array($show_all_comments) && count($show_all_comments) > 1)
          {
             $show_all_comments = $show_all_comments[1];
          }

          $onViewPage = $this -> _getParam('onViewPage');
          if ($onViewPage) {
            $show_all_comments = true;
          }
          $this->view->body = $this->view->$helper($action, array('noList' => true, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'ynfeed_comment_like_box_show' => 1), $method, $show_all_comments);
        }
    }

    public function openHideCommentAction() {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
          return;
    
        // Collect params
        $action_id = $this->_getParam('action_id');
        $action = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getActionById($action_id);
        $comment_id = $this -> _getParam('comment_id');
        $hideReply = false;
        if ($comment_id) 
        {
            $comment = $action->comments()->getComment($comment_id);
        }
        if(isset($comment->parent_comment_id) && !empty($comment->parent_comment_id)) 
        {
            $hideReply = true;
        }
    
        // Success
        $this->view->status = true;
    
        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
          $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
          $helper = 'ynfeed';
          $method = 'update';
          $show_all_comments = $this -> _getParam('show_all_comments');
          if(is_array($show_all_comments) && count($show_all_comments) > 1)
          {
             $show_all_comments = $show_all_comments[1];
          }

          $onViewPage = $this -> _getParam('onViewPage');
          if ($onViewPage) {
            $show_all_comments = true;
          }
          $this->view->body = $this->view->$helper($action, array('noList' => true, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'openHide' => $comment_id, 'hideReply' => $hideReply, 'ynfeed_comment_like_box_show' => 1), $method, $show_all_comments);
        }
    }
    
    public function unHideCommentAction() {
        // Make sure user exists
        if (!$this->_helper->requireUser()->isValid())
          return;
    
        // Collect params
        $action_id = $this->_getParam('action_id');
        $comment_id = $this -> _getParam('comment_id');
        $action = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getActionById($action_id);
        if ($comment_id) 
        {
            $comment = $action->comments()->getComment($comment_id);
        }
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $viewer_id = $viewer -> getIdentity();
        $hideTable = Engine_Api::_() -> getDbtable('hide', 'yncomment');
        $hideReply = false;
        if ($comment_id) 
        {
            $comment = $action->comments()->getComment($comment_id);
        }
        if(isset($comment->parent_comment_id) && !empty($comment->parent_comment_id)) 
        {
            $hideReply = true;
        }
        $this -> view -> status = false;
        if (!$comment)
            return;
        $hideTable -> delete(array('user_id = ?' => $viewer_id, 'hide_resource_type =? ' => $comment -> getType(), 'hide_resource_id =?' => $comment -> getIdentity()));
       
        // Success
        $this->view->status = true;
    
        // Redirect if not json context
        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
          $this->_helper->redirector->gotoRoute(array(), 'default', true);
        } else if ('json' === $this->_helper->contextSwitch->getCurrentContext()) {
          $helper = 'ynfeed';
          $method = 'update';
          $show_all_comments = $this -> _getParam('show_all_comments');
          if(is_array($show_all_comments) && count($show_all_comments) > 1)
          {
             $show_all_comments = $show_all_comments[1];
          }

          $onViewPage = $this -> _getParam('onViewPage');
          if ($onViewPage) {
            $show_all_comments = true;
          }
          $this->view->body = $this->view->$helper($action, array('noList' => true, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'hideReply' => $hideReply, 'ynfeed_comment_like_box_show' => 1), $method, $show_all_comments);
        }
    }
    
    protected function _previewImage($uri, Zend_Http_Response $response) 
    {
        return array('thumb' => $uri);
    }

    protected function _previewText($uri, Zend_Http_Response $response) {
        $body = $response -> getBody();
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getHeader('content-type'), $matches) || preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getBody(), $matches)) {
            $charset = trim($matches[1]);
        } else {
            $charset = 'UTF-8';
        }
        // Reduce whitespace
        $body = preg_replace('/[\n\r\t\v ]+/', ' ', $body);
        return array('title' => substr($body, 0, 63), 'description' => substr($body, 0, 255));
    }

    protected function _previewHtml($uri, Zend_Http_Response $response) 
    {
        $arr_return = array();
        $body = $response -> getBody();
        $body = trim($body);
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getHeader('content-type'), $matches) 
            || preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getBody(), $matches)) 
        {
            $charset = trim($matches[1]);
        } else {
            $charset = 'UTF-8';
        }
        if (function_exists('mb_convert_encoding')) {
            $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
        }

        // Get DOM
        if (class_exists('DOMDocument')) {
            $dom = new Zend_Dom_Query($body);
        } else {
            $dom = null;
            // Maybe add b/c later
        }

        $title = null;
        if ($dom) {
            $titleList = $dom -> query('title');
            if (count($titleList) > 0) {
                $title = trim($titleList -> current() -> textContent);
                $title = substr($title, 0, 255);
            }
        }
        $arr_return['title'] = $title;

        $description = null;
        if ($dom) {
            $descriptionList = $dom -> queryXpath("//meta[@name='description']");
            // Why are they using caps? -_-
            if (count($descriptionList) == 0) {
                $descriptionList = $dom -> queryXpath("//meta[@name='Description']");
            }
            if (count($descriptionList) > 0) {
                $description = trim($descriptionList -> current() -> getAttribute('content'));
                $description = substr($description, 0, 255);
            }
        }
        $arr_return['description'] = $description;

        $thumb = null;
        if ($dom) 
        {
            $mediumList = $dom -> queryXpath("//meta[@property='og:image']");
            if (count($mediumList) > 0) {
                $thumb = $mediumList -> current() -> getAttribute('content');
            }
            if(!$thumb)
            {
                $thumbList = $dom -> queryXpath("//link[@rel='image_src']");
                if (count($thumbList) > 0) {
                    $thumb = $thumbList -> current() -> getAttribute('href');
                }
             }
        }
        $arr_return['thumb'] = $thumb;
        return $arr_return;
    }

}