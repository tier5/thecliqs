<?php

class Sdtopbarmenu_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $this->_helper->requireUser();
  }

  public function indexAction()
  {
	$page = $this->_getParam('page');
    $viewer = Engine_Api::_()->user()->getViewer();
    $select = Engine_Api::_()->getDbTable('notifications', 'activity')
                        ->select()
                        ->where("`user_id` = ?", $viewer->getIdentity())
                        ->where("`type` <> 'friend_request'")
						//->where("`read` = ?", 0)
                        ->where("`type` <> 'message_new'")
                        ->order('notification_id DESC');
    $this->view->notifications = $notifications = Zend_Paginator::factory($select);
    $notifications->setCurrentPageNumber($page);
	
    $this->view->requests = $requests = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestsPaginator($viewer);
    $requests->setCurrentPageNumber($page);

    // Force rendering now
    $this->_helper->viewRenderer->postDispatch();
    $this->_helper->viewRenderer->setNoRender(true);

    $this->view->hasunread = false;

    // Now mark them all as read
    $ids = array();
    foreach( $notifications as $notification ) {
      $ids[] = $notification->notification_id;
    }
  }
  
  public function updateAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
	  $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'sdtopbarmenu');	
      $this->view->notificationCount = $notificationCount = count($notificationsTable->notificationsOnlys());
    }
	
	if($notificationCount != 0)
	{
    	$this->view->text = $this->view->translate($notificationCount);
	}
  }
  
  public function requestupdateAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {	
	  $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'sdtopbarmenu');
	  $this->view->requestsCount = $requestsCount = count($notificationsTable->friendrequestOnlys());
    }
	
	if($requestsCount != 0)
	{
    	$this->view->text = $this->view->translate($requestsCount);
	}
  }
  
  public function messageupdateAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $viewer->getIdentity() ) {
	  $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'sdtopbarmenu');	
      $this->view->messageCount = $messageCount = count($notificationsTable->messageOnlys());
    }
	
	if($messageCount != 0)
	{
    	$this->view->text = $this->view->translate($messageCount);
	}
  }
  
  public function pulldownAction()
  {
    $page = $this->_getParam('page');
    $viewer = Engine_Api::_()->user()->getViewer();
    $select = Engine_Api::_()->getDbTable('notifications', 'activity')
                        ->select()
                        ->where("`user_id` = ?", $viewer->getIdentity())
                        ->where("`type` <> 'friend_request'")
						->where("`read` = ?", 0)
                        ->where("`type` <> 'message_new'")
                        ->order('notification_id DESC');
    $this->view->notifications = $notifications = Zend_Paginator::factory($select);
    $notifications->setCurrentPageNumber($page);

    if( $notifications->getCurrentItemCount() <= 0 || $page > $notifications->getCurrentPageNumber() ) {
      $this->_helper->viewRenderer->setNoRender(true);
      return;
    }

    // Force rendering now
    $this->_helper->viewRenderer->postDispatch();
    $this->_helper->viewRenderer->setNoRender(true);
  }
  
  public function requestpulldownAction()
  {
    $page = $this->_getParam('page');
    $viewer = Engine_Api::_()->user()->getViewer();
    $select = Engine_Api::_()->getDbTable('notifications', 'activity')
                        ->select()
                        ->where("`user_id` = ?", $viewer->getIdentity())
						->where("`type` = 'friend_request'")
						->where("`read` = ?", 0)
                        ->where("`type` <> 'message_new'")
                        ->order('notification_id DESC');
    $this->view->friendrequest = $friendrequest = Zend_Paginator::factory($select);
    $friendrequest->setCurrentPageNumber($page);

    if( $friendrequest->getCurrentItemCount() <= 0 || $page > $friendrequest->getCurrentPageNumber() ) {
      $this->_helper->viewRenderer->setNoRender(true);
      return;
    }

    // Force rendering now
    $this->_helper->viewRenderer->postDispatch();
    $this->_helper->viewRenderer->setNoRender(true);
  }
  
  public function messagepulldownAction()
  {
    $page = $this->_getParam('page');
    $viewer = Engine_Api::_()->user()->getViewer();
    $select = Engine_Api::_()->getDbTable('notifications', 'activity')
                        ->select()
                        ->where("`user_id` = ?", $viewer->getIdentity())
						->where("`type` = 'message_new'")
						->where("`read` = ?", 0)
                        ->where("`type` <> 'friend_request'")
                        ->order('notification_id DESC');
    $this->view->messagenew = $messagenew = Zend_Paginator::factory($select);
    $messagenew->setCurrentPageNumber($page);

    if( $messagenew->getCurrentItemCount() <= 0 || $page > $messagenew->getCurrentPageNumber() ) {
      $this->_helper->viewRenderer->setNoRender(true);
      return;
    }

    // Force rendering now
    $this->_helper->viewRenderer->postDispatch();
    $this->_helper->viewRenderer->setNoRender(true);
  }
  
  public function hideAction()
  {
	$viewer = Engine_Api::_()->user()->getViewer();
    $notificationsTbl = Engine_Api::_()->getDbtable('notifications', 'activity');
	$request = Zend_Controller_Front::getInstance()->getRequest();
    $action_id = $request->getParam('actionid', 0);
	if(!empty($action_id) && $action_id!=0){
		$where = array(
		  '`user_id` = ?' => $viewer->getIdentity(),
		  '`read` = ?' => 0,
		  "`type` <> 'message_new'",
		  "`type` <> 'friend_request'",
		  '`notification_id` = ?' => $action_id);
	}
	else{
		$where = array(
		  '`user_id` = ?' => $viewer->getIdentity(),
		  '`read` = ?' => 0,
		  "`type` <> 'message_new'",
		  "`type` <> 'friend_request'");
	}
    $notificationsTbl->update(array('read' => 1), $where);
  }
  
  public function hidemessagAction()
  {
	$viewer = Engine_Api::_()->user()->getViewer();
    $notificationsTbl = Engine_Api::_()->getDbtable('notifications', 'activity');
	$request = Zend_Controller_Front::getInstance()->getRequest();
    $action_id = $request->getParam('actionid', 0);
	if(!empty($action_id) && $action_id!=0){
		$where = array(
		  '`user_id` = ?' => $viewer->getIdentity(),
		  '`read` = ?' => 0,
		  "`type` = 'message_new'",
		  "`type` <> 'friend_request'",
		  '`notification_id` = ?' => $action_id);
	}
	else{
	 	$where = array(
		  '`user_id` = ?' => $viewer->getIdentity(),
		  '`read` = ?' => 0,
		  "`type` = 'message_new'",
		  "`type` <> 'friend_request'");
	}
    $notificationsTbl->update(array('read' => 1), $where);
  }
  
  public function hiderequestAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $notificationsTbl = Engine_Api::_()->getDbtable('notifications', 'activity');
	$request = Zend_Controller_Front::getInstance()->getRequest();
    $action_id = $request->getParam('actionid', 0);
	if(!empty($action_id) && $action_id!=0){
		$where = array(
		  '`user_id` = ?' => $viewer->getIdentity(),
		  '`read` = ?' => 0,
		  "`type` = 'friend_request'",
		  "`type` <> 'message_new'",
		  '`notification_id` = ?' => $action_id);
	}
	else{
		$where = array(
		  '`user_id` = ?' => $viewer->getIdentity(),
		  '`read` = ?' => 0,
		  "`type` = 'friend_request'",
		  "`type` <> 'message_new'");
	}
    $notificationsTbl->update(array('read' => 1), $where);
  }
  
   public function friendConfirmAction() {
        // Get Viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            // Set Norender if user is not logged on
            return false;
        }
        $resource_id = $this->getRequest()->getParam('resource_id');
        $this->view->status = false;
        $this->view->resource_id = $resource_id;
        $user = Engine_Api::_()->getItem('user', $resource_id);
        if ($resource_id) {
            $uid    = $viewer->getIdentity();
			
			$userTable = Engine_Api::_()->getDbTable('users', 'user');
			$selectu = $userTable->select()
			->where("(user_id = $uid)
					OR (user_id = $resource_id)");
			$rows_user = $userTable->fetchAll($selectu);		
			foreach ($rows_user as $row_user){
				$row_user->member_count++;
				$row_user->save();
            }
			
            $userTb = Engine_Api::_()->getDbTable('membership', 'user');
            $db = $userTb->getAdapter();
            $db->beginTransaction();
            $select = $userTb->select()
                            ->where("(user_id = $uid AND resource_id = $resource_id)
                                    OR (user_id = $resource_id AND resource_id = $uid)")
                            ->where("active = 0");
            $rows = $userTb->fetchAll($select);
            try {
                if (count($rows)) {
                    foreach ($rows as $row) {
                        $row->active = 1;
                        $row->user_approved = 1;
                        $row->resource_approved = 1;
                        $row->save();
                    }
                     // Add activity
                      if( !$user->membership()->isReciprocal() ) {
                        Engine_Api::_()->getDbtable('actions', 'activity')
                            ->addActivity($user, $viewer, 'friends_follow', '{item:$subject} is now following {item:$object}.');
                      } else {
                        Engine_Api::_()->getDbtable('actions', 'activity')
                          ->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
                        Engine_Api::_()->getDbtable('actions', 'activity')
                          ->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
                      }
                      
                      // Add notification
                      if( !$user->membership()->isReciprocal() ) {
                        Engine_Api::_()->getDbtable('notifications', 'activity')
                          ->addNotification($user, $viewer, $user, 'friend_follow_accepted');
                      } else {
                        Engine_Api::_()->getDbtable('notifications', 'activity')
                          ->addNotification($user, $viewer, $user, 'friend_accepted');
                      }
                    // Set the requests as handled
                      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                          ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
                      if( $notification ) {
                        $notification->mitigated = true;
                        $notification->read = 1;
                        $notification->save();
                      }
                      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                          ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
                      if( $notification ) {
                        $notification->mitigated = true;
                        $notification->read = 1;
                        $notification->save();
                      }
                    $db->commit();
                    $this->view->status = true;
                }
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }   
    }
	
    public function friendCancelAction() {
        // Get Viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            // Set Norender if user is not logged on
            return false;
        }
        $resource_id = $this->getRequest()->getParam('resource_id');
        $user = Engine_Api::_()->getItem('user', $resource_id);  
        $this->view->status = false;
        $this->view->resource_id = $resource_id;
        if ($resource_id) {
            $uid    = $viewer->getIdentity();
            $userTb = Engine_Api::_()->getDbTable('membership', 'user');
            $db = $userTb->getAdapter();
            $db->beginTransaction();
            $select = $userTb->select()
                            ->where("(user_id = $uid AND resource_id = $resource_id)
                                    OR (user_id = $resource_id AND resource_id = $uid)")
                            ->where("active = 0");
            $rows = $userTb->fetchAll($select);
            try {
                if (count($rows)) {
                    foreach ($rows as $row) {
                        $row->delete();
                    }
                    // Set the requests as handled
                      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                          ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
                      if( $notification ) {
                        $notification->mitigated = true;
                        $notification->read = 1;
                        $notification->save();
                      }
                      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                          ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
                      if( $notification ) {
                        $notification->mitigated = true;
                        $notification->read = 1;
                        $notification->save();
                      }
                    $db->commit();
                    $this->view->status = true;
                }
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }
}
