<?php
class Ynbusinesspages_MemberController extends Core_Controller_Action_Standard
{

	public function init()
	{
		if (0 !== ($business_id = (int)$this -> _getParam('business_id')) && null !== ($business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id)))
		{
			Engine_Api::_() -> core() -> setSubject($business);
		}
		$this->_helper->requireUser();
    	$this->_helper->requireSubject('ynbusinesspages_business');
	}

	public function acceptAction()
	{
		// Check auth
		if( !$this->_helper->requireUser()->isValid() ) return;
		if( !$this->_helper->requireSubject()->isValid() ) return;

		// Make form
		$this->view->form = $form = new Ynbusinesspages_Form_Member_Accept();

		// Process form
		if( !$this->getRequest()->isPost() )
		{
			$this->view->status = false;
			$this->view->error = true;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
			return;
		}

		if( !$form->isValid($this->getRequest()->getPost()) )
		{
			$this->view->status = false;
			$this->view->error = true;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
			return;
		}

		// Process form
	    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
	    {
		    $viewer = Engine_Api::_()->user()->getViewer();
			$subject = Engine_Api::_()->core()->getSubject();
			$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
			$db->beginTransaction();
	
			try
			{
				$membership_status = $subject->membership()->getRow($viewer)->active;
				$subject->membership()
				->setUserApproved($viewer);
				
				// Set the request as handled
				$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
				$viewer, $subject, 'ynbusinesspages_invite');
				if( $notification )
				{
					$notification->mitigated = true;
					$notification->save();
				}
	
				// Add activity
				if (!$membership_status){
					$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
					$action = $activityApi->addActivity($viewer, $subject, 'ynbusinesspages_join');
				}
				$db->commit();
			}
			catch( Exception $e )
			{
				$db->rollBack();
				throw $e;
			}
	
			$this->view->status = true;
			$this->view->error = false;
	
			$message = Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the business %s');
			$message = sprintf($message, $subject->__toString());
			$this->view->message = $message;
	
			if( $this->_helper->contextSwitch->getCurrentContext() == "smoothbox" ) {
				return $this->_forward('success', 'utility', 'core', array(
			        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Business invite accepted')),
			        'layout' => 'default-simple',
			        'parentRefresh' => true,
				));
			}
	    }
	}
	
	public function approveAction()
	{
		// Check auth
		if( !$this->_helper->requireUser()->isValid() ) return;
		if( !$this->_helper->requireSubject()->isValid() ) return;

		// Get user
		if( 0 === ($user_id = (int) $this->_getParam('user_id')) ||
		null === ($user = Engine_Api::_()->getItem('user', $user_id)) )
		{
			return $this->_helper->requireSubject->forward();
		}

		// Make form
		$this->view->form = $form = new Ynbusinesspages_Form_Member_Approve();

		// Process form
		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
		{
			$viewer = Engine_Api::_()->user()->getViewer();
			$subject = Engine_Api::_()->core()->getSubject();
			$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
			$db->beginTransaction();

			try
			{
				$subject->membership()->setResourceApproved($user);
				$memberList = $subject -> getMemberList();
				$memberList -> add($user);
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, 'ynbusinesspages_accepted');
				$db->commit();
			}
			catch( Exception $e )
			{
				$db->rollBack();
				throw $e;
			}

			return $this->_forward('success', 'utility', 'core', array(
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Business request approved')),
		        'layout' => 'default-simple',
		        'parentRefresh' => true,
			));
		}
	}
	
	public function suggestAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$business_id = $this -> _getParam('business_id');
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id);
		if (!$business)
		{
			$data = null;
		}
		else
		{
			$table_user = Engine_Api::_() -> getDbtable('users', 'user');
			$table_user_name = $table_user -> info('name');
			$data = array();
			
			$select = $table_user -> select();
			
			if (0 < ($limit = (int)$this -> _getParam('limit', 10)))
			{
				$select -> limit($limit);
			}
			
			if (null !== ($text = $this -> _getParam('search', $this -> _getParam('value'))))
			{
				$select -> where('displayname LIKE ?', '%' . $text . '%');
			}
			
			if(!$business -> is_claimed)
			{
				$select -> where("$table_user_name.user_id <> ?", $business -> user_id);
			}
			foreach ($select->getTable()->fetchAll($select) as $friend)
			{
				$data[] = array(
					'type' => 'user',
					'id' => $friend -> getIdentity(),
					'guid' => $friend -> getGuid(),
					'label' => $friend -> getTitle(),
					'photo' => $this -> view -> itemPhoto($friend, 'thumb.icon'),
					'url' => $friend -> getHref(),
				);
			}
		}
		return $this -> _helper -> json($data);
	}
	
	public function requestAction()
	{
		// Check resource approval
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();

		// Check auth
		if( !$this->_helper->requireUser()->isValid() ) return;
		if( !$this->_helper->requireSubject()->isValid() ) return;
		if( !$this->_helper->requireAuth()->setAuthParams($subject, $viewer, 'view')->isValid() ) return;

		// Make form
		$this->view->form = $form = new Ynbusinesspages_Form_Member_Request();

		// Process form
		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
		{
			$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
			$db->beginTransaction();

			try
			{
				$subject->membership() -> addMember($viewer)->setUserApproved($viewer);
				$memberList = $subject -> getMemberList();
				$row = $subject->membership()->getRow($viewer);
				$row -> list_id = $memberList->getIdentity();
				$row -> save();
				
				$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
				$notifyApi->addNotification($subject->getOwner(), $viewer, $subject, 'ynbusinesspages_approve');

				$db->commit();
			}
			catch( Exception $e )
			{
				$db->rollBack();
				throw $e;
			}

			return $this->_forward('success', 'utility', 'core', array(
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your invite request has been sent.')),
		        'layout' => 'default-simple',
		        'parentRefresh' => true,
			));
		}
	}

	public function cancelAction()
	{
		// Check auth
		if( !$this->_helper->requireUser()->isValid() ) return;
		if( !$this->_helper->requireSubject()->isValid() ) return;

		// Make form
		$this->view->form = $form = new Ynbusinesspages_Form_Member_Cancel();

		// Process form
		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
			$user_id = $this->_getParam('user_id');
			$viewer = Engine_Api::_()->user()->getViewer();
			$subject = Engine_Api::_()->core()->getSubject();
			if( !$subject->authorization()->isAllowed($viewer, 'invite') &&
			$user_id != $viewer->getIdentity() &&
			$user_id ) {
				return;
			}

			if( $user_id ) {
				$user = Engine_Api::_()->getItem('user', $user_id);
				if( !$user ) {
					return;
				}
			} else {
				$user = $viewer;
			}

			$subject = Engine_Api::_()->core()->getSubject();
			$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
			$db->beginTransaction();
			try
			{
				$subject->membership()->removeMember($user);
				$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
				$subject->getOwner(), $subject, 'ynbusinesspages_approve');
				if( $notification ) {
					$notification->delete();
				}
				$db->commit();
			}
			catch( Exception $e )
			{
				$db->rollBack();
				throw $e;
			}

			return $this->_forward('success', 'utility', 'core', array(
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your invite request has been cancelled.')),
		        'layout' => 'default-simple',
		        'parentRefresh' => true,
			));
		}
	}
	
	
	public function joinAction()
  	{
	    // Check resource approval
	    $viewer = Engine_Api::_()->user()->getViewer();
	    $subject = Engine_Api::_()->core()->getSubject();
		
		$package = $subject -> getPackage();
		if(!$package -> getIdentity()) 
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		if(!$package -> allow_user_join_business)
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
	    // Check auth
	    if( !$this->_helper->requireUser()->isValid() ) return;
	    if( !$this->_helper->requireSubject()->isValid() ) return;
	    if( !$this->_helper->requireAuth()->setAuthParams($subject, $viewer, 'view')->isValid() ) return;
	
	    if( $subject->membership()->isResourceApprovalRequired() ) {
	      $row = $subject->membership()->getReceiver()
	        ->select()
	        ->where('resource_id = ?', $subject->getIdentity())
	        ->where('user_id = ?', $viewer->getIdentity())
	        ->query()
	        ->fetch(Zend_Db::FETCH_ASSOC, 0);
	        ;
	      if (empty($row)) {
	        // has not yet requested an invite
	        return $this->_helper->redirector->gotoRoute(array('action' => 'request', 'format' => 'smoothbox'));
	      } elseif ($row['user_approved'] && !$row['resource_approved']) {
	        // has requested an invite; show cancel invite page
	        return $this->_helper->redirector->gotoRoute(array('action' => 'cancel', 'format' => 'smoothbox'));
	      }
	    }
	
	    // Make form
	    $this->view->form = $form = new Ynbusinesspages_Form_Member_Join();
	
	    // Process form
	    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
	    {
	      $viewer = Engine_Api::_()->user()->getViewer();
	      $subject = Engine_Api::_()->core()->getSubject();
	      $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
	      $db->beginTransaction();
	
	      try
	      {
	        $membership_status = $subject->membership()->getRow($viewer)->active;
	        $subject->membership()
	          ->addMember($viewer)
	          ->setUserApproved($viewer)
	          ;
			$memberList = $subject -> getMemberList();
			$memberList -> add($viewer);
			$row = $subject->membership()->getRow($viewer);
			$row -> list_id = $memberList->getIdentity();
			$row -> save();
	
	        // Add activity if membership status was not valid from before
	        if (!$membership_status)
	        {
	        	$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
	        	$action = $activityApi->addActivity($viewer, $subject, 'ynbusinesspages_join');
	        }
	        $user = $subject -> getOwner();
	        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, 'ynbusinesspages_joined');
	        $db->commit();
	      }
	      catch( Exception $e )
	      {
	        $db->rollBack();
	        throw $e;
	      }
	
	      return $this->_forward('success', 'utility', 'core', array(
	        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Business joined')),
	        'layout' => 'default-simple',
	        'parentRefresh' => true,
	      ));
	    }
  	}
  	
  	public function leaveAction()
  	{
	    // Check auth
	    if( !$this->_helper->requireUser()->isValid() ) return;
	    if( !$this->_helper->requireSubject()->isValid() ) return;
	    $viewer = Engine_Api::_()->user()->getViewer();
	    $subject = Engine_Api::_()->core()->getSubject();
	
	    if( $subject->isOwner($viewer) ) return;
	
	    // Make form
	    $this->view->form = $form = new Ynbusinesspages_Form_Member_Leave();
	
	    // Process form
	    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
	    {
	    	$listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
	    	$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
	    	$db->beginTransaction();
	
	    	try
	    	{
	    		$subject->membership()->removeMember($viewer);
	    		$list = $listTbl -> getListByUser($viewer, $subject);
	    		$list -> remove($viewer);
	    		$db->commit();
	    	}
	    	catch( Exception $e )
	    	{
	    		$db->rollBack();
	    		throw $e;
	    	}
	
	    	return $this->_forward('success', 'utility', 'core', array(
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Business left')),
		        'layout' => 'default-simple',
		        'parentRefresh' => true,
	    	));
	    }
  	}
  	 
  	public function removeAction()
  	{
	    // Check auth
	    if( !$this->_helper->requireUser()->isValid() ) return;
	    if( !$this->_helper->requireSubject()->isValid() ) return;
	
	    // Get user
	    if( 0 === ($user_id = (int) $this->_getParam('user_id')) ||
	    null === ($user = Engine_Api::_()->getItem('user', $user_id)) )
	    {
	    	return $this->_helper->requireSubject->forward();
	    }
	
	    $business = Engine_Api::_()->core()->getSubject();
	
	    if( !$business->membership()->isMember($user) ) {
	    	throw new Exception('Cannot remove a non-member');
	    }
	
	    // Make form
	    $this->view->form = $form = new Ynbusinesspages_Form_Member_Remove();
	
	    // Process form
	    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
	    {
	    	$listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
	    	$db = $business->membership()->getReceiver()->getTable()->getAdapter();
	    	$db->beginTransaction();
	
	    	try
	    	{
	    		// Remove membership
	    		$business->membership()->removeMember($user);
				$list = $listTbl -> getListByUser($user, $business);
	    		$list -> remove($user);
	    		
	    		// Remove the notification?
	    		$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
	    		$business->getOwner(), $business, 'ynbusinesspages_approve');
	    		if( $notification ) {
	    			$notification->delete();
	    		}
	
	    		$db->commit();
	    	}
	    	catch( Exception $e )
	    	{
	    		$db->rollBack();
	    		throw $e;
	    	}
	
	    	return $this->_forward('success', 'utility', 'core', array(
		        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Business member removed.')),
		        'layout' => 'default-simple',
		        'parentRefresh' => true,
	    	));
	    }
  	}
  	 
  	public function inviteAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('ynbusinesspages_business') -> isValid())
			return;
		// Prepare data
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> business_id = $business -> getIdentity();

		$package = $business -> getPackage();
		if(!$package -> getIdentity()) 
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		if(!$package -> allow_user_invite_friend)
		{
			return $this -> _helper -> requireAuth() -> forward();
		}

		$this -> view -> form = $form = new Ynbusinesspages_Form_Member_Invite();
