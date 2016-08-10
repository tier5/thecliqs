<?php

class Ynmobile_Helper_Base{
    
    static protected $rootUrl;
    
    static protected $baseUrl;
    
    static protected $viewer;
    
    protected $data;
   
    protected $options;
    
    protected $params;
    
    function __construct($entry, $options, $params){
        
        $this->entry =  $entry;
        $this->options  = $options;
        
        $this->params = $params;
        
    }
    
    function getParam($key){
        return isset($this->params[$key])?$this->params[$key]:null;
    }
    
    
    function toSimpleArray(){
        return $this->toArray(array('simple_array'));
    }
    
    function getWorkingItem($sType, $iId){
        return $this->getYnmobileApi()->getWorkingItem($sType, $iId);
    }
    
    function getWorkingType($sType){
        return $this->getYnmobileApi()->getWorkingType($sType);
    }
    
    function getWorkingItemTable($sItemType){
        return $this->getYnmobileApi()->getWorkingItemTable($sItemType);
    }
    /**
     * @return Ynmobile_Api_Base
     */
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('base','ynmobile');
    }
    
    function getWorkingApi($api, $module = null){
        return $this->getYnmobileApi()->getWorkingApi($api, $module);
    }
    
    function getWorkingModule($module = null){
        return $this->getYnmobileApi()->getWorkingModule($module);
    }
    
    function getWorkingTable($table, $module =  null){
        
        return $this->getYnmobileApi()->getWorkingTable($table, $module);
    }
    
    static function getViewer(){
        if(null == self::$viewer){
            self::$viewer  = Engine_Api::_() -> user() -> getViewer();
        }
        return self::$viewer;
    }
    
    static function getViewerId(){
        $viewer = self::getViewer();
        $id = 0;
        if($viewer){
            $id =  $viewer->getIdentity();
        }
        return $id;
    }
    
    static function setViewer($viewer){
        self::$viewer = $viewer;
    }
    
    static function getSchema(){
        $schema = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
            $schema = "https";
        }
        return $schema. '://';
    }
    
    function field_simple_array(){
        $this->data['id'] =  $this->entry->getIdentity();
        $this->data['type'] =  $this->entry->getType();
        $this->data['title'] =  $this->entry->getTitle();
        
        $type  = isset($this->options['simple_img'])?$this->options['simple_img']: 'thumb.icon';
        
        $url = $this->entry->getPhotoUrl($type)
        ;
        $this->data['img'] =  $url?$this->finalizeUrl($url):$this->getNoImg($type);
    }
    
    static function getRootUrl(){
        if(!self::$rootUrl){
            self::$rootUrl = self::getSchema() . $_SERVER["SERVER_NAME"]. '/';
        }
        return self::$rootUrl;
    }
    
    /**
     * @return string etc: /se483/
     */
    static function getBaseUrl(){
        if(null == self::$baseUrl){
             self::$baseUrl =  self::getSchema() . str_replace("/index.php", '', $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
        }
        return self::$baseUrl;
    }
    
    static function finalizeUrl($url){
        if ($url && strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE){
           return self::getRootUrl(). ltrim( $url, '/');
        }
        return $url;
    }
    
    function getNoImg($type){
        if(@$this->options['no_imgs'][$type]){
            return self::getBaseUrl() . $this->options['no_imgs'][$type];
        }
    }
    
    
    function getExtra(){
        return array(
            'bCanLike'=>true,
        );
    }
    
    function toArray($fields =  array('iId','sTitle')){
        
        $this->data = array();
        
        foreach($fields as $field){
            if(method_exists($this, $method = 'field_' . $field)){
                $this->$method();
            }
        }
        return $this->data;
    }
    
    function field_id(){
        $this->data['id']=$this->entry->getIdentity();
    }
    
    function field_type(){
        $this->data['sModelType'] =  $this->entry->getType();
    }
    
    function field_title(){
        $this->data['sTitle'] = $this->entry->getTitle();
    }
    
    function field_desc(){
        $this->data['sDescription'] = $this->entry->getDescription();
    }
    
    function _field_img($type, $key){
        $url = $this->entry->getPhotoUrl($type);
        $this->data[$key] =  $url?$this->finalizeUrl($url):$this->getNoImg($type);
    }
    
    function field_timestamp(){
        if(isset($this->entry->creation_date)){
            $this->data['iTimeStamp'] =  strtotime($this->entry -> creation_date);    
        }else if($this->entry->date){
            $this->data['iTimeStamp'] =  strtotime($this->entry -> date);
        }
        
    }
    
    function field_imgIcon(){
        $this->_field_img('thumb.icon','imgIcon');
    }
    
    
    function field_imgNormal(){
        $this->_field_img('thumb.normal','sPhotoUrl'); 
    }
    
    function field_imgFull(){
        $this->_field_img('','sFullPhotoUrl');
    }
    
    function field_imgProfile(){
        $this->_field_img('thumb.profile','sProfilePhotoUrl'); 
    }
    
    function field_totalLike(){
        if(method_exists($this->entry, 'likes')){
            $this->data['iTotalLike']    = intval($this->entry->likes()->getLikeCount());    
        }
        
    }
    
    function field_liked(){
        if(method_exists($this->entry, 'likes')){
            $this->data['bIsLiked']       = $this->entry -> likes() -> isLike($this->getViewer())?1:0;
        }
    }
    
    function field_canLike(){
        if(!isset($this->data['bCanComment'])){
            $bCanComment  = (Engine_Api::_() -> authorization() -> isAllowed($this->entry, null, 'comment')) ? 1 : 0;
            $this->data['bCanComment'] =$bCanComment;
            $this->data['bCanLike'] =$bCanComment;
        }
    }
    
    function field_canComment(){
        if(!isset($this->data['bCanComment'])){
            $bCanComment  = (Engine_Api::_() -> authorization() -> isAllowed($this->entry, null, 'comment')) ? 1 : 0;
            $this->data['bCanComment'] =$bCanComment;
            $this->data['bCanLike'] =$bCanComment;
        }
    }
    
    function field_totalComment(){
        if(method_exists($this->entry, 'comments')){
            $this->data['iTotalComment'] = intval($this->entry->comments()->getCommentCount());    
        }
        
    }
    
    function field_totalView(){
        if(isset($this->entry->view_count)){
            $this->data['iTotalView']=  intval($this->entry->view_count);
        } else {
            $this->data['iTotalView'] = 0;
        }
    }
    
    function field_stats(){
        $this->field_type();
        $this->field_liked();
        $this->field_canComment();
        $this->field_canView();
        $this->field_totalComment();
        $this->field_totalLike();
        $this->field_totalView();
        $this->field_timestamp();
        $this->field_rate();
        $this->field_href();
    }
    
    function field_href(){
        $this->data['sHref'] = $this->finalizeUrl($this->entry->getHref());
    }
    
    function field_rate(){
            
        if(!isset($this->entry->rating)){
            return ;
        }
        
        $iEntryId =  $this->entry->getIdentity();
        $iViewerId =  $this->getViewerId();
        
        /**
         * Ynvideo_Api_Core or Video_Api_Core
         */
        $coreApi  =  $this->getWorkingApi('core', $this->module);
        
        
        if(!method_exists($coreApi, 'ratingCount')){
            return; 
        }
        
        $ratingCount =   $coreApi-> ratingCount($iEntryId);
        
        $rated = 0;
        
        if($iViewerId){
            $rated = $coreApi -> checkRated($iEntryId, $iViewerId) ? 1:0;    
        }
        
        $this->data['fRating'] = $this->entry->rating;
        $this->data['iRatingCount'] = $ratingCount;
        $this->data['bIsRating'] =  $rated;
    }
    
    function field_canView(){
        $this->data['bCanView']=$this->entry-> authorization() -> isAllowed($this->getViewer(), 'view');
    }
    
    function field_canEdit(){
        
        $editable =  (Engine_Api::_() -> authorization() -> isAllowed($this->entry, null, 'edit')) ? 1 : 0;;
        
        $this->data['bCanEdit'] =$editable; 
        $this->data['bCanDelete'] = $editable;
    }
    
    function field_canDelete(){
        
        $editable =  (Engine_Api::_() -> authorization() -> isAllowed($this->entry, null, 'edit')) ? 1 : 0;;
        
        $this->data['bCanEdit'] =$editable; 
        $this->data['bCanDelete'] = $editable;
    }
    
    public function field_likes(){
        if(method_exists($this->entry, 'likes')){
            $this->data['aLikes']=Engine_Api::_()-> getApi('like','ynmobile') -> getUserLike($this->entry);    
        }
                
    }
    
    public function field_totalPhoto()
    {
        $table =  $this->getWorkingTable('photos','album');

        $this->data['iTotalPhoto'] = (int)$table->select()
            ->from($table, new Zend_Db_Expr('COUNT(photo_id)'))
            ->where("owner_type = ?", $this->entry->getType())
            ->where("owner_id = ?", $this->entry->getIdentity())
            ->where("album_id > 0")
            ->limit(1)
            ->query()
            ->fetchColumn();
    }
    
    public function field_photos(){
        
        $limit = defined('LIMIT_FIELD_PHOTOS') ?LIMIT_FIELD_PHOTOS: 3;
        
        $engine = Engine_Api::_();
        
        $table = $this->getWorkingTable('photos','album');
        
        if(!$table){
            $this->data['iTotalPhotos'] = 0;
            $this->data['aPhotos'] = array();
            return ;
        }

        $select = $table->select()
            ->where("owner_type = ?", $this->entry->getType())
            ->where("owner_id = ?", $this->entry->getIdentity())
            ->where("album_id > 0");
            
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

    public function field_events(){
        
        $limit = defined('LIMIT_FIELD_EVENTS') ?LIMIT_FIELD_EVENTS: 3;
        
        $engine = Engine_Api::_() ;
        
        $table = $this->getWorkingTable('events','event');
        
        
        if(!$table){
            $this->data['total_event'] =  0;
            $this->data['events'] = array();
            return ;
        }

        $select = $table->select()
            ->where("parent_type = ?", $this->entry->getType())
            ->where("parent_id = ?", $this->entry->getIdentity())
            ;
            
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($limit);
        
        $total = $paginator->getTotalItemCount();
        
        $items = array();
        
        $appMeta  = Ynmobile_AppMeta::getInstance();
        
        $fields = array('id','title','imgIcon','type');
        foreach($paginator as $item){
            $items[] = $appMeta->getModelHelper($item)->toArray($fields);
        }
        
        $this->data['iTotalEvent'] =  $total;
        $this->data['aEvents'] =  $items;
    }
    
    
    
    public function field_albums($limit = 3){
        
        $limit = defined('LIMIT_FIELD_ALBUMS') ?LIMIT_FIELD_ALBUMS: 3;
            
        // Get paginator
        $engine = Engine_Api::_();
        
        $table= $this->getWorkingTable('albums','album');
        
        if(!$table){
            $this->data['iTotalAlbum'] =  0;
            $this->data['aAlbums'] = array();
            return ;
        }
        
        $select = $table->select()
           ->where("owner_type = ?", $this->entry->getType())
           ->where("owner_id = ?", $this->entry->getIdentity())
           ;
            
        $paginator =  Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($iLimit);
        $paginator->setCurrentPageNumber(1);
        
        $total = $paginator->getTotalItemCount();
        
        $items = array();
        
        $appMeta  =  Ynmobile_AppMeta::getInstance();
        
        $fields = array('simple_array');
        
        foreach($paginator as $item){
            $items[] = $appMeta->getModelHelper($item)->toArray($fields);
        }
        
        $this->data['iTotalAlbum'] = $total;
        $this->data['aAlbums'] =  $items;
    }

    public function field_blogs($limit = 3){
        
        $limit = defined('LIMIT_FIELD_BLOGS') ?LIMIT_FIELD_BLOGS: 3;
        
        $engine = Engine_Api::_();
        $table= $this->getWorkingTable('posts','blog');
        
        if(!$table){
            $this->data['iTotalBlog'] = 0;
            $this->data['aBlogs'] =  array();
            return ;
        }

        $select = $table->select()
            ->where("owner_type = ?", $this->entry->getType())
            ->where("owner_id = ?", $this->entry->getIdentity())
            ->where("is_approved = 1");
            
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage($limit);
        
        $total = $paginator->getTotalItemCount();
        
        $items = array();
        
        $appMeta  = Ynmobile_AppMeta::getInstance();
        
        $fields = array('simple_array');
        foreach($paginator as $item){
            $items[] = $appMeta->getModelHelper($item)->toArray($fields);
        }
        
        $this->data['iTotalBlog'] =  $total;
        $this->data['aBlogs'] =  $items;
    }

    
    public function field_user(){
        
        $user = $this->entry->getOwner();
        
        $fields =  array('simple_array');
        
        $helper = Ynmobile_AppMeta::getInstance()
            ->getModelHelper($user);
            
        $this->data['user'] = $helper ->toArray($fields);
        
    }
    
    public function field_as_attachment(){
        $this->data['iId'] = $this->entry->getIdentity();
        $this->field_id();
        $this->field_type();
        $this->field_desc();
        $this->field_imgNormal();
        $this->field_imgFull();
        $this->field_title();
        $this->field_href();
    }
    
    public function field_owner(){
        
        $user = $this->entry->getOwner();
        
        $fields =  array('simple_array');
        
        $helper = Ynmobile_AppMeta::getInstance()
            ->getModelHelper($user);
            
        $this->data['oOwner'] = $helper ->toArray($fields);
    }
    
    function field_parent(){
        $object  =  $this->entry->getParent();
        
        if(!$object){
            return $this->data['oParent'] = array();
        }
        
        return $this->data['oParent'] = Ynmobile_AppMeta::getInstance()->getModelHelper($object)->toSimpleArray();
    }
   
    function field_object(){
        $object  =  $this->entry->getObject();
        
        if(!$object){
            return $this->data['oObject'] = array();
        }
        
        return $this->data['oObject'] = Ynmobile_AppMeta::getInstance()->getModelHelper($object)->toSimpleArray();
    }
    
    function field_subject(){
        $object  =  $this->entry->getSubject();
        
        if(!$object){
            return $this->data['oObject'] = array();
        }
        
        return $this->data['oObject'] = Ynmobile_AppMeta::getInstance()->getModelHelper($object)->toSimpleArray();
    }
    function field_tags(){
        $tags = $this->entry->tags()->getTagMaps();
        $items = array();
        foreach($tags as $tag)
        {
            $items[] = $tag->getTag()->text;
        }
        $this->data['aTags'] = $items;
        $this->data['sTags'] = implode(',', $items);
    }
    
    /**
     * for edit data
     */
    function field_model(){
        if($this->entry){
            $model = $this->entry->toArray();
            $model['id'] = $this->entry->getIdentity();
        }
        
        $this->data['model'] = $model;
    }
    
    
    function field_viewOptions()
    {
        $this->data['viewOptions'] = $this->getYnmobileApi()->viewOptions();
    }
    
    public function field_auth(){
        $classified  = $this->entry;
        $auth = Engine_Api::_() -> authorization() -> context;
        $roles = array(
            'owner',
            'owner_member',
            'owner_member_member',
            'owner_network',
            'registered',
            'everyone'
        );
        foreach ($roles as $role)
        {
            if (1 === $auth -> isAllowed($classified, $role, 'view'))
            {
                $sViewPrivacy = $role;
            }
            if (1 === $auth -> isAllowed($classified, $role, 'comment'))
            {
                $sCommentPrivacy = $role;
            }
        }
        $this->data['auth']['view'] = $sViewPrivacy;
        $this->data['auth']['comment'] = $sCommentPrivacy;
        
    }
        
    /**
     * @predecated
     */
    function field_auth2(){
        
        $auth = Engine_Api::_() -> authorization() -> context;
        
        $view = 'everyone';
        $comment = 'everyone';
        
        $roles = array(
				'owner',
				'officer',
				'member',
				'registered',
				'everyone'
			);
        
        foreach ($roles as $role)
        {
            if (1 === $auth -> isAllowed($this->entry, $role, 'view'))
            {
                $view = $role;
            }
            if (1 === $auth -> isAllowed($this->entry, $role, 'comment'))
            {
                $comment = $role;
            }
        }
    
        $this->data['auth']['view'] =  $view;
        $this->data['auth']['comment'] = $comment;
    }
    
    function field_commentOptions(){
        $this->data['commentOptions'] =  $this->getYnmobileApi()->commentOptions();
    }
    
    function field_members(){
        
        $total = 0;
        $limit = 20;
        $index = 0;
        
        $aMembers = array();
        
        if(isset($this->entry->member_count)){
            $total = $this->entry->member_count;    
        }
        
        $appMeta = Ynmobile_AppMeta::getInstance();
        
        if($total){
            $members = $this->entry->membership()->getMembers();
            foreach ($members as $member){
                if($index >= $limit) break;
                ++ $index;
                $aMembers[] = $appMeta->getModelHelper($member)->toArray($fields=  array('simple_array'));
            }
        }
        
        $this->data['iTotalMember'] =  $total;
        $this->data['aMembers']     =  $aMembers;
    
    }
    
    
    function field_category(){
        if(isset($this->entry->category_id)){
            $this->data['iCategoryId'] =  $this->entry->category_id;
            $this->data['sCategory'] = $this->getYnmobileApi()->getCategoryName($this->entry->category_id);    
        }
        
    }
    
    function field_categoryOptions(){
        $this->data['categoryOptions'] =  $this->getYnmobileApi()->categories();   
    }
    
    function __call($method_name, $args){
        if(method_exists($this->entry, $method_name))
            return $this->entry->{$method_name}();
    }
}