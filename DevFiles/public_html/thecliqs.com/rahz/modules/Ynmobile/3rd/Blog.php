<?php
/**
 * SocialEngine
 * 
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2014 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Album.php longl $ 
 * @author     LONGL
 */

class Ynmobile_Api_Blog extends Core_Api_Abstract
{
	public function get($aData)
	{
		extract($aData);
		
		if (!isset($iPage))
		{
			$iPage = 1;
		}
		
		if ($iPage == '0')
		{
			return array();
		}
		
		if (!isset($iLimit))
		{
			$iLimit = 10;
		}
		
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
		
		if (isset($iShow) && !empty($iShow))
		{
			$values['show'] = $iShow;
			
			if( @$values['show'] == 2 ) {
				// Get an array of friend ids
				$table = Engine_Api::_()->getItemTable('user');
				$select = $viewer->membership()->getMembersSelect('user_id');
				$friends = $table->fetchAll($select);
				// Get stuff
				$ids = array();
				foreach( $friends as $friend )
				{
					$ids[] = $friend->user_id;
				}
				
				$values['users'] = $ids;
			}
		}
		
		if (isset($iUserId))
		{
			$values['user_id'] = $iUserId;
			if (!($iUserId == $viewer->getIdentity()))
				$values['visible'] = "1";
		}
		else
		{
			$values['visible'] = "1";
		}
		
		if (!isset($iDraft))
		{
			$values['draft'] = 0;
		}
		elseif ($iDraft == '0' || $iDraft == '1')
		{
			$values['draft'] = $iDraft;
		}
			
		if (!isset($iApproved))
		{
			$values['is_approved'] = 1;
		}
		elseif ($iApproved == '0' || $iApproved == '1')
		{
			$values['is_approved'] = $iApproved;
		}
		
		$values['limit'] = $iLimit;
		$values['page'] = $iPage;
		
		// Get blogs
		$paginator = Engine_Api::_()->ynblog()->getBlogsPaginator($values);
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
			return array();
		
		$entries = array();
		foreach ($paginator as $entry)
		{
			$user = $entry->getOwner();
			$sUserImageUrl = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sUserImageUrl != "")
			{
				$sUserImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sUserImageUrl);
			}
			else
			{
				$sUserImageUrl = NO_USER_ICON;
			}
			
