<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Event.php$
 * @author     LONGL
 */

class Ynmobile_Api_Event extends Core_Api_Abstract
{
	/**
	 * Input data:
	 * + iLimit: int, optional.
	 * + iPage: int, optional.
	 * + sWhen: string, optional. Default 'upcoming'.
	 * + iCategoryId int, optional.
	 * + 
	 *
	 * Output data:
	 Event array()
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see event/upcoming
	 *
	 * @global string $token
	 * @param array $aData
	 * @return array
	 */
	public function upcoming($aData)
	{
		extract($aData, EXTR_SKIP);
		
		if (!isset($aData['iPage']))
			$aData['iPage'] = 1;
		
		if ($aData['iPage'] == '0')
			return array();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to get my event!"),
				'result' => 0
			);
		}
		
		if (!isset($aData['iPage']))
		{
			$aData['iPage'] = 1;
		}
		if (!isset($aData['iLimit']))
		{
			$aData['iLimit'] = 12;
		}
		$filter = "future";
		if (isset($aData['sWhen']))
		{
			$filter = $aData['sWhen'];
		}
		if ($filter == "past")
		{
			$values['past'] = 1;
		}
		else
		{
			$values['future'] = 1;
			
		}
		
		//category_id
		if (isset($aData['iCategoryId']) && $aData['iCategoryId'])
		{
			$values['category_id'] = $aData['iCategoryId'];
		}
		
		if (isset( $aData['iUserId']))
			$values['user_id'] = $aData['iUserId'];
		
		
		if (isset($aData['sSearch']) && !empty($aData['sSearch']))
		{
			$aData['sSearch'] = trim($aData['sSearch']);
			$values['keyword'] = $aData['sSearch'];
		}
		
		
		if (isset($aData['sLat']) & isset($aData['sLong']))
		{
			$values['lat'] = $aData['sLat'];
			$values['long'] = $aData['sLong'];
			$values['mile_of'] = $aData['iWithin'];
		}
		
		if (isset( $aData['sOrder']))
		{
			//order
            $order = 'starttime';
            if (isset( $aData['sOrder']))
            {
                $order = strtolower($aData['sOrder']);
                if ($order == 'starttime'){
                    $order = "starttime ASC";   
                }
                elseif ($order == 'creation_date'){
                    $order = "creation_date DESC";              
                }
                elseif ($order == 'member_count'){
                    $values['order'] = "member_count";
                    $values['direction'] =  'DESC';
                }
            }
		}
		
		if (!empty($aData['sParentType']) && !empty($aData['iParentId']))
        {
			$values['parent_type'] = $aData['sParentType'];
            $values['parent_id'] =  $aData['iParentId'];
        }
		
		$values['search'] = 1;
		
		$events = Engine_Api::_() -> getItemTable('ynevent_event') -> getEventPaginator($values);
		$events -> setItemCountPerPage($aData['iLimit']);
		$events -> setCurrentPageNumber($aData['iPage']);
		
		$totalPage = (integer) ceil($events->getTotalItemCount() / $aData['iLimit']);
		if ($aData['iPage'] > $totalPage)
			return array(); 
		
		return $this -> parserEvents($events);
	}

	/**
	 * Input data:
	 * + iLimit: int, optional.
	 * + iPage: int, optional.
	 * + sWhen: string, optional. Default 'upcoming'.
	 *
	 * Output data:
	 Event array()
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see event/friend
	 *
	 * @global string $token
	 * @param array $aData
	 * @return array
	 */
	public function friend($aData)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to get friend's event!"),
				'result' => 0
			);
		}
		if (!isset($aData['iPage']))
		{
			$aData['iPage'] = 1;
		}
		if (!isset($aData['iLimit']))
		{
			$aData['iLimit'] = 12;
		}
		$filter = "future";
		if (isset($aData['sWhen']))
		{
			$filter = $aData['sWhen'];
		}
		if ($filter == "past")
		{
			$values['past'] = 1;
		}
		else
		{
			$values['future'] = 1;
		}

		if ($viewer -> getIdentity())
		{
			$values['users'] = $afriendIds = $viewer->membership()->getMembershipsOfIds();
		}

		$values['search'] = 1;
		$values['search_text'] = '';
		$events = Engine_Api::_() -> getItemTable('ynevent_event') -> getEventPaginator($values);
		$events -> setItemCountPerPage($aData['iLimit']);
		$events -> setCurrentPageNumber($aData['iPage']);
		return $this -> parserEvents($events);
	}

	/**
	 * Input data:
	 * + iLimit: int, optional.
	 * + iPage: int, optional.
	 * + sWhen: string, optional. Default 'upcoming'.
	 *
	 * Output data:
	 Event array()
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see event/my
	 *
	 * @global string $token
	 * @param array $aData
	 * @return array
	 */
	public function my($aData)
	{
		if (!isset($aData['iPage']))
		{
			$aData['iPage'] = 1;
		}
		if (!isset($aData['iLimit']))
		{
			$aData['iLimit'] = 12;
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$table = Engine_Api::_()->getDbtable('events', 'ynevent');
		$tableName = $table->info('name');
		
		$membership = Engine_Api::_()->getDbtable('membership', 'ynevent');
		$select = $membership->getMembershipsOfSelect($viewer);
		
		if (isset($aData['sSearch']) && !empty($aData['sSearch']))
		{
			$aData['sSearch'] = trim($aData['sSearch']);
			if ( is_numeric($aData['sSearch']) && ((int)$aData['sSearch'])>0 )
				$select->where("$tableName.zip_code = ?", $aData['sSearch']);
			else
				$select->where("$tableName.title like ?", '%' . $aData['sSearch'] . '%');
		}
		
		//$select->order('starttime ASC');
		
		$select -> order('creation_date DESC');
		$select -> group('repeat_group');
		
		$paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage($aData['iLimit']);
		$paginator->setCurrentPageNumber($aData['iPage']);
			
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $aData['iLimit']);
		if ($aData['iPage'] > $totalPage)
			return array();
		
		return $this -> parserEvents($paginator);
	}

	/**
	 * Input data:
	 * + iLimit: int, optional.
	 * + iPage: int, optional.
	 * + sWhen: string, optional. Default 'upcoming'.
	 *
	 * Output data:
	 Event array()
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see event/past
	 *
	 * @global string $token
	 * @param array $aData
	 * @return array
	 */
	public function past($aData)
	{
		if (!isset($aData['sWhen']))
		{
			$aData['sWhen'] = 'past';
		}
		return $this -> upcoming($aData);
	}

	/**
	 * Input data:
	 * events array()
	 *
	 * Output data:
	 * + iEventId: int.
	 * + sTitle: string.
	 * + bCanPostComment: bool.
	 * + sEventImageUrl: string.
	 * + sFullName: string.
	 * + iUserId: int.
	 * + sUserImageUrl: string.
	 * + iStartTime: int.
	 * + sStartTime: string.
	 * + sStartFullTime: string.
	 * + iEndTime: int.
	 * + sEndTime: string.
	 * + sEndFullTime: string.
	 * + iTimeStamp: int.
	 * + sLocation: string.
	 *
	 * @param array $aData
	 * @return array
	 */
	public function parserEvents($events)
	{
		$arr_results = array();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		foreach ($events as $event)
		{
			
			//$bCanPostComment = Engine_Api::_() -> authorization() -> isAllowed($event, null, 'comment');
			$bCanView = Engine_Api::_() -> authorization() -> isAllowed($event, null, 'view');
			
			$sEventImageUrl = $event -> getPhotoUrl(TYPE_OF_USER_IMAGE_EVENT);
			$sEventBigImageUrl = $event -> getPhotoUrl();
			
			if ($sEventImageUrl != "")
			{
				$sEventImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sEventImageUrl);
				$sEventBigImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sEventBigImageUrl);
			}
			else
			{
				$sEventImageUrl = NO_EVENT_ICON;
				$sEventBigImageUrl = NO_EVENT_ICON;
			}
			$user = $event -> getOwner();
			$sUserImageUrl = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sUserImageUrl != "")
			{
				$sUserImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sUserImageUrl);
			}
			else
			{
				$sUserImageUrl = NO_USER_ICON;
			}

			// Convert the dates for the viewer
			$start = strtotime($event -> starttime);
			$end = strtotime($event -> endtime);
			$create = strtotime($event -> creation_date);
			
			$oldTz = date_default_timezone_get();
			if ($viewer && $viewer -> getIdentity())
			{
				date_default_timezone_set($viewer -> timezone);
			}
			$start_date = date('Y-m-d H:i:s', $start);
			$end_date = date('Y-m-d H:i:s', $end);
			date_default_timezone_set($oldTz);
			
			$timeZone = Engine_Api::_()->ynmobile()->getUserTimeZone();
			$timeZone = $timeZone *(-1);
			$iStartTime = strtotime($timeZone.' hours',strtotime($start_date));
			$iEndTime = strtotime($timeZone.' hours',strtotime($end_date));
			
			$oldTz = date_default_timezone_get();
			// Convert the dates for the viewer
			if ($viewer && $viewer -> getIdentity())
			{
				date_default_timezone_set($viewer -> timezone);
			}

			$view = Zend_Registry::get('Zend_View');
			$memberInfo = $event -> membership() -> getMemberInfo($viewer);
			$iRsvp = -1;
			if ($memberInfo)
			{
			
				$iRsvp = $memberInfo -> rsvp;
			}
			$row = $event->membership()->getRow($viewer);
			$arr_results[] = array(
				'iFeatured'=> $event->featured,
				'iEventId' => $event -> getIdentity(),
				'iRsvp' => $iRsvp,
				'sTitle' => $event -> getTitle(),
				'sDescription' => strip_tags($event->description),
				'bCanView' => $bCanView,
				'sEventImageUrl' => $sEventImageUrl,
				'sEventBigImageUrl' => $sEventBigImageUrl,
				'iUserId' => $user -> getIdentity(),
				'sFullName' => $user -> getTitle(),
				'sUserImageUrl' => $sUserImageUrl,
					
				'iStartTime' => $iStartTime,
				'sStartTime' => date('l, F j', $start),
				'sStartFullTime' => date('l, F j', $start) . ' at ' . date('g:i a', $start),
				'sShortStartTime' => date('g:i A', $start),
				'iEndTime' => $iEndTime,
				'sEndTime' => date('l, F j', $end),
				'sEndFullTime' => date('l, F j', $end) . ' at ' . date('g:i a', $end),
					
				'iTimeStamp' => $create,
				'sLocation' => $event -> location,
				'iNumOfMember' => $event -> membership()->getMemberCount(true),
				'bIsResourceApproval' => $event -> membership()->isResourceApprovalRequired(),
				'bOnRequest' => (!$row->resource_approved && $row->user_approved) ? 1 : 0,
				'bIsInvited' => ($row->resource_approved && !$row->user_approved) ? 1 : 0,
				'bIsMember' => $event->membership()->isMember($viewer, true),
				'sUserTimezone' => Engine_Api::_()->ynmobile()->getUserTimeZone(),
				'sHref'=> Engine_Api::_()->ynmobile()->finalizeUrl($event->getHref()),
			);
			date_default_timezone_set($oldTz);

		}
		
		
		return $arr_results;
	}

	/**
	 * Input data:
	 * + parent_type: string, optional.
	 * + parent_id: int, optional.
	 * + category_id: string, optional.
	 * + title: string, required.
	 * + description: string, optional.
	 * + location: string, optional.
	 * + host: string, optional
	 * + start_month: int, required.
	 * + start_day: int, required.
	 * + start_year: int, required.
	 * + start_hour: int, required.
	 * + start_minute: int, required.
	 * + end_month: int, required.
	 * + end_day: int, required.
	 * + end_year: int, required.
	 * + end_hour: int, required.
	 * + end_minute: int, required.

	 * + auth_view: string, optional.
	 * + auth_comment: string, optional.
	 * + auth_photo: string, optional
	 * + photo: file/array/file path, optional.
	 * + search: int, optional
	 * + approval: int, optional
	 * + auth_invite: int, optional
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + iEventId: int.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see event/create
	 *
	 * @see Phpfox_Parse_Format
	 * @param array $aData
	 * @return array
	 */
	public function create($aData)
	{	
		if (!Engine_Api::_() -> authorization() -> isAllowed('event', null, 'create'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to create a new event!"),
				'result' => 0
			);
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$parent_type = 'user';
		$parent_id = $viewer -> getIdentity();
		if (!empty($aData['parent_type']))
		{
			$parent_type = $aData['parent_type'];
		}
		if (!empty($aData['parent_id']))
		{
			$parent_id = $aData['parent_id'];
		}

		if ($parent_type == 'group' && Engine_Api::_() -> hasItemType('group'))
		{
			$group = Engine_Api::_() -> getItem('group', $parent_id);
			if (is_null($group) || (!$group->getIdentity()))
			{
				return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This group is not existed!"),
					'result' => 0
				);
			}
			if (!Engine_Api::_() -> authorization() -> isAllowed($group, null, 'event'))
			{
				return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to create a new event!"),
					'result' => 0
				);
			}
		}
		else
		{
			$parent_type = 'user';
			$parent_id = $viewer -> getIdentity();
		}


		if (!isset($aData['title']) || trim($aData['title']) == "")
		{
			return array(
				'error_code' => 3,
				'error_element' => 'title',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Title is not valid!")
			);
		}

		// 2013-09-12 format Y-m-d
		if (isset($aData['start_date']) && $aData['start_date'] != "")
		{
			$date = explode("-", $aData['start_date']);
			$aData['start_year'] = $date[0];
			$aData['start_month'] = $date[1];
			$aData['start_day'] = $date[2];
		}
		
		if (isset($aData['start_time']) && $aData['start_time'] != "")
		{
			$date = explode(":", $aData['start_time']);
			$aData['start_hour'] = $date[0];
			$aData['start_minute'] = $date[1];
		}
		
		if (isset($aData['end_date']) && $aData['end_date'] != "")
		{
			$date = explode("-", $aData['end_date']);
			$aData['end_year'] = $date[0];
			$aData['end_month'] = $date[1];
			$aData['end_day'] = $date[2];
		}
		
		if (isset($aData['end_time']) && $aData['end_time'] != "")
		{
			$date = explode(":", $aData['end_time']);
			$aData['end_hour'] = $date[0];
			$aData['end_minute'] = $date[1];
		}
		
		// For start time.
		if (!isset($aData['start_month']))
		{
			return array(
					'error_code' => 4,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Start month is not valid!")
			);
		}
		if (!isset($aData['start_day']))
		{
			return array(
					'error_code' =>4,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Start day is not valid!")
			);
		}
		if (!isset($aData['start_year']))
		{
			return array(
					'error_code' => 4,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Start year is not valid!")
			);
		}
		
		
		if (!isset($aData['start_hour']))
		{
			return array(
					'error_code' => 4,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Start hour is not valid!")
			);
		}
		if (!isset($aData['start_minute']))
		{
			return array(
					'error_code' => 4,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Start minute is not valid!")
			);
		}
		
		// For end time.
		if (!isset($aData['end_month']))
		{
			$aData['end_month'] = $aData['start_month'];
		}
		if (!isset($aData['end_day']))
		{
			$aData['end_day'] = $aData['start_day'];
		}
		$aData['end_year'] = isset($aData['end_year']) ? (int)$aData['end_year'] : $aData['start_year'];
		
		
		if (!isset($aData['end_hour']))
		{
			$aData['end_hour'] = $aData['start_hour'];
		}
		if (!isset($aData['end_minute']))
		{
			$aData['end_minute'] = $aData['start_minute'];
		}
		
		if (!isset($aData['category_id']) || $aData['category_id'] <= '0')
		{
			$aData['category_id'] = 0;
		}
		
		if (!isset($aData['longitude']))
		{
			$aData['longitude'] = "";
		}
		
		if (!isset($aData['latitude']))
		{
			$aData['latitude'] = "";
		}
		
		if ( isset($aData['zip_code']) && !is_numeric($aData['zip_code']) )
		{
			return array(
					'error_code' => 4,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Zipcode is invailid!")
			);
		}
		
		// Process
		$values = $aData;
		$values['user_id'] = $viewer -> getIdentity();
		$values['parent_type'] = $parent_type;
		$values['parent_id'] = $parent_id;
		if ($parent_type == 'group' && Engine_Api::_() -> hasItemType('group') && empty($values['host']))
		{
			$values['host'] = $group -> getTitle();
		}

		// Convert times
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($viewer -> timezone);
		$start = mktime($aData['start_hour'], $aData['start_minute'], 0, $aData['start_month'], $aData['start_day'], $aData['start_year']);
		$end = mktime($aData['end_hour'], $aData['end_minute'], 0, $aData['end_month'], $aData['end_day'], $aData['end_year']);
		date_default_timezone_set($oldTz);
		$values['starttime'] = date('Y-m-d H:i:s', $start);
		$values['endtime'] = date('Y-m-d H:i:s', $end);
		
		if ($values['starttime'] >= $values['endtime'])
		{
			return array(
				'error_code' => 4,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("End Time should be greater than Start Time!")
			);
		}
		
		
		$db = Engine_Api::_() -> getDbtable('events', 'ynevent') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Create event
			$table = Engine_Api::_() -> getDbtable('events', 'ynevent');
			$event = $table -> createRow();
			$event -> setFromArray($values);
			$event -> save();

			// None repeated for default event
			$event -> repeat_type = 0;
			$event -> repeat_group = $event->getIdentity();
			$event -> save();
			
			// Adding event photo
			if(!empty($_FILES['image']))
			{
				$event = Engine_Api::_()->ynmobile()->setEventPhoto($event, $_FILES['image']);
			}
			
			// Add owner as member
			$event -> membership() -> addMember($viewer) -> setUserApproved($viewer) -> setResourceApproved($viewer);

			// Add owner rsvp
			$event -> membership() -> getMemberInfo($viewer) -> setFromArray(array('rsvp' => 2)) -> save();

			// Set auth
			$auth = Engine_Api::_() -> authorization() -> context;

			if ($values['parent_type'] == 'group')
			{
				$roles = array(
					'owner',
					'member',
					'parent_member',
					'registered',
					'everyone'
				);
			}
			else
			{
				$roles = array(
					'owner',
					'member',
					'owner_member',
					'owner_member_member',
					'owner_network',
					'registered',
					'everyone'
				);
			}

			if (empty($values['auth_view']))
			{
				$values['auth_view'] = 'everyone';
			}

			if (empty($values['auth_comment']))
			{
				$values['auth_comment'] = 'everyone';
			}
			
			if (empty($values['auth_photo']))
			{
				$values['auth_photo'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
			$photoMax = array_search($values['auth_photo'], $roles);

			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($event, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($event, $role, 'comment', ($i <= $commentMax));
				$auth -> setAllowed($event, $role, 'photo', ($i <= $photoMax));
			}

			if(!isset($values['auth_invite'])){
				$values['auth_invite'] = 1;
			}

			$auth -> setAllowed($event, 'member', 'invite', $values['auth_invite']);

			// Add an entry for member_requested
			$auth -> setAllowed($event, 'member_requested', 'view', 1);

			// Add action
			$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');

			$action = $activityApi -> addActivity($viewer, $event, 'ynevent_create');

			if ($action)
			{
				$activityApi -> attachActivity($action, $event);
			}
			// Commit
			$db -> commit();
			return array('iEventId' => $event -> getIdentity());
		}
		catch( Engine_Image_Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 5,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'),
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 5,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, Fail!"),
			);
		}

	}

	/**
	 * @see Event_Service_Process
	 *
	 * Input data:
	 * + iEventId: int, required.
	 * + category_id: string, optional.
	 * + title: string, required.
	 * + description: string, optional.
	 * + location: string, optional.
	 * + host: string, optional
	 *
	 * + start_month: int, required.
	 * + start_day: int, required.
	 * + start_year: int, required.
	 * + start_hour: int, required.
	 * + start_minute: int, required.
	 * + end_month: int, required.
	 * + end_day: int, required.
	 * + end_year: int, required.
	 * + end_hour: int, required.
	 * + end_minute: int, required.

	 * + auth_view: string, optional.
	 * + auth_comment: string, optional.
	 * + auth_photo: string, optional
	 * + photo: file/array/file path, optional.
	 * + search: int, optional
	 * + approval: int, optional
	 * + auth_invite: int, optional
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 *
	 * @param array $aData
	 * @return array|bool
	 */
	public function edit($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iEventId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$event = Engine_Api::_() -> getItem('ynevent_event', $aData['iEventId']);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		if (!$event)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
		
		if (!(Engine_Api::_() -> authorization() -> isAllowed($event, null, 'edit') || $event -> isOwner($viewer)))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this event!"),
				'result' => 0
			);
		}

		
		if (!isset($aData['title']) || trim($aData['title']) == "")
		{
			return array(
				'error_code' => 1,
				'error_element' => 'title',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Title is not valid!")
			);
		}
		
		// 2013-09-12 format Y-m-d
		if (isset($aData['start_date']) && $aData['start_date'] != "")
		{
			$date = explode("-", $aData['start_date']);
			$aData['start_year'] = $date[0];
			$aData['start_month'] = $date[1];
			$aData['start_day'] = $date[2];
		}
		
		if (isset($aData['start_time']) && $aData['start_time'] != "")
		{
			$date = explode(":", $aData['start_time']);
			$aData['start_hour'] = $date[0];
			$aData['start_minute'] = $date[1];
		}
		
		if (isset($aData['end_date']) && $aData['end_date'] != "")
		{
			$date = explode("-", $aData['end_date']);
			$aData['end_year'] = $date[0];
			$aData['end_month'] = $date[1];
			$aData['end_day'] = $date[2];
		}
		
		if (isset($aData['end_time']) && $aData['end_time'] != "")
		{
			$date = explode(":", $aData['end_time']);
			$aData['end_hour'] = $date[0];
			$aData['end_minute'] = $date[1];
		}
		
		// For start time.
		if (!isset($aData['start_month']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Start month is not valid!")
			);
		}
		if (!isset($aData['start_day']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Start day is not valid!")
			);
		}
		if (!isset($aData['start_year']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Start year is not valid!")
			);
		}
		

		if (!isset($aData['start_hour']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Start hour is not valid!")
			);
		}
		if (!isset($aData['start_minute']))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Start minute is not valid!")
			);
		}
		
		
		// For end time.
		
		if (!isset($aData['end_month']))
		{
			$aData['end_month'] = $aData['start_month'];
		}
		if (!isset($aData['end_day']))
		{
			$aData['end_day'] = $aData['start_day'];
		}
		$aData['end_year'] = isset($aData['end_year']) ? (int)$aData['end_year'] : $aData['start_year'];



		if (!isset($aData['end_hour']))
		{
			$aData['end_hour'] = $aData['start_hour'];
		}
		if (!isset($aData['end_minute']))
		{
			$aData['end_minute'] = $aData['start_minute'];
		}
		
		if (!isset($aData['longitude']))
		{
			$aData['longitude'] = '';
		}
		
		if (!isset($aData['latitude']))
		{
			$aData['latitude'] = '';
		}

		if ( isset($aData['zip_code']) && !is_numeric($aData['zip_code']) )
		{
			return array(
					'error_code' => 4,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Zipcode is invailid!")
			);
		}
		
		// Process
		$values = $aData;
        
        
        // Convert times
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($viewer->timezone);
        
        $values['starttime'] = $values['start_date']  .' ' . $values['start_time'];
        $values['endtime'] =  $values['end_date']  .' ' . $values['end_time'];
        
        $start = strtotime($values['starttime']);
        $end = strtotime($values['endtime']);
        
        date_default_timezone_set($oldTz);
        
        $values['starttime'] = date('Y-m-d H:i:s', $start);
        $values['endtime'] = date('Y-m-d H:i:s', $end);
        
        
		if ($values['starttime'] >= $values['endtime'])
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("End Time should be greater than Start Time!")
			);
		}
		// Check parent
		if (!isset($values['host']) && $event -> parent_type == 'group' && Engine_Api::_() -> hasItemType('group'))
		{
			$group = Engine_Api::_() -> getItem('group', $event -> parent_id);
			$values['host'] = $group -> getTitle();
		}

		// Process
		$db = Engine_Api::_() -> getItemTable('ynevent_event') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Set event info
			$event -> setFromArray($values);
			$event -> save();

			// Adding event photo
			if(!empty($_FILES['image']))
			{
				$event = Engine_Api::_()->ynmobile()->setEventPhoto($event, $_FILES['image']);
			}

			// Process privacy
			$auth = Engine_Api::_() -> authorization() -> context;

			if ($event -> parent_type == 'group')
			{
				$roles = array(
					'owner',
					'member',
					'parent_member',
					'registered',
					'everyone'
				);
			}
			else
			{
				$roles = array(
					'owner',
					'member',
					'owner_member',
					'owner_member_member',
					'owner_network',
					'registered',
					'everyone'
				);
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
			$photoMax = array_search($values['auth_photo'], $roles);

			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($event, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($event, $role, 'comment', ($i <= $commentMax));
				$auth -> setAllowed($event, $role, 'photo', ($i <= $photoMax));
			}

			$auth -> setAllowed($event, 'member', 'invite', $values['auth_invite']);

			// Commit
			$db -> commit();
			return array('iEventId' => $event -> getIdentity());
		}
		catch( Engine_Image_Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'),
			);
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, Fail!"),
			);
		}
	}

	/**
	 * @see Event_Service_Process
	 * Input data:
	 * + iEventId: int, required.
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + message: string.
	 * + result: bool.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see event/delete
	 *
	 * @param array $aData
	 * @return array
	 */
	public function delete($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iEventId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$event = Engine_Api::_() -> getItem('ynevent_event', $aData['iEventId']);

		if (!$event)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event doesn't exists or not authorized to delete"),
				'result' => 0
			);

		}

		if (!Engine_Api::_() -> authorization() -> isAllowed($event, null, 'delete'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this event!"),
				'result' => 0
			);
		}

		$db = $event -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$event -> delete();
			$db -> commit();
			return array(
				'result' => true,
				'message' => Zend_Registry::get('Zend_Translate') -> _('The selected event has been deleted.')
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, Fail!"),
			);
		}
	}

	/**
	 * Input data: N/A
	 *
	 * Output data:
	 * + iCategoryId: int.
	 * + sName: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see event/listcategories
	 *
	 * @param array $aData
	 * @return array
	 */
	public function listcategories($aData)
	{
		// Populate with categories
		$categories = Engine_Api::_() -> getDbtable('categories', 'ynevent') -> getCategoriesAssoc();
		asort($categories, SORT_LOCALE_STRING);
		$categoryOptions = array();
		foreach ($categories as $k => $v)
		{
			$categoryOptions[] = array(
				'iCategoryId' => $k,
				'sName' => $v
			);
		}
		return $categoryOptions;
	}

	/**
	 * Input data:
	 * + iEventId: int, required.
	 * + iRSVP: int, optional.
	 * + iLimit:: int, optional.
	 * + iLastMemberIdViewed: int, optional.
	 *
	 * Output data:
	 * + iEventId: int.
	 * + iTypeId: int.
	 * + iRSVP: int.
	 * + iUserId: int.
	 * + sFullName: string.
	 * + sUserImage: string.
	 *
	 * @see Mobile - API phpFox/Api V2.0
	 * @see event/viewgetlist
	 *
	 * @param array $aData
	 * @return array
	 */
	public function viewguestlist($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iEventId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$members = null;
		$event = Engine_Api::_() -> getItem('ynevent_event', $aData['iEventId']);
		if (!$event)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
		$iRsvp = isset($aData['iRSVP']) ? (int)$aData['iRSVP'] : 2;
		$iPageSize = isset($aData['iLimit']) ? (int)$aData['iLimit'] : -1;

		if ($viewer -> getIdentity() && $event -> isOwner($viewer))
		{
			$select = $event -> membership() -> getMembersSelect(false);
			if (isset($aData['iLastMemberIdViewed']) && $aData['iLastMemberIdViewed'] > 0)
			{
				$select -> where("user_id > ?", $aData['iLastMemberIdViewed']);
			}
			$select -> order("user_id");
			$waitingMembers = Zend_Paginator::factory($select);
			if ($iRsvp == 3)
			{
				$members = $waitingMembers;
			}
		}
		
		if (!$members)
		{
			$select = $event -> membership() -> getMembersObjectSelect();
			$select -> where("rsvp = ?", $iRsvp);

			if (isset($aData['iLastMemberIdViewed']) && $aData['iLastMemberIdViewed'] > 0)
			{
				$select -> where("user_id > ?", $aData['iLastMemberIdViewed']);
			}
			$select -> order("displayname");
			$members = Zend_Paginator::factory($select);
		}

		// Set item count per page and current page number
		$members -> setItemCountPerPage($iPageSize);
		/**
		 * @var array
		 */
		$aResult = array();
		foreach ($members as $member)
		{
			if (!empty($member -> resource_id))
			{
				$memberInfo = $member;
				$member = Engine_Api::_() -> user() -> getUser($memberInfo -> user_id);
			}
			else
			{
				$memberInfo = $event -> membership() -> getMemberInfo($member);
			}
			$sProfileImage = $member -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			$sBigProfileImage = $member -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
			
			if ($sProfileImage != "")
			{
				$sProfileImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sProfileImage);
				$sBigProfileImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sBigProfileImage);
			}
			else
			{
				$sProfileImage = NO_USER_ICON;
				$sProfileImage = NO_USER_NORMAL;
			}
			$aResult[] = array(
				'iEventId' => $event -> getIdentity(),
				'iTypeId' => 0,
				'iRSVP' => $memberInfo -> rsvp,
				'iUserId' => $member -> getIdentity(),
				'sFullName' => $member -> getTitle(),
				'sUserImage' => $sProfileImage,
				'sBigUserImage' => $sBigProfileImage
			);
		}
		return $aResult;
	}

	/**
	 * Input data:
	 * + iEventId: int, required.
	 *
	 * Output data:
	 * + iRsvp: int.
	 * + bIsFriend: bool.
	 * + iEventId: int.
	 * + sEventImageUrl: string.
	 * + sFullName: string.
	 * + iUserId: int.
	 * + sUserImageUrl: string.
	 * + iStartTime: int.
	 * + sStartTime: string.
	 * + sStartFullTime: string.
	 * + iEndTime: int.
	 * + sEndTime: string.
	 * + sEndFullTime: string.
	 * + iTimeStamp: int.
	 * + sTitle: string.
	 * + sDescription: string.
	 * + bIsInvisible: bool.
	 * + sCategory: array.
	 * + sLocation: string.
	 * + iStartYear: int.
	 * + iStartMonth: int.
	 * + iStartDate: int.
	 * + iStartHour: int.
	 * + iStartMinute: int.
	 * + iEndYear: int.
	 * + iEndMonth: int.
	 * + iEndDate: int.
	 * + iEndHour: int.
	 * + iEndMinute: int.
	 * + bCanPostComment: int.
	 *
	 * @see Mobile - API phpFox/Api V2.0
	 * @see event/view
	 *
	 * @param array $aData
	 * @return boolean|array
	 */
	public function view($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iEventId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing event identity!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$event = Engine_Api::_() -> getItem('ynevent_event', $aData['iEventId']);
		if (!$event)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
		
		$bCanPostComment = Engine_Api::_() -> authorization() -> isAllowed($event, null, 'comment');
		$bCanView = Engine_Api::_() -> authorization() -> isAllowed($event, null, 'view');
		
		$memberInfo = $event -> membership() -> getMemberInfo($viewer);
		$iRsvp = -1;
		if ($memberInfo)
		{

			$iRsvp = $memberInfo -> rsvp;
		}
		$owner = $event -> getOwner();
		$is_friend = false;
		if ($owner -> getIdentity() != $viewer -> getIdentity())
		{
			$is_friend = $viewer -> membership() -> isMember($owner);
		}
		
		$sEventImageUrl = $event -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
		$sEventBigImageUrl = $event -> getPhotoUrl();
		
		if ($sEventImageUrl != "")
		{
			$sEventImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sEventImageUrl);
			$sEventBigImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sEventBigImageUrl);
		}
		else
		{
			$sEventImageUrl = NO_EVENT_ICON;
			$sEventBigImageUrl = NO_EVENT_ICON;
		}
		$sUserImageUrl = $owner -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		if ($sUserImageUrl != "")
		{
			$sUserImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sUserImageUrl);
		}
		else
		{
			$sUserImageUrl = NO_USER_ICON;
		}
		$view = Zend_Registry::get('Zend_View');
		$start = strtotime($event -> starttime);
		$end = strtotime($event -> endtime);
		$create = strtotime($event -> creation_date);
		
		$oldTz = date_default_timezone_get();
		if ($viewer && $viewer -> getIdentity())
		{
			date_default_timezone_set($viewer -> timezone);
		}
		$start_date = date('Y-m-d H:i:s', $start);
		$end_date = date('Y-m-d H:i:s', $end);
		date_default_timezone_set($oldTz);
		
		$timeZone = Engine_Api::_()->ynmobile()->getUserTimeZone();
		$timeZone = $timeZone *(-1);
		$iStartTime = strtotime($timeZone.' hours',strtotime($start_date));
		$iEndTime = strtotime($timeZone.' hours',strtotime($end_date));
		
		$oldTz = date_default_timezone_get();
		// Convert the dates for the viewer
		if ($viewer && $viewer -> getIdentity())
		{
			date_default_timezone_set($viewer -> timezone);
		}
		
		$auth = Engine_Api::_()->authorization()->context;
		$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		foreach( $roles as $role )
		{
			if( 1 === $auth->isAllowed($event, $role, 'view') )
			{
				$sViewPrivacy = $role;
			}
			if( 1 === $auth->isAllowed($event, $role, 'comment') )
			{
				$sCommentPrivacy = $role;
			}
		}
		
		$aPrivacyText = Engine_Api::_()->getApi("Privacy","Ynmobile") -> simplePrivacy();
		$row = $event->membership()->getRow($viewer);
		$sHost = $event -> host;
		if(strpos($event -> host,'younetco_event_key_') !== FALSE)
		{
			$user_id = substr($event -> host, 19, strlen($event -> host));
			$user = Engine_Api::_() -> getItem('user', $user_id);
			$sHost = $user->getTitle();
		}
		$result = array(
			'iRsvp' => $iRsvp,
			'bIsFriend' => $is_friend,
			'iEventId' => $event -> getIdentity(),
			'sEventImageUrl' => $sEventImageUrl,
			'sEventBigImageUrl' => $sEventBigImageUrl,
				
			'sFullName' => $owner -> getTitle(),
			'iUserId' => $owner -> getIdentity(),
			'sUserImageUrl' => $sUserImageUrl,
			'iStartTime' => $iStartTime,
			'sStartFullTime' => date('l, F j, Y', $start) . ' at ' . date('g:i a', $start),
				
			'iEndTime' => $iEndTime,
			'sEndFullTime' => date('l, F j, Y', $end) . ' at ' . date('g:i a', $end),
				
			'iTimeStamp' => $create,
			'sTitle' => $event -> getTitle(),
			'sDescription' => strip_tags($event->description),
			'bIsInvisible' => !$event -> search,
			'iCategory' => $event-> category_id,
			'sCategory' => $view -> translate((string)$event -> categoryName()),
			'sLocation' => $event -> location,
			'sHost' => $sHost,
				
			'iStartYear' => date('Y', $start),
			'iStartMonth' => date('m', $start),
			'iStartDate' => date('d', $start),
			'iStartHour' => date('H', $start),
			'iStartMinute' => date('i', $start),
			'sStartDate' => date('Y', $start) . "-" . date('n', $start) . "-" . date('j', $start),
			'sStartTime' => date('G', $start) . ":" . date('i', $start),
				
			'iEndYear' => date('Y', $end),
			'iEndMonth' => date('m', $end),
			'iEndDate' => date('d', $end),
			'iEndHour' => date('H', $end),
			'iEndMinute' => date('i', $end),
			'sEndDate' => date('Y', $end) . "-" . date('n', $end) . "-" . date('j', $end),
			'sEndTime' => 	date('G', $end) . ":" . date('i', $end),
				
			'bCanPostComment' => $bCanPostComment,
			'bCanView' => $bCanView,
			'bCanInvite' => $event->authorization()->isAllowed($viewer, 'invite'),
				
			'sViewPrivacy' => $sViewPrivacy,
			'sViewPrivacyFull' => $aPrivacyText[$sViewPrivacy],
				
			'sCommentPrivacy' => $sCommentPrivacy,
			'sCommentPrivacyFull' => $aPrivacyText[$sCommentPrivacy],
				
			'bIsResourceApproval' => $event -> membership()->isResourceApprovalRequired(),
			'bOnRequest' => (!$row->resource_approved && $row->user_approved) ? 1 : 0,
			'bIsInvited' => ($row->resource_approved && !$row->user_approved) ? 1 : 0,
			'bIsMember' => $event->membership()->isMember($viewer, true),
			'sLongitude' => ($event->longitude != null) ? $event->longitude : '',
			'sLatitude' => ($event->latitude != null) ? $event->latitude : '',
			'sUserTimezone' => Engine_Api::_()->ynmobile()->getUserTimeZone(),
			'iNumOfMember' => $event -> membership()->getMemberCount(true),	
			'sHref'=> Engine_Api::_()->ynmobile()->finalizeUrl($event->getHref()),
		);
		date_default_timezone_set($oldTz);
		return $result;
	}

	/**
	 * @see Event_Service_Process
	 *
	 * Input data:
	 * + iEventId: int, required.
	 * + iRsvp: int, required.
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + result: int.
	 * + message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see event/addrsvp
	 *
	 * @param array $aData
	 * @return array
	 */
	public function addrsvp($aData)
	{
		
		if (!isset($aData['iEventId']))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iEventId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$event = Engine_Api::_() -> getItem('ynevent_event', $aData['iEventId']);
		if (!$event)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
		if (!$event -> authorization() -> isAllowed($viewer, 'view'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to view this event!"),
				'result' => 0
			);
		}

		$iRsvp = isset($aData['iRsvp']) ? (int)$aData['iRsvp'] : 2;

		$db = $event -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();

		
		try
		{
			$membership_status = $event -> membership() -> getRow($viewer) -> active;
			$event -> membership() -> setUserApproved($viewer);
			$row = $event -> membership() -> getRow($viewer);
			$row -> rsvp = $iRsvp;
			$row -> save();

			// Set the request as handled
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($viewer, $event, 'ynevent_invite');
			if ($notification)
			{
				$notification -> mitigated = true;
				$notification -> save();
			}

			// Add activity
			if (!$membership_status)
			{
				$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
				$action = $activityApi -> addActivity($viewer, $event, 'ynevent_join');
			}
			$db -> commit();
			return array(
				'error_code' => 0,
				'result' => 1,
				'message' => 'Your RSVP has been updated!',
				'event_data' => $this->detail(array('iEventId'=> $event->getIdentity())),
			);
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage(),
			);
		}
	}

	/**
	 * @see Event_Service_Process
	 *
	 * Input data:
	 * + iEventId: int, required.
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + result: int.
	 * + message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see event/join
	 *
	 * @param array $aData
	 * @return array
	 */
	public function join($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
					'error_code' => 1,
					'error_element' => 'iEventId',
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		$event = Engine_Api::_() -> getItem('ynevent_event', $aData['iEventId']);
		
		if (!$event)
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
		
		//Also using JOIN apis for sending request 
		$iRsvp = ($event->membership()->isResourceApprovalRequired()) ? 3 : 2;
		
		// Process form
		$db = $event -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();
		
		try
		{
			$membership_status = $event->membership()->getRow($viewer)->active;
		
			$event->membership()
				->addMember($viewer)
				->setUserApproved($viewer);
		
			$row = $event->membership()
				->getRow($viewer);
		
			$row -> rsvp = $iRsvp;
			$row->save();
		
			// Add activity if membership status was not valid from before
			if (!$membership_status){
				$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
				$action = $activityApi->addActivity($viewer, $event, 'ynevent_join');
			}
		
			$db->commit();
			
			//Also using JOIN apis for sending request
			if( $event->membership()->isResourceApprovalRequired() ) {
				return array(
						'error_code' => 0,
						'result' => 1,
						'message' => 'You have requested invite this event!',
						'event_data' =>$this->detail(array('iEventId'=> $event->getIdentity())),
				);
			}
			else {
				return array(
						'error_code' => 0,
						'result' => 1,
						'message' => 'You have joined this event!',
						'event_data' =>$this->detail(array('iEventId'=> $event->getIdentity())),
				);
			}
		}
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage(),
					'error_debug'=> $e->getMessage(),
			);
		}
	}
	
	/**
	 * @see Event_Service_Process
	 *
	 * Input data:
	 * + iEventId: int, required.
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + result: int.
	 * + message: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see event/leave
	 *
	 * @param array $aData
	 * @return array
	 */
	public function leave($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
					'error_code' => 1,
					'error_element' => 'iEventId',
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$event = Engine_Api::_() -> getItem('ynevent_event', $aData['iEventId']);
		
		if (!$event)
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
	
		if( $event->isOwner($viewer) ) {
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Can not leave this event")
			);
		}
	
		
		$db = $event->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();

		try
		{
			$event->membership()->removeMember($viewer);
			$db->commit();
			return array(
					'error_code' => 0,
					'result' => 1,
					'message' => 'You have left this event!',
					'event_data' =>$this->detail(array('iEventId'=> $event->getIdentity())),
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
	
	
	/**
	 * Input data:
	 * + iEventId: int, required.
	 * + sUserId: string, required. (string split by comma)
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + result: int.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see event/invite
	 *
	 * @param array $aData
	 * @return array
	 */
	public function invite($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iEventId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		// Prepare data
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$event = Engine_Api::_() -> getItem('ynevent_event', $aData['iEventId']);
		if (!$event)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not available!")
			);
		}
		// Prepare friends
		$friendsTable = Engine_Api::_() -> getDbtable('membership', 'user');
		$friendsIds = $friendsTable -> select() -> from($friendsTable, 'user_id') -> where('resource_id = ?', $viewer -> getIdentity()) -> where('active = ?', true) -> limit(100) -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
		if (!empty($friendsIds))
		{
			$friends = Engine_Api::_() -> getItemTable('user') -> find($friendsIds);
		}
		else
		{
			$friends = array();
		}

		// Process
		$table = $event -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$usersIds = explode(',', $aData['sUserId']);

			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			foreach ($friends as $friend)
			{
				if (!in_array($friend -> getIdentity(), $usersIds))
				{
					continue;
				}
				$event -> membership() -> addMember($friend) -> setResourceApproved($friend);
				$notifyApi -> addNotification($friend, $viewer, $event, 'ynevent_invite');
			}
			$db -> commit();
			return array(
				'error_code' => 0,
				'result' => 1,
				'message' => 'Members invited!',
				'event_data' =>$this->detail(array('iEventId'=> $event->getIdentity())),
			);
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Oops, Fail!"),
				'error_debug' => $e->getMessage(),
			);
		}
	}

	/**
	 * Input data:
	 * + iEventId: int, required.
	 * + iActionId: int, optional.
	 * + iMaxId: int, optional.
	 * + iMinId: int, optional.
	 * + iLimit: int, optional.
	 *
	 * Output data:
	 * + iActionId: int.
	 * + iUserId: int.
	 * + sUsername: string.
	 * + UserProfileImg_Url: string.
	 * + sFullName: string.
	 * + bCanPostComment: bool.
	 * + sTime: string.
	 * + sTimeConverted: string.
	 * + sActionType: string.
	 * + iItemId: int.
	 * + sItemTitle: item title of the object (album, music, user,..)
	 * + sItemType: object type (ex: music,...)
	 * + bIsLike: bool.
	 * + sContent: string
	 * + bReadMore: bool
	 * + iShare: (shareable: 1, 2, 3, 4)
	 * + aAttachments: array()
	 * + sModule
	 * + sType
	 * + sTitle
	 * + sDescription
	 * + sPhoto_Url
	 * + sLink_Url
	 * @see Mobile - API SE/Api V2.0
	 * @see event/getfeed
	 */
	public function getfeed($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iEventId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$aData['iItemId'] = $aData['iEventId'];
		$aData['sItemType'] = 'event';
		return Engine_Api::_() -> getApi('feed', 'ynmobile') -> get($aData);
	}

	/**
	 * Input data:
	 * + iEventId: int, optional.
	 *
	 * Output data:
	 * + iNumGoing: int.
	 * + iNumAll: int.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see event/getnumberguestlist
	 *
	 * @param array $aData
	 * @return array
	 */
	public function getnumberguestlist($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
				'error_code' => 1,
				'error_element' => 'iEventId',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$event = Engine_Api::_() -> getItem('ynevent_event', $aData['iEventId']);
		if (!$event)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
		if (!$event -> authorization() -> isAllowed($viewer, 'view'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to view this event!"),
				'result' => 0
			);
		}
		$iTotalGoing = $event -> getAttendingCount();
		$iTotalAll = $event -> getAttendingCount() + $event -> getMaybeCount() + $event -> getNotAttendingCount();

		return array(
			'iNumGoing' => $iTotalGoing,
			'iNumMaybe' => $event -> getMaybeCount(),
			'iNumNotAttending' => $event -> getNotAttendingCount(),
			'iNumAll' => $iTotalAll
		);
	}

	/**
	 * Input data:
	 * + iEventId: int, required.
	 * + image: file/array/file path, required.
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + result: bool.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see event/photoprofile
	 *
	 * @param array $aData
	 * @return array
	 */
	public function photoprofile($aData)
	{
		if (!isset($aData['iEventId']) || !isset($aData['image']))
		{
			return array(
				'error_code' => 1,
				'error_elements' => 'iEventId or image',
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$event = Engine_Api::_() -> getItem('ynevent_event', $aData['iEventId']);
		if (!$event)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
		if (!$event -> authorization() -> isAllowed($viewer, 'edit'))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this event!"),
				'result' => 0
			);
		}
		$event -> setPhoto($aData['image']);
		return array('result' => true);
	}

	
	
	public function getinvitepeople($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
					'error_code' => 1,
					'error_element' => 'iEventId',
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing event id!")
			);
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$event = Engine_Api::_() -> getItem('ynevent_event', $aData['iEventId']);
		
		// Prepare friends
		$friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
		$friendsIds = $friendsTable->select()
		->from($friendsTable, 'user_id')
		->where('resource_id = ?', $viewer->getIdentity())
		->where('active = ?', true)
		->limit(100)
		->query()
		->fetchAll(Zend_Db::FETCH_COLUMN);
		
		if (!empty($friendsIds)) {
			// 			$friends = Engine_Api::_()->getItemTable('user')->find($friendsIds);
			$friendTbl = Engine_Api::_()->getItemTable('user');
			$select = $friendTbl->select()->where('user_id IN (?)', $friendsIds)->order('displayname');
			$friends = $friendTbl->fetchAll($select);
		} else {
			$friends = array();
		}
		
		$friendUsers = array();
		foreach ($friends as $friend) {
			if ($event->membership()->isMember($friend, null)) {
				continue;
			}
			
			$sProfileImage = $friend -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			$sBigProfileImage = $friend -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
			if ($sProfileImage)
			{
				$sProfileImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sProfileImage);
				$sBigProfileImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sBigProfileImage);
			}
			else
			{
				$sProfileImage = NO_USER_ICON;
				$sBigProfileImage = NO_USER_NORMAL;
			}
			
			$friendUsers[] = array(
					'id' => $friend->getIdentity(),
					'sFullName' => $friend->getTitle(),
					'UserProfileImg_Url' => $sProfileImage,
					'BigUserProfileImg_Url' => $sBigProfileImage
			);
		}
		
		return $friendUsers;
	}
	
	public function listphotos($aData)
	{
		extract($aData);
	
		if (!isset($iPage))
			$iPage = 1;
	
		if ($iPage == '0')
			return array();
	
		if (!isset($iLimit))
			$iLimit = 10;
	
		if (!isset($iEventId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _('Missing event id')
			);
		}
	
		$event = Engine_Api::_()->getItem('event', $iEventId);
		if (!is_object($event) || !$event->getIdentity())
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _('Can not find this event')
			);
		}
	
		// Get paginator
		$album = $event->getSingletonAlbum();
		$paginator = $album->getCollectiblesPaginator();
	
		// Set item count per page and current page number
		$paginator->setItemCountPerPage($iLimit);
		$paginator->setCurrentPageNumber($iPage);
	
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
			return array();
	
		$result = array();
		foreach($paginator as $photo)
		{
			$user = $photo->getOwner();
			if (isset($iInDetails) && $iInDetails == '1')
			{
	
				$iTotalComment = $photo -> comments() -> getCommentPaginator() -> getTotalItemCount();
				$iTotalLike = $photo -> likes() -> getLikePaginator() -> getTotalItemCount();
					
				$sUserImageUrl = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
				if ($sUserImageUrl != "")
				{
					$sUserImageUrl = Engine_Api::_() -> ynmobile() -> finalizeUrl($sUserImageUrl);
				}
				else
				{
					$sUserImageUrl = NO_USER_ICON;
				}
				$viewer = Engine_Api::_() -> user() -> getViewer();
				$canComment = $photo->authorization()->isAllowed($viewer, 'comment');
				$result[] = array(
						'iPhotoId' => $photo -> getIdentity(),
						'iUserId' => $user -> getIdentity(),
						'sUserImageUrl' => $sUserImageUrl,
						'sUserName' => $user -> getTitle(),
						'sPhotoThumbUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($photo->getPhotoUrl('thumb.normal')),
						'sPhotoUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($photo->getPhotoUrl('thumb.profile')),
						'sTitle' => $photo -> getTitle(),
						'sDescription' => $photo -> description,
						'bCanPostComment' => $canComment,
						'bCanLike' => $canComment,
						'bCanComment' => $canComment,
						'bIsLiked' => $photo -> likes() -> isLike($viewer),
						'iTotalComment' => $iTotalComment,
						'iTotalLike' => $iTotalLike,
						'aUserLike' => Engine_Api::_() -> getApi('like', 'ynmobile') -> getUserLike($photo),
						'sItemType' => 'ynevent_photo',
						'sModelType'=>'ynevent_photo',
						'iTimeStamp' => strtotime($photo -> creation_date),
				);
			}
			else
			{
				$result[] = array(
						'iPhotoId' => $photo->getIdentity(),
						'iUserId' => $user->getIdentity(),
						'sUserName' => $user->getTitle(),
						'sModelType'=>'ynevent_photo',
						'sUserImageUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($user->getPhotoUrl()),
						'sPhotoThumbUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($photo->getPhotoUrl('thumb.normal')),
						'sPhotoUrl' => Engine_Api::_() -> ynmobile() -> finalizeUrl($photo->getPhotoUrl('thumb.profile'))
				);
			}
				
				
				
		}
		return $result;
	}
	
	
	/**
	 * Input data:
	 * + iLimit: int, optional.
	 * + iPage: int, optional.
	 * + sWhen: string, optional. Default 'upcoming'.
	 *
	 * Output data:
	 Event array()
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see event/getfeatured
	 *
	 * @global string $token
	 * @param array $aData
	 * @return array
	 */
	public function getfeatured($aData)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if (!isset($aData['iPage']))
		{
			$aData['iPage'] = 1;
		}
		if (!isset($aData['iLimit']))
		{
			$aData['iLimit'] = 12;
		}
		
		$table = Engine_Api::_() -> getItemTable('ynevent_event');
		$select = $table -> select() ->where("featured = 1") -> limitPage($aData['iPage'], $aData['iLimit']);
		$events = $table->fetchAll($select);
		
		$showedEvents = array();
		$auth = Engine_Api::_()->authorization()->context;
		foreach($events as $event) {
			if ($auth->isAllowed($event, $viewer, 'view')) {
				array_push($showedEvents, $event);
			}
		}
		$paginator = Zend_Paginator::factory($showedEvents);
		
		//$events = Engine_Api::_() -> getItemTable('ynevent_event') -> getEventPaginator(array('featured' => '1'));
		$paginator -> setItemCountPerPage($aData['iLimit']);
		$paginator -> setCurrentPageNumber($aData['iPage']);
		
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $aData['iLimit']);
		if ($aData['iPage'] > $totalPage)
			return array();
		
		return $this -> parserEvents($events);
	}
	
	
	public function setfeatured($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iEventId!")
			);
		}
		$event = Engine_Api::_()->getItem('event',$aData['iEventId']);
		$event->featured = 1;
		$event->save();
		
		return array(
				'error_code' => 0,
				'result' => 1,
				'message' => 'Set featured successfully!'
		);
	}
	
	
	public function unsetfeatured($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iEventId!")
			);
		}
		$event = Engine_Api::_()->getItem('event',$aData['iEventId']);
		$event->featured = 0;
		$event->save();
	
		return array(
				'error_code' => 0,
				'result' => 1,
				'message' => 'Set featured successfully!'
		);
	}
	
	public function request($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
					'error_code' => 1,
					'error_element' => 'iEventId',
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Parameter is not valid!")
			);
		}
	
		$event = Engine_Api::_() -> getItem('event', $aData['iEventId']);
	
		if (!$event)
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
	
	
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = $event;
		$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$subject -> membership() -> addMember($viewer) -> setUserApproved($viewer);
	
			// Add notification
			$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
			$notifyApi->addNotification($subject->getOwner(), $viewer, $subject, 'ynevent_approve');
	
			$db->commit();
				
			return array(
					'error_code' => 0,
					'message' => Zend_Registry::get('Zend_Translate')->_('Your invite request has been sent.'),
					'event_data' =>$this->detail(array('iEventId'=> $event->getIdentity())),
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
	
	
	public function cancel($aData)
	{
		if (!isset($aData['iEventId']))
		{
			return array(
					'error_code' => 1,
					'error_element' => 'iEventId',
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing event identity!")
			);
		}
	
		$event = Engine_Api::_() -> getItem('event', $aData['iEventId']);
	
		if (!$event)
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
	
	
		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = $event;
	
		$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();
		try
		{
			$subject->membership()->removeMember($viewer);
	
			// Remove the notification?
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
					$subject->getOwner(), $subject, 'ynevent_approve');
			if( $notification ) {
				$notification->delete();
			}
	
			$db->commit();
			return array(
					'error_code' => 0,
					'message' => Zend_Registry::get('Zend_Translate')->_('Your invite request has been cancelled.'),
					'event_data' =>$this->detail(array('iEventId'=> $event->getIdentity())),
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
	
	
	public function photo_detail($aData)
	{
		extract($aData);
		if (!isset($iPhotoId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_('Missing photo id.')
			);
		}
	
		$photo = Engine_Api::_()->getItem("ynevent_photo", $iPhotoId);
		if ($photo->getIdentity())
		{
			return array(
					'iPhotoId' => $iPhotoId,
					'sTitle' => $photo->title,
					'sDescription' => $photo->description,
			);
		}
		else
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_('This photo is not existed.')
			);
		}
	
	}
	
	public function photo_edit($aData)
	{
		extract($aData);
		if (!isset($iPhotoId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_('Missing photo id.')
			);
		}
	
		$photo = Engine_Api::_()->getItem("ynevent_photo", $iPhotoId);
	
		if ($photo->getIdentity())
		{
			if (isset($sTitle))
				$photo->title = $sTitle;
				
			if (isset($sDescription))
				$photo->description = $sDescription;
	
			$photo->save();
			return array(
					'error_code' => 0,
					'message' => Zend_Registry::get('Zend_Translate')->_('Edited successfully'),
					'iPhotoId' => $iPhotoId
			);
				
		}
		else
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_('This photo is not existed.')
			);
		}
	
	}
	
	public function photo_delete($aData)
	{
		extract($aData);
		if (!isset($iPhotoId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_('Missing photo id.')
			);
		}
	
		$photo = Engine_Api::_()->getItem("ynevent_photo", $iPhotoId);
	
		if ($photo->getIdentity())
		{
			$photo->delete();
			return array(
					'error_code' => 0,
					'message' => Zend_Registry::get('Zend_Translate')->_('Deleted successfully'),
			);
		}
		else
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate')->_('This photo is not existed.')
			);
		}
	
	} 
	
	/**
	 * all data needed for event edit form.
	 */
	public function formedit($aData){
		
		return array_merge(	
				$this->view($aData), 
				$this->formadd(array()
			));
	}
	
	/**
	 * form add
	 */
	public function formadd($aData){
		
		$privacyApi  = Engine_Api::_()->getApi('privacy','ynmobile');
		
		$response  =  array(
			'view_options'=> $privacyApi->privacy(),
			'comment_options'=> $privacyApi->privacycomment(),
			'category_options'=> $this->listcategories($aData),
		);
		
		return $response;
	}
	
	public function detail($aData){
		$response  = $this->view($aData);
		
		$response['aGuestStatistic'] = $this->getnumberguestlist($aData);
		$response['aGuestList']      = array(
			'notAttend'=>$this->viewguestlist(array_merge($aData, array('iRSVP'=>0))),
			'maybe'=>$this->viewguestlist(array_merge($aData, array('iRSVP'=>1))),
			'going'=> $this->viewguestlist(array_merge($aData, array('iRSVP'=>2))),
		);
		
		
		return $response;
	}
	
	function get($aData)
	{
		extract($aData, EXTR_SKIP);
		
		if(!isset($sView)){
			$sView = 'upcoming';
		}

		switch ($sView) {
			case 'friend':
				return $this->friend($aData);
			case 'my':
				return $this->my($aData);
				break;
			case 'past':
				return $this->past($aData);
				break;
			default:
				return $this->upcoming($aData);
		}
	}
	
	public function accept($aData)
	{
		extract($aData);
		if (!isset($iEventId))
		{
			return array(
					'error_code' => 1,
					'error_element' => 'iEventId',
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing event identity!")
			);
		}
		$subject = $event = Engine_Api::_() -> getItem('event', $iEventId);
		if (!is_object($event))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$membership_status = $subject -> membership() -> getRow($viewer) -> active;
			$subject->membership() -> setUserApproved($viewer);
			$row = $subject -> membership() -> getRow($viewer);
	
			$row->rsvp = (isset($iRsvp) && in_array(intval($iRsvp) , array(0 , 1 , 2))) ? $iRsvp : 2 ;
			$row->save();
	
			// Set the request as handled
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
					$viewer, $subject, 'ynevent_invite');
			if( $notification )
			{
				$notification->mitigated = true;
				$notification->save();
			}
	
			// Add activity
			if (!$membership_status){
				$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
				$action = $activityApi->addActivity($viewer, $subject, 'ynevent_join');
			}
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => Zend_Registry::get("Zend_Translate")->_("You have accepted the invite to the event."),
					'event_data' =>$this->detail(array('iEventId'=> $iEventId)),
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
	
	public function reject($aData)
	{
		extract($aData);
		if (!isset($iEventId))
		{
			return array(
					'error_code' => 1,
					'error_element' => 'iEventId',
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing event identity!")
			);
		}
		$subject = $event = Engine_Api::_() -> getItem('event', $iEventId);
		if (!is_object($event))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Event is not valid!")
			);
		}
		// Process
		$viewer = Engine_Api::_()->user()->getViewer();
		$db = $subject->membership()->getReceiver()->getTable()->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$subject->membership()->removeMember($viewer);
			// Set the request as handled
			$notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
					$viewer, $subject, 'ynevent_invite');
			if( $notification )
			{
				$notification->mitigated = true;
				$notification->save();
			}
	
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => Zend_Registry::get("Zend_Translate")->_("You have ignored the invite successfully."),
					'event_data' =>$this->detail(array('iEventId'=> $iEventId)),
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
	
	
}
