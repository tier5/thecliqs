<?php

class Ynmobile_Api_Setting{
    
    protected $_roles = array('owner', 'member', 'network', 'registered', 'everyone');
    
    function get_blocked_users($aData)
    {
        $return =  array();
        
        $iPage = isset($aData['iPage'])?intval($aData['iPage']): 1;
        $user =  Engine_Api::_()->user()->getViewer();
        
        // do not support paging, always load all
        if($iPage>1)
        {
            return $return;
        }
        
        $membershipTable = Engine_Api::_() -> getDbtable('membership', 'user');
        $membershipName = $membershipTable -> info('name');
        
        foreach ($user->getBlockedUsers() as $blocked_user_id) 
        {
            
                $entry = Engine_Api::_()->user()->getUser($blocked_user_id);
                
                if(!$entry) continue;
                
                //photoURL
                $sProfileImage = $entry -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
                $sBigProfileImage = $entry -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
    
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
                $isFriend = $entry -> membership() -> isMember($user, 1);
                // get mutual friends
                $iMutualFriends = 0;
    
                // Mututal friends/following mode
                $sql = "SELECT `user_id` FROM `{$membershipName}` WHERE (`active`= 1 and `resource_id`={$entry -> getIdentity()})
                    and `user_id` in (select `resource_id` from `engine4_user_membership` where (`user_id`={$user ->getIdentity()} and `active`= 1))";
                $friends = $membershipTable -> getAdapter() -> fetchcol($sql);
                $iMutualFriends = count($friends);
                
                
                $row = array(
                    'iUserId' => $entry -> getIdentity(),
                    'sFullName' => $entry -> getTitle(),
                    'UserProfileImg_Url' => $sProfileImage,
                    'BigUserProfileImg_Url' => $sBigProfileImage,
                    'isFriend' => $isFriend,
                    'iMutualFriends' => $iMutualFriends,
                    'isBlocked'=>1,
                    'isBlockedBy'=>($user->isBlockedBy($entry) && !$user->isAdmin())
                );
                
                $return[] =  $row;

        }
        
        return $return;
    }
    
