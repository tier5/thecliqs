<?php
/**
 * SocialEngine
 * 
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2014 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Blog.php longl $ 
 * @author     LONGL
 */

class Ynmobile_Service_Blog extends Ynmobile_Service_Base
{
    protected $module = 'blog';
    
    protected $mainItemType = 'blog';
    
    
    function get_blog_select($params = array()){
   
    $table = Engine_Api::_()->getDbtable('blogs', 'blog');
    $rName = $table->info('name');

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');
    //$tmTable = Engine_Api::_()->getDbtable('tagmaps', 'blog');
    //$tmName = $tmTable->info('name');

    $select = $table->select()
      ->order( !empty($params['orderby']) ? $params['orderby'].' DESC' : $rName.'.creation_date DESC' );
    
    if( !empty($params['user_id']) && is_numeric($params['user_id']) )
    {
      $select->where($rName.'.owner_id = ?', $params['user_id']);
    }

    if( !empty($params['user']) && $params['user'] instanceof User_Model_User )
    {
      $select->where($rName.'.owner_id = ?', $params['user_id']->getIdentity());
    }

    if( !empty($params['users']) )
    {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($rName.'.owner_id in (?)', new Zend_Db_Expr($str));
    }

    if( !empty($params['tag']) )
    {
      $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($tmName, "$tmName.resource_id = $rName.blog_id")
        ->where($tmName.'.resource_type = ?', 'blog')
        ->where($tmName.'.tag_id = ?', $params['tag']);
    }

    if( !empty($params['category']) )
    {
      $select->where($rName.'.category_id = ?', $params['category']);
    }

    if( isset($params['draft']) )
    {
      $select->where($rName.'.draft = ?', $params['draft']);
    }

    //else $select->group("$rName.blog_id");

    // Could we use the search indexer for this?
    if( !empty($params['search']) )
    {
      $select->where($rName.".title LIKE ? OR ".$rName.".body LIKE ?", '%'.$params['search'].'%');
    }

    if( !empty($params['start_date']) )
    {
      $select->where($rName.".creation_date > ?", date('Y-m-d', $params['start_date']));
    }

    if( !empty($params['end_date']) )
    {
      $select->where($rName.".creation_date < ?", date('Y-m-d', $params['end_date']));
    }

    if( !empty($params['visible']) )
    {
      $select->where($rName.".search = ?", $params['visible']);
    }

    return $select;
  
    }

    public function fetch($aData){
    	if(isset($aData['sView']) && $aData['sView'] == 'my'){
    		return $this->my($aData);
    	}else{
    		return $this->get($aData);
    	}
    }
    
	public function get($aData)
	{
		extract($aData);
        
        $iPage = @$iPage?intval($iPage):1;
        $iLimit =  @$iLimit?intval($iLimit):20;
        $sView  = @$sView?$sView: 'all';
		
		
		//init parameters for searching entries
		$viewer = Engine_Api::_()->user()->getViewer();
		$values = array();
		
		if (isset($sSearch) && !empty($sSearch))
		{
			$values['search'] = $sSearch;
		}
		
		if (isset($iCategory) && !empty($iCategory))
		{
			$values['category'] = $iCategory;
		}
		
		if (isset($sOrder) && !empty($sOrder))
		{
			$values['orderby'] = $sOrder;
		}
		
		if ($sView == 'my')
		{
			$values['user_id'] = $iUserId;
			if (!($iUserId == $viewer->getIdentity())){
    			 $values['visible'] = "1";
                
                if($this->getWorkingModule('blog') == 'ynblog'){
                    $values['is_approved'] = 1;
                }   
			}
		}
		else
		{
			$values['visible'] = "1";
            
            if($this->getWorkingModule('blog') == 'ynblog'){
                $values['is_approved'] = 1;
            }
		}
        
        
		
		if (!isset($iDraft))
		{
			$values['draft'] = 0;
		}
		elseif ($iDraft == '0' || $iDraft == '1')
		{
			$values['draft'] = $iDraft;
		}
		
		$values['limit'] = $iLimit;
		$values['page'] = $iPage;
		
        
        
		// Get blogs
		$workingTable =$this->getWorkingTable('blogs','blog');
        $workingApi   =  $this->getWorkingApi('core','blog');
        
        if(method_exists($workingTable, 'getBlogsPaginator')){
            $paginator =$workingTable ->getBlogsPaginator($values);    
        }else if(method_exists($workingApi, 'getBlogsPaginator')){
            $paginator = $workingApi->getBlogsPaginator($values);    
        }
        
        return Ynmobile_AppMeta::_exports_by_page($paginator, $iPage, $iLimit, $fields = array('listing'));
        
	}
	
	
	public function my($aData)
	{
		extract($aData);
		if (!isset($iUserId) || empty($iUserId))
		{
			$viewer = Engine_Api::_()->user()->getViewer();
			$iUserId = $viewer->getIdentity();
		}
		
		$aData['iUserId'] = $iUserId;
		$aData['iDraft'] = '-1';
		return $this->get($aData);
	}
	
	
	public function delete($aData)
	{
		extract($aData);
        
        $translate = Zend_Registry::get('Zend_Translate');
        
        $blogTable =  $this->getWorkingTable('blogs','blog');
        
        
		if(!isset($iEntryId) || empty($iEntryId)){
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("Missing iEntryId!")
			);
		}
        
