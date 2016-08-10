<?php
class Ynlistings_Plugin_Core
{
	public function onItemDeleteAfter($event)
	{
		$payload = $event -> getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$subject_id = $request -> getParam("id_subject", null);
			$type = $request -> getParam("type_parent", null);
			if ($type == 'ynlistings_listing')
			{
				if ($subject_id)
				{
					$type = $request -> getParam("case", null);
					switch ($type) {
					case 'video':
							$ynvideo_enabled = Engine_Api::_()->ynlistings()->checkYouNetPlugin('ynvideo');
							if($ynvideo_enabled)
							{
								$module_video = "ynvideo";
							}
							else {
								$module_video = "video";
							}
							$key = 'ynlistings_predispatch_url:' . $module_video . '.index.manage';
							$value = $view -> url(array(
								'controller' => 'video',
								'action' => 'manage',
								'listing_id' => $subject_id,
							), 'ynlistings_extended', true);
							$_SESSION[$key] = $value;
							break;			
					}
				}
			}
		}
	}	

	public function onItemUpdateAfter($event)
	{
		$payload = $event -> getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$subject_id = $request -> getParam("id_subject", null);
			$type = $request -> getParam("type_parent", null);
			if ($type == 'ynlistings_listing')
			{
				if ($subject_id)
				{
					$type = $payload -> getType();
					switch ($type) {
					case 'video':
							$ynvideo_enabled = Engine_Api::_()->ynlistings()->checkYouNetPlugin('ynvideo');
							if($ynvideo_enabled)
							{
								$module_video = "ynvideo";
							}
							else {
								$module_video = "video";
							}
							$profile = $request -> getParam("profile", null);
							if(!empty($profile)){
								$video_type = 'profile_video';
							}
							else {
								$video_type = 'video';
							}
							$table = Engine_Api::_() -> getDbTable('mappings', 'ynlistings');
							$select = $table -> select() -> where('listing_id =?', $subject_id) -> where('item_id = ?', $payload -> getIdentity()) -> limit(1);
							$video_row = $table -> fetchRow($select);
							if(!$video_row)
							{
								$db = $table->getAdapter();
	    						$db->beginTransaction();
									$row = $table -> createRow();
								    $row -> setFromArray(array(
								       'listing_id' => $subject_id,
								       'item_id' => $payload -> getIdentity(),
								       'user_id' => $payload -> owner_id,				       
								       'type' => $video_type,
								       'creation_date' => date('Y-m-d H:i:s'),
								       'modified_date' => date('Y-m-d H:i:s'),
								       ));
								    $row -> save();
								    $listing = Engine_Api::_() -> getItem('ynlistings_listing', $subject_id);
									$video = Engine_Api::_() -> getItem('video', $payload -> getIdentity());
									$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
									$action = $activityApi->addActivity($viewer, $listing, 'ynlistings_video_create');
									if($action) {
										$activityApi->attachActivity($action, $video);
									}
								$db->commit();
								$key = 'ynlistings_predispatch_url:' . $module_video . '.index.manage';
								if(!empty($profile)){
									$value = $view -> url(array(
										'controller' => 'video',
										'action' => 'list',
										'listing_id' => $subject_id,
									), 'ynlistings_extended', true);
									$_SESSION[$key] = $value;
								}
								else
								{
									$value = $view -> url(array(
										'controller' => 'video',
										'action' => 'manage',
										'listing_id' => $subject_id,
									), 'ynlistings_extended', true);
									$_SESSION[$key] = $value;
								}
							}
							else
							{
								$key = 'ynlistings_predispatch_url:' . $module_video . '.index.manage';
								if($video_type == 'profile_video')
								{
									$value = $view -> url(array(
										'controller' => 'video',
										'action' => 'list',
										'listing_id' => $subject_id,
									), 'ynlistings_extended', true);
									$_SESSION[$key] = $value;
								}	
								else
								{
									$value = $view -> url(array(
										'controller' => 'video',
										'action' => 'manage',
										'listing_id' => $subject_id,
									), 'ynlistings_extended', true);
									$_SESSION[$key] = $value;
								}
							}
							break;			
					}
				}
			}
		}
	}	
	
	public function onItemCreateAfter($event)
	{
		$payload = $event -> getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$subject_id = $request -> getParam("id_subject", null);
			$type = $request -> getParam("type_parent", null);
			if ($type == 'ynlistings_listing')
			{
				if ($subject_id)
				{
					$type = $payload -> getType();
					switch ($type) {
						case 'video':
							$profile = $request -> getParam("profile", null);
							if(!empty($profile)){
								$video_type = 'profile_video';
							}
							else {
								$video_type = 'video';
							}
							$table = Engine_Api::_() -> getDbTable('mappings', 'ynlistings');
							$db = $table->getAdapter();
    						$db->beginTransaction();
								$row = $table -> createRow();
							    $row -> setFromArray(array(
							       'listing_id' => $subject_id,
							       'item_id' => $payload -> getIdentity(),
							       'user_id' => $payload -> owner_id,				       
							       'type' => $video_type,
							       'creation_date' => date('Y-m-d H:i:s'),
							       'modified_date' => date('Y-m-d H:i:s'),
							       ));
							    $row -> save();
								$listing = Engine_Api::_() -> getItem('ynlistings_listing', $subject_id);
								$video = Engine_Api::_() -> getItem('video', $payload -> getIdentity());
								$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
								$action = $activityApi->addActivity($viewer, $listing, 'ynlistings_video_create');
								if($action) {
									$activityApi->attachActivity($action, $video);
								}
							$db -> commit();	
							$ynvideo_enabled = Engine_Api::_()->ynlistings()->checkYouNetPlugin('ynvideo');
							if($ynvideo_enabled)
							{
								$module_video = "ynvideo";
							}
							else {
								$module_video = "video";
							}
							$key = 'ynlistings_predispatch_url:' . $module_video . '.index.view';
							if(!empty($profile)){
								$value = $view -> url(array(
									'controller' => 'video',
									'action' => 'list',
									'listing_id' => $subject_id,
								), 'ynlistings_extended', true);
								$_SESSION[$key] = $value;
							}
							else
							{
								$value = $view -> url(array(
									'controller' => 'video',
									'action' => 'manage',
									'listing_id' => $subject_id,
								), 'ynlistings_extended', true);
								$_SESSION[$key] = $value;
							}
							break;		
					}
				}
			}
		}
	}
    
    public function onStatistics($event) {
        $table = Engine_Api::_()->getItemTable('ynlistings_listing');
        $select = new Zend_Db_Select($table->getAdapter());
        $select->from($table->info('name'), 'COUNT(*) AS count');
        $event->addResponse($select->query()->fetchColumn(0), 'listing(s)');
    }
}
