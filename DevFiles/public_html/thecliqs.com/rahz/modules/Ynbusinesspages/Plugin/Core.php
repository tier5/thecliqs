<?php
class Ynbusinesspages_Plugin_Core
{
	public function onStatistics($event)
	{
		$table = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
		$select = new Zend_Db_Select($table -> getAdapter());
		$select -> from($table -> info('name'), 'COUNT(*) AS count');
		$event -> addResponse($select -> query() -> fetchColumn(0), 'business');
	}
	
	public function onItemDeleteAfter($event)
	{
		$payload = $event->getPayload();
		
		if( is_array($payload) &&  $payload['type'] == 'ynbusinesspages_business') 
		{
			$proxyTbl = Engine_Api::_()->getApi('Layout', 'Ynbusinesspages') -> getProxyTable();
			$proxySelect = $proxyTbl->select()
				->where('subject_type = ?', $payload['type'])
				->where('subject_id = ?', $payload['identity']);
			foreach ($proxyTbl->fetchAll($proxySelect) as $proxy) {
				$proxy->delete();
			} 		
		}
		if(in_array($payload['type'], array('video', 'music_playlist', 'mp3music_album', 'event', 'blog', 'classified', 'groupbuy_deal', 'contest', 'ynlistings_listing', 'poll', 'folder', 'ynwiki_page', 'ynbusinesspages_photo', 'ynbusinesspages_post', 'ynbusinesspages_topic', 'ynmusic_song', 'ynmusic_album', 'ynultimatevideo_video')))
		{
		    $type = $payload['type'];
            if ($type == 'contest') $type = 'yncontest_contest';
			Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> deleteItem(array('type' => $type, 'item_id' => $payload['identity']));
		}
		$request = Zend_Controller_Front::getInstance() -> getRequest();    
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$business_id = $request -> getParam("business_id", $request -> getParam("subject_id", null));
			$type = $request -> getParam("parent_type", null);
			$case = $request -> getParam("case", null);
            if (is_null($case)) {
                $case = $payload['type'];
            }
			if ($type == 'ynbusinesspages_business')
			{
				if ($business_id)
				{
				    $mappings = Engine_Api::_()->getDbTable('mappings', 'ynbusinesspages');
					switch ($case) 
					{								
					case 'folder':
							if($request -> getParam("view_folder"))
							{							
								$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.file.delete';
								$value = $view -> url(array(
									'slug' => $request -> getParam("slug"),
									'folder_id' => $request -> getParam("parent_folder_id"),
									'business_id' => $business_id,
									),'ynbusinesspages_view_folder', true);
								$_SESSION[$key] = $value;
							}
							else 
							{							
								$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
								$value = $view -> url(array(
									'controller' => 'file',
									'action' => 'list',
									'business_id' => $business_id,	
								), 'ynbusinesspages_extended', true);
								$_SESSION[$key] = $value;
								
							}
							break;	
											
					case 'video':	
							$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
							$value = $view -> url(array(
								'controller' => 'video',
								'action' => 'manage',
								'subject' => 'ynbusinesspages_business_'.$business_id,	
							), 'ynbusinesspages_extended', true);
							$_SESSION[$key] = $value;
							break;
							
					case 'event':	
							$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
							$value = $view -> url(array(
								'controller' => 'event',
								'action' => 'manage',
								'business_id' => $business_id,	
							), 'ynbusinesspages_extended', true);
							$_SESSION[$key] = $value;
							break;	

                    case 'blog':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'blog',
                                'action' => 'list',
                                'business_id' => $business_id,  
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
                            
                    case 'classified':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'classified',
                                'action' => 'list',
                                'business_id' => $business_id,  
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
                            
                    case 'contest':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.my-contest.index';
                            $value = $view -> url(array(
                                'controller' => 'contest',
                                'action' => 'list',
                                'business_id' => $business_id,  
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
                            
                    case 'ynlistings_listing':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'listings',
                                'action' => 'list',
                                'business_id' => $business_id,  
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
                            
                    case 'music_playlist':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'music',
                                'action' => 'list',
                                'type' => 'music_playlist',
                                'business_id' => $business_id,  
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
                            
                    case 'poll':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'poll',
                                'action' => 'list',
                                'business_id' => $business_id,  
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
					case 'ynultimatevideo_video':
						$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
						$value = $view -> url(array(
							'controller' => 'ultimate-video',
							'action' => 'manage',
							'subject' => 'ynbusinesspages_business_'.$business_id,
						), 'ynbusinesspages_extended', true);
						$_SESSION[$key] = $value;
						break;
                    }
				}
			}
		}
	}
	
	public function onItemCreateAfter($event)
	{
		$request = Zend_Controller_Front::getInstance() -> getRequest();    
		$payload = $event -> getPayload();
		if (!is_object($payload))
		{
			return;
		}
        if(!$request)
            { return;}
		$table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
		if($payload -> getType() == 'activity_action')
		{
			$object = $payload -> getObject();
			if($object)
			{
			    if(in_array($object -> getType(), array('network')))
                {
                    return;
                }
				$owner = $table -> getOwner($object);
				if($owner && $owner -> getType() == 'ynbusinesspages_business' 
					&& (!in_array($payload -> type, array('post', 'post_self')) || (in_array($payload -> type, array('post', 'post_self')) && $payload -> object_type == 'ynbusinesspages_business')))
				{
					$payload -> subject_id = $owner -> getIdentity();
					$payload -> subject_type = 'ynbusinesspages_business';
					$payload -> save();
				}
			}
		}
		$business_session = new Zend_Session_Namespace('ynbusinesspages_business');
		$business_id = $business_session -> businessId;
		$view = Zend_Registry::get('Zend_View');
		if($business_id)
		{
			$owner_id = $business_id;
			$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id);
			$owner_type = 'ynbusinesspages_business';
			switch ($payload -> getType()) 
			{
				case 'activity_action':
					$payload -> subject_id = $business_id;
					$payload -> subject_type = 'ynbusinesspages_business';
					if($payload -> type == 'share')
					{
						$payload -> object_id = $business_id;
						$payload -> object_type = 'ynbusinesspages_business';
					}
					$payload -> save();
					break;
				
				case 'ynbusinesspages_photo':
				case 'ynbusinesspages_post':
				case 'ynbusinesspages_topic':
				case 'ynbusinesspages_review':
				case 'video':
				case 'music_playlist':
				case 'mp3music_album':
				case 'event':
				case 'folder':
                case 'file':
				case 'ynwiki_page':
				case 'poll':
				case 'ynlistings_listing':
				case 'contest':
				case 'groupbuy_deal':
				case 'classified':
				case 'blog':
				case 'ynmusic_song':
				case 'ynmusic_album':
				case 'ynultimatevideo_video':
                    
                    $type = $payload -> getType();
                    if ($type == 'contest') $type = 'yncontest_contest';
					$row = $table -> createRow();
                    
				    $row -> setFromArray(array(
				       'business_id' => $owner_id,
				       'item_id' => $payload -> getIdentity(),
				       'owner_id' => $owner_id,				       
				       'owner_type' => $owner_type,				       
				       'type' => $type,
				       'creation_date' => date('Y-m-d H:i:s'),
				       'modified_date' => date('Y-m-d H:i:s'),
				       ));
					$row -> save();
					
					if($payload -> getType() == 'video')
					{
						if(Engine_Api::_() -> hasModuleBootstrap('ynvideo'))
						{
							$module_video = "ynvideo";
						}
						else 
						{
							//ynvideo already send feed
							$video = Engine_Api::_()->getItem('video', $payload -> getIdentity());
							$video -> parent_type = 'ynbusinesspages_business';
							$video -> parent_id = $business_id;
							$video -> save();
							$viewer = Engine_Api::_() -> user() -> getViewer();
							$item = Engine_Api::_() -> getItem($video -> parent_type, $video -> parent_id );
							$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
							$action = $activityApi->addActivity($viewer, $item, 'ynbusinesspages_video_create');
							if($action) {
								$activityApi->attachActivity($action, $video);
							}
							// Rebuild privacy
							$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
							foreach ($actionTable->getActionsByObject($video) as $action)
							{
								$actionTable -> resetActivityBindings($action);
							}
							$module_video = "video";
						}
						
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new video'));
						if($payload -> type == 0)
							$key = 'ynbusinesspages_predispatch_url:' . $module_video . '.index.manage';
						else
							$key = 'ynbusinesspages_predispatch_url:' . $module_video . '.index.view';
						
						$value = $view -> url(array(
							'controller' => 'video',
							'action' => 'manage',
							'subject' => $business->getGuid(),
						), 'ynbusinesspages_extended', true);
						$_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'music_playlist') {
						
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new music'));
						$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.playlist.view';
						$value = $view -> url(array(
							'controller' => 'music',
							'action' => 'list',
							'business_id' => $business_id,
							'type' => 'music',
						), 'ynbusinesspages_extended', true);
						$_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'mp3music_album') {
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new music'));
						$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.album.edit';
						$value = $view -> url(array(
							'controller' => 'music',
							'action' => 'list',
							'business_id' => $business_id,
							'type' => 'mp3music',
						), 'ynbusinesspages_extended', true);
						$_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'event') {
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new event'));
						if($request -> getParam('module') == 'event')
								$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.profile.index';
							else
								$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
						$value = $view -> url(array(
							'controller' => 'event',
							'action' => 'manage',
							'business_id' => $business_id,
						), 'ynbusinesspages_extended', true);
						$_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'folder') {
						$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
						// Add activity
						$viewer = Engine_Api::_() -> user() -> getViewer();
						$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_folder_create');
						if($action) {
							$activityApi->attachActivity($action, $payload);
						}
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new folder'));
						$key = 'ynbusinesspages_predispatch_url:' . 'ynbusinesspages' . '.profile.index';
						$value = $view -> url(array(
						'controller' => 'file',
						'action' => 'list',
						'business_id' => $business_id,
						), 'ynbusinesspages_extended', true);
						$_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'ynwiki_page') {
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new page'));
						$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.set-permission';
						$value = $view -> url(array(
							'controller' => 'wiki',
							'action' => 'list',
							'business_id' => $business_id,
							),'ynbusinesspages_extended', true);
						$_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'blog') {
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new blog'));
						$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                        $value = $view -> url(array(
                            'controller' => 'blog',
                            'action' => 'list',
                            'business_id' => $business_id,
                        ), 'ynbusinesspages_extended', true);
                        $_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'classified') {
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new classified'));
						$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.success';
                        $value = $view -> url(array(
                            'controller' => 'classified',
                            'action' => 'list',
                            'business_id' => $business_id,
                        ), 'ynbusinesspages_extended', true);
                        $_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'groupbuy_deal') {
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new classified listing'));
					 	$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage-selling';
                        $value = $view -> url(array(
                            'controller' => 'groupbuy',
                            'action' => 'list',
                            'business_id' => $business_id,
                        ), 'ynbusinesspages_extended', true);
                        $_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'contest') {
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new contest'));
					 	$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.my-contest.index';
                        $value = $view -> url(array(
                            'controller' => 'contest',
                            'action' => 'list',
                            'business_id' => $business_id,
                        ), 'ynbusinesspages_extended', true);
                        $_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'ynlistings_listing') {
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new listing'));
					 	$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.view';
                        $value = $view -> url(array(
                            'controller' => 'listings',
                            'action' => 'list',
                            'business_id' => $business_id,
                        ), 'ynbusinesspages_extended', true);
                        $_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'ynmusic_album') {
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new social music album'));
					 	$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.albums.edit';
                        $value = $view -> url(array(
                            'controller' => 'social-music',
                            'action' => 'list',
                            'type' => 'album',
                            'business_id' => $business_id,
                        ), 'ynbusinesspages_extended', true);
                        $_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'ynmusic_alonesong') {
						// send notification to followers
						$item = Engine_Api::_()->getItem('ynmusic_alonesong', $payload->getIdentity());
						$numOfSongs = count($item->song_ids);
						$business -> sendNotificationToFollowers($view -> translate(array('add %s social music song', '%s social music songs', $numOfSongs), $numOfSongs));
					 	$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.songs.manage';
                        $value = $view -> url(array(
                            'controller' => 'social-music',
                            'action' => 'list',
                            'type' => 'song',
                            'business_id' => $business_id,
                        ), 'ynbusinesspages_extended', true);
                        $_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'ynultimatevideo_video') {
						$business -> sendNotificationToFollowers($view -> translate('new ultimate video'));
						$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.view';
						$value = $view -> url(array(
							'controller' => 'ultimate-video',
							'action' => 'list',
							'business_id' => $business_id,
						), 'ynbusinesspages_extended', true);
						$_SESSION[$key] = $value;
					}
					else if($payload -> getType() == 'poll') {
						// send notification to followers
						$business -> sendNotificationToFollowers($view -> translate('new poll'));
					 	$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.poll.view';
                        $value = $view -> url(array(
                            'controller' => 'poll',
                            'action' => 'list',
                            'business_id' => $business_id,
                        ), 'ynbusinesspages_extended', true);
                        $_SESSION[$key] = $value;
                        break;
					}
					break;
			}
		}
		else 
		{
			$business_id = $request -> getParam("business_id", $request -> getParam("subject_id", null));
			$type = $request -> getParam("parent_type", null);
			
			if ($type == 'ynbusinesspages_business')
			{
				$widget_id = $request -> getParam("tab", null);
				if ($business_id)
				{
					$type = $payload -> getType();
					$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
					$viewer = Engine_Api::_() -> user() -> getViewer();
					$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $business_id);
					switch ($type) 
					{
						case 'video':
							$row = $table -> createRow();
						    $row -> setFromArray(array(
						       'business_id' => $business_id,
						       'item_id' => $payload -> getIdentity(),
						       'type' => 'video',
						       'owner_id' => $payload -> owner_id,				       
			       			   'owner_type' => 'user',		
						       'creation_date' => date('Y-m-d H:i:s'),
						       'modified_date' => date('Y-m-d H:i:s'),
						       ));
							$row -> save();
							
							//ynvideo already send feed
							$video = Engine_Api::_()->getItem('video', $payload -> getIdentity());
							$video -> parent_type = 'ynbusinesspages_business';
							$video -> parent_id = $business_id;
							$video -> save();
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_video_create');
							if($action) {
								$activityApi->attachActivity($action, $video);
							}
							// Rebuild privacy
							$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
							foreach ($actionTable->getActionsByObject($video) as $action)
							{
								$actionTable -> resetActivityBindings($action);
							}
							
							if(Engine_Api::_() -> hasModuleBootstrap('ynvideo'))
							{
								$module_video = "ynvideo";
							}
							else 
							{
								$module_video = "video";
							}
							
							// send notification to followers
							$business -> sendNotificationToFollowers($view -> translate('new video'));
							if($payload -> type == 0)
                                $key = 'ynbusinesspages_predispatch_url:' . $module_video . '.index.manage';
                            else
                                $key = 'ynbusinesspages_predispatch_url:' . $module_video . '.index.view';
							$value = $view -> url(array(
								'controller' => 'video',
								'action' => 'manage',
								'subject' => $business->getGuid(),
							), 'ynbusinesspages_extended', true);
							$_SESSION[$key] = $value;
							break;
						
						case 'music_playlist':	
							$row = $table -> createRow();
						    $row -> setFromArray(array(
						       'business_id' => $business_id,
						       'item_id' => $payload -> getIdentity(),
						       'type' => 'music_playlist',
						       'owner_id' => $payload -> owner_id,				       
			       			   'owner_type' => 'user',	
						       'creation_date' => date('Y-m-d H:i:s'),
						       'modified_date' => date('Y-m-d H:i:s'),
						       ));
							$row -> save();
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_music_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
							// send notification to followers
							$business -> sendNotificationToFollowers($view -> translate('new music playlist'));
							
							$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.playlist.view';
							$value = $view -> url(array(
								'controller' => 'music',
								'action' => 'list',
								'business_id' => $business_id,
								'type' => 'music',
							), 'ynbusinesspages_extended', true);
							$_SESSION[$key] = $value;
							break;
						
						case 'mp3music_album':
							$table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
							try {
								$row = $table -> createRow();
							    $row -> setFromArray(array(
							       'business_id' => $business_id,
							       'item_id' => $payload -> getIdentity(),
							       'type' => 'mp3music_album',
							       'owner_id' => $payload -> user_id,				       
				       			   'owner_type' => 'user',	
							       'creation_date' => date('Y-m-d H:i:s'),
							       'modified_date' => date('Y-m-d H:i:s'),
							       ));
							    $row -> save();
								
							}
							catch (Exception $e) {
								die($e);
							}
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_mp3music_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
							// send notification to followers
							$business -> sendNotificationToFollowers($view -> translate('new mp3music album'));
							
							$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.album.edit';
							$value = $view -> url(array(
								'controller' => 'music',
								'action' => 'list',
								'business_id' => $business_id,
								'type' => 'mp3music',
							), 'ynbusinesspages_extended', true);
							$_SESSION[$key] = $value;
							break;
							
						case 'event':
							$row = $table -> createRow();
						    $row -> setFromArray(array(
						       'business_id' => $business_id,
						       'item_id' => $payload -> getIdentity(),
						       'type' => 'event',
						       'owner_id' => $payload -> user_id,				       
			       			   'owner_type' => 'user',	
						       'creation_date' => date('Y-m-d H:i:s'),
						       'modified_date' => date('Y-m-d H:i:s'),
						       ));
						    $row -> save();
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_event_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
							// send notification to followers
							$business -> sendNotificationToFollowers($view -> translate('new event'));
							if($request -> getParam('module') == 'event')
								$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.profile.index';
							else
								$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
							$value = $view -> url(array(
								'controller' => 'event',
								'action' => 'manage',
								'business_id' => $business_id,
							), 'ynbusinesspages_extended', true);
							$_SESSION[$key] = $value;
							break;
                            
                        case 'blog':
                            $table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
                            try {
                                $row = $table -> createRow();
                                $row -> setFromArray(array(
                                   'business_id' => $business_id,
                                   'item_id' => $payload -> getIdentity(),
                                   'type' => 'blog',
                                   'owner_id' => $payload -> owner_id,                     
                                   'owner_type' => 'user',  
                                   'creation_date' => date('Y-m-d H:i:s'),
                                   'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                $row -> save();
                                
                            }
                            catch (Exception $e) {
                                die($e);
                            }
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_blog_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
                            // send notification to followers
                            $business -> sendNotificationToFollowers($view -> translate('new blog entry'));
                            
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'blog',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
                            
                        case 'classified':
                            $table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
                            try {
                                $row = $table -> createRow();
                                $row -> setFromArray(array(
                                   'business_id' => $business_id,
                                   'item_id' => $payload -> getIdentity(),
                                   'type' => 'classified',
                                   'owner_id' => $payload -> owner_id,                     
                                   'owner_type' => 'user',  
                                   'creation_date' => date('Y-m-d H:i:s'),
                                   'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                $row -> save();
                                
                            }
                            catch (Exception $e) {
                                die($e);
                            }
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_classified_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
                            // send notification to followers
                            $business -> sendNotificationToFollowers($view -> translate('new classified listing'));
                            
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'classified',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.view';
                            $_SESSION[$key] = $value;
                            break;
                            
                        case 'groupbuy_deal':
                            $table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
                            try {
                                $row = $table -> createRow();
                                $row -> setFromArray(array(
                                   'business_id' => $business_id,
                                   'item_id' => $payload -> getIdentity(),
                                   'type' => 'groupbuy_deal',
                                   'owner_id' => $payload -> user_id,                     
                                   'owner_type' => 'user',  
                                   'creation_date' => date('Y-m-d H:i:s'),
                                   'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                $row -> save();
                                
                            }
                            catch (Exception $e) {
                                die($e);
                            }
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_deal_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
                            // send notification to followers
                            $business -> sendNotificationToFollowers($view -> translate('new group buy deal'));
                            
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage-selling';
                            $value = $view -> url(array(
                                'controller' => 'groupbuy',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
                            
                        case 'contest':
                            $table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
                            try {
                                $row = $table -> createRow();
                                $row -> setFromArray(array(
                                   'business_id' => $business_id,
                                   'item_id' => $payload -> getIdentity(),
                                   'type' => 'yncontest_contest',
                                   'owner_id' => $payload -> user_id,                     
                                   'owner_type' => 'user',  
                                   'creation_date' => date('Y-m-d H:i:s'),
                                   'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                $row -> save();
                                
                            }
                            catch (Exception $e) {
                                die($e);
                            }
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_contest_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
                            // send notification to followers
                            $business -> sendNotificationToFollowers($view -> translate('new contest'));
                            
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.my-contest.index';
                            $value = $view -> url(array(
                                'controller' => 'contest',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
                            
                        case 'ynlistings_listing':
                            $table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
                            try {
                                $row = $table -> createRow();
                                $row -> setFromArray(array(
                                   'business_id' => $business_id,
                                   'item_id' => $payload -> getIdentity(),
                                   'type' => 'ynlistings_listing',
                                   'owner_id' => $payload -> user_id,                     
                                   'owner_type' => 'user',  
                                   'creation_date' => date('Y-m-d H:i:s'),
                                   'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                $row -> save();
                                
                            }
                            catch (Exception $e) {
                                die($e);
                            }
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_listing_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
                            // send notification to followers
                            $business -> sendNotificationToFollowers($view -> translate('new listing'));
                            
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.view';
                            $value = $view -> url(array(
                                'controller' => 'listings',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
                            
                        case 'poll':
                            $table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
                            try {
                                $row = $table -> createRow();
                                $row -> setFromArray(array(
                                   'business_id' => $business_id,
                                   'item_id' => $payload -> getIdentity(),
                                   'type' => 'poll',
                                   'owner_id' => $payload -> user_id,                     
                                   'owner_type' => 'user',  
                                   'creation_date' => date('Y-m-d H:i:s'),
                                   'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                $row -> save();
                                
                            }
                            catch (Exception $e) {
                                die($e);
                            }
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_poll_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
                            // send notification to followers
                            $business -> sendNotificationToFollowers($view -> translate('new poll'));
                            
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.poll.view';
                            $value = $view -> url(array(
                                'controller' => 'poll',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
							
						case 'folder':
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_folder_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							// send notification to followers
							$business -> sendNotificationToFollowers($view -> translate('new folder'));
							
							if($request -> getParam("view_folder"))
							{
								$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.folder.view';
								$value = $view -> url(array(
								'slug' => $request -> getParam("slug"),
								'folder_id' => $request -> getParam("parent_folder_id"),
								'business_id' => $business_id,
								),'ynbusinesspages_view_folder', true);
								$_SESSION[$key] = $value;
							}
							else
							{
								$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
								$value = $view -> url(array(
								'controller' => 'file',
								'action' => 'list',
								'business_id' => $business_id,
								), 'ynbusinesspages_extended', true);
								$_SESSION[$key] = $value;
							}
							break;
							
						case 'ynmusic_album':
                            $table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
                            try {
                                $row = $table -> createRow();
                                $row -> setFromArray(array(
                                   'business_id' => $business_id,
                                   'item_id' => $payload -> getIdentity(),
                                   'type' => 'ynmusic_album',
                                   'owner_id' => $payload -> user_id,                     
                                   'owner_type' => 'user',  
                                   'creation_date' => date('Y-m-d H:i:s'),
                                   'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                $row -> save();
                                
                            }
                            catch (Exception $e) {
                                die($e);
                            }
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_ynmusic_album_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
                            // send notification to followers
                            $business -> sendNotificationToFollowers($view -> translate('new social music album'));
                            
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.albums.edit';
                            $value = $view -> url(array(
                                'controller' => 'social-music',
                                'action' => 'list',
                                'type' => 'album',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                            break;
						
						case 'ynmusic_song':
                            $table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
                            try {
                                $row = $table -> createRow();
                                $row -> setFromArray(array(
                                   'business_id' => $business_id,
                                   'item_id' => $payload -> getIdentity(),
                                   'type' => 'ynmusic_song',
                                   'owner_id' => $payload -> user_id,                     
                                   'owner_type' => 'user',  
                                   'creation_date' => date('Y-m-d H:i:s'),
                                   'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                $row -> save();
                                
                            }
                            catch (Exception $e) {
                                die($e);
                            }
                            break;
						
						case 'ynmusic_alonesong':
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_ynmusic_songs_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
							// send notification to followers
							$item = Engine_Api::_()->getItem('ynmusic_alonesong', $payload->getIdentity());
							$numOfSongs = count($item->song_ids);
							$business -> sendNotificationToFollowers($view -> translate(array('add %s social music song', '%s social music songs', $numOfSongs), $numOfSongs));
						 	$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.songs.manage';
	                        $value = $view -> url(array(
	                            'controller' => 'social-music',
	                            'action' => 'list',
	                            'type' => 'song',
	                            'business_id' => $business_id,
	                        ), 'ynbusinesspages_extended', true);
	                        $_SESSION[$key] = $value;
                            break;
							
						case 'ynwiki_page':
							
							// Add activity
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_page_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							
                            // send notification to followers
                            $business -> sendNotificationToFollowers($view -> translate('new page'));
						
							$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.set-permission';
							$value = $view -> url(array(
								'controller' => 'wiki',
								'action' => 'list',
								'business_id' => $business_id,
								),'ynbusinesspages_extended', true);
							$_SESSION[$key] = $value;
							break;
						case 'ynultimatevideo_video':
							$action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_ynultimatevideo_video_create');
							if($action) {
								$activityApi->attachActivity($action, $payload);
							}
							$table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
							try {
								$row = $table -> createRow();
								$row -> setFromArray(array(
									'business_id' => $business_id,
									'item_id' => $payload -> getIdentity(),
									'type' => 'ynultimatevideo_video',
									'owner_id' => $payload -> owner_id,
									'owner_type' => 'user',
									'creation_date' => date('Y-m-d H:i:s'),
									'modified_date' => date('Y-m-d H:i:s'),
								));
								$row -> save();
							}
							catch (Exception $e) {
								die($e);
							}
							break;
					}
				}
			}
		}
	}
	
	public function addActivity($event)
    {
    	$payload = $event -> getPayload();
		$subject = $payload['subject'];
		$object = $payload['object'];

		// Only for object=business
		if ($object instanceof Ynbusinesspages_Model_Business)
		{
			$event -> addResponse(array(
				'type' => 'business',
				'identity' => $object -> getIdentity()
			));
		}
  	}
	
	public function getActivity($event)
	{
		// Detect viewer and subject
		$payload = $event -> getPayload();
		$user = null;
		$subject = null;
		if ($payload instanceof User_Model_User)
		{
			$user = $payload;
		}
		else
		if (is_array($payload))
		{
			if (isset($payload['for']) && $payload['for'] instanceof User_Model_User)
			{
				$user = $payload['for'];
			}
			if (isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract)
			{
				$subject = $payload['about'];
			}
		}
		if (null === $user)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			if ($viewer -> getIdentity())
			{
				$user = $viewer;
			}
		}
		if (null === $subject && Engine_Api::_() -> core() -> hasSubject())
		{
			$subject = Engine_Api::_() -> core() -> getSubject();
		}

		// Get feed settings
		$content = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('activity.content', 'everyone');

		// Get event memberships
		if ($user)
		{
			$data = Engine_Api::_() -> getDbtable('membership', 'ynbusinesspages') -> getMembershipsOfIds($user);
			if (!empty($data) && is_array($data))
			{
				$event -> addResponse(array(
					'type' => 'business',
					'data' => $data,
				));
			}
		}
	}

	public function onRenderLayoutDefault($event) {
        // Arg should be an instance of Zend_View
        $view = $event->getPayload();
        $request = Zend_Controller_Front::getInstance() -> getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $isMobile = Engine_Api::_()->ynbusinesspages()->isMobile2();
		$isPages = (Engine_Api::_()->getApi('settings', 'core')->getSetting('ynbusinesspages_compare_allpages', 0)) ? true : ($module == 'ynbusinesspages');
        $business_session = new Zend_Session_Namespace('ynbusinesspages_business');
        $business_id = $business_session -> businessId;
        if($isPages && !$business_id && ($view instanceof Zend_View) && !$isMobile && !(($module == 'ynbusinesspages') && ($controller == 'compare') && ($action == 'index'))) {
            $view->headScript()->prependFile($view->layout()->staticBaseUrl . 'application/modules/Ynbusinesspages/externals/scripts/render-compare-bar.js');
        }
    }
	
	public function onItemUpdateAfter($event)
	{
	    
		$payload = $event -> getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$business_id = $request -> getParam("business_id", $request -> getParam("subject_id", null));
			$type = $request -> getParam("parent_type", null);
			
			if ($type == 'ynbusinesspages_business')
			{
				if ($business_id)
				{
					$type = $payload -> getType();
					switch ($type) 
					{
						case 'music_playlist':
							$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.playlist.view';					
							$value = $view -> url(array(
								'controller' => 'music',
								'action' => 'list',
								'business_id' => $business_id,
								'type' => 'music',
							), 'ynbusinesspages_extended', true);
							$_SESSION[$key] = $value;
							break;
							
						case 'mp3music_album':
							$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.album.manage';
							$value = $view -> url(array(
								'controller' => 'music',
								'action' => 'list',
								'business_id' => $business_id,
								'type' => 'mp3music',
							), 'ynbusinesspages_extended', true);
							$_SESSION[$key] = $value;
						
							break;
                            
                        case 'blog':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'blog',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                        
                            break;
                            
                        case 'classified':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'classified',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                        
                            break;
                            
                        case 'groupbuy_deal':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage-selling';
                            $value = $view -> url(array(
                                'controller' => 'groupbuy',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                        
                            break;
                            
                        case 'contest':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.my-contest.view';
                            $value = $view -> url(array(
                                'controller' => 'contest',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                        
                            break;
                            
                        case 'ynlistings_listing':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'listings',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                        
                            break;
                            
                        case 'poll':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.manage';
                            $value = $view -> url(array(
                                'controller' => 'poll',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                        
                            break;
                            
                        case 'ynjobposting_job':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.jobs.view';
                            $value = $view -> url(array(
                                'controller' => 'job',
                                'action' => 'list',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                        
                            break;
                            
					case 'folder':
							$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.folder.view';
							$value = $view -> url(array(
								'controller' => 'file',
								'action' => 'list',
								'business_id' => $business_id,	
							), 'ynbusinesspages_extended', true);
							$_SESSION[$key] = $value;
						
							break;	
							
					case 'event':
							$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.profile.index';
							$value = $view -> url(array(
								'controller' => 'event',
								'action' => 'manage',
								'business_id' => $business_id,	
							), 'ynbusinesspages_extended', true);
							$_SESSION[$key] = $value;
							break;	
					
					case 'ynmusic_album':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.albums.view';
                            $value = $view -> url(array(
                                'controller' => 'social-music',
                                'action' => 'list',
                                'type' => 'album',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                        
                            break;
							
					case 'ynmusic_song':
                            $key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.songs.view';
                            $value = $view -> url(array(
                                'controller' => 'social-music',
                                'action' => 'list',
                                'type' => 'song',
                                'business_id' => $business_id,
                            ), 'ynbusinesspages_extended', true);
                            $_SESSION[$key] = $value;
                        
                            break;
								
					case 'video':
							$ynvideo_enabled = Engine_Api::_() -> hasModuleBootstrap('ynvideo');
                            $table = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages');
                            $select = $table -> select() -> where('business_id = ?', $business_id) -> where('item_id = ?', $payload -> getIdentity()) -> where('type = ?', 'video') -> limit(1);
                            $video_row = $table -> fetchRow($select);
                            if (!$video_row) {
                                $business_session = new Zend_Session_Namespace('ynbusinesspages_business');
                                $owner_id = $business_session -> businessId;
                                $row = $table -> createRow();
                                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                                $viewer = Engine_Api::_() -> user() -> getViewer();
                                $business = Engine_Api::_()->getItem('ynbusinesspages_business', $business_id);
                                if ($owner_id) {
                                    $row -> setFromArray(array(
                                       'business_id' => $owner_id,
                                       'item_id' => $payload -> getIdentity(),
                                       'owner_id' => $owner_id,                    
                                       'owner_type' => 'ynbsusinesspages_business',                    
                                       'type' => $type,
                                       'creation_date' => date('Y-m-d H:i:s'),
                                       'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                   if(!Engine_Api::_() -> hasModuleBootstrap('ynvideo')) {
                                        //ynvideo already send feed
                                        $video = Engine_Api::_()->getItem('video', $payload -> getIdentity());
                                        $video -> parent_type = 'ynbusinesspages_business';
                                        $video -> parent_id = $business_id;
                                        $video -> save();
                                        $item = Engine_Api::_() -> getItem($video -> parent_type, $video -> parent_id );
                                        $action = $activityApi->addActivity($viewer, $item, 'ynbusinesspages_video_create');
                                        if($action) {
                                            $activityApi->attachActivity($action, $video);
                                        }
                                        // Rebuild privacy
                                        $actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
                                        foreach ($actionTable->getActionsByObject($video) as $action) {
                                            $actionTable -> resetActivityBindings($action);
                                        }
                                    }
                                     
                                }
                                else {
                                    $row -> setFromArray(array(
                                       'business_id' => $business_id,
                                       'item_id' => $payload -> getIdentity(),
                                       'type' => 'video',
                                       'owner_id' => $payload -> owner_id,                     
                                       'owner_type' => 'user',      
                                       'creation_date' => date('Y-m-d H:i:s'),
                                       'modified_date' => date('Y-m-d H:i:s'),
                                       ));
                                    $row -> save();
                                    
                                    //ynvideo already send feed
                                    $video = Engine_Api::_()->getItem('video', $payload -> getIdentity());
                                    $video -> parent_type = 'ynbusinesspages_business';
                                    $video -> parent_id = $business_id;
                                    $video -> save();
                                    
                                    // Add activity
                                    $action = $activityApi->addActivity($viewer, $business, 'ynbusinesspages_video_create');
                                    if($action) {
                                        $activityApi->attachActivity($action, $video);
                                    }
                                    // Rebuild privacy
                                    $actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
                                    foreach ($actionTable->getActionsByObject($video) as $action)
                                    {
                                        $actionTable -> resetActivityBindings($action);
                                    }
                                }
                                // send notification to followers
                                $business -> sendNotificationToFollowers($view -> translate('new video'));
                            }
                            if($ynvideo_enabled) {
                                $module_video = "ynvideo";
                            }
                            else {
                                $module_video = "video";
                            }
                            
							$key = 'ynbusinesspages_predispatch_url:' . $module_video . '.index.manage';
								$value = $view -> url(array(
									'controller' => 'video',
									'action' => 'manage',
									'subject' => 'ynbusinesspages_business_'.$business_id,	
								), 'ynbusinesspages_extended', true);
								$_SESSION[$key] = $value;
							break;		
					case 'ynultimatevideo_video':
						$key = 'ynbusinesspages_predispatch_url:' . $request -> getParam('module') . '.index.view';
						$value = $view -> url(array(
							'controller' => 'ultimate-video',
							'action' => 'manage',
							'business_id' => $business_id,
						), 'ynbusinesspages_extended', true);
						$_SESSION[$key] = $value;
						break;
					}
				}
			}
		}
	}	
}