		if (!Engine_Api::_() -> authorization() -> isAllowed('blog', null, 'delete')){
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("You don't have permission to delete this entry!"),
			);
		}
        
        $entry = $blogTable->findRow($iEntryId);
        
		try 
		{
			$entry->delete();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => $translate -> _("Deleted entry successfully!")
			);
		} 
		catch (Exception $e) 
		{
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage()
			);
		}
	}
	
	
	public function edit($aData)
	{
		extract($aData);
        
        $translate = Zend_Registry::get('Zend_Translate');
        
        $blogTable =  $this->getWorkingTable('blogs','blog');
        
        
		if(!isset($iEntryId) || empty($iEntryId))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iEntryId!")
			);
		}
	
		$viewer = Engine_Api::_()->user()->getViewer();
		$blog = $blogTable->findRow($iEntryId);
		
		if (is_null($blog))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This entry is not existed!")
			);
		}
	
		if (!$blog-> authorization() -> isAllowed(null, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this entry!"),
			);
		}
		
		$auth = Engine_Api::_()->authorization()->context;
		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
	
		// Process
		$db = $blogTable->getAdapter();
		
		$db->beginTransaction();
	
		try
		{
			//title
			if (isset($sTitle) && !empty($sTitle))
			{
				$sTitle = html_entity_decode($sTitle, ENT_QUOTES, 'UTF-8');
				$blog->title = $sTitle;
			}
			
			//category
			if (isset($iCategoryId) && !empty($iCategoryId))
			{
				$blog->category_id = $iCategoryId;
			}
			
			//draft
			if (isset($iDraft) && ($iDraft == '0' || $iDraft == '1'))
			{
				$blog->draft = $iDraft;
			}
			
			//can search
			if (isset($iSearch))
			{
				$blog->search = $iSearch;
			}
			
			//body
			if (isset($sBody) && !empty($sBody))
			{
				$sBody = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
				
				$filter = new Engine_Filter_Html();
				$filter->setForbiddenTags();
				$allowed_tags = array_map('trim', explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'blog', 'auth_html')));
				$filter->setAllowedTags($allowed_tags);
				$blog->body = $filter->filter($sBody);
			}
			
			$blog->modified_date = date('Y-m-d H:i:s');
			$blog->save();
	
			// Auth
			if( empty($sAuthView) ) 
			{
				$sAuthView = 'everyone';
			}
	
			if( empty($sAuthComment) ) {
				$sAuthComment = 'everyone';
			}
	
			$viewMax = array_search($sAuthView, $roles);
			$commentMax = array_search($sAuthComment, $roles);
	
			foreach( $roles as $i => $role ) {
				$auth->setAllowed($blog, $role, 'view', ($i <= $viewMax));
				$auth->setAllowed($blog, $role, 'comment', ($i <= $commentMax));
			}
	
			// handle tags
			if (isset($sTags) && !empty($sTags))
			{
				$tags = preg_split('/[,]+/', $sTags);
				$blog->tags()->setTagMaps($viewer, $tags);
			}
			
			// insert new activity if blog is just getting published
			$action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($blog);
			if( count($action->toArray()) <= 0 && $iDraft == '0' ) 
			{
				$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, $this->getActivityType('blog_new'));
				if( $action != null ) {
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
				}
			}
	
			// Rebuild privacy
			$actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
			foreach( $actionTable->getActionsByObject($blog) as $action ) {
				$actionTable->resetActivityBindings($action);
			}
	
			// Send notifications for subscribers
			
			$this->getWorkingTable('subscriptions','blog')->sendNotifications($blog);
	
			$db->commit();
	
			return array(
				'message' => Zend_Registry::get('Zend_Translate') -> _("Edited successfully!")
			);
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage(),
			);
		}
	}
	
	public function create($aData)
	{
	    extract($aData);
        
        $translate = Zend_Registry::get('Zend_Translate');
        
        $blogTable =  $this->getWorkingTable('blogs','blog');
        
	    $blog = $blogTable->fetchNew();
	    $itemType = $blog->getType();
        
		if (!Engine_Api::_() -> authorization() -> isAllowed($itemType, null, 'create')){
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("You don't have permission to create a new blog entry!"),
			);
		}
		
		extract($aData);
        
		$viewer = Engine_Api::_()->user()->getViewer();
		
		// CHECKING QUOTA
		$values['user_id'] = $viewer->getIdentity();
        
    	// Get blogs
        $workingTable =$this->getWorkingTable('blogs','blog');
        $workingApi   =  $this->getWorkingApi('core','blog');
        
        if(method_exists($workingTable, 'getBlogsPaginator')){
            $paginator =$workingTable ->getBlogsPaginator($values);    
        }else if(method_exists($workingApi, 'getBlogsPaginator')){
            $paginator = $workingApi->getBlogsPaginator($values);    
        }
        
    	$quota = Engine_Api::_()->authorization()
    	   ->getPermission($viewer->level_id, $itemType, 'max');
    	
    	$current_count = $paginator->getTotalItemCount();
        
		if (($current_count >= $quota) && !empty($quota))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You have already uploaded the maximum number of entries allowed."),
			);
		}
		
		if (!isset($sTitle) || empty($sTitle))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Entry title is empty!"),
			);
		}
		
		if (!isset($sBody) || empty($sBody))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Entry body is empty!"),
			);
		}
		
		if (!isset($iCategoryId) || empty($iCategoryId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Entry must belong to a category!"),
			);
		}
		
		// Process
		
		$db = $blogTable->getAdapter();
		$db->beginTransaction();
	
		try {
			
            $blog->setFromArray(array(
                'title'=>$sTitle,
                'category_id'=>$iCategoryId,
                'owner_type'=>$viewer->getType(),
                'owner_id'=>$viewer->getIdentity(),
                'search'=>1,
                'is_approved'=>1,
                'draft'=>0,
            ));
			
			$sBody = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
			$filter = new Engine_Filter_Html();
			$filter->setForbiddenTags();
			$allowed_tags = array_map('trim', explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'blog', 'auth_html')));
			$filter->setAllowedTags($allowed_tags);
			$blog->body = $filter->filter($sBody);
            
            if($this->getWorkingModule('blog') == 'ynblog'){
                
                $blog_moderation = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynblog.moderation', 0 );
                if ($blog_moderation) 
                {
                    $blog -> is_approved = 0;
                }
            }
            
			
			//draft
			if (isset($iDraft) && $iDraft)
			{
				$blog->draft = $iDraft;
			}
			
			//can search
			if (isset($iSearch))
			{
				$blog->search = $iSearch;
			}
			
			$blog->save();
	
			// Auth
			$auth = Engine_Api::_()->authorization()->context;
			$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
	
			if( empty($sAuthView) ) 
			{
				$sAuthView = 'everyone';
			}
	
			if( empty($sAuthComment) ) 
			{
				$sAuthComment = 'everyone';
			}
	
			$viewMax = array_search($sAuthView, $roles);
			$commentMax = array_search($sAuthComment, $roles);
	
			foreach( $roles as $i => $role ) {
				$auth->setAllowed($blog, $role, 'view', ($i <= $viewMax));
				$auth->setAllowed($blog, $role, 'comment', ($i <= $commentMax));
			}
	
			// Add tags
			$tags = preg_split('/[,]+/', $sTags);
			$blog->tags()->addTagMaps($viewer, $tags);
	
			// Add activity only if blog is published
			if( $iDraft == 0)     {
			    $post = 0;
                if($this->getWorkingModule('blog') == 'ynblog'){
                    if($blog -> is_approved == 1){
                        $post = 2;    
                    }
                }else{
                    $post  = 1;
                }
                if($post){
                    $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, $this->getActivityType('blog_new'));
                    
                    // make sure action exists before attaching the blog to the activity
                    if( $action ) {
                        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
                    }    
                }
				
	
			}
	
			// Send notifications for subscribers
			$this->getWorkingTable('subscriptions', 'blog')->sendNotifications($blog);
	
			// Commit
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'module'=>$this->getWorkingModule('blog'),
				'iEntryId' => $blog->getIdentity(),
			);
			
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage(),
			);
		}
	}
	
	public function view($aData)
	{
		extract($aData);
        
        if(empty($fields)) $fields  = 'detail';
        
        $fields = explode(',', $fields);
        
        $translate = Zend_Registry::get('Zend_Translate');
        
        $blogTable =  $this->getWorkingTable('blogs','blog');
        
        $blog =  $blogTable->findRow($iEntryId);
		
		if( !$blog) 
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This entry is not existed!")
			);
		}
        
        $viewer =  $this->getViewer();
        if($viewer){
            if($blog->getOwner()->getGuid() != $viewer->getGuid()){
                $blog->view_count ++;
                $blog->save();
            }    
        }
        
        
        return Ynmobile_AppMeta::_export_one($blog, $fields);
	}
	
	public function add_subscription($aData)
	{
		extract($aData);
        
		if(!isset($iUserId) || empty($iUserId))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iUserId!")
			);
		}
		
		// Get viewer and subject
		$viewer = Engine_Api::_()->user()->getViewer();
		$user = Engine_Api::_()->user()->getUser($iUserId);
		
		if (!($user->getIdentity()))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This user is not existed!")
			);
		}
		
		// Get subscription table
		$subscriptionTable = $this->getWorkingTable('subscriptions', 'blog');
	
		// Check if they are already subscribed
		if( $subscriptionTable->checkSubscription($user, $viewer) ) 
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate')->_('You are already subscribed to this member\'s blog.')
			);
		}
	
		// Process
		$db = $user->getTable()->getAdapter();
		$db->beginTransaction();
		
		try 
		{
			$subscriptionTable->createSubscription($user, $viewer);
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate')->_('You are now subscribed to this member\'s blog.'),
			);
		} 
		catch( Exception $e ) 
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	public function remove_subscription($aData)
	{

		extract($aData);
        
		if(!isset($iUserId) || empty($iUserId))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iUserId!")
			);
		}
		
		// Get viewer and subject
		$viewer = Engine_Api::_()->user()->getViewer();
		$user = Engine_Api::_()->user()->getUser($iUserId);
		
		if (!($user->getIdentity()))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This user is not existed!")
			);
		}
		
		// Get subscription table
		$subscriptionTable = $this->getWorkingTable('subscriptions', 'blog');
	
		// Check if they are already not subscribed
		if( !$subscriptionTable->checkSubscription($user, $viewer) ) 
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate')->_('You are already not subscribed to this member\'s blog.')
			);
		}
	
		// Process
		$db = $user->getTable()->getAdapter();
		$db->beginTransaction();
		
		try 
		{
			$subscriptionTable->removeSubscription($user, $viewer);
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate')->_('You are no longer subscribed to this member\'s blog.'),
			);
		} 
		catch( Exception $e ) 
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}

	public function become($aData)
	{
		return array(
			'error_code' => 1,
			'error_message' => Zend_Registry::get('Zend_Translate')->_('This function is only supported on Advanced Blog module ')
		);
	}
	
	
	public function formedit($aData)
	{

		return array_merge(	
				$this->view($aData), 
				$this->formadd(array()
		));
	}
	
	public function publish($aData)
	{
		extract($aData);
        
        $translate  = Zend_Registry::get('Zend_Translate');
        
		if(!isset($iEntryId) || empty($iEntryId))
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate -> _("Missing iEntryId!")
			);
		}
		
		// Check permission
		$viewer = Engine_Api::_()->user()->getViewer();
        
        $blogTable = $this->getWorkingTable('blogs','blog');
        
		$blog  =  $blogTable->findRow($iEntryId);
        
		if( is_null($blog) ) 
		{
			return array(
				'error_code' => 1,
				'error_message' => $translate-> _("This entry is not existed!")
			);
		}
		
		$blog->draft = 0;
		$blog->save();
		
		// insert new activity if blog is just getting published
      	
  	    $post = 0;
        if($this->getWorkingModule('blog') == 'ynblog'){
            if($blog -> is_approved == 1){
                $post = 2;    
            }
        }else{
            $post  = 1;
        }
        if($post){
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($blog);
            
            if(count($action->toArray()) <= 0){
                $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, $this->getActivityType('blog_new'));
                
                // make sure action exists before attaching the blog to the activity
                if( $action ) {
                    Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $blog);
                }    
            }   
        }
      	
		// Rebuild privacy
	    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
	    foreach( $actionTable->getActionsByObject($blog) as $action ) {
	        $actionTable->resetActivityBindings($action);
	    }
		
		return array(
			'error_code' => 0,
			'error_message' => '',
			'message' => $translate -> _("Publish an entry successfully!")
		);
	}
	
	public function permissions($aData)
	{
		extract($aData);
        
        $translate = Zend_Registry::get('Zend_Translate');
        
        $blogTable =  $this->getWorkingTable('blogs','blog');
        
        $itemType =  $blogTable->fetchNew()->getType();
        
		return array(
			'bCanView' => (Engine_Api::_() -> authorization() -> isAllowed($itemType, null, 'view')) ? true : false,
			'bCanCreate' => (Engine_Api::_() -> authorization() -> isAllowed($itemType, null, 'create')) ? true : false,
		);
	}
}