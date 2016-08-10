<?php

class Ynmobile_Api_Injector{
    
    static $inst;
    
    function target(){
        return array();
    }
    /**
     * @return Ynmobile_Api_Injector
     */
    static function __(){
        if(null == self::$inst){
            self::$inst = new self;
        }
        return self::$inst;
    }
    
    function isFriend(&$target, $user, $viewer){
        
        
        $row = $viewer->membership()->getRow($user);
        $target['isFriend'] = 0;
        $target['isSentRequest'] = 0;
        $target['isSentRequestBy'] = 0;
        
        if(null == $row){
        }else if($row->user_approved == 0){
            $target['isSentRequest'] =  1;
        }else if($row->resource_approved == 0){
            $target['isSentRequestBy'] =  1;
        }else{
            $target['isFriend'] = 1;
        }
    }
    
    function iMutualFriend(&$target, $user, $viewer){
        $friendsTable = Engine_Api::_() -> getDbtable('membership', 'user');
        $friendsName = $friendsTable -> info('name');
        // Mututal friends/following mode
        $sql = "SELECT `user_id` FROM `{$friendsName}` WHERE (`active`= 1 and `resource_id`={$user -> getIdentity()})
            and `user_id` in (select `resource_id` from `engine4_user_membership` where (`user_id`={$viewer ->getIdentity()} and `active`= 1))";
        $friends = $friendsTable -> getAdapter() -> fetchcol($sql);
        $target['iMutualFriends'] = count($friends);
    }
    
    function sProfileImage(&$target, $user){
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
        $target['UserProfileImg_Url'] =  $sProfileImage;
        $target['BigUserProfileImg_Url'] =  $sBigProfileImage;
    }
    
    function dataFriend(&$target, $oUser, $iLimit  =  3){

        $membershipTable = Engine_Api::_() -> getDbtable('membership', 'user');
        $membershipName =  $membershipTable->info('name');
        
        $select = $membershipTable -> select()
        -> from(array('m1'=>$membershipName), array('resource_id'))
        -> setIntegrityCheck(false);
        
        $select -> where("`m1`.`user_id` = ?", $oUser->getIdentity()) -> where('`m1`.`active` = ?',1);
        
        $paginator  =  Zend_Paginator::factory($select);
        
        $total = $paginator->getTotalItemCount();
        
        $select -> limit($iLimit);
        
        $ids = array();
        
        foreach ($membershipTable->fetchAll($select) as $entry)
        {
            $ids[] = $entry -> resource_id;
        }
        
        // Get the items
        $items = array();
        
        $injector =  Ynmobile_Api_Injector::__();
        
        foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $entry)
        {
            $obj  =  $injector->target();
            
            $item = array(
                'id' => $entry -> getIdentity(),
                'sFullName' => $entry -> getTitle()
            );
            
            $injector->sProfileImage($item, $entry); 
            
            $items[] = $item;
        }
        
        $target['dataFriend'] =  array(
            'total'=>$total,
            'items'=>$items,
        );
    }

    function dataAlbum(&$target, $oUser, $iLimit =3){
            
            // Get paginator
            $paginator = null;
            
            if(Engine_Api::_()->hasItemType('album')){
                
                // support default album module.
                $paginator = Engine_Api::_()->getItemTable('album')
                            ->getAlbumPaginator(array('owner' => $oUser));
                            
            } elseif(Engine_Api::_()->hasItemType('advalbum'))
            {
                /**
                 * support advanced album
                 */
                $paginator = Engine_Api::_()->getItemTable('advalbum')
                            ->getAlbumPaginator(array('owner' => $oUser));
            }
            
            if(!$paginator){
                $target['dataAlbum']  = array(
                    'total'=>0,
                    'items'=>array());
                return ;
            }
    
            // Set item count per page and current page number
            $paginator->setItemCountPerPage($iLimit);
            $paginator->setCurrentPageNumber(1);
            
            $total = $paginator->getTotalItemCount();
            
            $items = array();
            
            foreach($paginator as $entry){
                $items[] =  array(
                    'iAlbumId'=> $entry->getIdentity(),
                    'sAlbumImageUrl'=> $entry->getPhotoUrl('thumb.icon'),
                );
            }
            
            $target['dataAlbum'] =  array(
                'total'=>$total,
                'items'=>$items,
            );
        }
}
