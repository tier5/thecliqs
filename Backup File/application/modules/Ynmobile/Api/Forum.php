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

class Ynmobile_Api_Forum extends Ynmobile_Service_Forum
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
		
		$categoryTable = Engine_Api::_()->getItemTable('forum_category');
		if (isset($iCategoryId) && !empty($iCategoryId))
		{
			$categorySelect = $categoryTable->select()->where('category_id = ?', $iCategoryId);
		}
		else
		{
			$categorySelect = $categoryTable->select()->limit($iLimit)->order('order ASC');
		}
		
		
		$categories = Zend_Paginator::factory($categorySelect);
		$categories->setCurrentPageNumber($iPage);
		$categories->setItemCountPerPage($iLimit);
		
		$totalPage = (integer) ceil($categories->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
			return array();
		
		$forumTable = Engine_Api::_()->getItemTable('forum');
		$forumSelect = $forumTable->select()
		->order('order ASC')
		;
		$forums = array();
		foreach( $forumTable->fetchAll() as $forum ) {
			if( Engine_Api::_()->authorization()->isAllowed($forum, null, 'view') ) {
				$order = $forum->order;
				while( isset($forums[$forum->category_id][$order]) ) {
					$order++;
				}
				$forums[$forum->category_id][$order] = $forum;
				ksort($forums[$forum->category_id]);
			}
		}
		
		$result = array();
		foreach( $categories as $category )
		{
			if( empty($forums[$category->category_id]) ) 
			{
				continue;
			}
			
			$forumCategory = array();
			foreach( $forums[$category->category_id] as $forum )
			{
				$forumCategory[] = array(
						'iForumId' => $forum->getIdentity(),
						'sForumTitle' => $forum->getTitle(),
						'sDescription' => $forum->getDescription(),
						'iTopicCount' => $forum->topic_count,
						'iPostCount' => $forum->post_count,
				);
					
			}
			
			$result[] = array(
				'iCategoryId' => $category->getIdentity(),
				'sTitle' => $category->title,
				'iForumCount' => count($forumCategory),
				'aForums' => $forumCategory,
			);
			
		}
		
		return $result;
	}
	
	public function view_category($aData)
	{
		extract($aData);
		if (!isset($iCategoryId) || empty($iCategoryId))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Forum category!")
			);
		}
		
		return $this->get($aData);
	}
	
	
	public function view_forum($aData)
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
		
		if (!isset($iForumId) || empty($iForumId))
		{
			return array(
				'error_code' => 0,
				'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Forum identity!")
			);
		}
		
		$forum = Engine_Api::_()->getItem('forum', $iForumId);
		if( is_null($forum) )
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This forum is not existed!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !Engine_Api::_() -> authorization() -> isAllowed($forum, $viewer, 'view') ) {
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You do not have any permission to do view this forum!")
			);
		}
		
		// Increment view count
		$forum->view_count = new Zend_Db_Expr('view_count + 1');
		$forum->save();
		
		$canPost = $forum->authorization()->isAllowed(null, 'topic.create');
		
		// Get params
		if ( isset($sSort) )
		{
			switch($sSort)
			{
				case 'popular':
					$order = 'view_count';
					break;
				case 'recent':
				default:
					$order = 'modified_date';
					break;
			}
		}
		else 
		{
			$order = 'modified_date';	
		}
		
		// Make paginator
		$table = Engine_Api::_()->getItemTable('forum_topic');
		$select = $table->select()
		->where('forum_id = ?', $forum->getIdentity())
		->order('sticky DESC')
		->order($order . ' DESC');
		;
		
		if (isset($sSearch) || !empty($sSearch))
		{
			$select->where('title LIKE ? OR description LIKE ?', '%' . $sSearch . '%');
		}
		
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($iPage);
		$paginator->setItemCountPerPage($iLimit);
		
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
			return array();
		
		$result = array();
		$forumTitle = $forum->getTitle();
		foreach( $paginator as $i => $topic )
		{
			$last_post = $topic->getLastCreatedPost();
			if( $last_post ) 
			{
				$last_user = Engine_Api::_()->user()->getUser($last_post->user_id);
			} 
			else 
			{
				$last_user = Engine_Api::_()->user()->getUser($topic->user_id);
			}
			
			$modifiedDate = strtotime($topic -> modified_date);
			$lastPostedDate = strtotime($last_post->creation_date);
			
			$result[] = array(
					'iTopicId' => $topic->getIdentity(),
					'sTitle' => $topic->getTitle(),
					'iViewCount' => $topic->view_count,
					'iReplyCount' => $topic->post_count-1,
					'iLastUserId' => $last_user->getIdentity(),
					'sLastUserName' => $last_user->getTitle(),
					'sModifiedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($modifiedDate),
					'sLastPostedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($lastPostedDate),
					'iForumId' => $iForumId,
					'sForumTitle' => $forumTitle,
					'bSticky' => $topic->sticky, 
			);
		}
		return $result;		
	}
	
	public function view_topic($aData)
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
		
		if (!isset($iTopicId) || empty($iTopicId))
		{
			return array(
				'error_code' => 0,
				'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Topic identity!")
			);
		}
		
		$topic = Engine_Api::_()->getItem('forum_topic', $iTopicId);
		if( is_null($topic) )
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This topic is not existed!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$forum = $topic->getParent();
		
		if ( !Engine_Api::_()->authorization()->isAllowed($topic, $viewer, 'view') )
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You do not have any permission to do view this topic!")
			);
		}
		
		// Views
		if( !$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id ) {
			$topic->view_count = new Zend_Db_Expr('view_count + 1');
			$topic->save();
		}
		
		// CODE HERE is moved to view_topic funcition
		
		// Keep track of topic user views to show them which ones have new posts
		if( $viewer->getIdentity() ) {
			$topic->registerView($viewer);
		}
		
		$table = Engine_Api::_()->getItemTable('forum_post');
		$select = $topic->getChildrenSelect('forum_post', array('order'=>'post_id ASC'));
		$paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage($iLimit);
		
		// set up variables for pages
		$post = null;
		if (isset($iPostId) && !empty($iPostId))
		{
			$post = Engine_Api::_()->getItem('forum_post', $iPostId);
		}
		
		// if there is a post_id
		if( $iPostId && $post && !$iPage )
		{
			$icpp = $paginator->getItemCountPerPage();
			$post_page = ceil(($post->getPostIndex() + 1) / $icpp);
			$paginator->setCurrentPageNumber($post_page);
		}
		
		// Use specified page
		else if( $iPage )
		{
			$paginator->setCurrentPageNumber($iPage);
		}
		
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
			return array();
		
		// Auth for topic
		$canPost = false;
		if( !$topic->closed && Engine_Api::_()->authorization()->isAllowed($forum, null, 'post.create') ) {
			$canPost = true;
		}
		if( Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.edit') ) {
			$canEdit = true;
		}
		
		// Auth for posts
		$canEdit_Post = false;
		$canDelete_Post = false;
		if($viewer->getIdentity()){
			$canEdit_Post = Engine_Api::_()->authorization()->isAllowed('forum', $viewer->level_id, 'post.edit');
			$canDelete_Post = Engine_Api::_()->authorization()->isAllowed('forum', $viewer->level_id, 'post.delete');
		}
		
		
		$posts = array();
		foreach( $paginator as $post )
		{
			$user = $post->getOwner();
			$body = $post->body;
			$doNl2br = false;
			if( strip_tags($body) == $body ) {
				$body = nl2br($body);
			}
			
			$postPhoto = $post->getPhotoUrl();
			if ($postPhoto)
			{
				$postPhoto = Engine_Api::_()->ynmobile()->finalizeUrl($postPhoto);
			}
			
			$userPhoto = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($userPhoto != "")
			{
				$userPhoto = Engine_Api::_() -> ynmobile() -> finalizeUrl($userPhoto);
			}
			else
			{
				$userPhoto = NO_USER_ICON;
			}
			
			$canEditPost = false; $canDeletePost = false;
			if( $canEdit ){
				$canEditPost = true;
				$canDeletePost = true;
			}
			elseif( $post->user_id != 0 && $post->isOwner($viewer) && !$topic->closed )
			{
				if( $canEdit_Post )
				{
					$canEditPost = true;
				}
				if( $canDelete_Post)
				{
					$canDeletePost = true;
				}
			}
            
            $arrImages = array();
            if ($post->file_id)
            {
                $arrImages[] = Engine_Api::_()->ynmobile()->finalizeUrl($post -> getPhotoUrl());
            }
			
			$posts[] = array(
				'iTopicId' => $topic->getIdentity(),
				'iPostId' => $post->getIdentity(),
				'iUserId' => $post->user_id,
				'sUserName' => $user->getTitle(),
				'sUserPhoto' => $userPhoto,
				'sSignature' =>  $post->getSignature(),
				'sCreationDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->creation_date)),
				'sModifiedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->modified_date)),
				'sBody' => $body,
				'sPhotoUrl' => $postPhoto,
				'bCanPost' => $canPost,
				'bCanEditPost' => $canEditPost,
				'bCanDeletePost' => $canDeletePost,
				'aImageUrls' => $arrImages
			);
		}
		
		return $posts;
	}
	
	
	public function post_reply($aData)
	{
		extract($aData);
		if (!isset($iTopicId) || empty($iTopicId))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Topic identity!")
			);
		}
		
		$topic = Engine_Api::_()->getItem('forum_topic', $iTopicId);
		if( is_null($topic) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This topic is not existed!")
			);
		}
		
		if (!isset($sBody) || empty($sBody))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Post content can not be empty!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$forum = $topic->getParent();
		
		if( !Engine_Api::_()->authorization()->isAllowed($forum, $viewer, 'post.create') ) {
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You do not have any permission to post reply!")
			);
		}
		if( $topic->closed ) {
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This topic is closed!")
			);
		}
	
		$allowHtml = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_html', 0);
		$allowBbcode = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_bbcode', 0);
		
		if( isset($iQuoteId) && !empty($iQuoteId) ) {
			$quote = Engine_Api::_()->getItem('forum_post', $iQuoteId);
			if($quote->user_id == 0) {
				$owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
			} else {
				$owner_name = $quote->getOwner()->__toString();
			}
			
			if( $allowHtml || !$allowBbcode ) {
				$sBody = "<blockquote><strong>" . "{$owner_name} said:" . "</strong><br />" . $quote->body . "</blockquote><br />" . $sBody;
			} else {
				$sBody = "[blockquote][b]" . strip_tags("{$owner_name} said:") . "[/b]\r\n" . htmlspecialchars_decode($quote->body, ENT_COMPAT) . "[/blockquote]\r\n" . $sBody;
			}
		}
	
		if( !$allowHtml ) {
			$filter = new Engine_Filter_HtmlSpecialChars();
		} else {
			$filter = new Engine_Filter_Html();
			$filter->setForbiddenTags();
			$allowed_tags = array_map('trim', explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'forum', 'commentHtml')));
			$filter->setAllowedTags($allowed_tags);
		}
		
		$sBody = $filter->filter($sBody);
		if ($sBody == '')
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Post content is invalid!")
			);
		}
		
		// Process
		$values['body'] = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
		$values['user_id'] = $viewer->getIdentity();
		$values['topic_id'] = $topic->getIdentity();
		$values['forum_id'] = $forum->getIdentity();
		
		$topicTable = Engine_Api::_()->getDbtable('topics', 'forum');
		$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
		$postTable = Engine_Api::_()->getDbtable('posts', 'forum');
		$userTable = Engine_Api::_()->getItemTable('user');
		$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
		$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
	
		$topicOwner = $topic->getOwner();
		$isOwnTopic = $viewer->isSelf($topicOwner);
	
		$watch = (bool) $iWatch;
		$isWatching = $topicWatchesTable
		->select()
		->from($topicWatchesTable->info('name'), 'watch')
		->where('resource_id = ?', $forum->getIdentity())
		->where('topic_id = ?', $topic->getIdentity())
		->where('user_id = ?', $viewer->getIdentity())
		->limit(1)
		->query()
		->fetchColumn(0)
		;
	
		$db = $postTable->getAdapter();
		$db->beginTransaction();
	
		try 
		{
			$post = $postTable->createRow();
			$post->setFromArray($values);
			$post->save();
	
			if( !empty($_FILES['image']) ) 
			{
				try 
				{
					$post = $this->setPostPhoto($post, $_FILES['image']);
				} 
				catch( Engine_Image_Adapter_Exception $e ) 
				{
					
				}
			}
	
			// Watch
			if( false === $isWatching ) 
			{
				$topicWatchesTable->insert(array(
						'resource_id' => $forum->getIdentity(),
						'topic_id' => $topic->getIdentity(),
						'user_id' => $viewer->getIdentity(),
						'watch' => (bool) $watch,
				));
			} 
			else if( $watch != $isWatching ) 
			{
				$topicWatchesTable->update(array(
						'watch' => (bool) $watch,
				), array(
						'resource_id = ?' => $forum->getIdentity(),
						'topic_id = ?' => $topic->getIdentity(),
						'user_id = ?' => $viewer->getIdentity(),
				));
			}
	
			// Activity
			$action = $activityApi->addActivity($viewer, $topic, 'forum_topic_reply');
			if( $action ) 
			{
				$action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
			}
	
			// Notifications
			$notifyUserIds = $topicWatchesTable->select()
			->from($topicWatchesTable->info('name'), 'user_id')
			->where('resource_id = ?', $forum->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('watch = ?', 1)
			->query()
			->fetchAll(Zend_Db::FETCH_COLUMN)
			;
	
			$view = Zend_Registry::get("Zend_View");
			
			foreach( $userTable->find($notifyUserIds) as $notifyUser ) {
				// Don't notify self
				if( $notifyUser->isSelf($viewer) ) {
					continue;
				}
				if( $notifyUser->isSelf($topicOwner) ) {
					$type = 'forum_topic_response';
				} else {
					$type = 'forum_topic_reply';
				}
				$notifyApi->addNotification($notifyUser, $viewer, $topic, $type, array(
						'message' => $view->BBCode($post->body), // @todo make sure this works
				));
			}
	
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get("Zend_Translate")->_("Posted reply successfully.!"),
				'iPostId' => $post->getIdentity(),
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
	
	public function post_delete($aData)
	{
		extract($aData);
		if (!isset($iPostId) || empty($iPostId))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing post identity!")
			);
		}
		
		$post = Engine_Api::_()->getItem('forum_post', $iPostId);
		if( is_null($post) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This post is not existed!")
			);
		}
		
	    $viewer = Engine_Api::_()->user()->getViewer();
	    $topic = $post->getParent();
	    $forum = $topic->getParent();
	    
	    if( !Engine_Api::_()->authorization()->isAllowed($post, $viewer, 'delete') &&
	        !Engine_Api::_()->authorization()->isAllowed($forum, $viewer, 'topic.delete') ) 
	    {
	      return array(
	      		'error_code' => 1,
	      		'error_message' => Zend_Registry::get('Zend_Translate') -> _("You can not delete this post!")
	      );
	    }
	    
	    // Process
	    $table = Engine_Api::_()->getItemTable('forum_post');
	    $db = $table->getAdapter();
	    $db->beginTransaction();
	
	    try
	    {
		      $post->delete();
		      $db->commit();
		      return array(
		      		'error_code' => 0,
		      		'error_message' => '',
		      		'message' => Zend_Registry::get('Zend_Translate') -> _("Deleted post successfully!")
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

	
	public function post_edit($aData)
	{
		extract($aData);
		if (!isset($iPostId) || empty($iPostId))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing post identity!")
			);
		}
		
		$post = Engine_Api::_()->getItem('forum_post', $iPostId);
		if( is_null($post) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This post is not existed!")
			);
		}
		
		if (!isset($sBody) || empty($sBody))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Post content can not be empty!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$topic = $post->getParent();
		$forum = $topic->getParent();
		
		if( !Engine_Api::_()->authorization()->isAllowed($post, $viewer, 'edit') &&
				!Engine_Api::_()->authorization()->isAllowed($forum, $viewer, 'topic.edit') )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You can not edit this post!")
			);
		}
	
		$allowHtml = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_html', 0);
		$allowBbcode = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_bbcode', 0);
	
		// Process
		$table = Engine_Api::_()->getItemTable('forum_post');
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$post->body = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
			$post->edit_id = $viewer->getIdentity();
	
			//DELETE photo here.
			if( !empty($iDeletePhoto) && $iDeletePhoto ) {
				$post->deletePhoto();
			}
	
			if( !empty($_FILES['image']) ) {
				$post = $this->setPostPhoto($post, $_FILES['image']);
			}
	
			$post->save();
			$db->commit();
	
			return array(
		      		'error_code' => 0,
		      		'error_message' => '',
		      		'message' => Zend_Registry::get('Zend_Translate') -> _("Edited post successfully!")
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
	
	
	public function topic_watch($aData)
	{
		extract($aData);
		if (!isset($iTopicId) || empty($iTopicId))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Topic identity!")
			);
		}
		
		$topic = Engine_Api::_()->getItem('forum_topic', $iTopicId);
		if( is_null($topic) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This topic is not existed!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$forum = $topic->getParent();
		
		
		if( !Engine_Api::_()->authorization()->isAllowed($forum, $viewer, 'view') ) 
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You can not view this forum!")
			);
		}
	
		$watch = (isset($iWatch) && $iWatch) ? true : false;
	
		$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
		$db = $topicWatchesTable->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $forum->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0)
			;
	
			if( false === $isWatching ) 
			{
				$topicWatchesTable->insert(array(
						'resource_id' => $forum->getIdentity(),
						'topic_id' => $topic->getIdentity(),
						'user_id' => $viewer->getIdentity(),
						'watch' => (bool) $watch,
				));
			} 
			else if( $watch != $isWatching ) 
			{
				$topicWatchesTable->update(array(
						'watch' => (bool) $watch,
				), array(
						'resource_id = ?' => $forum->getIdentity(),
						'topic_id = ?' => $topic->getIdentity(),
						'user_id = ?' => $viewer->getIdentity(),
				));
			}
	
			$db->commit();
			$message = ($watch) 
				? Zend_Registry::get('Zend_Translate') -> _("Set watching successfully!")
				: Zend_Registry::get('Zend_Translate') -> _("Stop watching successfully!");
			
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => $message
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
	
	public function topic_create($aData)
	{
		extract($aData);	
		if (!isset($iForumId) || empty($iForumId))
		{
			return array(
				'error_code' => 0,
				'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Forum identity!")
			);
		}
		
		$forum = Engine_Api::_()->getItem('forum', $iForumId);
		if( is_null($forum) )
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("This forum is not existed!")
			);
		}
	
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if ( !Engine_Api::_()->authorization()->isAllowed($forum, $viewer, 'topic.create') ) 
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You can not create the forum topic!")
			);
		}
	
		if (empty($sTitle))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Topic title can not be empty!")
			);
		}
		
		if (empty($sBody))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Topic body can not be empty!")
			);
		}
	
		if (!isset($iWatch))
			$iWatch = '1';
		
		// Process
		$values['title'] = $sTitle;
		$values['body'] = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
		$values['watch'] = ($iWatch == '1') ? 1 : 0;
		$values['user_id'] = $viewer->getIdentity();
		$values['forum_id'] = $forum->getIdentity();
	
		$topicTable = Engine_Api::_()->getDbtable('topics', 'forum');
		$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
		$postTable = Engine_Api::_()->getDbtable('posts', 'forum');
	
		$db = $topicTable->getAdapter();
		$db->beginTransaction();
	
		try {
	
			// Create topic
			$topic = $topicTable->createRow();
			$topic->setFromArray($values);
			$topic->title = $values['title'];
			$topic->description = $values['body'];
			$topic->save();
	
			// Create post
			$values['topic_id'] = $topic->getIdentity();
	
			$post = $postTable->createRow();
			$post->setFromArray($values);
			$post->save();
	
			if( !empty($_FILES['image']) ) {
				$post = $this->setPostPhoto($post, $_FILES['image']);
			}
	
			$auth = Engine_Api::_()->authorization()->context;
			$auth->setAllowed($topic, 'registered', 'create', true);
	
			// Create topic watch
			$topicWatchesTable->insert(array(
					'resource_id' => $forum->getIdentity(),
					'topic_id' => $topic->getIdentity(),
					'user_id' => $viewer->getIdentity(),
					'watch' => (bool) $values['watch'],
			));
	
			// Add activity
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$action = $activityApi->addActivity($viewer, $topic, 'forum_topic_create');
			if( $action ) {
				$action->attach($topic);
			}
	
			$db->commit();
			
			return array(
					'error_code' => 0,
					'error_message' => '',
					'iTopicId' => $topic->getIdentity(),
					'message' => Zend_Registry::get('Zend_Translate') -> _("Created topic successfully!")
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
	
	public function info($aData)
	{
		extract($aData);
		if (!isset($iForumId) || empty($iForumId))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Forum identity!")
			);
		}
		$forum = Engine_Api::_()->getItem('forum', $iForumId);
		if( is_null($forum) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This forum is not existed!")
			);
		}
		
		return array(
				'iForumId' => $forum -> getIdentity(),
				'sForumTitle' => $forum -> getTitle(),
				'sForumDescription' => $forum -> getDescription(),
				'iViewCount' => $forum -> view_count,
				'iTopicCount' => $forum -> topic_count,
				'iPostCount' => $forum -> post_count,
		);
	}
	
	public function topic_delete($aData) 
    {
    	extract($aData);
		$table = Engine_Api::_()->getItemTable('forum_topic');
		$topic = $table->find($iTopicId)->current();
		
		if(!$topic){
			return array(
				'error_code'=>1,
				'error_message'=>'Invalid topic',
			);
		}
		
		$forum = $topic->getParent();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		//check permission delete topic
        if(!Engine_Api::_()->authorization()->isAllowed($forum,null,'topic.delete'))
		{
			return array(
				'error_code'=>1,
				'error_message'=>'You dont\'t have permission to delete this topic',
			);
		}
        
        // Process
        
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $topic->delete();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return array(
			'error_code'=>'',
			'message'=>'',
			'iForumId'=>$forum->getIdentity(),
			'iTopicId'=>$topic->getIdentity(),
		);
    }
	
	public function topic_info($aData)
	{
		extract($aData);
		if (!isset($iTopicId) || empty($iTopicId))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Topic identity!")
			);
		}
		
		$topic = Engine_Api::_()->getItem('forum_topic', $iTopicId);
		if( is_null($topic) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This topic is not existed!")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$forum = $topic->getParent();
		
		// Check watching
		$isWatching = null;
		if( $viewer->getIdentity() ) {
			$topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'forum');
			$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $forum->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0)
			;
			if( false === $isWatching ) {
				$isWatching = null;
			} else {
				$isWatching = (bool) $isWatching;
			}
		}
		
		// Auth for topic
		$canPost = false;
		$canEdit = false;
		$canDelete = false;
		if( !$topic->closed && Engine_Api::_()->authorization()->isAllowed($forum, null, 'post.create') ) {
			$canPost = true;
		}
		if( Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.edit') ) {
			$canEdit = true;
		}
		if( Engine_Api::_()->authorization()->isAllowed($forum, null, 'topic.delete') ) {
			$canDelete = true;
		}
		
		return array(
				'iTopicId' => $iTopicId,
				'sTopicTitle' => $topic->getTitle(),
				'iForumId' => $forum->getIdentity(),
				'sForumTitle' => $forum->getTitle(),
				'bCanPost' => $canPost,
				'bCanEdit' => $canEdit,
				'bCanDelete' => $canDelete,
				'bIsWatching' => $isWatching,
				'bIsSticky' => $topic->sticky,
				'bIsClosed' => $topic->closed,
		);
	}
	
	protected  function setPostPhoto($post, $photo)
	{
		if( is_array($photo) && !empty($photo['tmp_name']) ) {
			$file = $photo['tmp_name'];
			$fileName = $photo['name'];
		} else if( is_string($photo) && file_exists($photo) ) {
			$file = $photo;
			$fileName = $photo;
		} else {
			throw new Event_Model_Exception('invalid argument passed to setPhoto');
		}
	
		$name = basename($file);
		
		$extension = ltrim(strrchr($fileName, '.'), '.');
		$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
		$name = $base . "." . $extension;
		
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
				'parent_id' => $post->getIdentity(),
				'parent_type'=>'forum_post'
		);
	
		// Save
		$storage = Engine_Api::_()->storage();
        
        $angle = 0;
        if (function_exists("exif_read_data"))
        {
            $exif = exif_read_data($file);
            if (!empty($exif['Orientation']))
            {
                switch($exif['Orientation'])
                {
                    case 8 :
                        $angle = 90;
                        break;
                    case 3 :
                        $angle = 180;
                        break;
                    case 6 :
                        $angle = -90;
                        break;
                }
            }
        }
	
		// Resize image (main)
		$image = Engine_Image::factory();
		$image->open($file);
        
        if ($angle != 0)
            $image -> rotate($angle);
        
		$image->resize(2000, 2000)
		->write($path.'/m_'.$name)
		->destroy();
	
		// Store
		$iMain = $storage->create($path.'/m_'.$name, $params);
	
		// Remove temp files
		@unlink($path.'/m_'.$name);
	
		// Update row
		$post->modified_date = date('Y-m-d H:i:s');
		$post->file_id = $iMain->getIdentity();
		$post->save();
	
		return $post;
	}
	
	
	public function post_info($aData)
	{
		extract($aData);
		if (!isset($iPostId) || empty($iPostId))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing post identity!")
			);
		}
		
		$post = Engine_Api::_()->getItem('forum_post', $iPostId);
		if( is_null($post) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This post is not existed!")
			);
		}

		$user = $post->getOwner();
		$body = $post->body;
		$doNl2br = false;
		if( strip_tags($body) == $body ) {
			$body = nl2br($body);
		}
			
		$postPhoto = $post->getPhotoUrl();
		if ($postPhoto)
		{
			$postPhoto = Engine_Api::_()->ynmobile()->finalizeUrl($postPhoto);
		}
		else
		{
			$postPhoto = "";
		}
			
		$userPhoto = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
		if ($userPhoto != "")
		{
			$userPhoto = Engine_Api::_() -> ynmobile() -> finalizeUrl($userPhoto);
		}
		else
		{
			$userPhoto = NO_USER_ICON;
		}
		
		return array(
				'iPostId' => $post->getIdentity(),
				'iUserId' => $post->user_id,
				'sUserName' => $user->getTitle(),
				'sUserPhoto' => $userPhoto,
				'sSignature' =>  $post->getSignature(),
				'sCreationDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->creation_date)),
				'sModifiedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->modified_date)),
				'sBody' => $body,
				'sPhotoUrl' => $postPhoto,
		);
	}
	
	
}