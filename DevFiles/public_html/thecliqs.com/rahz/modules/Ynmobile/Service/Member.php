<?php 

class Ynmobile_Service_Member extends Ynmobile_Service_Base{
    
    protected $module = 'user';
    
    public function form_search(){
        
        $gender_options = array(array('val'=>'0','val'=>'All'));
        
        $fieldTable = Engine_Api::_()->fields()->getTable('user', 'meta');
        $optionTable = Engine_Api::_()->fields()->getTable('user', 'options');
        
        $genderField = $fieldTable->fetchRow(
            $fieldTable->select()
            ->where('type=?','gender')
            ->limit(1));
        ;
        
        
        if($genderField){
            $gender_select =  $optionTable->select()
                ->where('field_id=?',$genderField->field_id);
           

           foreach($optionTable->fetchAll($gender_select) as $entry){
               $gender_options[] = array('key'=>$entry->option_id, 'val'=>$entry->label);
           };
        }
        
        return array(
            'gender_options'=>$gender_options,
            'age_options'=> array()
        );
    }
    
    public function fetch($aData)
    {
                   
        extract($aData, EXTR_SKIP);
        
        $fields = explode(',', $fields);
        
        $sSearch  = isset($sSearch)?$sSearch: '';
        $iPage =  isset($iPage)? intval($iPage): 1;
        $iLimit = isset($iLimit)? intval($iLimit): 10;
        $iGender = isset($iGender)? intval($iGender): 0;
        $bHasPhoto = isset($bHasPhoto)?intval($bHasPhoto): 0;
        $bIsOnline = isset($bIsOnline)?intval($bIsOnline): 0;
        $bExcludeMe = isset($bExcludeMe)? intval($bExcludeMe): 0;
        $iBirthdateMin = isset($iBirthdateMin)?intval($iBirthdateMin):0;
        $iBirthdateMax = isset($iBirthdateMax)?intval($iBirthdateMax):0;
        
        
        $options = array();
        
        if($iGender){
            $options['gender'] =  $iGender;
        }
        
        if($iBirthdateMin){
            $options['birthdate']['min']=  $iBirthdateMin;
        }
        
        if($iBirthdateMax){
            $options['birthdate']['max']=  $iBirthdateMax;
        }
        
        $viewer = Engine_Api::_() -> user() -> getViewer();
        
        $this->view->form = $form;
    
        // Get table info
        $table = Engine_Api::_()->getItemTable('user');
        $userTableName = $table->info('name');
    
        $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
        $searchTableName = $searchTable->info('name');
         
        // Contruct query
        $select = $table->select()
        //->setIntegrityCheck(false)
        ->from($userTableName)
        ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
        //->group("{$userTableName}.user_id")
        ->where("{$userTableName}.search = ?", 1)
        ->where("{$userTableName}.enabled = ?", 1);
        
        
        // excludes me
        if($bExcludeMe){
            $select->where("{$userTableName}.user_id <> ?", $viewer->getIdentity());
        }   
        
        
        $searchDefault = true;  
      
        // Build the photo and is online part of query
        if( isset($bHasPhoto) && $bHasPhoto ) {
          $select->where($userTableName.'.photo_id != ?', "0");
          $searchDefault = false;
        }
        
        if( isset($bIsOnline) && $bIsOnline ) {
          $select
            ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
            ->group("engine4_user_online.user_id")
            ->where($userTableName.'.user_id != ?', "0");
          
          $searchDefault = false;
        }

        // Add displayname
        if( !empty($sSearch) ) {
          $db = $table->getAdapter();
          $likeSearch =  $db->quote("%{$sSearch}%");
          $equalSearch  = $db->quote($sSearch);
            
          $select->where("(`{$userTableName}`.`username` LIKE {$likeSearch} or `{$userTableName}`.`displayname` LIKE {$likeSearch} or `{$userTableName}`.`email`={$equalSearch})");
          $searchDefault = false;
        }
        
        // Build search part of query
        $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
        foreach( $searchParts as $k => $v ) {
          $select->where("`{$searchTableName}`.{$k}", $v);
          
          if(isset($v) && $v != ""){
            $searchDefault = false;
          }
        }
        
        if($searchDefault){
          $select->order("{$userTableName}.lastlogin_date DESC");
        } else {
          $select->order("{$userTableName}.displayname ASC");
        }
        
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($iPage);
        $paginator->setItemCountPerPage($iLimit);
        
        
        if($paginator->count() < $iPage){
            return array();
        }
        
        // Get the items
        $result = array();
        $appMeta = Ynmobile_AppMeta::getInstance();
        
        foreach ($paginator as $entry){
            $result[]  =  $appMeta->getModelHelper($entry)->toArray($fields);
          
        }
        return $result;
    }
}
