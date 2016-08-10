<?php
class Yncontest_Model_Entry extends Core_Model_Item_Abstract{
	
	//protected $_type = 'entry';
	protected $_parent_type = "contest";
	public function getTitle(){
		
		if(isset($this->entry_name)){
			return $this->entry_name;
		}
		return null;
	}
	
	public function getSlug($str = null) {
		return trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($this -> entry_name))), '-');
	}
	public function isFavourited($user_id = 0){
		if($user_id == 0){
			return false;
		}
		$sql  = "select favourite_id from engine4_yncontest_entriesfavourites where user_id=$user_id and entry_id={$this->entry_id}";
		$row = Engine_Db_Table::getDefaultAdapter()->fetchOne($sql);
		return (bool)$row;
	}
	
	public function isVoted($user_id = 0){
		if($user_id == 0){
			return false;
		}
		$sql  = "select vote_id from engine4_yncontest_votes where user_id=$user_id and entry_id={$this->entry_id}";
		$row = Engine_Db_Table::getDefaultAdapter()->fetchOne($sql);
		return (bool)$row;
	}
	public function checkCompare(){
		if($this->entry_type == "advalbum")
			return true;
		return false;
	}
	public function checkFollow($contest_id)
	{
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$followTable = Engine_Api::_()->getDbtable('entriesfollows', 'yncontest');
		$select = $followTable->select()
		->where('contest_id = ?', $contest_id)
		->where('entry_id = ?', $this->entry_id)
		->where('user_id = ?', $viewer->getIdentity());
		
		$row = $followTable->fetchRow($select);
		
		if($row)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public function getModelPlugin(){
		$obj = NULL;
		switch($this->entry_type)
		{
			case 'ynblog' :
				$blogPlugin = $this->getPluginsBlog();	
				$obj = Engine_Api::_() -> getDbtable('blogs', $blogPlugin) -> find($this->item_id) -> current();
				break;
			case 'advalbum' :
				$albumPlugin = Engine_Api::_()->yncontest()->getPluginsAlbum();
				$obj = Engine_Api::_() -> getDbtable('photos', $albumPlugin) -> find($this->item_id) -> current();
				break;
			case 'ynvideo' :
				//$videoPlugin = Engine_Api::_()->yncontest()->getPluginsVideo();
				//$obj = Engine_Api::_() -> getDbtable('videos', $videoPlugin) -> find($this->item_id) -> current();
				$obj = Engine_Api::_() -> getItemTable('video') -> find($this->item_id) -> current();
				
				break;
		}
		
		return $obj;
	}
	
	public function getPhotoUrl($type = null){		
		if($this->entry_type == 'ynblog'){			
			$user = Engine_Api::_() -> user() -> getUser($this -> user_id);		
			return $user->getPhotoUrl($type);			
		}
		if($this->entry_type == 'mp3music'){			
			if(parent::getPhotoUrl($type) == '')
			{	
				return "application/modules/Yncontest/externals/images/nophoto_album_song_thumb_icon.png";
			}
			return parent::getPhotoUrl($type);
		}

		if($this->entry_type == 'ynmusic') {
			return "application/modules/Yncontest/externals/images/nophoto_album_song_thumb_icon.png";
		}
			
		if($this->entry_type == 'ynultimatevideo'){
			if(parent::getPhotoUrl($type) == '')
			{
				return "application/modules/Yncontest/externals/images/nophoto_video_thumb_profile.png";
			}
			return parent::getPhotoUrl($type);
		}

		return parent::getPhotoUrl($type);
	}
	
	public function sendNotMailOwner($user1, $user2, $keyNot, $keyAct, $params = null ){
		
		if(!empty($keyNot))	{ 
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			
			$contest = Engine_Api::_()->getItem('contest', $this->contest_id);				
			$notifyApi -> addNotification($user1,$user2, $contest, $keyNot, $params);
		}
		if(!empty($keyAct))	{
			$action = @Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user1, $this, $keyAct);
			if( $action != null )
			{
				Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $this);
			}
		}
	}
	public function sendNotMailFollwer($admin, $keyNot){
	
		if(!empty($keyNot))	{
				
			$follow_table = Engine_Api::_()->getItemTable('yncontest_follows');
			$followUsers = $follow_table->getUserFolowContest($this->contest_id);
			foreach($followUsers as $followUser){
				//send notification
				$user = Engine_Api::_() -> user() -> getUser($followUser -> user_id);
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$notifyApi -> addNotification($user,$admin, $this, $keyNot);
				
			}
			
			$follow_table = Engine_Api::_()->getItemTable('yncontest_entriesfollows');
			$followUsers = $follow_table->getUserFolowEntries($this->entry_id);
			foreach($followUsers as $followUser){
				//send notification
				$user = Engine_Api::_() -> user() -> getUser($followUser -> user_id);
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$notifyApi -> addNotification($user,$admin, $this, $keyNot);
				
			}
				
	
		}
	
	}
	
	public function checkCotOwner(){
		$contest = Engine_Api::_()->getItemTable('contest')->find($this->contest_id)->current();
		//echo $contest->contest_id;die;
		$viewer = Engine_Api::_()->user()->getViewer();			
		if($contest->IsOwner($viewer))
			return true;
		return false;
	}
	
	
	public function checkFavourite($contest_id)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$favouriteTable = Engine_Api::_()->getDbtable('entriesfavourites', 'yncontest');
		$select = $favouriteTable->select()
		->where('entry_id = ?', $this->entry_id)
		->where('contest_id = ?', $contest_id)
		->where('user_id = ?', $viewer->getIdentity());
		$row = $favouriteTable->fetchRow($select);
		if($row)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	
	
	public function checkVote()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$favouriteTable = Engine_Api::_()->getDbtable('votes', 'yncontest');
		$select = $favouriteTable->select()
		->where('entry_id = ?', $this->entry_id)
		->where('user_id = ?', $viewer->getIdentity());
		$row = $favouriteTable->fetchRow($select);
		if($row)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public function comments() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}
	
	public function likes() {
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}
	
	public function getHref($params = array())
	{
		$slug = $this->getSlug();
	
		$params = array_merge(array(
				'route' => 'yncontest_myentries',
				'action' => 'view',
				'reset' => true,
				'id' => $this->entry_id,
				'slug' => $slug,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance()->getRouter()
		->assemble($params, $route, $reset);
	}
	public function getDescription(){
		if(isset($this->summary)){
			return $this->summary;
		}
		return null;
	}
	
	public function getWhoVoted()
	{
		$voteTbl = new Yncontest_Model_DbTable_Votes();
		$userIds = $voteTbl->fetchAll("entry_id = " . $this->getIdentity());
		$users = array();
		foreach ($userIds as $id)
		{
			$users[] = Engine_Api::_()->user()->getUser($id);
		}
		return $users;
	}
	public function getNextEntry($entry, $contest, $viewer)
    {
	    $table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
	    $select = $table->select()
	        ->where('contest_id = ?', $this->contest_id)
	        ->where('`entry_id` > ?', $entry->getIdentity())
	        ->order('entry_id ASC')
	        ->limit(1);
	    $entry = $table->fetchRow($select);
		
		if($entry)
	    {	//only contest owner or entry owner can view entry is denied or peding or activated
	    	if((empty($entry->activated) || $entry->approve_status == 'denied' || $entry->approve_status == 'pending'  )  && !$entry->IsOwner($viewer) && !$contest->isOwner($viewer) ) {				
				return $this->getNextEntry($entry, $contest, $viewer);
			}			
			return $entry;
	    }	
	    return null;
    }
  
  public function getPreviousEntry($entry, $contest, $viewer)
  {
    $table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
    $select = $table->select()
        ->where('contest_id = ?', $this->contest_id)
        ->where('`entry_id` < ?', $entry->getIdentity())
        ->order('entry_id DESC')
        ->limit(1);	
    $entry = $table->fetchRow($select);
          
    if($entry)
    {
    	if((empty($entry->activated) || $entry->approve_status == 'denied' || $entry->approve_status == 'pending'  )  && !$entry->IsOwner($viewer) && !$contest->isOwner($viewer) ) {
			return $this->getPreviousEntry($entry, $contest, $viewer);
		}	
		return $entry;
    }	
    return null;
  }
  public function getMediaType()
  {
  	return 'entry';
  }
}
