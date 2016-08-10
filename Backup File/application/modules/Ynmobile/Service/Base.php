<?php

class Ynmobile_Service_Base extends Core_Api_Abstract {

    protected $module = 'core';
    protected $mainItemType = 'user';

    
    protected $availabePrivacyOptions  = array(
          'everyone'              => 'Everyone',
          'registered'            => 'All Registered Members',
          'owner_network'         => 'Friends and Networks',
          'owner_member_member'   => 'Friends of Friends',
          'owner_member'          => 'Friends Only',
          'owner'                 => 'Just Me'
    );
    
    static $workingTypes = array();
    
    function getViewer(){
        return Engine_Api::_()->user()->getViewer();    
    }
    
    function getWorkingItem($sType, $iId){
        return Engine_Api::_()->getItem($this->getWorkingType($sType), $iId);
    }
    
    function getWorkingItemTable($sType){
        return Engine_Api::_()->getItemTable($this->getWorkingType($sType));
    }
	
	function finalizeUrl($url){
		return Ynmobile_Helper_Base::finalizeUrl($url);
	}
    
    /**
     * get working item type.
     */
    function getWorkingType($type){
        
        if(isset(self::$workingTypes[$type])){
            return self::$workingTypes[$type];
        }
        
        if(Engine_Api::_()->hasItemType($type)){
            return self::$workingTypes[$type] =  $type;
        }
        
        $parts = explode('_', $type,2);
        if(count($parts) > 1){
            $module =    $parts[0];
            $track  =    $parts[1];
        }else{
            $module = $type;
            $track  = $type;    
        }
      
        $module = $this->getWorkingModule($module);
        return self::$workingTypes[$type] =  $module .'_'. $track;
    }
    
    function getActivityType($type){
        $parts = explode('_', $type,2);
        
        if(count($parts) > 1){
            return $this->getWorkingModule($module) . '_'. $parts[1];
        }else{
            return $type;               
        } 
    }
    

    function getWorkingModule($module = null) {
        return Ynmobile_AppMeta::getWorkingModule($module?$module:$this->module);
    }

    function getWorkingApi($api = 'core', $module = null) {

        $module = $this->getWorkingModule($module);
        
        return Engine_Api::_()->getApi($api, $module);
    }

    function getWorkingTable($table, $module=null){
        
        $module = $this->getWorkingModule($module);
        
        return Engine_Api::_()->getDbTable($table, $module);
    }
    
    protected $categoryAssoc = array();
    
    function getCategoryName($id){
        $assoc  = $this->loadCategoryAssoc();
        
        return isset($assoc[$id])?$assoc[$id]:'';
    }
    
    function loadCategoryAssoc(){
        
        if($this->categoryAssoc){
            return $this->categoryAssoc;
        }
                
        $table = $this -> getWorkingTable('categories', $this->module);
        
        $named   = array_pop(array_intersect(array(
            'category_name',
            'title'
        ),array_values($table->info('cols'))));
        
        $select = $table -> select()->order($named);

        foreach ($table->fetchAll($select) as $cate) {
            $this->categoryAssoc[$cate->getIdentity()] = $cate->{$named};
        }

        return $this->categoryAssoc;
    }

    /**
     * get get category options
     */
    function categories() {

        foreach ($this->loadCategoryAssoc() as $id=>$title) {
            $options[] = array(
                'id' => intval($id),
                'title' => $title,
            );
        }

        return $options;
    }
    
    function getPrivacyValue($entry, $list, $action){
        $viewer =  Engine_Api::_()->user()->getViewer();
        
        $type =  $this->getWorkingType($entry->getType());
        $availableLabels  = $this->availabePrivacyOptions;
        $auth = Engine_Api::_()->authorization()->context;
        
        if(!$viewer){
            return array();
        }
        
        // Element: auth_view
        $options = (array) Engine_Api::_()
            ->authorization()
            ->getAdapter('levels')
            ->getAllowed($type, $viewer, $list);
            
        $options = array_reverse(array_intersect_key($availableLabels, array_flip($options)));
        $result = array();
        
        foreach($options as $role=>$label){
           if($auth->isAllowed($entry, $role, $action)){
               $result = array('id'=>$role,'title'=>$label);
           }
        }
        
        /**
         * init if empty values.
         */
        if(empty($result) && count($options)){
            foreach($options as $role->$label){
                $result = array('id'=>$role,'title'=>$label);
            }
        }
        
        return $result;
    }
    
    /**
     * @return array[{id: int, title: label}] // options
     * etc: [{id: everyone, title: "everyone"}, ... ]
     */
    function getPrivacyOptions($itemType, $action){
        
        $viewer =  Engine_Api::_()->user()->getViewer();
        
        $type =  $this->getWorkingType($itemType);
        $availableLabels  = $this->availabePrivacyOptions;
        
        if(!$viewer){
            return array();
        }
        
        // Element: auth_view
        $options = (array) Engine_Api::_()
            ->authorization()
            ->getAdapter('levels')
            ->getAllowed($type, $viewer, $action);
            
        $options = array_intersect_key($availableLabels, array_flip($options));
        $result = array();
        foreach($options as $key=>$title){
            $result[] = array(
                'id'=>$key,
                'title'=>$title,
            );
        }
        
        return $result;
    }

    function viewOptions() {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_view');
    }
    
    function photoOptions() {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_photo');
    }

    function eventOptions() {
       return $this->getPrivacyOptions($this->mainItemType, 'auth_event');
    }

    function commentOptions() {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_comment');
    }
    
    
    function inviteOptions() {
        return $this->getPrivacyOptions($this->mainItemType, 'auth_invite');
    }

    /**
     * form add
     */
    public function formadd($aData) {

        $response = array(
            'viewOptions' => $this -> viewOptions(),
            'commentOptions' => $this -> commentOptions(),
            'categoryOptions' => $this -> categories(),
        );

        return $response;
    }
}
