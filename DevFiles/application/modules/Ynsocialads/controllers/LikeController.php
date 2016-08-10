<?php
class Ynsocialads_LikeController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $type = $this->_getParam('type');
    $identity = $this->_getParam('id');
    if( $type && $identity ) {
      $item = Engine_Api::_()->getItem($type, $identity);
      if( $item instanceof Core_Model_Item_Abstract && 
          (method_exists($item, 'comments') || method_exists($item, 'likes')) ) {
        if( !Engine_Api::_()->core()->hasSubject() ) {
          Engine_Api::_()->core()->setSubject($item);
        }
      }
    }

    $this->_helper->requireSubject();
  }

 
  public function likeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $comment_id = $this->_getParam('comment_id');

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    
    if( $comment_id ) {
      $commentedItem = $subject->comments()->getComment($comment_id);
    } else {
      $commentedItem = $subject;
    }
    
    // Process
    $db = $commentedItem->likes()->getAdapter();
    $db->beginTransaction();

    try {
      
      $commentedItem->likes()->addLike($viewer);
      
      // Add notification
      $owner = $commentedItem->getOwner();
      $this->view->owner = $owner->getGuid();
      if( $owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity() ) {
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
          'label' => $commentedItem->getShortType()
        ));
      }
      
      // Stats
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    // For comments, render the resource
    if( $subject->getType() == 'core_comment' ) {
      $type = $subject->resource_type;
      $id = $subject->resource_id;
      Engine_Api::_()->core()->clearSubject();
    } else {
      $type = $subject->getType();
      $id = $subject->getIdentity();
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like added');
    $this->view->body = $this->view->action('list', 'comment', 'core', array(
      'type' => $type,
      'id' => $id,
      'format' => 'html',
      'page' => 1,
    ));
	
	//handle like
	$isLike = 1;
	$likes = $subject->likes()->getAllLikesUsers();
	$return_str = "";
	$aUserLike = $subject->getUserLike();
	if(count($aUserLike) > 0){
		$iUserId = $aUserLike[0]['iUserId'];
		$user = Engine_Api::_() -> getItem('user', $iUserId);
		$sDisplayName = $aUserLike[0]['sDisplayName'];
		$return_str = "<a href='".$user->getHref()."'>".$sDisplayName."</a>";
		if($isLike)
		{
			if(count($likes) > 2)
			{
				$return_str = ", " . $return_str . $this->view -> translate(array(" and %s other liked this.", " and %s others liked this." ,count($likes) -1), count($likes) -1);	
			}
			else 
			{
				$return_str = ", ". $return_str . $this->view -> translate(' liked this.');
			}
		}
		else {
			if(count($likes) > 1)
			{
				$return_str = $return_str. $this->view -> translate(array(" and %s other liked this."," and %s others liked this.", count($likes)), count($likes));
			}
			else 
			{
				$return_str = $return_str . $this->view -> translate(' liked this.');
			}
		}
	}
	else 
	{
		if($isLike)
		{
			if(count($likes) > 1)
			{
				$return_str .= $this->view -> translate(array("and %s other liked this.", "and %s others liked this.", count($likes) -1), count($likes) -1); 
			}
			else 
			{
				$return_str .= $this->view -> translate(' liked this.');
			}
		}
		else {
			if(count($likes) > 0)
			{
				$return_str .= count($likes). $this->view -> translate(' people liked this.');
			}
		}
	}
				
	$this->view->list = $return_str;
	
    $this->_helper->contextSwitch->initContext();
  }

  public function unlikeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $comment_id = $this->_getParam('comment_id');

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    if( $comment_id ) {
      $commentedItem = $subject->comments()->getComment($comment_id);
    } else {
      $commentedItem = $subject;
    }

    // Process
    $db = $commentedItem->likes()->getAdapter();
    $db->beginTransaction();

    try
    {
      $commentedItem->likes()->removeLike($viewer);

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    // For comments, render the resource
    if( $subject->getType() == 'core_comment' ) {
      $type = $subject->resource_type;
      $id = $subject->resource_id;
      Engine_Api::_()->core()->clearSubject();
    } else {
      $type = $subject->getType();
      $id = $subject->getIdentity();
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like removed');
    $this->view->body = $this->view->action('list', 'comment', 'core', array(
      'type' => $type,
      'id' => $id,
      'format' => 'html',
      'page' => 1,
    ));
	
	//handle unlike
	$isLike = 0;
	$likes = $subject->likes()->getAllLikesUsers();
	$return_str = "";
	$aUserLike = $subject->getUserLike();
	if(count($aUserLike) > 0){
		$iUserId = $aUserLike[0]['iUserId'];
		$user = Engine_Api::_() -> getItem('user', $iUserId);
		$sDisplayName = $aUserLike[0]['sDisplayName'];
		$return_str = "<a href='".$user->getHref()."'>".$sDisplayName."</a>";
		if($isLike)
		{
			if(count($likes) > 2)
			{
				$return_str = ", " . $return_str . $this->view -> translate(array(" and %s other liked this.", " and %s others liked this." ,count($likes) -1), count($likes) -1);	
			}
			else 
			{
				$return_str = ", ". $return_str . $this->view -> translate(' liked this.');
			}
		}
		else {
			if(count($likes) > 1)
			{
				$return_str = $return_str. $this->view -> translate(array(" and %s other liked this."," and %s others liked this.", count($likes)), count($likes));
			}
			else 
			{
				$return_str = $return_str . $this->view -> translate(' liked this.');
			}
		}
	}
	else 
	{
		if($isLike)
		{
			if(count($likes) > 1)
			{
				$return_str .= $this->view -> translate(array("and %s other liked this.", "and %s others liked this.", count($likes) -1), count($likes) -1); 
			}
			else 
			{
				$return_str .= $this->view -> translate(' liked this.');
			}
		}
		else {
			if(count($likes) > 0)
			{
				$return_str .= count($likes). $this->view -> translate(' people liked this.');
			}
		}
	}
	$this->view->count = count($likes);			
	$this->view->list = $return_str;
	
    $this->_helper->contextSwitch->initContext();
  }
  
}