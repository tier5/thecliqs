<?php

class Ynmobile_Helper_User extends Ynmobile_Helper_Base{
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('user','ynmobile');
    }
    
    function field_id(){
        $this->data['iUserId'] =  $this->entry->getIdentity();
    }
    
    /**
     * maps alias from base settings.
     */
    function field_setting(){
        
        $user  =  $this->entry;
        
        $view  =  $this->getYnmobileApi()->getPrivacyValue($user, 'auth_view','view');
        
        $comment  =  $this->getYnmobileApi()->getPrivacyValue($user, 'auth_comment','comment');
        
        $sLocale =  '';
        
        $this->data['setting'] =  array(
            'auth_view'=>$view['id'],
            'sauth_view'=>$view['title'],
            
            'locale'=>$user->locale,
            'auth_comment'=>$comment['id'],
            'sauth_comment'=>$comment['title'],
            'search'=>$user->search?1:0,
            'email'=>$user->email,
            'username'=>$user->username,
            'timezone'=>$user->timezone,
            'sTimezone'=> $this->getYnmobileApi()->_getTimezoneLabel($user->timezone),
            'locale'=>$user->locale,
            'sLocale'=>$this->getYnmobileApi()->_getLocaleLabel($user->locale),
        );
    }
    
    public function field_photos(){
        
        $limit = defined('LIMIT_FIELD_PHOTOS') ?LIMIT_FIELD_PHOTOS: 3;
        $table = $this->getWorkingTable('photos','album');
        
        // apply roles of photo can views.
        
        if(!$table){
            $this->data['iTotalPhotos'] = 0;
            $this->data['aPhotos'] = array();
            return ;
        }

        $albumIds = Engine_Api::_()->getApi('photo','ynmobile')->getAlbumsCanView($this->entry);
        $albumIds[] = 0;

        $select = $table->select()
            ->where("owner_type = ?", $this->entry->getType())
            ->where("owner_id = ?", $this->entry->getIdentity())
            ->where("album_id IN (?)", $albumIds)
            ->order('photo_id desc');
            
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($limit);
        
        $total = $paginator->getTotalItemCount();
        
        $items = array();
        
        $appMeta  = Ynmobile_AppMeta::getInstance();
        
        $fields = array('simple_array');
        foreach($paginator as $item){
            $items[] = $appMeta->getModelHelper($item)->toArray($fields);
        }
        
        $this->data['iTotalPhoto'] =  $total;
        $this->data['aPhotos'] =  $items;
    }

    function field_cover(){

        $this->data['sCoverUrl'] = '';
        $iPhotoId = 0;

        $user = $this->entry;

        if(isset($user->cover_id) && $user->cover_id){
            $iPhotoId = $user->cover_id;
        }

        if(isset($user->user_cover) && $user->user_cover){
            $iPhotoId = $user->user_cover;
        }

        if($iPhotoId){
            $this->field_cover_from_photo_id($iPhotoId);
        }

        if(empty($this->data['sCoverUrl'])){
            $this->field_cover_from_ynmobile();
        }
    }
    
    /**
     * 
     * get cover photo
     * @return string
     */
    function field_cover_from_ynmobile(){
        $sCoverUrl = '';
        
        $table = Engine_Api::_()->getDbtable('profilecovers','ynmobile');
        
        $select  =  $table->select()->where('owner_id=?', $this->entry->getIdentity())
            ->where('owner_type=?', $this->entry->getType())
            ;
            
        $row =  $table->fetchRow($select);
        
        if($row){
            $sCoverUrl = $this->finalizeUrl($row->getPhotoUrl());
        }
        
        $this->data['sCoverUrl'] = $sCoverUrl;
    }


    function field_cover_from_photo_id($iPhotoId){

        $item =  $this->getYnmobileApi()->getWorkingItem('album_photo', $iPhotoId);

        if($item){
            $this->data['sCoverUrl']  =  $this->finalizeUrl($item->getPhotoUrl());
        }
    }
    
    function field_actions(){
        $viewer =  $this->getViewer();
        $user =  $this->entry;
        $levelAuth =  Engine_Api::_() -> authorization() -> getAdapter('levels') ;
        
        if(!$viewer){
            return;
        }
        
        /**
         * action belong to owner only.
         */
        if($viewer->getGuid() == $user->getGuid())
        {
            
            return ;
        }
        
        $friendship_status = $viewer->membership()->getRow($user);
        
        /**
         * match friend permission
         */
        if(null == $friendship_status){
            // please check
            // admin/user/settings/friends
            // Who Can Become Friends
            $this->data['actions']['bCanSentRequest'] =  1;
        }else if($friendship_status->user_approved == 0){
            $this->data['actions']['bCanCancelRequest'] =  1;
        }else if($friendship_status->resource_approved == 0){
            $this->data['actions']['bCanAcceptRequest'] =  1;
        }else{
            $this->data['actions']['bCanRemoveFriend'] =  1;
        }
        
        
        $permission = Engine_Api::_()
            ->authorization()->getPermission($viewer->level_id, 'messages', 'create');
        
        $bCanSendMessage = 0;
        /**
         * check can send message permission.
         */
        if($permission )
        {
            $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
            if( $messageAuth == 'none' ) {
              
              // do not allow send message feature.
              $bCanSendMessage = 0;
              
            } elseif( $messageAuth == 'friends' ) {
              // Get data
              $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
              
              if( !$direction ) {
                //one way
                $friendship_status = $viewer->membership()->getRow($user);
              }
              else{
                $friendship_status = $user->membership()->getRow($viewer);  
              } 
        
              if( $friendship_status && $friendship_status->active) {
                  $bCanSendMessage = 1;
              }
            }else{
                // everyone can send message
                $bCanSendMessage = 1;
            }
        }
        $this->data['actions']['bCanSendMessage']= $bCanSendMessage;
        
        
        
        /**
         * user level setting for member is control under
         * authorization/level/edit
         */
        $blocked = $user->isBlockedBy($viewer)?1:0;
        
        
        
        $canBlock = $levelAuth -> getAllowed('user', $viewer, 'block');
        
        $this->data['actions']['bCanUnBlock'] = $blocked;
        $this->data['actions']['bCanBlock']  =  (!$blocked && $canBlock)?1:0;
    }

    
    function field_friend(){
        
        
        $this->data['isFriend'] = 0;
        $this->data['isSentRequestBy'] = 0;
        $this->data['isSentRequest'] = 0;
        
        $viewer =  $this->getViewer();
        
        if(!$viewer){
            return;
        }
        
        $row = $viewer->membership()->getRow($this->entry);
        
        if(null == $row){
        }else if($row->user_approved == 0){
            $this->data['isSentRequest'] =  1;
        }else if($row->resource_approved == 0){
            $this->data['isSentRequestBy'] =  1;
        }else{
            $this->data['isFriend'] = 1;
        }
    }
    
    function field_imgIcon(){
        $this->_field_img('thumb.icon','sPhotoUrl');
    }
    
    public function field_totalMutualFriend(){
        
        $user  = $this->entry;
        $viewer  = $this->getViewer();
        
        $friendsTable = Engine_Api::_() -> getDbtable('membership', 'user');
        $friendsName = $friendsTable -> info('name');
        
        // Mututal friends/following mode
        $sql = "SELECT `user_id` FROM `{$friendsName}` WHERE (`active`= 1 and `resource_id`={$user -> getIdentity()})
            and `user_id` in (select `resource_id` from `engine4_user_membership` where (`user_id`={$viewer ->getIdentity()} and `active`= 1))";
        $friends = $friendsTable -> getAdapter() -> fetchcol($sql);
        
        $this->data['iMutualFriends'] = count($friends);
    }
    
    public function field_totalFriend()
    {
        $iUserId = $this->entry->getIdentity();

        $membershipTable = Engine_Api::_() -> getDbtable('membership', 'user');
        $membershipName = $membershipTable -> info('name');
        $select = $membershipTable -> select() -> from($membershipTable, new Zend_Db_Expr('COUNT(resource_id)'));

        $this->data['iTotalFriend'] = (int)$select
        -> where("{$membershipName}.user_id = ?", $iUserId)
        -> where('active = 1')
        -> limit(1)
        -> query()
        -> fetchColumn();
    }
    
    public function field_block(){
        $viewer =  $this->getViewer();
        $user =  $this->entry;
        
        $this->data['isBlocked'] = $user->isBlockedBy($viewer)?1:0;
        $this->datap['isBlockedBy'] = ($viewer->isBlockedBy($user) && !$viewer->isAdmin()) ? 1:0;
    }
    
    public function field_profile(){
        
        $profile = array();
       
        $oViewer = $this->getViewer();
        $iUserId = $this->entry->getIdentity();
        $oUser = Engine_Api::_() -> user() -> getUser($iUserId);
        
        $profile['id'] =  $iUserId;
        
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
            if ($oViewer && $oViewer -> getIdentity())
            {
                if ($oViewer -> getIdentity() == $oUser -> getIdentity())
                {
                    $relationship = 'self';
                }
                else
                if ($oViewer -> membership() -> isMember($oUser, true))
                {
                    $relationship = 'friends';
                }
                else
                {
                    $relationship = 'registered';
                }
            }
        }
        $show_hidden = $oViewer -> getIdentity() ? ($oUser -> getOwner() -> isSelf($oViewer) || 'admin' === Engine_Api::_() -> getItem('authorization_level', $oViewer -> level_id) -> type) : false;
        
        $genderOptions = $this->getYnmobileApi()->getGenderOptions();;
        
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
                        $profile['first_name'] =  $value;
                        break;
                    case 'last_name' :
                        $profile['last_name'] =  $value;
                        break;
                    case 'gender' :
                        $profile['gender'] = $value;
                        $profile['sGender'] = isset($genderOptions[$value])?$genderOptions[$value]:'';
                        break;
                    case 'birthdate' :
                        $profile['birthdate1'] = ($value) ? date('Y-m-d', strtotime($value)) : "";
                        $profile['birthdate2'] = ($value) ? date('M j, Y', strtotime($value)) : "";
                        $profile['birthdate'] = $value;
                        break;
                    case 'relationship_status' :
                        // this field is deprecated
                        $profile['relationship_status'] = $value;
                        break;
                    case 'zip_code' :
                        $profile['zip_code'] = $value;
                        break;
                    case 'city' :
                        $profile['city'] = $value;
                        break;
                    case 'location' :
                        $profile['location'] = $value;
                        break;
                    case 'about_me' :
                        $profile['about_me'] = $value;
                        break;
                    case 'facebook':
                        $profile['facebook'] =  $value;
                        break;
                    case 'twitter':
                        $profile['twitter'] =  $value;
                        break;
                    case 'aim':
                        $profile['aim'] =  $value;
                        break;
                    case 'website':
                        $profile['website'] = $value;
                        break;
                    default :
                        $profile[$field -> type] = $value;
                        break;
                }
            }
        }

        $this->data['profile'] = $profile;
                
    }
    
    function field_friends($iLimit  =  3){

        $oUser = $this->entry;
        $oViewer = $this->getViewer();
        
        $membershipTable = Engine_Api::_() -> getDbtable('membership', 'user');
        $membershipName =  $membershipTable->info('name');
        
        $select = $membershipTable -> select()
        -> from(array('m1'=>$membershipName), array('resource_id'))
        -> setIntegrityCheck(false);
        
        $select -> where("`m1`.`user_id` = ?", $oUser->getIdentity()) -> where('`m1`.`active` = ?',1);
        
        $paginator  =  Zend_Paginator::factory($select);
        
        $this->data['iTotalFriend'] = $paginator->getTotalItemCount();
        
        $select -> limit($iLimit);
        
        $ids = array();
        
        foreach ($membershipTable->fetchAll($select) as $entry)
        {
            $ids[] = $entry -> resource_id;
        }
        
        // Get the items
        $items = array();
        
        $fields = array('simple_array');
        
        $table =  Engine_Api::_()->getItemTable('user');
        
        foreach ($table->find($ids) as $entry)
        {
            $items[] = Ynmobile_AppMeta::getInstance()->getModelHelper($entry)->toArray($fields);
        }
        
        $this->data['aFriends'] =  $items;
    }

    /**
     * @return array{2=>'Male', ... }
     */
    function field_genderOptions(){
       $this->data['aGenderOptions'] =  $this->getYnmobileApi()->getGenderOptions();
    }
    
    function field_groupActions(){
        
        $group = $this->getParam('group');
        
        if(!$group) return ;
        
        $this->data['actions'] = Engine_Api::_()->getApi('group','ynmobile')->getGroupStateByUser($group,$this->entry);
        
    }
    
    function field_token(){
        $token = $this->getParam('token');
        if(!$token){return ;}
        
        $this->data['token'] = $token['token_id'];    
    }
    
    function field_listing(){
        $this->field_id();
        $this->field_type();
        $this->field_title();
        $this->field_imgIcon();
        $this->field_imgFull();
        $this->field_imgProfile();
        $this->field_friend();
        
    }
    
    function field_detail(){
        $this->field_listing();
        $this->field_setting();
        $this->field_profile();
        $this->field_cover();
        $this->field_token();
        $this->field_friends();
        $this->field_photos();
        $this->field_block();
        $this->field_canView();
        $this->field_canComment();
    }
}