//
		// Not posting
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$values = $this -> getRequest() -> getPost();

		if (empty($values['users']) && empty($values['recipients']))
		{
			$form->addError('You have not entered any users or emails to invite joining business !');
			return;
		}

		// Process
		$table = $business -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$usersIds = $values['users'];
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			if ($form -> getElement('message'))
			{
				$message = $form -> getElement('message') -> getValue();
			}

			if (!empty($usersIds))
			{
				$friendTbl = Engine_Api::_() -> getItemTable('user');
				$select = $friendTbl -> select() -> where('user_id IN (?)', $usersIds) -> order('displayname');
				$friends = $friendTbl -> fetchAll($select);
				foreach ($friends as $friend)
				{
					$business -> membership() -> addMember($friend) -> setResourceApproved($friend);
					$memberList = $business -> getMemberList();
					$row = $business->membership()->getRow($friend);
					$row -> list_id = $memberList->getIdentity();
					$row -> save();

					if (isset($message) && !empty($message))
					{
						$notifyApi -> addNotification($friend, $viewer, $business, 'ynbusinesspages_invite_message', array('message' => $message));
					}
					else
					{
						$notifyApi -> addNotification($friend, $viewer, $business, 'ynbusinesspages_invite');
					}
				}
				$db -> commit();
			}
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		//Invite people via email
		$recipients = $values['recipients'];
		$message = $values['message'];

		if (isset($message) && !empty($message))
		{
			$sent = $this -> InviteViaEmail($recipients, $message, $business, "ynbusinesspages_invite_message");
		}
		else
		{
			$sent = $this -> InviteViaEmail($recipients, $message, $business, "ynbusinesspages_invite");
		}
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$callbackUrl = $this -> view -> url(array('id' => $business -> getIdentity()), 'ynbusinesspages_profile', true);
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRedirect' => $callbackUrl,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Members invited'))
			));
		}
		else
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Members invited')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}
	
	public function InviteViaEmail($recipients, $message = NULL, $object, $type)
	{
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$user = Engine_Api::_() -> user() -> getViewer();
		// Check recipients
		if (is_string($recipients))
		{
			$recipients = preg_split("/[\s,]+/", $recipients);
		}
		if (is_array($recipients))
		{
			$recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
		}
		if (!is_array($recipients) || empty($recipients))
		{
			return 0;
		}

		// Only allow a certain number for now
		$max = $settings -> getSetting('invite.max', 10);
		if (count($recipients) > $max)
		{
			$recipients = array_slice($recipients, 0, $max);
		}

		// Check message
		$message = trim($message);
		$emailsSent = 0;
		foreach ($recipients as $recipient)
		{
			try
			{
				$defaultParams = array(
					'host' => $_SERVER['HTTP_HOST'],
					'email' => $recipient,
					'date' => time(),
					'recipient_title' => "Guest",
					'sender_title' => $user -> getTitle(),
					'sender_link' => $user -> getHref(),
					'object_title' => $object -> getTitle(),
					'object_link' => $object -> getHref(),
					'object_photo' => $object -> getPhotoUrl('thumb.icon'),
					'object_description' => $object -> getDescription(),
					'message' => $message,
				);
				Engine_Api::_() -> getApi('mail', 'core') -> sendSystem($recipient, 'notify_' . $type, $defaultParams);
			}
			catch (Exception $e)
			{
				// Silence
				if (APPLICATION_ENV == 'development')
				{
					throw $e;
				}
				continue;
			}
			$emailsSent++;
		}
		return $emailsSent;
	}
	
	public function rejectAction()
	{
		// Check auth
		if( !$this->_helper->requireUser()->isValid() ) return;
		if( !$this->_helper->requireSubject()->isValid() ) return;

		// Make form
		$this->view->form = $form = new Ynbusinesspages_Form_Member_Reject();

		// Process form
		if( !$this->getRequest()->isPost() )
		{
			$this->view->status = false;
			$this->view->error = true;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
			return;
		}

		if( !$form->isValid($this->getRequest()->getPost()) )
		{
			$this->view->status = false;
			$this->view->error = true;
			$this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
			return;
		}

		// Process form
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();
		$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();

		try
		{
			$subject->membership()->removeMember($viewer);

			// Set the request as handled
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
			$viewer, $subject, 'ynbusinesspages_invite');
			if( $notification )
			{
				$notification->mitigated = true;
				$notification->save();
			}
	   
			$db->commit();
		}
		catch( Exception $e )
		{
			$db->rollBack();
			throw $e;
		}

		$this->view->status = true;
		$this->view->error = false;
		$message = Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the business %s');
		$message = sprintf($message, $subject->__toString());
		$this->view->message = $message;

		if( $this->_helper->contextSwitch->getCurrentContext() == "smoothbox" ) {
			return $this->_forward('success', 'utility', 'core', array(
	        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Business invite rejected')),
	        'layout' => 'default-simple',
	        'parentRefresh' => true,
			));
		}
	}
	public function ajaxGetFriendsAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('ynbusinesspages_business') -> isValid())
			return;
		// Prepare data
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();

		$package = $business -> getPackage();
		if(!$package -> getIdentity())
		{
			return $this -> _helper -> requireAuth() -> forward();
		}

		if(!$package -> allow_user_invite_friend)
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		// Prepare friends
		$params = $this->getAllParams();
		$search = $params['search'];
		$friendsTable = Engine_Api::_() -> getDbtable('membership', 'user');
		$membersTable = Engine_Api::_() -> getDbtable('membership', 'ynbusinesspages');
		$membersIds = $membersTable->select() -> from($membersTable, 'user_id')->where('resource_id = ?', $business -> getIdentity()) -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
		$friendsIds = $friendsTable -> select() -> from($friendsTable, 'user_id')
			-> where('resource_id = ?', $viewer->getIdentity())
			-> where('active = ?', true)
			-> where('user_id NOT IN (?)',$membersIds)
			-> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
		if (!empty($friendsIds))
		{
			$friendTbl = Engine_Api::_() -> getItemTable('user');
			if( $search )
			{
				$select = $friendTbl -> select() -> where('user_id IN (?)', $friendsIds) ->where('displayname LIKE ?', '%' . $search . '%')-> order('displayname');
			}
			else
			{
				$select = $friendTbl -> select() -> where('user_id IN (?)', $friendsIds)-> order('displayname');
			}
			$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
			$this->view->paginator->setItemCountPerPage(15);
			if(isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page'],1);
		}
		else
			{
				$this -> view -> paginator = $paginator = Zend_Paginator::factory(array());
			}
	}
}
