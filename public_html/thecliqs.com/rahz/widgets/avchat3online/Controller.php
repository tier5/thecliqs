<?php

class Widget_Avchat3onlineController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

	
  	//GET AVCHAT 3 GENERAL SETTINGS
  	$avchat_settings = Engine_Api::_()->getApi('settings', 'core')->getFlatSetting('avchat3', array());
	
	
  		print_r($iMain->storage_path);
  	//GET AVCHAT INSTANCE
	$connectionstring = $avchat_settings['rtmp_connectionstring'];
	$connectionstring_array = explode('/', $connectionstring);
	$instance = $connectionstring_array[count($connectionstring_array) - 1];

	
	//GET CURRENT USER
	$viewer = Engine_Api::_()->user()->getViewer();
	
	
  	//SET LEVEL ID
    if(!isset($viewer->level_id) || $viewer->level_id == null){
    	$level_id = '5';
    }else{
    	$level_id = $viewer->level_id;
    }
   
	
	//GET BASE URL
	  $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
	  //$base_url = 'videochat/';
	//PARSE XML
	$xml = $this->_loadUsersList($base_url, $instance);
	
	
	
	
	
	//SET OPEN METHOD FOR AVCHAT3
	$this->open_method = $avchat_settings['open_method'];
	
	//INITIALIZE ARRAYS
	$available_rooms = array();
	$online_chatters = array();
	$visitors = 0;
	
	
	

	if($xml){
		foreach($xml->children() as $room){
			
			
			
			//GET ROOMS ATTRIBUTES
			$room_attributes = $room->attributes();
			
			//SET ROOMS ATTRIBUTES
			$available_rooms[(string)$room_attributes->id]['room_name'] = (string)$room_attributes->name;
			$available_rooms[(string)$room_attributes->id]['users_count'] = count($room->user);
			$available_rooms[(string)$room_attributes->id]['is_private'] = (string)$room_attributes->passworded;
			
			
			
			foreach($room->user as $user_in_room){
				//GET USER ATTRIBUTES
				$user_in_room_attributes = $user_in_room->attributes();
				$user_id = (string)$user_in_room_attributes->siteId;
					
				if($user_id != ''){
					//GET PROFILE PICTURE
					$user_infos = Engine_Api::_()->user()->getUser($user_id);
					
					
				  	$user_profile_picture = $this->_getViewerProfilePicture($user_id, $user_infos->photo_id);
							  
					
					//SET USER ATTRIBUTES
					$online_chatters[$user_id]['username'] = (string)$user_in_room_attributes->name;
					$online_chatters[$user_id]['has_cam'] = (string)$user_in_room_attributes->cam;
					$online_chatters[$user_id]['has_mic'] = (string)$user_in_room_attributes->mic;
					$online_chatters[$user_id]['cam_is_private'] = (string)$user_in_room_attributes->camIsPrivate;
					$online_chatters[$user_id]['profile_thumb'] = $user_profile_picture;
					$online_chatters[$user_id]['displayname'] = $user_infos->displayname;
					//print_r($user_profile_picture);
				
					
				}else{
					$visitors++;
				}
			}
		}
		
	}else{
//		return $this->setNoRender();
	}
	
					
	//SEND ARRAYS TO TEMPLATE
	$this->view->available_rooms = $available_rooms;
	$this->view->online_chatters = $online_chatters;
	$this->view->visitors = $visitors;
	
  }
  
  
	protected function _loadUsersList($base_url, $instance)
	{
		$xml = simplexml_load_file('videochat/users_'.$instance.'.xml');
		unset($xml->not_in_rooms);
    	return $xml;
	}
  
  
	protected function _getViewerProfilePicture($id_user, $id_photo){

		$iMain = Engine_Api::_()->getItem('storage_file', $id_photo);
	
		if(empty($iMain)){

	  		$profile_picture = 'application/modules/User/externals/images/nophoto_user_thumb_profile.png';	

	  	}else{
			
	  		 $profile_picture = $iMain->storage_path;
	
	  	}
		
		
		return $profile_picture;
		
	}
}