    function privacy_browse($aData){
        
        extract($aData);
        
        $form = array();
        $formData = array();
        
        $user=  Engine_Api::_()->user()->getViewer();
        
        Engine_Api::_()->core()->setSubject($user);
        
        $auth = Engine_Api::_()->authorization()->context;
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        
        $form['bCanEdit'] =  true;
        
        // does not need to show this form.
        // if( !Engine_Api::_()->getDbtable('permissions', 'authorization')->isAllowed($user, $user, 'search') ) {
          // $form['bCanEdit'] =  false;
        // }
            
        $formData['search'] = $user->search ?1:0;
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            
            $search = intval($search);
                        
            $user->setFromArray(array('search'=>$search));
            $user->save();
            
            return array(
                'message'=>'Your changes has been saved',
                'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
            );
        }
        
        
        return array(
            'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
        );
    }
    
    function general_email($aData){
        $form = array();
        $user=  Engine_Api::_()->user()->getViewer();
        
        $view =  Zend_Registry::get('Zend_View');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            try{
                $sEmail =  isset($aData['sEmail'])?$aData['sEmail']: null;
            
                if(empty($sEmail)){
                    return array(
                        'error_code'=>1,
                        'error_message'=>$view->translate('Missing sEmail parametters'),
                    );
                    
                }     
                
                $user->email =  $sEmail;
                $user->save();
                
            
                return array(
                    'message'=>$view->translate('Your email has ben saved'),
                    'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
                );
                            
            }catch(Exception $ex){
                return array(
                    'error_code'=>1,
                    'error_message'=>$view->translate('This email is exists!'),
                );    
            }
            
            
        };
       
       
       return array(
            'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
       );
    }
    
    function general_timezone($aData){
        
        
        
        $form = array();
        $formData = array();
        $user=  Engine_Api::_()->user()->getViewer();
        
        $form['timezone_options'] =  Engine_Api::_()->getApi('core','ynmobile')->_getTimezones();
        
        $formData['sTimezone'] =  $user->timezone;
        $view =  Zend_Registry::get('Zend_View');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
           $sTimezone =  isset($aData['sTimezone'])?$aData['sTimezone']: null;
            
            if(empty($sTimezone)){
                return array(
                    'error_code'=>1,
                    'error_message'=>$view->translate('Missing sTimezone parametters'),
                );
                
            }     
            
            $user->timezone =  $sTimezone;
            $user->save();
            
            
            return array(
                'message'=>$view->translate('Your timezone has ben saved'),
                'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
            );
        }
        
        $export  =  Ynmobile_AppMeta::_export_one($user, array('setting'));
       return array(
            'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
       );
        
    }

    function general_locale($aData){
        
        $form = array();
        $formData = array();
        $user=  Engine_Api::_()->user()->getViewer();
        
        $form['locale_options'] =  Engine_Api::_()->getApi('core','ynmobile')->_getLocales();
        
        $formData['sLocale'] =  $user->locale;
        $view =  Zend_Registry::get('Zend_View');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
           $sLocale =  isset($aData['sLocale'])?$aData['sLocale']: null;
            
            if(empty($sLocale)){
                return array(
                    'error_code'=>1,
                    'error_message'=>$view->translate('Missing sLocale parametters'),
                );
                
            }     
            
            $user->locale =  $sLocale;
            $user->save();
            
            
            return array(
                'message'=>$view->translate('Your locale has ben saved'),
                'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
            );
        }
        
       
        return array(
            'form'=>$form,
            'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
        );  
        
    }
    
    function notification($aData){
        
        $user = Engine_Api::_()->user()->getViewer();
        
        Engine_Api::_()->core()->setSubject($user);
        
        $auth = Engine_Api::_()->authorization()->context;
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
    
        // Build the different notification types
        $modules = Engine_Api::_()->getDbtable('modules', 'core')->getModulesAssoc();
        $notificationTypes = Engine_Api::_()->getDbtable('notificationTypes', 'activity')->getNotificationTypes();
        $notificationSettings = Engine_Api::_()->getDbtable('notificationSettings', 'activity')->getEnabledNotifications($user);
    
        $notificationTypesAssoc = array();
        $notificationSettingsAssoc = array();
        foreach( $notificationTypes as $type ) {
          if( in_array($type->module, array('core', 'activity', 'fields', 'authorization', 'messages', 'user')) ) {
            $elementName = 'general';
            $category = 'General';
          } else if( isset($modules[$type->module]) ) {
            $elementName = preg_replace('/[^a-zA-Z0-9]+/', '-', $type->module);
            $category = $modules[$type->module]->title;
          } else {
            $elementName = 'misc';
            $category = 'Misc';
          }
    
          $notificationTypesAssoc[$elementName]['category'] = $category;
          $notificationTypesAssoc[$elementName]['types'][$type->type] = 'ACTIVITY_TYPE_' . strtoupper($type->type);
    
          if( in_array($type->type, $notificationSettings) ) {
            $notificationSettingsAssoc[$elementName][] = $type->type;
          }
        }
    
        ksort($notificationTypesAssoc);
    
        $notificationTypesAssoc = array_filter(array_merge(array(
          'general' => array(),
          'misc' => array(),
        ), $notificationTypesAssoc));
        
        $view =  Zend_Registry::get('Zend_View');
    
        // Make form
        $elements =  array();
        foreach( $notificationTypesAssoc as $elementName => $info ) {
            
            $options  = array();
            
            foreach($info['types'] as $key=>$val){
                $options[] =  array(
                    'key'=>$key,
                    'val'=>$view->translate($val),
                    'checked'=>array_search($key, $notificationSettings)!==false?1:0,
                );
            }
            $elements[] = array(
                'label'=> $info['category'],
                'options'=>$options,
                'values'=>(array) @$notificationSettingsAssoc[$elementName],
            );
        }
    
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $checkedValues = (array)$aData['checkedValues'];
            // Set notification setting
            Engine_Api::_()->getDbtable('notificationSettings', 'activity')
                ->setEnabledNotifications($user, $checkedValues);        
            }
        
        return $elements;
    }
    
    
    public function privacy_view($aData){
        $form = array();
        $formData = array(
            'sPrivacy'=>'',);
        
        $user=  Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->core()->setSubject($user);
        
        $auth = Engine_Api::_()->authorization()->context;
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        //Who can view your profile?
        $form['privacyField'] = true;
        
        $availableLabels = array(
          'owner'       => 'Only Me',
          'member'      => 'Only My Friends',
          'network'     => 'Friends & Networks',
          'registered'  => 'All Registered Members',
          'everyone'    => 'Everyone',
        );
    
        $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_view');
        $view_options = array_intersect_key($availableLabels, array_flip($view_options));
        
        $view_options_objs = array();
        foreach($view_options as $key=>$val){
            $view_options_objs[] = array('key'=>$key, 'val'=>$val);
        }
        
        $form['privacy_options'] = $view_options_objs;

        foreach( $this->_roles as $role ) {
          if( 1 === $auth->isAllowed($user, $role, 'view') ) {
            $formData['sPrivacy'] =  $role;
          }
        }

        $view = Zend_Registry::get('Zend_View');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            
            $sPrivacy =  $aData['sPrivacy']?$aData['sPrivacy']:null;
            
            if(empty($sPrivacy)){
                $sPrivacy = end(Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_view'));
            }
            
            $sPrivacy = empty($sPrivacy)
                     ? 'everyone'
                     : $sPrivacy;
                     
            $privacy_max_role = array_search($sPrivacy, $this->_roles);
            
            foreach( $this->_roles as $i => $role ){
                $auth->setAllowed($user, $role, 'view', ($i <= $privacy_max_role) );
            }
            
            
            return array(
                'message'=>$view->translate('Settings were successfully saved.'),
                'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
            );        
        }
        
        
       
        return array(
            'form'=>$form,
            'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
        );  
    }

    public function privacy_comment($aData){
        $form = array();
        $formData = array();
        
        $user=  Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->core()->setSubject($user);
        
        $auth = Engine_Api::_()->authorization()->context;
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
          //Who can post on your profile?
        $availableLabelsComment = array(
          'owner'       => 'Only Me',
          'member'      => 'Only My Friends',
          'network'     => 'Friends & Networks',
          'registered'  => 'All Registered Members',
        );

        // Init profile comment
        $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_comment');
        $comment_options = array_intersect_key($availableLabelsComment, array_flip($comment_options));
        
        $comment_options_objs = array();
        foreach($comment_options as $key=>$val){
            $comment_options_objs[] =  array('key'=>$key, 'val'=>$val);
        }
        
        $form['comment_options'] = $comment_options_objs;
        
        $view  = Zend_Registry::get('Zend_View');
        
        foreach( $this->_roles as $role ) {
          if( 1 === $auth->isAllowed($user, $role, 'comment') ) {
            $formData['sComment'] = $role;
          }
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            
            $sComment =  $aData['sComment']?$aData['sComment']:null;
            
            if(empty($sComment)){
                $sComment = end(Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_comment'));
            }
            
            $sComment = empty($sComment)
                     ? 'registered'
                     : $sComment;
                     
            $comment_max_role = array_search($sComment, $this->_roles);
            
            foreach( $this->_roles as $i => $role ){
                $auth->setAllowed($user, $role, 'comment', ($i <= $comment_max_role) );
            }   
    
            
            return array(
                'message'=>$view->translate('Settings were successfully saved.'),
                'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
            );        
        }
        
        return array(
            'form'=>$form,
            'result'=> Ynmobile_AppMeta::_export_one($user, array('setting')),
        );
    }

    public function privacy_activity($aData){
        $form = array();
        $formData = array();
        
        $user=  Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->core()->setSubject($user);
        
        $auth = Engine_Api::_()->authorization()->context;
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        // publish action type
        $form['publishTypesField'] = true;

        // Init publishtypes
        if(false == Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.publish', true) ) {
          $form['publishTypesField'] = false;
        }
        
        $actionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getEnabledActionTypesAssoc();
        unset($actionTypes['signup']);
        unset($actionTypes['postself']);
        unset($actionTypes['post']);
        unset($actionTypes['status']);
        
        $actionTypes_objs = array();
        
        $actionTypesEnabled = Engine_Api::_()->getDbtable('actionSettings', 'activity')->getEnabledActions($user);
        
        foreach($actionTypes as $key=>$val){
            $actionTypes_objs[] =  array('key'=>$key, 'val'=>$val, 'checked'=> array_search($key, $actionTypesEnabled) !== false? true:false);
        }
        
        $form['publishTypes_options'] = $actionTypes_objs;
        
        
        
        $formData['publishTypes'] =  $actionTypesEnabled;
        $view =  Zend_Registry::get('Zend_View');
        
        if($_SERVER['REQUEST_METHOD']=='POST'){
            try{
                $publishTypes = $aData['checkedValues'];
                $publishTypes[] = 'signup';
                $publishTypes[] = 'post';
                $publishTypes[] = 'status';
                Engine_Api::_()->getDbtable('actionSettings', 'activity')
                ->setEnabledActions($user, (array) $publishTypes);
                
            }catch(Exception $ex){
                return array(
                    'error_code'=>1,
                    'error_message'=>$ex->getMessage(),
                );
            }
            return array(
                'message'=>$view->translate('Your changes have been saved.'),
            );
        }
        
        return array(
            'form'=>$form,
            'formData'=>$formData,
        );
    }
}
