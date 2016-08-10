<?php

class Ynmobile_Engine_Api extends Engine_Api
{

	static $type_maps = array(
		'group' => 'group',
		'group_album' => 'advgroup_album',
		'group_category' => 'advgroup_category',
		'group_list' => 'advgroup_list',
		'group_list_item' => 'advgroup_list_item',
		'group_photo' => 'advgroup_photo',
		'group_post' => 'advgroup_post',
		'group_topic' => 'advgroup_topic',
		'album' => 'advalbum_album',
		'album_photo' => 'advalbum_photo',
		'photo' => 'advalbum_photo',
		'forum'  => 'ynforum_forum', 
		'forum_category'  => 'ynforum_category',
		'forum_container'  => 'ynforum_container',
		'forum_post'  => 'ynforum_post',
		'forum_signature'  => 'ynforum_signature',
		'forum_topic'  => 'ynforum_topic',
		'forum_list'  => 'ynforum_list',
		'forum_list_item'  => 'ynforum_list_item',
		);
	// Item handling stuff

	  /**
	   * Checks if the item of $type has been registered
	   * 
	   * @param string $type
	   * @return bool
	   */
	  public function hasItemType($type)
	  {
	    $this->_loadItemInfo();
	    return isset($this->_itemTypes[$type]) || isset(self::$type_maps[$type]);
	  }

	
	function getItemInfo($type, $key  = null){

		if(isset(self::$type_maps[$type])){
			$type =  self::$type_maps[$type];
		}

		return parent::getItemInfo($type, $key);
	}
}