			$creationDate = strtotime($entry -> creation_date);
			$categories = Engine_Api::_()->getDbTable("categories", "ynblog")->getCategoriesAssoc();
			$status = $entry->getStatus();
			$entries[] = array(
				'iEntryId' => $entry->blog_id,
				'sTitle' => $entry->getTitle(),
				'sDescription' => $entry->getDescription(),
				'iUserId' => $user->getIdentity(),
				'sUserName' => $user->getTitle(),
				'sUserImageUrl' => $sUserImageUrl,
				'aUserLike' => Engine_Api::_()-> getApi('like','ynmobile') -> getUserLike($entry),
				'iTotalLikes' => $entry->likes()->getLikeCount(),
				'iTotalComments' => $entry->comments()->getCommentCount(),
				'sCreationDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($creationDate),
				'iCategoryId' => $entry->category_id,
				'sCategory' => (isset($categories[$entry -> category_id])) ? ($categories[$entry -> category_id]) : "",
				'iTimestamp' => strtotime($entry -> creation_date),
				'bIsDraft' => ($entry -> draft) ? true : false,
				'bCanEdit' => (Engine_Api::_() -> authorization() -> isAllowed($entry, null, 'edit')) ? true : false,
				'bCanDelete' => (Engine_Api::_() -> authorization() -> isAllowed($entry, null, 'delete')) ? true : false,
				'bIsApproved' => ($entry -> is_approved) ? true : false,
				'sStatus' => Zend_Registry::get('Zend_Translate') -> _($status['condition']),
			);
		}
		return $entries;
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
		$aData['iDraft'] = -1;
		$aData['iApproved'] = -1;
		return $this->get($aData);
	}
	
	
	public function delete($aData)
	{
		extract($aData);
		if(!isset($iEntryId) || empty($iEntryId))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iEntryId!")
			);
		}
		if (!Engine_Api::_() -> authorization() -> isAllowed('blog', null, 'delete'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this entry!"),
			);
		}
		$entry = Engine_Api::_()->getItem("blog", $iEntryId);
		try 
		{
			$entry->delete();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate') -> _("Deleted entry successfully!")
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
		if(!isset($iEntryId) || empty($iEntryId))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iEntryId!")
			);
		}
	
		$viewer = Engine_Api::_()->user()->getViewer();
		$blog = Engine_Api::_()->getItem('blog', $iEntryId);
		
		if (is_null($blog))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This entry is not existed!")
			);
		}
	
		if (!Engine_Api::_() -> authorization() -> isAllowed('blog', null, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this entry!"),
			);
		}
		
		$auth = Engine_Api::_()->authorization()->context;
		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
	
		// Process
		$db = Engine_Db_Table::getDefaultAdapter();
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
			if (isset($iCategory) && !empty($iCategory))
			{
				$blog->category_id = $iCategory;
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
				$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, 'ynblog_new');
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
			Engine_Api::_()->getDbtable('subscriptions', 'ynblog')
			->sendNotifications($blog);
	
			$db->commit();
	
			return array(
				'error_code' => 0,
				'error_message' => '',
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
	
	public function categories($aData)
	{
		// Populate with categories
		$categories = Engine_Api::_() -> getItemTable('blog_category') -> fetchAll();
		foreach ($categories as $category)
		{
			$categoryOptions[] = array(
					'iCategoryId' => $category->category_id,
					'sName' => $category->category_name,
			);
		}
		return $categoryOptions;
	}
	
	
	public function create($aData)
	{
		if (!Engine_Api::_() -> authorization() -> isAllowed('blog', null, 'create'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to create a new blog entry!"),
			);
		}
		
		extract($aData);
		$viewer = Engine_Api::_()->user()->getViewer();
		
		// CHECKING QUOTA
    	$current_count = Engine_Api::_()->getItemTable('blog')->getCountBlog($viewer);
    	$quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'blog', 'max');
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
		
		if (!isset($iCategory) || empty($iCategory))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Entry must belong to a category!"),
			);
		}
		
		// Process
		$table = Engine_Api::_()->getItemTable('blog');
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try {
			// Create blog
			$blog = $table->createRow();
			$blog->title = $sTitle;
			$blog->category_id = $iCategory;
			$blog->owner_type = $viewer->getType();
			$blog->owner_id = $viewer->getIdentity();
			
			$sBody = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
			$filter = new Engine_Filter_Html();
			$filter->setForbiddenTags();
			$allowed_tags = array_map('trim', explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'blog', 'auth_html')));
			$filter->setAllowedTags($allowed_tags);
			$blog->body = $filter->filter($sBody);
			
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
			
			$blog_moderation = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynblog.moderation', 0 );
			if ($blog_moderation) 
			{
				$blog -> is_approved = 0;
			} 
			else 
			{
				$blog -> is_approved = 1;
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
			if( $iDraft == 0 && $blog -> is_approved == 1) {
				$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $blog, 'ynblog_new');
	
				// make sure action exists before attaching the blog to the activity
				if( $action ) 
				{
					Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action,$blog);
				}
				
				// Send notifications for subscribers
				Engine_Api::_()->getDbtable( 'subscriptions', 'ynblog' )->sendNotifications ($blog );
				$blog->add_activity = 1;
				$blog->save ();
			}
			
			// Send notifications for subscribers
			Engine_Api::_()->getDbtable('subscriptions', 'ynblog')
			->sendNotifications($blog);
	
			// Commit
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
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
		if(!isset($iEntryId) || empty($iEntryId))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iEntryId!")
			);
		}
		
		// Check permission
		$viewer = Engine_Api::_()->user()->getViewer();
		$blog = Engine_Api::_()->getItem('blog', $iEntryId);
		if( is_null($blog) ) 
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This entry is not existed!")
			);
		}
	
		$bCanPostComment = Engine_Api::_() -> authorization() -> isAllowed($blog, null, 'comment');
		$bCanView = Engine_Api::_() -> authorization() -> isAllowed($blog, null, 'view');
	
		// Prepare data 
		$blogTable = Engine_Api::_()->getItemTable('blog');
	
		if( !$blog->isOwner($viewer) ) 
		{
			$blogTable->update(array(
					'view_count' => new Zend_Db_Expr('view_count + 1'),
			), array(
					'blog_id = ?' => $blog->getIdentity(),
			));
		}
	
		// Get tags
		$blogTags = $blog->tags()->getTagMaps();
		$aTags = array();
		foreach($blogTags as $tag)
		{
			$aTags[] = $tag->getTag()->text;
		}
		
		$user = $blog->getOwner();
		$sUserImageUrl = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		if ($sUserImageUrl != "")
		{
			$sUserImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sUserImageUrl);
		}
		else
		{
			$sUserImageUrl = NO_USER_ICON;
		}
		$bCanComment = (Engine_Api::_() -> authorization() -> isAllowed($blog, null, 'comment')) ? true : false;
		$creationDate = strtotime($blog -> creation_date);
		$categories = Engine_Api::_()->getDbTable("categories", "ynblog")->getCategoriesAssoc();
		$subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'ynblog');
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
			if (1 === $auth -> isAllowed($blog, $role, 'view'))
			{
				$sViewPrivacy = $role;
			}
			if (1 === $auth -> isAllowed($blog, $role, 'comment'))
			{
				$sCommentPrivacy = $role;
			}
		}
		$status = $blog->getStatus();
		return array(
			'iEntryId' => $blog->getIdentity(),
			'sTitle' => $blog->title,
			'sDescription' => $blog->getDescription(),
			'sBody' => $blog->body,
			'iUserId' => $user->getIdentity(),
			'sUserName' => $user->getTitle(),
			'sUserImageUrl' => $sUserImageUrl,
			'aUserLike' => Engine_Api::_()-> getApi('like','ynmobile') -> getUserLike($blog),
			'iTotalLike' => $blog->likes()->getLikeCount(),
			'iTotalComment' => $blog->comments()->getCommentCount(), 
			'sTags' => implode(", ", $aTags),
			'sCreationDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($creationDate),
			'bCanComment' => $bCanComment,
			'bCanLike' => $bCanComment,
			'bCanView' => (Engine_Api::_() -> authorization() -> isAllowed($blog, null, 'view')) ? true : false,
			'bCanEdit' => (Engine_Api::_() -> authorization() -> isAllowed($blog, null, 'edit')) ? true : false,
			'bCanDelete' => (Engine_Api::_() -> authorization() -> isAllowed($blog, null, 'delete')) ? true : false,
			'bIsPublished' => ($blog -> draft) ? false : true,
			'bIsSubscribed' => ( $subscriptionTable->checkSubscription($user, $viewer) ) ? true : false,
			'iTimestamp' => strtotime($blog -> creation_date),
			'iTotalView' => $blog -> view_count,
			'iTotalComment' => $blog -> comment_count,
			'iCategory' => $blog -> category_id,
			'sCategory' => (isset($categories[$blog -> category_id])) ? ($categories[$blog -> category_id]) : "",
			'bIsDraft' => ($blog -> draft) ? true : false,
			'bIsLiked' => $blog -> likes() -> isLike($viewer),
			'sViewPrivacy' => $sViewPrivacy,
			'sCommentPrivacy' => $sCommentPrivacy,
			'bIsApproved' => ($blog -> is_approved) ? true : false,
			'sStatus' => Zend_Registry::get('Zend_Translate') -> _($status['condition']),
		);
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
		$subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'ynblog');
	
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
		$subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'ynblog');
	
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
		extract($aData);
		if(!isset($iEntryId) || empty($iEntryId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iEntryId!")
			);
		}
		
		// Check permission
		$viewer = Engine_Api::_()->user()->getViewer();
		$blog = Engine_Api::_()->getItem('blog', $iEntryId);
		
		if( is_null($blog) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This entry is not existed!")
			);
		}
		
		if (!Engine_Api::_() -> authorization() -> isAllowed($blog, null, 'view') )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You do not have any permission to set becoming member!")
			);
		}
			
		// Process
		$table = Engine_Api::_ ()->getDbtable ( 'becomes', 'ynblog' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();
		try 
		{
			// Create become_member
			$become = $table->createRow ();
			$become->blog_id = $blog->blog_id;
			$become->user_id = $viewer->getIdentity();
			$become->save();
	
			$blog->become_count = $blog->become_count + 1;
			$blog->save ();
			// Commit
			$db->commit ();
			return array(
				'error_code' => 0,
				'error_message' => "",
				'message' => Zend_Registry::get('Zend_Translate') -> _("Set becoming member successfully!"),
			);
		} 
		catch ( Exception $e ) 
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage(),
			);
		}
	}
	
	public function formadd($aData)
	{
		extract($aData);
		$privacyApi  = Engine_Api::_()->getApi('privacy','ynmobile');
		$response  =  array(
			'view_options'=> $privacyApi->allowedPrivacy('blog', 'auth_view'),
			'comment_options'=> $privacyApi->allowedPrivacy('blog', 'auth_comment'),
			'category_options'=> $this->categories($aData),
		);
		return $response;
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
		if(!isset($iEntryId) || empty($iEntryId))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iEntryId!")
			);
		}
		
		// Check permission
		$viewer = Engine_Api::_()->user()->getViewer();
		$blog = Engine_Api::_()->getItem('blog', $iEntryId);
		if( is_null($blog) ) 
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This entry is not existed!")
			);
		}
		
		$blog->draft = 0;
		if (! $blog->add_activity && ! $blog->draft && $blog->is_approved) 
		{
			$action = Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $viewer, $blog, 'ynblog_new' );
			
			if ($action) 
			{
				Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->attachActivity ( $action, $blog );
			}

			$blog->add_activity = 1;
		}
		$blog->save();
		return array(
			'error_code' => 0,
			'error_message' => '',
			'message' => Zend_Registry::get('Zend_Translate') -> _("Publish an entry successfully!")
		);
	}
	
	public function permissions($aData)
	{
		extract($aData);
		return array(
			'bCanView' => (Engine_Api::_() -> authorization() -> isAllowed('blog', null, 'view')) ? true : false,
			'bCanCreate' => (Engine_Api::_() -> authorization() -> isAllowed('blog', null, 'create')) ? true : false,
		);
	}
}