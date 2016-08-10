<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynfeed
 * @copyright  Copyright 2014 YouNet Company
 * @author     YouNet Company
 */
class Ynfeed_Widget_WelcomeTabController extends Engine_Content_Widget_Abstract {
	public function indexAction() 
	{
		// only show on home page - not subject
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (Engine_Api::_() -> core() -> hasSubject() || !$viewer -> getIdentity()) 
		{
		    return $this -> setNoRender();
		}
		$content = Engine_Api::_() -> getDbtable('welcomes', 'ynfeed') -> getWelcome();
		if(!$content)
		{
			return $this -> setNoRender();
		}
		$this -> view -> content = $content;
		
		// Get Friend Requests
		$this -> view -> friend_requests = Engine_Api::_() -> ynfeed() -> getFriendRequests(array('limit' => $content -> number_of_friend));
		
		// Get Member Suggestions
		$this -> view -> member_suggestions = Engine_Api::_() -> ynfeed() -> getMemberSuggestions(array('limit' => $content -> number_of_member));
		
		// Get Group Suggestions
		$this -> view -> group_suggestions = Engine_Api::_() -> ynfeed() -> getGroupSuggestions(array('limit' => $content -> number_of_group));
		
		// Get Event Suggestions
		$this -> view -> event_suggestions = Engine_Api::_() -> ynfeed() -> getEventSuggestions(array('limit' => $content -> number_of_event));
		
		// Get Most Liked Items
		$this -> view -> most_liked_items = Engine_Api::_() -> ynfeed() -> getMostLikedItems(array('limit' => $content -> number_of_like));
	}
}