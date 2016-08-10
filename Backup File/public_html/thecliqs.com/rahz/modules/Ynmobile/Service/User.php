<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: User.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Service_User extends Ynmobile_Service_Base
{
    
	const AUTO_VIRIFY_EMAIL_WHEN_SIGNUP_VIA_MOBILE = 1;
    protected $module = 'user';
    
    protected $mainItemType = 'user';
    
    protected $availabePrivacyOptions  = array(
          'everyone'              => 'Everyone',
          'registered'            => 'All Registered Members',
          'network'               => 'Friends and Networks',
          'member'                => 'Only My Friends',
          'owner'                 => 'Only Me'
    );
    
    
	const REQUIRED_FOR_SIGNUP = TRUE;
	
    function detail($aData){
        extract($aData);
        
        if(empty($fields)) $fields  = 'detail';
        
        $fields = explode(',', $fields);
                
        $user = Engine_Api::_()->user()->getUser(intval($iUserId));
        
        if(!$user){
            return array(
                'error_code'=>1,
                'error_message'=> 'Member not found!',
            );
        }
        
        return Ynmobile_AppMeta::_export_one($user, $fields);
    }
    
    function _getTimezoneLabel($timezone){
        $timezones = array('US/Pacific'  => '(UTC-8) Pacific Time (US & Canada)',
        'US/Mountain' => '(UTC-7) Mountain Time (US & Canada)',
        'US/Central'  => '(UTC-6) Central Time (US & Canada)',
        'US/Eastern'  => '(UTC-5) Eastern Time (US & Canada)',
        'America/Halifax'   => '(UTC-4)  Atlantic Time (Canada)',
        'America/Anchorage' => '(UTC-9)  Alaska (US & Canada)',
        'Pacific/Honolulu'  => '(UTC-10) Hawaii (US)',
        'Pacific/Samoa'     => '(UTC-11) Midway Island, Samoa',
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
        'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',);
        
        return isset($timezones[$timezone])?$timezones[$timezone]:$timezone;
    }
    
    function _getLocaleLabel($localeKey){
        
        if(empty($localeKey) || $localeKey == 'auto'){
            return 'Automatic';
        }
        
         // Init default locale
        $locale = Zend_Registry::get('Locale');
    
        $localeMultiKeys = array_merge(
          array_keys(Zend_Locale::getLocaleList())
        );
        
        $languages = Zend_Locale::getTranslationList('language', $locale);
        $territories = Zend_Locale::getTranslationList('territory', $locale);
        
             
        foreach($localeMultiKeys as $key)
        {     
           if (!empty($languages[$key])) 
           {
             $localeMultiOptions[$key] = $languages[$key];
           }
           else
           {
                $locale = new Zend_Locale($key);
                $region = $locale->getRegion();
                $language = $locale->getLanguage();         
                if ((!empty($languages[$language]) && (!empty($territories[$region])))) {
                   $localeMultiOptions[$key] =  $languages[$language] . ' (' . $territories[$region] . ')';
                }else{
                    $localeMultiOptions[$key] = $languages[$language];
                }
           }
        }
        return $localeMultiOptions[$localeKey];
    }
    
    
	protected function simpleLogin($viewer, $source = 'facebook')
	{
		$user_id = $viewer->getIdentity();
		Zend_Auth::getInstance()->getStorage()->write($user_id);
        
        if (!$viewer -> verified)
            {
                return array(
                    'error_message' => Zend_Registry::get('Zend_Translate') -> _('This account still requires either email verification.'),
                    'error_code' => 4,
                    'result' => 0
                );
            }
            else if (!$viewer -> approved)
            {
                return array(
                    'error_message' => Zend_Registry::get('Zend_Translate') -> _('This account still requires admin approval.'),
                    'error_code' => 5,
                    'result' => 0
                );
            }else if(!$viewer->enabled){
                return array(
                    'error_message' => Zend_Registry::get('Zend_Translate') -> _('This account still requires admin enable.'),
                    'error_code' => 5,
                    'result' => 0
                );
            }
        
		// Register login
		$viewer->lastlogin_date = date("Y-m-d H:i:s");
		
		// Get ip address
		$db = Engine_Db_Table::getDefaultAdapter();
		$ipObj = new Engine_IP();
		$ipExpr = new Zend_Db_Expr($db -> quoteInto('UNHEX(?)', bin2hex($ipObj -> toBinary())));
			
		if( 'cli' !== PHP_SAPI ) {
			$viewer->lastlogin_ip = $ipExpr;
				
			Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
				'user_id' => $user_id,
				'ip' => $ipExpr,
				'timestamp' => new Zend_Db_Expr('NOW()'),
				'state' => 'success',
				'source' => $source,
			));
		}
		
		$viewer->save();
			
		// Increment sign-in count
		Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('user.logins');
			
		// Run post login hook
		$event = Engine_Hooks_Dispatcher::getInstance() -> callEvent('onUserLoginAfter', $viewer);
		/**
		 * @var array
		*/
		$oToken = Engine_Api::_() -> getDbtable('tokens', 'ynmobile');
		$aToken = $oToken -> createToken($viewer);
			
		$profileimage = $viewer -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		if ($profileimage != "")
		{
			$profileimage = Engine_Api::_() -> ynmobile() -> finalizeUrl($profileimage);
		}
		else
		{
			$profileimage = NO_USER_ICON;
		}
		
		if($viewer->timezone && $viewer->timezone != 'auto'){
			date_default_timezone_set($viewer->timezone);
		}
        
        return Ynmobile_AppMeta::_export_one($viewer, array('detail','account'), array('token'=>$aToken));
		
		// fix issue for new version of iphone.
		return array_merge(
			$this->profile(array('iUserId'=> $viewer->getIdentity())),
			$this->field_setting($viewer),
			array('error_code' => 0,
				'result' => 1,
				'user_id' => $viewer -> getIdentity(),
				'email' => $sEmail,
				'full_name' => $viewer -> getTitle(),
				'user_name' => $viewer -> username,
				'profileimage' => $profileimage,
				'timezone_offset'=> date('Z'),
                'language'=>$viewer->language,
				'token' => $aToken['token_id'])
			);
		
		return array(
				'error_code' => 0,
				'result' => 1,
				'user_id' => $viewer -> getIdentity(),
				'email' => $sEmail,
				'full_name' => $viewer -> getTitle(),
				'user_name' => $viewer -> username,
				'timezone'=>$viewer->timezone,
                'locale'=>$viewer->locale,
                'language'=>$viewer->language,
				'profileimage' => $profileimage,
				'token' => $aToken['token_id'],
				'bCanSearch'=> $viewer->search?1:0,
		);
	}
    

    /**
     * return simple friends
     * @return array(
     *  id: int,
     *  sFullName: string,
     *  UserProfileImg_Url: string,
     *  BigUserProfileImg_Url: string
     * )
     */
    public function _getAbout($oUser){
        $oViewer  =  Engine_Api::_()->user()->getViewer();
        
        $profile = array();
        
        if (!$oUser){
            return array(
                'error_code' => 1,
                'error_message' => "Profile is not valid!",
            );
        }
        
        // Load fields view helpers
        $view = Zend_Registry::get('Zend_View');
        $view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

        // Values
        $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($oUser);
        
        // Calculate viewer-subject relationship
        $usePrivacy = ($oUser instanceof User_Model_User);
        
        if ($usePrivacy)
        {
            $relationship = 'everyone';
            if ($oViewer && $oViewer -> getIdentity()){
                if ($oViewer -> getIdentity() == $oUser -> getIdentity()){
                    $relationship = 'self';
                }elseif ($oViewer -> membership() -> isMember($oUser, true)){
                    $relationship = 'friends';
                }else{
                    $relationship = 'registered';
                }
            }
        }
        $show_hidden = $oViewer -> getIdentity() ? ($oUser -> getOwner() -> isSelf($oViewer) || 'admin' === Engine_Api::_() -> getItem('authorization_level', $oViewer -> level_id) -> type) : false;
        
        foreach ($fieldStructure as $index => $map)
        {
            $field = $map -> getChild();
            $value = $field -> getValue($oUser);
            if (!$field || $field -> type == 'profile_type')
                continue;
            if (!$field -> display && !$show_hidden)
                continue;
            $isHidden = !$field -> display;

            // Get first value object for reference
            $firstValue = $value;
            if (is_array($value))
            {
                $firstValue = $value[0];
            }

            // Evaluate privacy
            if ($usePrivacy && !empty($firstValue -> privacy) && $relationship != 'self')
            {
                if ($firstValue -> privacy == 'self' && $relationship != 'self')
                {
                    $isHidden = true;
                    //continue;
                }
                else
                if ($firstValue -> privacy == 'friends' && ($relationship != 'friends' && $relationship != 'self'))
                {
                    $isHidden = true;
                    //continue;
                }
                else
                if ($firstValue -> privacy == 'registered' && $relationship == 'everyone')
                {
                    $isHidden = true;
                    //continue;
                }
            }
            
            if ((!$isHidden || $show_hidden) && $firstValue)
            {
                $value = $firstValue -> value;
                switch ($field -> type)
                {
                    case 'first_name' :
                        $profile['sFirstName'] =  $value;
                        break;
                    case 'last_name' :
                        $profile['sLastName'] =  $value;
                        break;
                    case 'gender' :
                        $profile['iGender'] = $value;
                        $profile['sGender'] = $value == 2?'Male':'Female';
                        break;
                    case 'birthdate' :
                        $profile['sBirthday'] = ($value) ? date('M j, Y', strtotime($value)) : "";
                        
                        break;
                    case 'relationship_status' :
                        // this field is deprecated
                        $profile['sRelationshipStatus'] = $this -> getRelation($value);
                        break;
                    case 'zip_code' :
                        $profile['sZipCode'] = $value;
                        break;
                    case 'city' :
                        $profile['sCity'] = $value;
                        break;
                    case 'location' :
                        $profile['sLocation'] = $value;
                        break;
                    case 'about_me' :
                        $profile['sAboutMe'] = $value;
                        break;
                    case 'facebook':
                        $profile['sFacebook'] =  $value;
                        break;
                    case 'twitter':
                        $profile['sTwitter'] =  $value;
                        break;
                    case 'aim':
                        $profile['sAim'] =  $value;
                        break;
                    case 'website':
                        $profile['sWebsite'] = $value;
                        break;
                    default :
                        $profile[$field -> type] = $value;
                        break;
                }
            }
        }
        
        $sProfileImage = $oUser -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
        $sProfileBigImage = $oUser -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
        
        if ($sProfileImage != ""){
            $sProfileImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sProfileImage);
            $sProfileBigImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sProfileBigImage);
        }else{
            $sProfileImage = NO_USER_ICON;
            $sProfileBigImage = NO_USER_NORMAL;
        }
        
        $injector  = Ynmobile_Api_Injector::_();
        
        $profile['iUserId'] = $oUser->getIdentity();
        $profile['sFullName'] = $oUser->getTitle();
        
        
        return $profile;
    }
    
    public function get_about($aData){
        extract($aData);
        
        $iUserId = intval($iUserId);
        
        $oViewer  = Engine_Api::_()->user()->getViewer();
        if($iUserId){
            $oUser    = Engine_Api::_()->user()->getUser($iUserId);    
        }else{
            $oUser = $oViewer;
        }
        
        return $this->_getAbout($oUser);
    }
    
    /**
     * @since 4.08
     */
    public function verify_account($aData){

        $viewer = Engine_Api::_() -> user() -> getViewer();

        if(!$viewer || !$viewer->getIdentity() || !$viewer->email ){
            return array('error_code'=>1, 'error_message'=>'Invalid account');
        }

        $profileimage = $viewer -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
        if ($profileimage != "")
        {
            $profileimage = Engine_Api::_() -> ynmobile() -> finalizeUrl($profileimage);
        }
        else
        {
            $profileimage = NO_USER_ICON;
        }
        
        if($viewer->timezone && $viewer->timezone != 'auto'){
            date_default_timezone_set($user->timezone);
        }
        
        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
        
        // fix issue for new version of iphone.
        // fix issue for new version of iphone.
        return  array_merge(
            $this->profile(array('iUserId'=> $viewer->getIdentity())),
            $this->field_setting($viewer),
            array('error_code' => 0,
                'result' => 1,
                'user_id' => $viewer -> getIdentity(),
                'email' => $viewer->email,
                'full_name' => $viewer -> getTitle(),
                'user_name' => $viewer -> username,
                'profileimage' => $profileimage,
                'timezone_offset'=> date('Z'),
                'timezone'=>$viewer->timezone,
                'locale'=>$viewer->locale,
                'language'=>$viewer->language,
                'token' => $aData['token'],
                'bSubscription' => ($subscriptionsTable->check($viewer)) ? true : false
            ));

    }
    /**
     * tak
     */
    function field_setting($user){
        
        $view  =  $this->getPrivacyValue($user, 'auth_view','view');
        
        $comment  =  $this->getPrivacyValue($user, 'auth_comment','comment');
        
        $sLocale =  '';
        
        return $this->data['setting'] =  array(
            'auth_view'=>$view['id'],
            'sauth_view'=>$view['title'],
            'auth_comment'=>$comment['id'],
            'sauth_comment'=>$comment['title'],
            'search'=>$user->search?1:0,
            'email'=>$user->email,
            'timezone'=>$user->timezone,
            'sTimezone'=> $this->_getTimezoneLabel($user->timezone),
            'username'=>$user->username,
            'locale'=>$user->locale,
            'sLocale'=>$this->_getLocaleLabel($user->locale),
        );
    }
    
    /**
     * @since 4.08
     */
    public function edit_info($aData){
        extract($aData);
        
        $id  = isset($id)? intval($id): 0;
        
        if (!$id){
            return array( 'error_code' => 1, 'error_message' => 'Missing user identity' );
        }
        
        $user = Engine_Api::_()->user()->getUser($id);
        
        if(!$user || !$user->getIdentity()){
            return array( 'error_code' => 1, 'error_message' => 'Invalid user' );
        }
        
        if (@$first_name == ""){
            return array( 'error_code' => 1, 'error_message' => 'first_name is invalid' );
        }   
        
        if (@$last_name == ""){
            return array( 'error_code' => 1, 'error_message' => 'last_name is invalid' );
        }   
        
        $aFieldInfo = array(
            'first_name' => html_entity_decode($first_name, ENT_QUOTES, 'UTF-8'),
            'last_name' => html_entity_decode($last_name, ENT_QUOTES, 'UTF-8'),
        );
        
        if(@$gender !== null){
            $aFieldInfo['gender'] = $gender; // user can gender    
        }
        
        if(@$birthdate !== null){
            $aFieldInfo['birthdate'] = $birthdate; // user can unset birthdate
        } 
        
        if(@$website !== null){
            $aFieldInfo['website'] = html_entity_decode($website, ENT_QUOTES, 'UTF-8');    
        }
        
        if (@$twitter !== null)
                $aFieldInfo['twitter'] = html_entity_decode($twitter, ENT_QUOTES, 'UTF-8');
            
        if (@$facebook !== null){
            $aFieldInfo['facebook'] = html_entity_decode($facebook, ENT_QUOTES, 'UTF-8');
        }
            
            
        if (@$aim!== null){
            $aFieldInfo['aim'] = html_entity_decode($aim, ENT_QUOTES, 'UTF-8');
        }
            
        
        if (@$about_me != null){
            $aFieldInfo['about_me'] = html_entity_decode($about_me, ENT_QUOTES, 'UTF-8');
        }
               
        try
        {
            $aliasedFields = $user->fields()->getFieldsObjectsByAlias();
            $topLevelId = 0;
            $topLevelValue = null;
            if( isset($aliasedFields['profile_type']) ) {
                $aliasedFieldValue = $aliasedFields['profile_type']->getValue($user);
                $topLevelId = $aliasedFields['profile_type']->field_id;
                $topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
                if( !$topLevelId || !$topLevelValue ) {
                    $topLevelId = null;
                    $topLevelValue = null;
                }
            }

            $formArgs = array(
                    'topLevelId' => $topLevelId,
                    'topLevelValue' => $topLevelValue,
            );
        
            if ($formArgs)
            {
                $struct = Engine_Api::_() -> fields() -> getFieldsStructureFull($user, $formArgs['topLevelId'], $formArgs['topLevelValue']);
                $arr_data = array();
                foreach ($struct as $fskey => $map)
                {
                    $field = $map -> getChild();
                    $type = $field -> type;
                    if (isset($aFieldInfo[$type]))
                    {
                        $arr_data[$fskey] = $aFieldInfo[$type];
                    }
                    
                }
                
                $values = Engine_Api::_() -> fields() -> getFieldsValues($user);
                
                foreach ($arr_data as $key => $value)
                {
                    $parts = explode('_', $key);
                    if (count($parts) != 3)
                        continue;
                    list($parent_id, $option_id, $field_id) = $parts;
        
                    // Array mode
                    if (is_array($value))
                    {
                        // Lookup
                        $valueRows = $values -> getRowsMatching(array(
                                'field_id' => $field_id,
                                'item_id' => $user -> getIdentity()
                        ));
        
                        // Delete all
                        $prevPrivacy = null;
                        foreach ($valueRows as $valueRow)
                        {
                            if (!empty($valueRow -> privacy))
                            {
                                $prevPrivacy = $valueRow -> privacy;
                            }
                            $valueRow -> delete();
                        }
        
                        // Insert all
                        $indexIndex = 0;
                        if (is_array($value) || !empty($value))
                        {
                            foreach ((array) $value as $singleValue)
                            {
                                $valueRow = $values -> createRow();
                                $valueRow -> field_id = $field_id;
                                $valueRow -> item_id = $user -> getIdentity();
                                $valueRow -> index = $indexIndex++;
                                $valueRow -> value = $singleValue;
                                $valueRow -> save();
                            }
                        }
                        else
                        {
                            $valueRow = $values -> createRow();
                            $valueRow -> field_id = $field_id;
                            $valueRow -> item_id = $user -> getIdentity();
                            $valueRow -> index = 0;
                            $valueRow -> value = '';
                            $valueRow -> save();
                        }
                    }
        
                    // Scalar mode
                    else
                    {
                        // Lookup
                        $valueRow = $values -> getRowMatching(array(
                                'field_id' => $field_id,
                                'item_id' => $user -> getIdentity(),
                                'index' => 0
                        ));
        
                        // Create if missing
                        $isNew = false;
                        if (!$valueRow)
                        {
                            $isNew = true;
                            $valueRow = $values -> createRow();
                            $valueRow -> field_id = $field_id;
                            $valueRow -> item_id = $user -> getIdentity();
                        }
        
                        $valueRow -> value = htmlspecialchars($value);
                        $valueRow -> save();
                    }
                }

                // Update display name
                $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
                $user->setDisplayName($aliasValues);
                $user->save();
                
                // Update search table
                Engine_Api::_() -> getApi('core', 'fields') -> updateSearch($user, $values);
        
                // Fire on save hook
                Engine_Hooks_Dispatcher::getInstance() -> callEvent('onFieldsValuesSave', array(
                    'item' => $user,
                    'values' => $values
                ));
        
                if (isset($_FILES['image']))
                {
                    $user -> setPhoto($_FILES['image']);
                    if (isset($aData['sCoordinates']) && isset($aData['iWidth']))
                        $this -> process_avatar($user, $aData['sCoordinates'], $aData['iWidth'], $aData['iHeight']);
                }
            }
        }
        catch( Exception $e )
        {
            return array(
                    'error_code' => 1,
                    'error_message' => $e -> getMessage()
            );
        }
        return array(
            'error_code' => 0,
            'error_message' => '',
            'message' => 'Edited successfully',
            'full_name' => $user->getTitle()
        );
    }

	protected function loginByFacebook($aData)
	{
		if (!isset($aData['sLoginUID']))
		{
			return array(
				'error_code' => 10,
				'error_element' => 'login facebook',
				'error_message' => 'missing sLoginUID'
			);
		}
		else 
		{
			$facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
			$user_id = $facebookTable->select()
				->from($facebookTable, 'user_id')
				->where('facebook_uid = ?', $aData['sLoginUID'])
				->query()
				->fetchColumn();
			
			$flag = false;
			
			// WHEN USER USING FACEBOOK CONNECTION ALREADY
			if( $user_id ) 
			{
				//SHOULD CHECK $VIEWER HERE
				$viewer = Engine_Api::_()->getItem('user', $user_id);
				if ($viewer->getIdentity())
				{
					$flag = true;
				}				
				else
				{
					$flag = false;
					//SHOULD SIGNUP HERE
					return array(
							'error_message' => Zend_Registry::get('Zend_Translate') -> _("No user found."),
							'error_step' => 'signup',
							'error_code' => 3,
							'result' => 0
					);
				}
				
			}
			else if (isset ($aData['sEmail']))
			{
				$user_table = Engine_Api::_() -> getDbtable('users', 'user');
				$user_select = $user_table -> select() -> where('email = ?', $aData['sEmail']);
				
				// GETTING USER BY EMAIL
				$viewer = $user_table -> fetchRow($user_select);
				
				if (is_object($viewer) && $viewer->getIdentity()) //THIS USER SIGNED UP OUR SYSTEM BEFORE
				{
					//UPDATE FACEBOOK TABLE
					$this->updateAgentForFacebook($viewer, $aData['sLoginUID']);
					$flag = true;
				}
				else // NEW USER
				{
					//SHOULD SIGNUP HERE
					return array(
							'error_message' => Zend_Registry::get('Zend_Translate') -> _("No record found with this email."),
							'error_step' => 'signup',
							'error_code' => 3,
							'result' => 0
					);
				}
			}
			else {
				//SHOULD SIGN UP HERE
			}
			
			if (!$flag)
			{
				return array(
					'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid credentials.'),
					'error_step' => 'signup',
					'error_code' => 8,
					'result' => 0
				);	
			}
			
			return $this->simpleLogin($viewer, 'facebook');
			
		}
	}
	
	protected function updateAgentForFacebook($user, $sLoginUID)
	{
	    try{
		//UPDATE USER FACEBOOK TABLE
		$facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        
        $facebookTable->delete('facebook_uid='. $sLoginUID);
        
		$facebookAgent = $facebookTable->insert(array(
				'user_id' => $user->getIdentity(),
				'facebook_uid' => $sLoginUID,
				'access_token' => '',
				'code' => '',
				'expires' => 0,
		));
		
		//WHEN YNSOCIAL-CONNECT EXISTED
		if (Engine_Api::_() -> hasModuleBootstrap("social-connect"))
		{
		      /**
               * cleanup old data
               */    
            Engine_Api::_()->getDbtable('agents', 'socialconnect')->delete('identity='.$sLoginUID);
            Engine_Api::_()->getDbtable('accounts', 'socialconnect')->delete('identity='.$sLoginUID);
            
            
    		    $api = Engine_Api::_() -> getApi('Core', 'SocialConnect');
                $api -> createAccount($user->getIdentity(), $sLoginUID, 'facebook', array());    
		    
			
		}
            }catch(Exception $ex){
                
            }
	}
	
	
	protected function loginByTwitter($aData)
	{
		if (!isset($aData['sLoginUID']))
		{
			return array(
					'error_code' => 10,
					'error_element' => 'login twitter',
					'error_message' => 'missing sLoginUID'
			);
		}
		else
		{
			$twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
			$user_id = $twitterTable->select()
			->from($twitterTable, 'user_id')
			->where('twitter_uid = ?', $aData['sLoginUID'])
			->query()
			->fetchColumn();
	
			$flag = false;
				
			// WHEN USER USING TWITTER CONNECTION ALREADY
			if( $user_id )
			{
				//SHOULD CHECK $viewer HERE
				$viewer = Engine_Api::_()->getItem('user', $user_id);
				if ($viewer->getIdentity() && $viewer->email !=  '')
				{
					$flag = true;
				}
				else
				{
					$flag = false;
					//SHOULD SIGNUP HERE
					return array(
							'error_message' => Zend_Registry::get('Zend_Translate') -> _("No user found."),
							'error_step' => 'signup',
							'error_code' => 3,
							'result' => 0
					);
				}
					
			}
			else if (isset ($aData['sEmail']) && $aData['sEmail'])
			{
				$user_table = Engine_Api::_() -> getDbtable('users', 'user');
				$user_select = $user_table -> select() -> where('email = ?', $aData['sEmail']);
					
				// GETTING USER BY EMAIL
				$viewer = $user_table -> fetchRow($user_select);
					
				if (is_object($viewer) && $viewer->getIdentity() && $viewer->email != '') //THIS USER SIGNED UP OUR SYSTEM BEFORE
				{
					//UPDATE TWITTER TABLE
					$this->updateAgentForTwitter($viewer, $aData['sLoginUID']);
					$flag = true;
				}
				else // NEW USER
				{
					//SHOULD SIGNUP HERE
					return array(
							'error_message' => Zend_Registry::get('Zend_Translate') -> _("No record found."),
							'error_step' => 'signup',
							'error_code' => 3,
					);
				}
			}
			else {
				//SHOULD SIGN UP HERE
				return array(
						'error_message' => Zend_Registry::get('Zend_Translate') -> _("No record found with this email."),
						'error_step' => 'signup',
						'error_code' => 3,
				);
			}
				
			if (!$flag)
			{
				return array(
						'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid credentials.'),
						'error_step' => 'signup',
						'error_code' => 8,
				);
			}
	
			return $this->simpleLogin($viewer, 'twiiter');
	
		}
	}
	
	protected function updateAgentForTwitter($user, $sLoginUID)
	{
		//UPDATE USER TWITTER TABLE
		$twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
		$twitterAgent = $twitterTable->insert(array(
							'user_id' => $user->getIdentity(),
							'twitter_uid' => $sLoginUID,
							'twitter_token' => '',
							'twitter_secret' => '',
					));
	
		//WHEN YNSOCIAL-CONNECT EXISTED
		if (Engine_Api::_() -> hasModuleBootstrap("social-connect"))
		{
			$api = Engine_Api::_() -> getApi('Core', 'SocialConnect');
			$api -> createAccount($user->getIdentity(), $sLoginUID, 'twitter', array());
		}
	}
	
	protected function loginByLinkedin($aData)
	{
		//DO NOTHING
		return array(
			'error_message' => 'will update later',
			'error_code' => 1
		);
	}
	
	/**
	 * Input data:
	 * + sEmail: string, required.
	 * + sPassword: string, required.
	 *
	 * Output data:
	 * + error_message: string.
	 * + error_code: int.
	 * + result: int.
	 * + user_id: int.
	 * + full_name: string.
	 * + user_name: string.
	 * + profileimage: string.
	 * + token: string.
	 *
	 * @see Mobile - API SE/Api V2.0.
	 * @see user/login
	 *
	 * @param array $aData
	 * @return array
	 */
	public function login($aData, $id = null)
	{
		if (isset($aData['sLoginBy']))
		{
			$sLoginBy = strtolower($aData['sLoginBy']);
			if ( !in_array($sLoginBy, array('facebook', 'twitter', 'linkedin')) )
			{
				return array(
						'error_message' => 'login by invailid method',
						'error_code' => 9
				);
			}
			else 
			{
				$methodName = "loginBy" . ucfirst($sLoginBy);
				if (method_exists($this, $methodName))
				{
					return $this->{$methodName}($aData);
				}
					
			}
		}
		
		/**
		 * @var string
		 */
		$sPassword = isset($aData['sPassword']) ? $aData['sPassword'] : '';
		/**
		 * @var string
		 */
		$sEmail = isset($aData['sEmail']) ? $aData['sEmail'] : '';
		
		if (empty($sEmail))
		{
			return array(
				'error_message' => 'Missing email address',
				'error_element' => 'email',
				'error_code' => 1
			);
		}
		
		if (empty($sPassword))
		{
			return array(
				'error_message' => 'Missing password',
				'error_element' => 'password',
				'error_code' => 2
			);
		}

		$user_table = Engine_Api::_() -> getDbtable('users', 'user');
		$user_select = $user_table -> select() -> where('email = ?', $sEmail);
		// If post exists
		$user = $user_table -> fetchRow($user_select);

		// Get ip address
		$db = Engine_Db_Table::getDefaultAdapter();
		$ipObj = new Engine_IP();
		$ipExpr = new Zend_Db_Expr($db -> quoteInto('UNHEX(?)', bin2hex($ipObj -> toBinary())));

		// Check if user exists
		if (empty($user) || $user->email == '')
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("No record found with this email."),
				'error_code' => 3,
				'result' => 0
			);
		}

		// Check if user is verified and enabled
		if (!$user -> enabled)
		{
			if (!$user -> verified)
			{
				return array(
					'error_message' => Zend_Registry::get('Zend_Translate') -> _('This account still requires either email verification.'),
					'error_code' => 4,
					'result' => 0
				);
			}
			else if (!$user -> approved)
			{
				return array(
					'error_message' => Zend_Registry::get('Zend_Translate') -> _('This account still requires admin approval.'),
					'error_code' => 5,
					'result' => 0
				);
			}

		}

		// Handle subscriptions
		if (Engine_Api::_() -> hasModuleBootstrap('payment'))
		{
			// Check for the user's plan
			$subscriptionsTable = Engine_Api::_() -> getDbtable('subscriptions', 'payment');
			if (!$subscriptionsTable -> check($user))
			{
				// Register login
				Engine_Api::_() -> getDbtable('logins', 'user') -> insert(array(
					'user_id' => $user -> getIdentity(),
					'email' => $email,
					'ip' => $ipExpr,
					'timestamp' => new Zend_Db_Expr('NOW()'),
					'state' => 'unpaid',
				));
				// Redirect to subscription page
				$subscriptionSession = new Zend_Session_Namespace('Payment_Subscription');
				$subscriptionSession -> unsetAll();
				$subscriptionSession -> user_id = $user -> getIdentity();
			}
		}

		// Run pre login hook
		$event = Engine_Hooks_Dispatcher::getInstance() -> callEvent('onUserLoginBefore', $user);

		// Version 3 Import compatibility
		if (empty($user -> password))
		{
			$compat = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.compatibility.password');
			$migration = null;
			try
			{
				$migration = Engine_Db_Table::getDefaultAdapter() -> select() -> from('engine4_user_migration') -> where('user_id = ?', $user -> getIdentity()) -> limit(1) -> query() -> fetch();
			}
			catch( Exception $e )
			{
				$migration = null;
				$compat = null;
			}
			if (!$migration)
			{
				$compat = null;
			}

			if ($compat == 'import-version-3')
			{

				// Version 3 authentication
				$cryptedPassword = self::_version3PasswordCrypt($migration['user_password_method'], $migration['user_code'], $password);
				if ($cryptedPassword === $migration['user_password'])
				{
					// Regenerate the user password using the given password
					$user -> salt = (string) rand(1000000, 9999999);
					$user -> password = $password;
					$user -> save();
					Engine_Api::_() -> user() -> getAuth() -> getStorage() -> write($user -> getIdentity());
					// @todo should we delete the old migration row?
				}
				else
				{
					return array(
						'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid credentials'),
						'error_code' => 6,
						'result' => 0
					);
				}
				// End Version 3 authentication

			}
			else
			{
				// Register login
				Engine_Api::_() -> getDbtable('logins', 'user') -> insert(array(
					'user_id' => $user -> getIdentity(),
					'email' => $email,
					'ip' => $ipExpr,
					'timestamp' => new Zend_Db_Expr('NOW()'),
					'state' => 'v3-migration',
				));

				return array(
					'error_message' => Zend_Registry::get('Zend_Translate') -> _('There appears to be a problem logging in. Please reset your password with the Forgot Password link.'),
					'error_code' => 7,
					'result' => 0
				);
			}
		}

		// Normal authentication
		else
		{
			$authResult = Engine_Api::_() -> user() -> authenticate($sEmail, $sPassword);
			$authCode = $authResult -> getCode();
			Engine_Api::_() -> user() -> setViewer();

			if ($authCode != Zend_Auth_Result::SUCCESS)
			{
				// Register login
				Engine_Api::_() -> getDbtable('logins', 'user') -> insert(array(
					'user_id' => $user -> getIdentity(),
					'email' => $sEmail,
					'ip' => $ipExpr,
					'timestamp' => new Zend_Db_Expr('NOW()'),
					'state' => 'bad-password',
				));

				return array(
					'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid credentials. Please check the email or password.'),
					'error_code' => 8,
					'result' => 0
				);
			}
		}
		// Success!
		if ($user -> getIdentity())
		{
			$user -> lastlogin_date = date("Y-m-d H:i:s");
			if ('cli' !== PHP_SAPI)
			{
				$user -> lastlogin_ip = $ipExpr;
			}
			$user -> save();
		}
		
		// Register login
		$loginTable = Engine_Api::_() -> getDbtable('logins', 'user');
		$loginTable -> insert(array(
			'user_id' => $user -> getIdentity(),
			'email' => $sEmail,
			'ip' => $ipExpr,
			'timestamp' => new Zend_Db_Expr('NOW()'),
			'state' => 'success',
			'active' => true,
		));

		// Increment sign-in count
		Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('user.logins');

		// Run post login hook
		$event = Engine_Hooks_Dispatcher::getInstance() -> callEvent('onUserLoginAfter', $user);
		/**
		 * @var array
		 */
		$oToken = Engine_Api::_() -> getDbtable('tokens', 'ynmobile');
		$aToken = $oToken -> createToken($user);
        
        return Ynmobile_AppMeta::_export_one($user, array('detail'), array('token'=>$aToken));
        

		$profileimage = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		if ($profileimage != "")
		{
			$profileimage = Engine_Api::_() -> ynmobile() -> finalizeUrl($profileimage);
		}
		else
		{
			$profileimage = NO_USER_ICON;
		}
		
		if($user->timezone && $user->timezone != 'auto'){
			date_default_timezone_set($user->timezone);
		}

		
		// fix issue for new version of iphone.
		return array_merge(
			$this->profile(array('iUserId'=> $user->getIdentity())),
			$this->field_setting($user),
			array('error_code' => 0,
				'result' => 1,
				'user_id' => $user -> getIdentity(),
				'email' => $sEmail,
				'full_name' => $user -> getTitle(),
				'user_name' => $user -> username,
				'profileimage' => $profileimage,
				'timezone_offset'=> date('Z'),
				'timezone'=>$user->timezone,
                'locale'=>$user->locale,
                'bCanSearch'=> $user->search?1:0,
                'language'=>$user->language,
				'token' => $aToken['token_id'],
			)	
		);
	}

	/**
	 * process logout
	 *
	 * Input data:
	 * N/A
	 *
	 * Output data:
	 * + result: int.
	 *
	 * @global string $token
	 * @param array $aData
	 * @return array
	 */
	function logout($aData)
	{
		global $token;

		if (NULL == $token)
		{
			return array(
				'error_message' => 'token required!',
				'error_code' => 1,
				'result' => 0
			);
		}
		$oToken = Engine_Api::_() -> getDbtable('tokens', 'ynmobile');
		$oToken -> deleteToken($token);

		return array('result' => 1);
	}

	/**
	 * Input data: N/A
	 *
	 * Output data:
	 * + sFullName: string.
	 * + iUserId: int.
	 * + UserProfileImg_Url: string.
	 * + sWorkat: string.
	 * + sGraduated: string.
	 * + sFrom: string.
	 * + isFriend: bool.
	 * + PhotoImg_Url: string.
	 * + FriendImg1_Url: string.
	 * + FriendImg2_Url: string.
	 * + FriendImg3_Url: string.
	 * + FriendImg4_Url: string.
	 * + FriendImg5_Url: string.
	 * + FriendImg6_Url: string.
	 * + PhotoImage_Url: string.
	 *
	 * @param array $aVals
	 * @param int $iForceUserId
	 * @return array
	 */
	function profile($aData)
	{

		$oViewer = Engine_Api::_() -> user() -> getViewer();
		$bCanView = true;
		// check public settings
		$require_check = Engine_Api::_() -> getApi('settings', 'core') -> core_general_profile;
		if (!$require_check && !$oViewer -> getIdentity())
		{
			$bCanView = false;
		}
		extract($aData, EXTR_SKIP);
		/**
		 * @var int
		 */

		$direction = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('user.friends.direction', 1);
		$isSentRequest = false;
		$isSentRequestBy = false;
		
		if (isset($iUserId))
		{
			$oUser = Engine_Api::_() -> user() -> getUser($iUserId);

			if (!$direction)//ONE WAY
			{
				$row = $oUser -> membership() -> getRow($oViewer);
			}
			else //TWO WAY
			{
				$row = $oViewer -> membership() -> getRow($oUser);
			}
			if ($row !== null)
			{
				$isSentRequest = ($row -> user_approved == 0) ? true : false;
				$isSentRequestBy = ($row->resource_approved == 0) ? true : false;
			}
				
		}

		$iUserId = isset($iUserId) ? (int)$iUserId : $oViewer -> getIdentity();
		$oUser = Engine_Api::_() -> user() -> getUser($iUserId);

		if (!$oUser)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Profile is not valid!")
			);
		}
		//check auth
		if (!$oUser -> authorization() -> isAllowed($oViewer, 'view'))
		{
			$bCanView = false;
		}
		
		if ($oViewer->isBlockedBy($oUser) && !$oViewer->isAdmin())
		{
			$bCanView = false;
		}
		
		$isFriend = 0;
		// Increment view count
		if (!$oUser -> isSelf($oViewer))
		{
			$oUser -> view_count++;
			$oUser -> save();
			//check is friend
			$isFriend = $oUser -> membership() -> isMember($oViewer, true);
		}

		$select = $oUser -> membership() -> getMembersOfSelect();
		$select -> limit(6);
		$oFriends = Zend_Paginator::factory($select);
		$aFriends = array();
		foreach ($oFriends as $friend)
		{
			$aFriends[] = $friend;
		}
		$aFriendImages = array();
		for ($i = 0; $i < 6; $i++)
		{
			if (isset($aFriends[$i]) && $friend = Engine_Api::_() -> user() -> getUser($aFriends[$i]['resource_id']))
			{
				$sProfileImage = $friend -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
				if ($sProfileImage != "")
				{
					$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
				}
				else
				{
					$sProfileImage = NO_USER_ICON;
				}
				$aFriendImages[] = $sProfileImage;
			}
			else
			{
				$aFriendImages[] = NO_USER_ICON;
			}
		}

		$oLatestPhoto = Engine_Api::_() -> ynmobile() -> getMyLatestPhoto($iUserId);

		if ($oLatestPhoto)
		{
			$sPhotoImageUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($oLatestPhoto -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL));
		}
		else
		{
			$sPhotoImageUrl = NO_USER_NORMAL;
		}

		//user photoURL
		$sUserImage = $oUser -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
		$sBigUserImage = $oUser -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
		
		if ($sUserImage != "")
		{
			$sUserImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImage);
			$sBigUserImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sBigUserImage);
		}
		else
		{
			$sUserImage = NO_USER_ICON;
			$sBigUserImage = NO_USER_NORMAL;
		}

		$aUserDetails = Engine_Api::_() -> getApi('Profile', 'Ynmobile') -> info(array('iUserId' => $oUser -> getIdentity()));
		
		$profileimage = $oUser -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		if ($profileimage != "")
		{
			$profileimage = Engine_Api::_() -> ynmobile() -> finalizeUrl($profileimage);
		}
		else
		{
			$profileimage = NO_USER_ICON;
		}
		
		return array(
			'bCanView' => $bCanView,
			'bCanPostComment' => ($oUser -> authorization() -> isAllowed($oViewer, 'comment')) ? true : false,
			'sFullName' => $oUser -> getTitle(),
			'iUserId' => $oUser -> getIdentity(),
			'UserProfileImg_Url' => $sUserImage,
			'fullname'=> $oUser -> getTitle(),
			'BigUserProfileImg_Url' => $sBigUserImage,
			'profileimage'=> $profileimage,
			'sFrom' => '',
			'isFriend' => $isFriend,
			'isBlocked' => ($oUser->isBlockedBy($oViewer)),
			'isBlockedBy' => ($oViewer->isBlockedBy($oUser) && !$oViewer->isAdmin()),
			'isSentRequest' => $isSentRequest,
			'isSentRequestBy' => $isSentRequestBy,
			'PhotoImg_Url' => $sPhotoImageUrl,
			'FriendImg1_Url' => $aFriendImages[0],
			'FriendImg2_Url' => $aFriendImages[1],
			'FriendImg3_Url' => $aFriendImages[2],
			'FriendImg4_Url' => $aFriendImages[3],
			'FriendImg5_Url' => $aFriendImages[4],
			'FriendImg6_Url' => $aFriendImages[5],
			'sGender' => isset($aUserDetails['BasicInfo']['Gender']) ? $aUserDetails['BasicInfo']['Gender'] : "",
			'sDayOfBirth' => isset($aUserDetails['BasicInfo']['Date_Of_Birth']) ? $aUserDetails['BasicInfo']['Date_Of_Birth'] : "",
			'sLocation' => isset($aUserDetails['BasicInfo']['Location']) ? $aUserDetails['BasicInfo']['Location'] : "",
			'sCity' => isset($aUserDetails['BasicInfo']['City']) ? $aUserDetails['BasicInfo']['City'] : "",
			'sZipPostalCode' => isset($aUserDetails['BasicInfo']['Zip_Postal_Code']) ? $aUserDetails['BasicInfo']['Zip_Postal_Code'] : "",
			'sRelationshipStatus' => isset($aUserDetails['BasicInfo']['Relationship_Status']) ? $aUserDetails['BasicInfo']['Relationship_Status'] : "",
			'iTotalOfPhotos' => Engine_Api::_() -> ynmobile() -> getUserTotalPhoto($iUserId),
			'iTotalOfFriends' => Engine_Api::_() -> ynmobile() -> getUserTotalFriend($iUserId), 
		);
	}

	/**
	 * Check email is ban.
	 * @param string $sEmail
	 */
	protected function checkEmail($sEmail)
	{
		if (empty($sEmail) || !isset($sEmail))
		{
			return array(
				'error_message' => 'Missing email address',
				'error_element' => 'email',
				'error_step' => '1',
				'error_code' => 1
			);
		}
		// Split email address up and disallow '..'
		if ((strpos($sEmail, '..') !== false) || (!filter_var($sEmail, FILTER_VALIDATE_EMAIL)))
		{
			return array(
				'error_message' => 'Email address invalid',
				'error_element' => 'email',
				'error_step' => '1',
				'error_code' => 2
			);
		}
		$userTable = Engine_Api::_() -> getDbtable('users', 'user');
		$select = $userTable -> select() -> where('email = ?', $sEmail);
		$row = $userTable -> fetchRow($select);
		if (count($row))
		{
			return array(
				'error_message' => 'Someone has already registered this email address, please use another one.',
				'error_element' => 'email',
				'error_step' => '1',
				'error_code' => 3
			);

		}
		$bannedEmailsTable = Engine_Api::_() -> getDbtable('BannedEmails', 'core');
		if ($bannedEmailsTable -> isEmailBanned($sEmail))
		{
			return array(
				'error_message' => 'This email address is not available, please use another one.',
				'error_element' => 'email',
				'error_step' => '1',
				'error_code' => 4
			);

		}

		return array('error_code' => 0);
	}

	/**
	 * Check user is ban.
	 * @param string $sUserName
	 */
	protected function checkUserName($sUserName)
	{
		if (empty($sUserName) || !isset($sUserName))
		{
			return array(
				'error_message' => 'Missing user name',
				'error_element' => 'username',
				'error_step' => '1',
				'error_code' => 1
			);
		}
		
		$oRegex = new Zend_Validate_Regex('/^[a-z][a-z0-9]*$/i');
		if (!($oRegex->isValid($sUserName)))
			return array(
					'error_message' => 'User name is not valid.',
					'error_element' => 'username',
					'error_step' => '1',
					'error_code' => 1
			);
		
		$oStringLength = new Zend_Validate_StringLength(4, 64);
		if (!($oStringLength->isValid($sUserName)))
			return array(
					'error_message' => 'User name is must between 4 and 64 characters.',
					'error_element' => 'username',
					'error_step' => '1',
					'error_code' => 1
			);
		
		
		$userTable = Engine_Api::_() -> getDbtable('users', 'user');
		$select = $userTable -> select() -> where('username = ?', $sUserName);
		$row = $userTable -> fetchRow($select);
		if ($row)
		{
			return array(
				'error_message' => 'Someone has already registered this user name, please use another one.',
				'error_element' => 'username',
				'error_step' => '1',
				'error_code' => 1
			);
		}

		$bannedUsernamesTable = Engine_Api::_() -> getDbtable('BannedUsernames', 'core');

		if ($bannedUsernamesTable -> isUsernameBanned($sUserName))
		{
			return array(
				'error_message' => 'This user name is not available, please use another one.',
				'error_element' => 'username',
				'error_step' => '1',
				'error_code' => 2
			);
		}
		return array('error_code' => 0);
	}

	/**
	 * Input data:
	 * + sEmail: string, required.
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V2.0.
	 * @see user/forgot
	 *
	 * @param array $aData
	 * @return array
	 */
	public function forgot($aData)
	{
		/**
		 * @var string.
		 */
		if (!isset($aData['sEmail']) || $aData['sEmail'] == '')
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing email address")
			);
		}
		$sEmail = $aData['sEmail'];
		
		// Split email address up and disallow '..'
		if ((strpos($sEmail, '..') !== false) || (!filter_var($sEmail, FILTER_VALIDATE_EMAIL)))
		{
			return array(
					'error_message' => 'Invalid email address ',
					'error_element' => 'email',
					'error_step' => '1',
					'error_code' => 2
			);
		}

		// Check for existing user
		$user = Engine_Api::_() -> getDbtable('users', 'user') -> fetchRow(array('email = ?' => $sEmail));
		if (!$user || !$user -> getIdentity())
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("No record found with this email")
			);
		}

		// Check to make sure they're enabled
		if (!$user -> enabled)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This user account has not yet been verified or disabled by an admin.")
			);
		}

		// Ok now we can do the fun stuff
		$forgotTable = Engine_Api::_() -> getDbtable('forgot', 'user');
		$db = $forgotTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Delete any existing reset password codes
			$forgotTable -> delete(array('user_id = ?' => $user -> getIdentity()));

			// Create a new reset password code
			$code = base_convert(md5($user -> salt . $user -> email . $user -> user_id . uniqid(time(), true)), 16, 36);
			$forgotTable -> insert(array(
				'user_id' => $user -> getIdentity(),
				'code' => $code,
				'creation_date' => date('Y-m-d H:i:s'),
			));

			$view = Zend_Registry::get('Zend_View');
			// Send user an email
			Engine_Api::_() -> getApi('mail', 'core') -> sendSystem($user, 'core_lostpassword', array(
				'host' => $_SERVER['HTTP_HOST'],
				'email' => $user -> email,
				'date' => time(),
				'recipient_title' => $user -> getTitle(),
				'recipient_link' => $user -> getHref(),
				'recipient_photo' => $user -> getPhotoUrl('thumb.icon'),
				'object_link' => $view -> url(array(
					'module' => 'user',
					'controller' => 'auth', 
					'action' => 'reset',
					'code' => $code,
					'uid' => $user -> getIdentity()
				)),
				'queue' => false,
			));

			$db -> commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
			);
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e -> getMessage(),
			);
		}
	}

	public function add($aData)
	{
		$values["email"] = $aData['sEmail'];
		$values["displayname"] = $aData['sDisplayname'];
		$values["username"] = $aData['sUsername'];
		$values["password"] = $aData['sPassword'];
		$values["password_conf"] = $aData['sPassword_conf'];
		$values["level_id"] = $aData['iLevelId'];
		$values["approved"] = $aData['iApproved'];
		$values["verified"] = $aData['iVerified'];
		$values["enabled"] = $aData['iEnabled'];
		$values['locale'] = "auto";
		$values['language'] = "en_US";
		$values['timezone'] = "US/Pacific";
		$values['search'] = 1;
		$values['photo_id'] = 0;
		$values['creation_date'] = date("Y-m-d H:i:s");

		$userTable = Engine_Api::_() -> getItemTable('user');
		$eselect = $userTable -> select() -> where("email =?", $values['email']);
		$uselect = $userTable -> select() -> where("username =?", $values['username']);

		$erow = $userTable -> fetchAll($eselect);
		$urow = $userTable -> fetchAll($uselect);

		if (count($erow) > 0)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Email has been existed!"),
			);
		}

		if (count($urow) > 0)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Username has been existed!"),
			);
		}

		if ($values['password'] != $values['password_conf'])
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Password does not match!"),
			);
		}

		$user = $userTable -> createRow();
		$user -> setFromArray($values);
		$user -> save();

		if ($values['level_id'] == 1 || $values['level_id'] == 2)
		{
			$user -> level_id = $values['level_id'];
			$user -> enabled = $user -> verified = $user -> approved = 1;
			$user -> save();
		}
		/*
		 $aLoginData['sEmail'] = $values['email'];
		 $aLoginData['sPassword'] = $values['password'];

		 $aReturn = $this->login($aLoginData);
		 $aReturn['for_test'] = ($aData['iForTest'] == "1") ? "1" : "0";
		 return $aReturn;
		 */

		return array(
			'error_code' => 0,
			'result' => 1,
			'user_id' => $user -> getIdentity(),
			'email' => $values['email'],
			'full_name' => $user -> getTitle(),
			'user_name' => $user -> username,
		);
	}

	public function checkin($aData)
	{
// 		ini_set('display_startup_errors', 1);
// 		ini_set('display_errors', 1);
// 		ini_set('error_reporting', -1);
		
		if (!isset($aData['sLocation']))
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Missing location title'),
				'error_code' => 1,
				'result' => 0
			);
		}
		if (!isset($aData['fLatitude']))
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Missing location latitude'),
				'error_code' => 1,
				'result' => 0
			);
		}
		if (!isset($aData['fLongitude']))
		{
			return array(
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Missing location longitude'),
				'error_code' => 1,
				'result' => 0
			);
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();

		$table = Engine_Api::_() -> getDbTable("maps", "ynmobile");
		$map = $table -> createRow();
		$map -> title = $aData['sLocation'];
		$map -> latitude = $aData['fLatitude'];
		$map -> longitude = $aData['fLongitude'];
		$map -> user_id = $viewer -> getIdentity();
		$map -> save();

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

		$object = $viewer;
		if (isset($aData['iUserId']))
		{
			$object = Engine_Api::_()->user()->getUser($aData['iUserId']);
			if (!$object->getIdentity())
				$object = $viewer;
		}
		if (isset($aData['sSubjectType']) && isset($aData['iSubjectId']))
		{
			$object = Engine_Api::_()->getItem($aData['sSubjectType'], $aData['iSubjectId']);
			if ($object === null){
				$object = $viewer;
			}
		}
		
		$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
		
		$body = $aData['sStatus'] . " - at " . $map -> title;
		$type = 'post';
		if ($viewer -> isSelf($object))
		{
			$type = 'post_self';
		}
		$action = $activityApi -> addActivity($viewer, $object, $type, $body, array(
			'status' => ($aData['sStatus']) ? $aData['sStatus'] : "",
			'location' => $map -> title
		));

		if ($action)
		{
			$activityApi -> attachActivity($action, $map);
		}

		return array(
			'error' => 0,
			'message' => 'Check in sucessfully'
		);

	}

	public function signup_term()
	{
		return array(
			"error_code" => 0,
			"error_message" => '',
			"message" => Zend_Registry::get('Zend_Translate') -> _("ynmobile_term_condition")
		);
	}

	public function signup_check_email($aData)
	{
		return $this -> checkEmail($aData['sEmail']);
	}

	public function signup_check_username($aData)
	{
		return $this -> checkUserName($aData['sUserName']);
	}

	public function signup_timezone()
	{
		$aTimezone = array(
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
			'Indian/Cocos' => '(UTC+6:30) Cocos Islands, Yangon',
			'Asia/Krasnoyarsk' => '(UTC+7) Bangkok, Jakarta, Hanoi',
			'Asia/Hong_Kong' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei',
			'Asia/Tokyo' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk',
			'Australia/Adelaide' => '(UTC+9:30) Adelaide, Darwin',
			'Australia/Sydney' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam',
			'Asia/Magadan' => '(UTC+11) Magadan, Solomon Is., New Caledonia',
			'Pacific/Auckland' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington',
		);

		$result = array();
		foreach ($aTimezone as $key => $value)
		{
			$result[] = array(
				'sValue' => $key,
				'sPhrase' => $value
			);
		}

		return $result;
	}

	protected function signup_sendmail($user)
	{
		// Mail stuff
		$settings = Engine_Api::_()->getApi('settings', 'core');
		
		$random = ($settings->getSetting('user.signup.random', 0) == 1);
		$emailadmin = ($settings->getSetting('user.signup.adminemail', 0) == 1);
		
		if( $emailadmin ) {
			// the signup notification is emailed to the first SuperAdmin by default
			$users_table = Engine_Api::_()->getDbtable('users', 'user');
			$users_select = $users_table->select()
				->where('level_id = ?', 1)
				->where('enabled >= ?', 1);
			$super_admin = $users_table->fetchRow($users_select);
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
		if( $random ) {
			$mailParams['password'] = $data['password'];
		}
		
		switch( $settings->getSetting('user.signup.verifyemail', 0) ) 
		{
			case 0:
				// only override admin setting if random passwords are being created
		        if( $random ) 
		        {
		          $mailType = 'core_welcome_password';
		        }
		        
				if( $emailadmin ) 
				{
					$mailAdminType = 'notify_admin_user_signup';
			        
					$mailAdminParams = array(
						'host' => $_SERVER['HTTP_HOST'],
						'email' => $user->email,
						'date' => date("F j, Y, g:i a"),
						'recipient_title' => $super_admin->displayname,
						'object_title' => $user->displayname,
						'object_link' => $user->getHref(),
					);
			
				}
				break;
		
			case 1:
				// send welcome email
				$mailType = ($random ? 'core_welcome_password' : 'core_welcome');
				if( $emailadmin ) {
					$mailAdminType = 'notify_admin_user_signup';
		
					$mailAdminParams = array(
							'host' => $_SERVER['HTTP_HOST'],
							'email' => $user->email,
							'date' => date("F j, Y, g:i a"),
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
						'verify' => $verify_row->code
				), 'user_signup', true);
				
				
				if( $emailadmin ) {
					$mailAdminType = 'notify_admin_user_signup';
		
					$mailAdminParams = array(
							'host' => $_SERVER['HTTP_HOST'],
							'email' => $user->email,
							'date' => date("F j, Y, g:i a"),
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
		
		
		// Send Welcome E-mail
		if( isset($mailType) && $mailType ) {
			Engine_Api::_()->getApi('mail', 'core')->sendSystem(
				$user,
				$mailType,
				$mailParams
			);
		}
		
		// Send Notify Admin E-mail
		if( isset($mailAdminType) && $mailAdminType ) {
			Engine_Api::_()->getApi('mail', 'core')->sendSystem(
				$user,
				$mailAdminType,
				$mailAdminParams
			);
		}
	}
	
	public function signup_account($aData)
	{
		if (self::REQUIRED_FOR_SIGNUP)
		{
			$requiredArr = array(
				'sUserName',
				'sEmail',
				'sPassword',
				'sFirstName',
				'sLastName'
			);

			for ($i = 0; $i < count($requiredArr); $i++)
			{
				$key = $requiredArr[$i];
				if (!isset($aData[$key]))
				{
					return array(
						'error_message' => "Missing $key",
						'error_code' => 1
					);
				}

			}
		}

		$aCheckingEmail = $this -> checkEmail($aData['sEmail']);
		if ($aCheckingEmail['error_code'] != '0')
			return $aCheckingEmail;

		$aCheckingUserName = $this -> checkUserName($aData['sUserName']);
		if ($aCheckingUserName['error_code'] != '0')
			return $aCheckingUserName;

		try
		{
			//creating user model
			$userTable = Engine_Api::_() -> getItemTable('user');
			$user = $userTable -> createRow();
			$user -> username = $aData['sUserName'];
			$user -> email = $aData['sEmail'];
			$user -> password = $aData['sPassword'];
			

			if (isset($aData['sTimeZone']))
				$user -> timezone = $aData['sTimeZone'];

			$user->setDisplayName(array(
					'first_name' => $aData['sFirstName'],
					'last_name' => $aData['sLastName']
			));
			
			$user -> save();
			
			
			if(self::AUTO_VIRIFY_EMAIL_WHEN_SIGNUP_VIA_MOBILE == 1){
				$user->verified =  1;
				$user->save();
			}
			
			

			//UPDATE USER AGENT 
			if (isset($aData['sLoginBy']) && isset($aData['sLoginUID'])){
				$aData['sLoginBy'] = strtolower($aData['sLoginBy']);
				$methodName = "updateAgentFor" . ucfirst($aData['sLoginBy']);
				if (method_exists($this, $methodName))
				{
					$this->{$methodName}($user, $aData['sLoginUID']);
				} 
			}
			
			$aFieldInfo = array(
				'first_name' => $aData['sFirstName'],
				'last_name' => $aData['sLastName'],
				'birthdate' => $aData['sBirthday'],
				'website' => $aData['sWebsite'],
				'twitter' => $aData['sTwitter'],
				'facebook' => $aData['sFacebook'],
				'aim' => $aData['sAim'],
				'about_me' => $aData['sAbout'],
			);
			
			foreach ($aFieldInfo as $k => $v)
			{
				if (is_string($v))
					$aFieldInfo[$k] = html_entity_decode($v, ENT_QUOTES, 'UTF-8');
			}
			
			if ( isset($aData['iGender']) && ($aData['iGender'] != "") )
				$aFieldInfo['gender'] = $this -> getGenderValue($aData['iGender']);

			$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('user');
			$formArgs = array();
			if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type')
			{
				$profileTypeField = $topStructure[0] -> getChild();
				$options = $profileTypeField -> getOptions();
				$formArgs = array(
					'topLevelId' => $profileTypeField -> field_id,
					'topLevelValue' => $options[0] -> option_id,
				);
			}

			if ($formArgs)
			{
				$struct = Engine_Api::_() -> fields() -> getFieldsStructureFull($user, $formArgs['topLevelId'], $formArgs['topLevelValue']);
				$arr_data = array();
				foreach ($struct as $fskey => $map)
				{
					$field = $map -> getChild();
					$type = $field -> type;
					if (isset($aFieldInfo[$type]))
					{
						$arr_data[$fskey] = $aFieldInfo[$type];
					}
				}

				$profileTypeValue = $formArgs['topLevelValue'];
				$values = Engine_Api::_() -> fields() -> getFieldsValues($user);

				$valueRow = $values -> createRow();
				$valueRow -> field_id = $profileTypeField -> field_id;
				$valueRow -> item_id = $user -> getIdentity();
				$valueRow -> value = $profileTypeValue;
				$valueRow -> save();

				foreach ($arr_data as $key => $value)
				{
					$parts = explode('_', $key);
					if (count($parts) != 3)
						continue;
					list($parent_id, $option_id, $field_id) = $parts;

					// Array mode
					if (is_array($value))
					{
						// Lookup
						$valueRows = $values -> getRowsMatching(array(
							'field_id' => $field_id,
							'item_id' => $user -> getIdentity()
						));

						// Delete all
						$prevPrivacy = null;
						foreach ($valueRows as $valueRow)
						{
							if (!empty($valueRow -> privacy))
							{
								$prevPrivacy = $valueRow -> privacy;
							}
							$valueRow -> delete();
						}

						// Insert all
						$indexIndex = 0;
						if (is_array($value) || !empty($value))
						{
							foreach ((array) $value as $singleValue)
							{
								$valueRow = $values -> createRow();
								$valueRow -> field_id = $field_id;
								$valueRow -> item_id = $user -> getIdentity();
								$valueRow -> index = $indexIndex++;
								$valueRow -> value = $singleValue;
								$valueRow -> save();
							}
						}
						else
						{
							$valueRow = $values -> createRow();
							$valueRow -> field_id = $field_id;
							$valueRow -> item_id = $user -> getIdentity();
							$valueRow -> index = 0;
							$valueRow -> value = '';
							$valueRow -> save();
						}
					}

					// Scalar mode
					else
					{
						// Lookup
						$valueRow = $values -> getRowMatching(array(
							'field_id' => $field_id,
							'item_id' => $user -> getIdentity(),
							'index' => 0
						));

						// Create if missing
						$isNew = false;
						if (!$valueRow)
						{
							$isNew = true;
							$valueRow = $values -> createRow();
							$valueRow -> field_id = $field_id;
							$valueRow -> item_id = $user -> getIdentity();
						}

						$valueRow -> value = htmlspecialchars($value);
						$valueRow -> save();
					}
				}

				// Update search table
				Engine_Api::_() -> getApi('core', 'fields') -> updateSearch($user, $values);

				// Fire on save hook
				Engine_Hooks_Dispatcher::getInstance() -> callEvent('onFieldsValuesSave', array(
					'item' => $user,
					'values' => $values
				));

				if (isset($aData['sUserImageUrl']) && $aData['sUserImageUrl'] != '')
				{
					$filepath = $this->loadImage($aData['sUserImageUrl']);
					$user -> setPhoto($filepath);
					if (isset($aData['sCoordinates']) && $aData['sCoordinates'] && isset($aData['iWidth']))
						$this -> process_avatar($user, $aData['sCoordinates'], $aData['iWidth'], $aData['iHeight']);
					@unlink($filepath);
				}
				else if (isset($_FILES['image']))
				{
					$user -> setPhoto($_FILES['image']);
					if (isset($aData['sCoordinates']) && $aData['sCoordinates'] && isset($aData['iWidth']))
						$this -> process_avatar($user, $aData['sCoordinates'], $aData['iWidth'], $aData['iHeight']);
				}
				
				// Run post signup hook
				$event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserSignupAfter', $user);
				
				// for user enabled
				
				// do not verify user, apply system rule.
				// $user->enabled = true;
				// $user->verified = true;
				// $user->approved = true;
// 				
				$user->save();
				
				if (isset($aData['iPackageId']))
				{
					$subResult = Engine_Api::_() 
						-> getApi('subscription', 'ynmobile')
						-> add_subscription(array(
							'iPackageId' => $aData['iPackageId'],
							'iUserId' => $user->getIdentity()
					));
					$iSubscriptionId = $subResult['iSubscriptionId'];
					$package = Engine_Api::_()->getItem('payment_package', $aData['iPackageId']);
				}
				
				// Handle email verification or pending approval
				if( !$user->enabled ) {
					Engine_Api::_()->user()->setViewer(null);
					Engine_Api::_()->user()->getAuth()->getStorage()->clear();
				
					$confirmSession = new Zend_Session_Namespace('Signup_Confirm');
					$confirmSession->approved = $user->approved;
					$confirmSession->verified = $user->verified;
					$confirmSession->enabled  = $user->enabled;
				}
				
				// Handle normal signup
				else {
					
				}
                Engine_Hooks_Dispatcher::getInstance()
                        ->callEvent('onUserEnable', $user);
				
				$this->signup_sendmail($user);
				
				$aLoginParams = array(
						'sEmail' => $aData['sEmail'],
						'sPassword' => $aData['sPassword'],
				);
				$result = $this -> login($aLoginParams);
				
				if ($iSubscriptionId)
				{
					$result['iSubscriptionId'] = $iSubscriptionId;
				}
				if ($package)
				{
					$result['bIsFreePackage'] = ($package->isFree()) ? true : false;
				}
				return $result;
			}
		}
		catch( Exception $e )
		{
			return array(
				'error_code' => 1,
				'error_step' => '2',
				'error_message' => $e -> getMessage()
			);
		}

	}

    /**
     * @return file entry
     */
    function _save_cover_to_file_entry($entry, $photo){
        if( $photo instanceof Zend_Form_Element_File ) {
          $file = $photo->getFileName();
          $fileName = $file;
        } else if( $photo instanceof Storage_Model_File ) {
          $file = $photo->temporary();
          $fileName = $photo->name;
        } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
          $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
          $file = $tmpRow->temporary();
          $fileName = $tmpRow->name;
        } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
          $file = $photo['tmp_name'];
          $fileName = $photo['name'];
        } else if( is_string($photo) && file_exists($photo) ) {
          $file = $photo;
          $fileName = $photo;
        } else {
          throw new Engine_Api_Exception('invalid argument passed to setPhoto');
        }
    
        if( !$fileName ) {
          $fileName = $file;
        }
    
        $name = basename($file);
        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
          'parent_type' => $entry->getType(),
          'parent_id' => $entry->getIdentity(),
          'user_id' => $entry->getIdentity(),
          'name' => basename($fileName),
        );
    
        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
    
        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
          ->resize(720, 720)
          ->write($mainPath)
          ->destroy();
    
        // Resize image (profile)
        $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
          ->resize(200, 400)
          ->write($profilePath)
          ->destroy();
    
        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file)
          ->resize(140, 160)
          ->write($normalPath)
          ->destroy();
    
        // Resize image (icon)
        $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
        $image = Engine_Image::factory();
        $image->open($file);
    
        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;
    
        $image->resample($x, $y, $size, $size, 48, 48)
          ->write($squarePath)
          ->destroy();
    
        // Store
        $iMain = $filesTable->createFile($mainPath, $params);
        $iProfile = $filesTable->createFile($profilePath, $params);
        $iIconNormal = $filesTable->createFile($normalPath, $params);
        $iSquare = $filesTable->createFile($squarePath, $params);
    
        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');
    
        // Remove temp files
        @unlink($mainPath);
        @unlink($profilePath);
        @unlink($normalPath);
        @unlink($squarePath);


        return $iMain;
    }

    function _save_file_entry_to_photo($owner, $iMain, $sAlbumType = 'profile', $sTitle = "Profile Cover", $iAlbumId = 0){

        $albumTable = $this->getWorkingTable('albums', 'album');
        $photoTable = $this->getWorkingTable('photos', 'album');

        if (in_array($sAlbumType, array(
            'wall',
            'profile',
            'message'
        )))
        {
            $album = $albumTable -> getSpecialAlbum($owner, $sAlbumType);
        }
        else
        {
            $album = $albumTable -> getSpecialAlbum($owner, 'wall');
        }
        
        $photo = $photoTable -> createRow();

        $photo -> setFromArray(array(
            'owner_type' => $owner->getType(),
            'owner_id' => $owner -> getIdentity(),
            'album_id'=>$album -> getIdentity(),
            'title'=>$sTitle,
            'description'=>'',
        ));

        $photo -> save();

        $photo -> order = $photo -> photo_id;
        
        $photo->save();

        if($album && $album->photo_id ==0){
            $album->photo_id =  $photo->getIdentity();
            $album->modified_date =  date('Y-m-d H:i:s');
            $album->save();
        }

        return $photo;
    }

    function _save_profilecovers($entry, $iMain){
        
        $table = Engine_Api::_()->getDbTable('profilecovers','ynmobile');
        
        $select  = $table->select()
            ->where('owner_id=?', $entry->getIdentity())
            ->where('owner_type=?', $entry->getType());
            
        $row = $table->fetchRow($select);
        
        if(!$row){
            $row = $table->fetchNew();
            $row->owner_id = $entry->getIdentity();
            $row->owner_type = $entry->getType();
            $row->creation_date = date('Y-m-d H:i:s');
        }
        // Update row
        $row->modified_date = date('Y-m-d H:i:s');
        $row->photo_id = $iMain->file_id;
        $row->save();
        
        return $row;
      
    }
    
    function edit_cover($aData){
        extract($aData);
        
        $user  = null;

        if (isset($iUserId) && $iUserId){

            $user = Engine_Api::_()->user()->getUser($iUserId);    

            if(!$user){
                return array( 'error_code' => 1, 'error_message' => 'Missing user identity' );    
            }
        }


        if(!$user || $user->getIdentity() == 0){
            $user = Engine_Api::_() -> user() -> getViewer();
        }



        try{
            if (isset($_FILES['image']))
            {
                
                $iMain = $this->_save_cover_to_file_entry($user, $_FILES['image']);

                
                $cover =  $this->_save_profilecovers($user, $iMain);                

                $photo =  $this->_save_file_entry_to_photo($user, $iMain);

                $user->setFromArray(array(
                    'cover_id'=> $photo->getIdentity(),
                    'user_cover'=>$photo->getIdentity(),
                    ));
                

                $user->save();

                return array(
                    'sCoverUrl'=> $this->finalizeUrl($cover->getPhotoUrl()), 
                    'message'=>'cover is updated'
                );
            }
        }catch(Exception $ex){

            var_dump($ex);
            exit;

            return array(
                'error_code' => 1,
                // 'error_message' => 'There is no file.',
                'error_line'=> $ex->getLine(),
                'error_file'=> $ex->getFile(),
                'error_message'=>$ex->getMessage(),
            );
        }
         return array(
            'error_code' => 1,
            'error_message' => 'There is no file.'
        );
    }
    
	public function signup_avatar($aData)
	{
		extract($aData);
		
		if (!isset($iUserId))
			return array( 'error_code' => 1, 'error_message' => 'Missing user identity' );
		
		if (!($iUserId) || !is_numeric($iUserId))
			return array( 'error_code' => 1, 'error_message' => 'iUserId is invalid' );
		
		try
		{
			$user = Engine_Api::_()->user()->getUser($iUserId);
			if ( $user->getIdentity() <= 0 )
				return array( 'error_code' => 1, 'error_message' => 'This user is not existed' );
			
			if (isset($_FILES['image']))
			{
				$user -> setPhoto($_FILES['image']);
				if ( isset($sCoordinates) && $sCoordinates && isset($iWidth)  && $iWidth && isset($iHeight) && $iHeight)
					$this -> process_avatar($user, $sCoordinates, $iWidth, $iHeight);
				
				$iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);
				$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update',
						'{item:$subject} added a new profile photo.');
				
				if( $action ) 
				{
					$event = Engine_Hooks_Dispatcher::_()
			            ->callEvent('onUserProfilePhotoUpload', array(
			                'user' => $user,
			                'file' => $iMain,
			              ));
			
			          $attachment = $event->getResponse();
					if( !$attachment ) 
						$attachment = $iMain;
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
				}
			}
			else
			{
				return array(
					'error_code' => 1,
					'error_message' => 'There is no file.'
				);
			}
			
			$sUserImage = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			
			if ($sUserImage != "")
			{
				$sUserImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImage);
			}
			else
			{
				$sUserImage = NO_USER_ICON;
			}
            
            $BigUserImage = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
            
            if ($BigUserImage != "")
            {
                $BigUserImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($BigUserImage);
            }
            else
            {
                $BigUserImage = NO_USER_PROFILE;
            }
			
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => 'Edited successfully',
					'user_image' => $sUserImage,
					'big_user_image'=>$BigUserImage,
			);
		}
		catch ( Exception $e)
		{
			return array(
					'error_code' => 1,
					'error_step' => 3,
					'error_message' => $e->getMessage()
			);
		}
	}
	
	public function edit_avatar($aData)
	{
		return $this->signup_avatar($aData);
	}
	
	protected function process_avatar($user, $coordinates, $iViewWidth, $iViewHeight)
	{
		$storage = Engine_Api::_() -> storage();
		$iSquare = $storage -> get($user -> photo_id, 'thumb.icon');
		
		list($x, $y, $w, $h) = explode(':', $coordinates);
		
		//If the image width is over 200, we will use the profile photo to scale
		if ($iViewWidth >= 200)
		{
			$iProfile = $storage -> get($user -> photo_id, 'thumb.profile');
			$fRatio = floatval($iViewWidth)  / 200;
			$pName = $iProfile -> getStorageService() -> temporary($iProfile);
			$iName = dirname($pName) . '/nis_' . basename($pName);
			
			$x = $x / $fRatio;
			$y = $y / $fRatio;
			$w = $w / $fRatio;
			$h = $h / $fRatio;
		}
		else //when the image width is smaller than 200, we will use the orginal file
		{
			if (!is_null($_FILES['image']['tmp_name']))
			{
				if( is_array($_FILES['image']) && !empty($_FILES['image']['tmp_name']) )
				{
					$pName = $_FILES['image']['tmp_name'];
					$fileName = $_FILES['image']['name'];
					$extension = ltrim(strrchr(basename($fileName), '.'), '.');
					$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
					$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
					$iName = $path . DIRECTORY_SEPARATOR . "nis_$base." . $extension;
				}
			}
			else
			{
				$iProfile = $storage -> get($user -> photo_id, 'thumb.profile');
				$pName = $iProfile -> getStorageService() -> temporary($iProfile);
				$iName = dirname($pName) . '/nis_' . basename($pName);
			}
		}
		
		$image = Engine_Image::factory();
		$image -> open($pName) -> resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48) -> write($iName) -> destroy();
		$iSquare -> store($iName);

		// Remove temp files
		@unlink($iName);
	}

	/**
	 *
	 * Input data:
	 * + iUserId: int, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see user/block
	 *
	 * @param array $aData
	 * @return array
	 */
	public function block($aData)
	{
		// Get id of friend to add
		$user_id = $aData['iUserId'];
		if (!$user_id)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("No member specified")
			);
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('block', 'user') -> getAdapter();
		$db->beginTransaction();
		try
		{
			
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$user = Engine_Api::_() -> getItem('user', $user_id);
			$viewer -> addBlock($user);
			
			if ($user -> membership() -> isMember($viewer, null))
			{
				$user -> membership() -> removeMember($viewer);
			}
			
			// Set the requests as handled
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($viewer, $user, 'friend_request');
			if ($notification)
			{
				$notification -> mitigated = true;
				$notification -> read = 1;
				$notification -> save();
			}
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
			if ($notification)
			{
				$notification -> mitigated = true;
				$notification -> read = 1;
				$notification -> save();
			}
			
			$db->commit();
			return array(
				'error_code' => 0,
				'result' => 1,
				'model_data'=> Engine_Api::_()->getApi('profile','ynmobile')->detail(array('iUserId'=> $user->getIdentity()))
			);
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("An error has occurred."),
				'error_debug'=> $e->getMessage(),
			);
		}
	}

	/**
	 *
	 * Input data:
	 * + iUserId: int, required.
	 *
	 * Output data:
	 * + result: int.
	 * + error_code: int.
	 * + error_message: string.
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see user/unblock
	 *
	 * @param array $aData
	 * @return array
	 */
	public function unblock($aData)
	{
		$user_id = $aData['iUserId'];
		if (!$user_id)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("No member specified")
			);
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('block', 'user') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$user = Engine_Api::_() -> getItem('user', $user_id);

			$viewer -> removeBlock($user);

			$db -> commit();

			return array(
				'error_code' => 0,
				'result' => 1,
				'model_data'=> Engine_Api::_()->getApi('profile','ynmobile')->detail(array('iUserId'=> $user->getIdentity()))
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();

			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("An error has occurred.")
			);
		}
	}

	/**
	 * Input data:
	 * + sDisplayName: string
	 * + iLimit: int, optional.
	 * + iLastMemberIdViewed: int, optional.
	 * + sType: string, optional. Ex: "more" or "new".
	 *
	 * Output data:
	 * + sFullName: string.
	 * + id: int.
	 * + UserProfileImg_Url: string.
	 * + BigUserProfileImg_Url: string
	 * + isFriend: bool
	 * + iMutualFriends: int
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see user/members
	 *
	 * @param array $aData
	 * @return array
	 */
	public function members($aData)
	{
		extract($aData, EXTR_SKIP);

		$oViewer = Engine_Api::_() -> user() -> getViewer();

		if (!isset($iLimit))
		{
			$iLimit = 20;
		}
		if (!isset($iLastMemberIdViewed))
		{
			$iLastMemberIdViewed = 0;
		}

		// Get table info
		$table = Engine_Api::_() -> getItemTable('user');
		$userTableName = $table -> info('name');
		// Contruct query
		$select = $table -> select() -> from($userTableName) -> where("{$userTableName}.search = ?", 1) -> where("{$userTableName}.enabled = ?", 1) -> order("{$userTableName}.user_id DESC");
		// Add displayname
		if (!empty($sDisplayName))
		{
			$select -> where("(`{$userTableName}`.`displayname` LIKE ?)", "%{$sDisplayName}%");
		}

		// Condition for "new" and "more" or "confirm" case.
		if (isset($sType))
		{
			if ($sType == 'new')
			{
				$select -> where("{$userTableName}.user_id > ?", (int)$iLastMemberIdViewed);
			}
			else
			if ($sType == 'more')// "more" case.
			{
				$select -> where("{$userTableName}.user_id < ?", (int)$iLastMemberIdViewed);
			}
		}
		$select -> limit($iLimit);
		$members = Zend_Paginator::factory($select);

		// Get the items
		$aUsers = array();

		foreach ($members as $user)
		{
			//photoURL
			$sProfileImage = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			$sBigProfileImage = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);

			if ($sProfileImage)
			{
				$sProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sProfileImage);
				$sBigProfileImage = Engine_Api::_() -> ynmobile() -> finalizeUrl($sBigProfileImage);
			}
			else
			{
				$sProfileImage = NO_USER_ICON;
				$sBigProfileImage = NO_USER_NORMAL;
			}
			//check is friend
			$isFriend = $user -> membership() -> isMember($oViewer);
			// get mutual friends
			$iMutualFriends = 0;
			// Diff friends
			$friendsTable = Engine_Api::_() -> getDbtable('membership', 'user');
			$friendsName = $friendsTable -> info('name');
			// Mututal friends/following mode
			$sql = "SELECT `user_id` FROM `{$friendsName}` WHERE (`active`= 1 and `resource_id`={$user -> getIdentity()})
		        and `user_id` in (select `resource_id` from `engine4_user_membership` where (`user_id`={$oViewer ->getIdentity()} and `active`= 1))";
			$friends = $friendsTable -> getAdapter() -> fetchcol($sql);
			$iMutualFriends = count($friends);

			$aUsers[] = array(
				'id' => $user -> getIdentity(),
				'sFullName' => $user -> getTitle(),
				'UserProfileImg_Url' => $sProfileImage,
				'BigUserProfileImg_Url' => $sBigProfileImage,
				'isFriend' => $isFriend,
				'iMutualFriends' => $iMutualFriends
			);
		}
		return $aUsers;
	}

	protected function getGenderValue($iGender)
	{
		$fieldOptionTable = Engine_Api::_() -> fields() -> getTable('user', 'options');
		$select = $fieldOptionTable -> select();
		if ($iGender == '1')
		{
			$select -> where("label = ?", "Male") -> limit(1) -> order('option_id');
		}
		else
		{
			$select -> where("label = ?", "Female") -> limit(1) -> order('option_id');
		}
		$fieldOption = $fieldOptionTable -> fetchRow($select);
		return $fieldOption -> option_id;

	}
	
	protected $genderOptions  = array();
	
	
	/**
     * @return array [option_id: => label]
     */
	function getGenderOptions(){
	    
	    if(null != $this->genderOptions){
	        return $this->genderOptions;
	    }
        $aGenderOptions = array();
        
        $fieldTable = Engine_Api::_()->fields()->getTable('user', 'meta');
        $optionTable = Engine_Api::_()->fields()->getTable('user', 'options');
        
        $genderField = $fieldTable->fetchRow(
            $fieldTable->select()
            ->where('type=?','gender')
            ->limit(1));
        ;
        
        
        if($genderField){
            $genderSelect =  $optionTable->select()
                ->where('field_id=?',$genderField->field_id);
           

           foreach($optionTable->fetchAll($genderSelect) as $entry){
               $aGenderOptions[$entry->option_id] = $entry->label;
           };
        }
        
        return $this->genderOptions = $aGenderOptions;
	}
	
	public function edit_profile($aData)
	{
		extract($aData);
		
		if (!isset($iUserId))
			return array( 'error_code' => 1, 'error_message' => 'Missing user identity' );
		
		if (!($iUserId) || !is_numeric($iUserId))
			return array( 'error_code' => 1, 'error_message' => 'iUserId is invalid' );
		
		try
		{
			$user = Engine_Api::_()->user()->getUser($iUserId);
			if ( $user->getIdentity() <= 0 )
				return array( 'error_code' => 1, 'error_message' => 'This user is not existed' );
			
			if (isset($sFirstName) && $sFirstName == "")
				return array( 'error_code' => 1, 'error_message' => 'sFirstName is invalid' );
			
			if (isset($sLastName) && $sLastName == "")
				return array( 'error_code' => 1, 'error_message' => 'sLastName is invalid' );
			
			$aFieldInfo = array(
					'first_name' => html_entity_decode($aData['sFirstName'], ENT_QUOTES, 'UTF-8'),
					'last_name' => html_entity_decode($aData['sLastName'], ENT_QUOTES, 'UTF-8'),
			);
			
			if (isset($iGender) && ($iGender != "") ) 
				$aFieldInfo['gender'] = $this -> getGenderValue($iGender);
			else
				$aFieldInfo['gender'] = "";
			
			if (isset($sBirthday))
				$aFieldInfo['birthdate'] = $sBirthday;
			
			if (isset($sWebsite))
				$aFieldInfo['website'] = html_entity_decode($sWebsite, ENT_QUOTES, 'UTF-8');
			
			if (isset($sTwitter))
				$aFieldInfo['twitter'] = html_entity_decode($sTwitter, ENT_QUOTES, 'UTF-8');
			
			if (isset($sFacebook))
				$aFieldInfo['facebook'] = html_entity_decode($sFacebook, ENT_QUOTES, 'UTF-8');
				
			if (isset($sAim))
				$aFieldInfo['aim'] = html_entity_decode($sAim, ENT_QUOTES, 'UTF-8');
			
			if (isset($sAbout))
				$aFieldInfo['about_me'] = html_entity_decode($sAbout, ENT_QUOTES, 'UTF-8');
				
			$aliasedFields = $user->fields()->getFieldsObjectsByAlias();
			$topLevelId = 0;
			$topLevelValue = null;
			if( isset($aliasedFields['profile_type']) ) {
				$aliasedFieldValue = $aliasedFields['profile_type']->getValue($user);
				$topLevelId = $aliasedFields['profile_type']->field_id;
				$topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
				if( !$topLevelId || !$topLevelValue ) {
					$topLevelId = null;
					$topLevelValue = null;
				}
			}

			$formArgs = array(
					'topLevelId' => $topLevelId,
					'topLevelValue' => $topLevelValue,
			);
		
			if ($formArgs)
			{
				$struct = Engine_Api::_() -> fields() -> getFieldsStructureFull($user, $formArgs['topLevelId'], $formArgs['topLevelValue']);
				$arr_data = array();
				foreach ($struct as $fskey => $map)
				{
					$field = $map -> getChild();
					$type = $field -> type;
					if (isset($aFieldInfo[$type]))
					{
						$arr_data[$fskey] = $aFieldInfo[$type];
					}
					
				}
				
				$values = Engine_Api::_() -> fields() -> getFieldsValues($user);
				
				foreach ($arr_data as $key => $value)
				{
					$parts = explode('_', $key);
					if (count($parts) != 3)
						continue;
					list($parent_id, $option_id, $field_id) = $parts;
		
					// Array mode
					if (is_array($value))
					{
						// Lookup
						$valueRows = $values -> getRowsMatching(array(
								'field_id' => $field_id,
								'item_id' => $user -> getIdentity()
						));
		
						// Delete all
						$prevPrivacy = null;
						foreach ($valueRows as $valueRow)
						{
							if (!empty($valueRow -> privacy))
							{
								$prevPrivacy = $valueRow -> privacy;
							}
							$valueRow -> delete();
						}
		
						// Insert all
						$indexIndex = 0;
						if (is_array($value) || !empty($value))
						{
							foreach ((array) $value as $singleValue)
							{
								$valueRow = $values -> createRow();
								$valueRow -> field_id = $field_id;
								$valueRow -> item_id = $user -> getIdentity();
								$valueRow -> index = $indexIndex++;
								$valueRow -> value = $singleValue;
								$valueRow -> save();
							}
						}
						else
						{
							$valueRow = $values -> createRow();
							$valueRow -> field_id = $field_id;
							$valueRow -> item_id = $user -> getIdentity();
							$valueRow -> index = 0;
							$valueRow -> value = '';
							$valueRow -> save();
						}
					}
		
					// Scalar mode
					else
					{
						// Lookup
						$valueRow = $values -> getRowMatching(array(
								'field_id' => $field_id,
								'item_id' => $user -> getIdentity(),
								'index' => 0
						));
		
						// Create if missing
						$isNew = false;
						if (!$valueRow)
						{
							$isNew = true;
							$valueRow = $values -> createRow();
							$valueRow -> field_id = $field_id;
							$valueRow -> item_id = $user -> getIdentity();
						}
		
						$valueRow -> value = htmlspecialchars($value);
						$valueRow -> save();
					}
				}

				// Update display name
				$aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
				$user->setDisplayName($aliasValues);
				$user->save();
				
				// Update search table
				Engine_Api::_() -> getApi('core', 'fields') -> updateSearch($user, $values);
		
				// Fire on save hook
				Engine_Hooks_Dispatcher::getInstance() -> callEvent('onFieldsValuesSave', array(
				'item' => $user,
				'values' => $values
				));
		
				if (isset($_FILES['image']))
				{
					$user -> setPhoto($_FILES['image']);
					if (isset($aData['sCoordinates']) && isset($aData['iWidth']))
						$this -> process_avatar($user, $aData['sCoordinates'], $aData['iWidth'], $aData['iHeight']);
				}
				
				return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => 'Edited successfully',
					'full_name' => $user->getTitle()
				);
			}
		}
		catch( Exception $e )
		{
			return array(
					'error_code' => 1,
					'error_step' => '2',
					'error_message' => $e -> getMessage()
			);
		}
		
	}
	
	public function edit_general($aData)
	{
		extract($aData);
		if (!isset($iUserId))
			return array( 'error_code' => 1, 'error_message' => 'Missing user identity' );
		
		if (!($iUserId) || !is_numeric($iUserId))
			return array( 'error_code' => 1, 'error_message' => 'iUserId is invalid' );
		 
		$user = Engine_Api::_()->user()->getUser($iUserId);
		if ( $user->getIdentity() <= 0 )
			return array( 'error_code' => 1, 'error_message' => 'This user is not existed' );
		
		try 
		{
			if (isset($sEmail))
			{
				$aCheckingEmail = $this -> checkEmail($sEmail);
				if ($aCheckingEmail['error_code'] != '0')
					return $aCheckingEmail;
					
				$user -> email = $sEmail;
			}
			
			if (isset($sUserName))
			{
				$aCheckingUserName = $this -> checkUserName($aData['sUserName']);
				if ($aCheckingUserName['error_code'] != '0')
					return $aCheckingUserName;
			
				$user -> username = $sUserName;
			}
			
			if (isset($sTimeZone))
				$user -> timezone = $sTimeZone;
			
			$user->save();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => 'Edited successfully'
			);
		} 
		catch (Exception $e) 
		{
			return array( 'error_code' => 1, 'error_message' => $e->getMessage() );
		}
		
	}
	
	public function loadImage($image_url)
    {
		$filename   = 'user_avatar_'. time(). '.png';
		$filepath   = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'. DIRECTORY_SEPARATOR . $filename;
		$image_url = html_entity_decode($image_url, ENT_QUOTES, 'UTF-8');
		
		$ch = curl_init($image_url);
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

    function change_password($aData){
        
        extract($aData);
        
        $user = Engine_Api::_()->user()->getViewer();
        
        if(empty($sNewPassword)
        || empty($sOldPassword)
        || strlen($sNewPassword) < 6
        ){
            return array(
                'error_code'=>1,
                'error_message'=>'Invalid parametters'
           );
        };
        
        // check current password
        // Process form
        $userTable = Engine_Api::_()->getItemTable('user');
        $db = $userTable->getAdapter();
    
        // Check old password
        $salt = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret', 'staticSalt');
        
        $select = $userTable->select()
          ->from($userTable, new Zend_Db_Expr('TRUE'))
          ->where('user_id = ?', $user->getIdentity())
          ->where('password = ?', new Zend_Db_Expr(sprintf('MD5(CONCAT(%s, %s, salt))', $db->quote($salt), $db->quote($sOldPassword))))
          ->limit(1)
          ;
          
        $valid = $select
          ->query()
          ->fetchColumn()
          ;
    
        if( !$valid ) {
          return array(
            'error_code'=>1,
            'error_message'=>'Save successful',
          );
        }
        
        $user->setFromArray(array('password'=>$sNewPassword));
        $user->save();
        
        return array(
            'error_code'=>0,
            'message'=>'Your password has been updated'
        );
    }
}
