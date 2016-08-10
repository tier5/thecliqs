<?php

class Ynmobile_Helper_Group extends Ynmobile_Helper_Base{
    
    function field_id(){
        $this->data['iGroupId'] =  $this->entry->getIdentity();
    }
    
    function getYnmobileApi(){
        return Engine_Api::_()->getApi('group','ynmobile');
    }
    
    function field_auth(){
        
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
    
    function field_perms(){
        
        $viewer=$this->getViewer();
        
        
        $this->data['bCanInvite']      = $this->entry->authorization()->isAllowed($viewer,'invite');
        $this->data['bCanComment']      = $this->entry->authorization()->isAllowed($viewer,'comment');
        $this->data['bCanView']         = $this->entry->authorization()->isAllowed($viewer,'view');
        $this->data['bCanUploadPhoto']  = $this->entry->authorization()->isAllowed($viewer,'photo');
        $this->data['bCanCreateEvent']  = $this->entry->authorization()->isAllowed($viewer,'event');
        $this->data['bCanCreateTopic']  = $this->entry->authorization()->isAllowed($viewer,'comment');
    }
    
    function field_listing(){
        $this->field_id();
        $this->field_title();
        $this->field_desc();
        $this->field_type();
        $this->field_members();
        $this->field_category();
        $this->field_actions();
        $this->field_totalMember();
        $this->field_user();
        $this->field_imgNormal();
        $this->field_imgFull();
        
    }
    
    function field_detail(){
        $this->field_listing();
        $this->field_photos();
        $this->field_auth();
        $this->field_perms();
		$this -> field_statistics();
    }
    
    /**
     * 
     */
    function field_desc(){
       return $this->data['sDescription'] =  strip_tags($this->entry->description); 
    }
    
    function field_photos(){
        $limit =10;
        $total = 0;
        $items = array();
        
        // $api =  $this->getYnmobileApi();
        // $module = $api->getWorkingModule();
        $table  = $this->getWorkingTable('photos','group');
        $select  = $table->select()
            ->where('group_id=?', $this->entry->getIdentity());
        
        $paginator = Zend_Paginator::factory($select);
        
        $total = $paginator->getTotalItemCount();
        
        $paginator->setItemCountPerPage($limit);
        $appMeta  =  Ynmobile_AppMeta::getInstance();
        
        $fields = array('simple_array');
        
        foreach($paginator as $row){
            $items[] = $appMeta->getModelHelper($row)->toArray($fields);
        }
        
        $this->data['iTotalPhoto'] =$total;
        $this->data['aPhotos'] =  $items;
    }
    
    function field_canInvite(){
        $this->data['bCanInvite'] = $this->entry->authorization()->isAllowed(null,'invite');
    }
    
    function field_actions(){
        $this->data['actions'] =  $this->getYnmobileApi()->getGroupStateByUser($this->entry, $this->getViewer());
    } 
	function field_statistics(){
		$table = Engine_Api::_()->getItemTable('event');
		$select = $table->select()->where('parent_type = ?', 'group');
		$select = $select->where('parent_id = ?', $this->entry->getIdentity());
		$this->data['iTotalEvent'] = Zend_Paginator::factory($select) ->getTotalItemCount();
		$topicTbl = (Engine_Api::_() -> hasModuleBootstrap('advgroup'))
			? Engine_Api::_()->getItemTable("advgroup_topic")
			: Engine_Api::_()->getItemTable("group_topic");
		$topicSelect = $topicTbl->select()->where('group_id = ?', $this->entry->getIdentity());
		$this->data['iTotalTopic'] = Zend_Paginator::factory($topicSelect) ->getTotalItemCount();
		$waitingMembers = Zend_Paginator::factory($this->entry->membership()->getMembersSelect(false));
		$this->data['iTotalGuest'] = $waitingMembers ->getTotalItemCount();
	}
}
