<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Netlogtemplatedefault
 * @copyright  Copyright 2010-2012 SocialEnginePro
 * @license    http://www.socialenginepro.com
 * @author     altrego aka Vadim ( provadim@gmail.com )
 */

class Netlogtemplatedefault_Widget_NetlogFriendsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		// Don't render this if friendships are disabled
	if( !Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible )
		return $this->setNoRender();

		// Don't render this if not authorized
	$viewer = Engine_Api::_()->user()->getViewer();

	if ( !$viewer->getIdentity() )
		return $this->setNoRender();

		// Multiple friend mode
	$select = $viewer->membership()->getMembersOfSelect();
	$this->view->friends = $friends = $paginator = Zend_Paginator::factory($select);  

		// Set item count per page and current page number
	$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 40));
	$paginator->setCurrentPageNumber($this->_getParam('page', 1));

		// Get stuff
	$ids = array();
	foreach( $friends as $friend )
		$ids[] = $friend->resource_id;

		// Get the items
	$friendUsers = array();
	foreach( Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser ) {
			// Get info about messages
		$conversation = $this->_haveUnread($viewer->getIdentity(), $friendUser->getIdentity());
		if ( $conversation ) {
			$messageStatus = array('status'=>'user1_unread', 'conversation'=>$conversation);
		}
		else {
			$conversation = $this->_haveUnread($friendUser->getIdentity(), $viewer->getIdentity());
			if ( $conversation!=0 )
				$messageStatus = array('status'=>'user2_unread', 'conversation'=>$conversation);
			else
				$messageStatus = array('status'=>'noUnreadMessages', 'conversation'=>false);
		}

		$onlineStatus = $this->_onlineStatus($friendUser);
		$profileStatus = $friendUser->status()->getLastStatus($friendUser);
		if ( !empty($profileStatus) ) {
			$profileStatus = strip_tags($profileStatus->body);
		} else {
			$profileStatus = '';
		}

		if ( mb_strlen($profileStatus)>75 )
			$profileStatus = mb_substr($profileStatus,0,75) . '...';

		if ($onlineStatus['status']=='offline') {
			$friendUsers[] = array(
				'user'=>$friendUser,
				'message'=>$messageStatus,
				'online_status'=>$onlineStatus,
				'profile_status'=>$profileStatus
			);
		} else {
			array_unshift($friendUsers,
				array(
					'user'=>$friendUser,
					'message'=>$messageStatus,
					'online_status'=>$onlineStatus,
					'profile_status'=>$profileStatus
				)
			);
		}
	}

	$this->view->friendUsers = $friendUsers;

  }


  private function _haveUnread($user1_id, $user2_id) {

	$rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
	$select = Engine_Api::_()->getDbtable('recipients', 'messages')->select()
		->setIntegrityCheck(false)
		->from(array('in'=>$rName), new Zend_Db_Expr('in.inbox_message_id AS id'))
		->joinLeft(array('out'=>$rName), 'in.conversation_id=out.conversation_id')
		->where('in.user_id = ?', $user1_id)
		->where('in.inbox_deleted = ?', 0)
		->where('in.inbox_read = ?', 0)
		->where('out.user_id = ?', $user2_id);
	$data = Engine_Api::_()->getDbtable('recipients', 'messages')->fetchRow($select);

	if ( !$data )
		return false;

	$conversation = Engine_Api::_()->getItem('messages_message', $data->id);

	return $conversation;

  }


  private function _onlineStatus($user) {

	$now = time();
	$onlineTime = date('Y-m-d H:i:s', strtotime('-20 minutes'));
	$nowDay = date('d', $now);
	$nowWeek = date('W', $now);

		// check user online status
	$oName = Engine_Api::_()->getDbtable('online', 'user')->info('name');
	$select = Engine_Api::_()->getDbtable('online', 'user')->select()
		->from($oName, new Zend_Db_Expr('COUNT(user_id) AS online'))
		->where('user_id = ?', $user->getIdentity())
		->where('active > ?', $onlineTime);
	$data = Engine_Api::_()->getDbtable('online', 'user')->fetchRow($select);

		// get user status if it online
	if ( $data->online ) {
			// get netlog status, if user set it, else set status = online
		$netlog_status = Engine_Api::_()->getDbtable('userstatus', 'Netlogtemplatedefault')->getStatus($user->getIdentity());
		if ( $netlog_status )
			$status['status'] = $netlog_status;
		else
			$status['status'] = 'online';

		$delta = abs( strtotime($user->lastlogin_date) - $now);

		if( $delta < 60 ) {
			$val = null;
			$key = 'a few seconds ago';
		}
		elseif( $delta < 3600 ) {
			$val = floor($delta / 60);
			$key = array('Online for %s minute', 'Online for %s minutes', $val);
		}
		elseif( $delta < 86400 ) {
			$val = floor($delta / (60 * 60));
			$key = array('Online for %s hour', 'Online for %s hours', $val);
		}
		else {
			$val = floor($delta / (60 * 60 * 24));
			$key = array('Online for %s day', 'Online for %s days', $val);
		}
		$translator = $this->view->getHelper('translate');

		if( $translator ) {
			$status['login_time'] = $translator->translate($key, $val);
		} else {
			$key = is_array($key) ? $string[0] : $key;
			$status['login_time'] = sprintf($key, $val);
		}
	} else {
		$status['status'] = 'offline';
	}

	if( $status['status']=='online' ) {
		$status['class'] = 'online';
	} elseif ( $status['status']=='away' || $status['status']=='out to lunch' ) {
		$status['class'] = 'away';
	} elseif ( $status['status']=='busy' || $status['status']=='unavailable' ) {
		$status['class'] = 'busy';
	} else {
		$status['class'] = 'offline';
	}

	return $status;

  }

}