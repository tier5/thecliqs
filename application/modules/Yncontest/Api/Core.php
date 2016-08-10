<?php

class Yncontest_Api_Core extends Core_Api_Abstract
{
	protected $_moduleName = 'yncontest';

	public $arrPlugins = array(
			'ynblog' => 'Blog',
			'blog' => 'Blog',
			'advalbum' => 'Photo',
			'album'=>  'Photo',
			'ynvideo' => 'Video',
			'video' => 'Video',
			'music' => 'Music',
			'mp3music' => 'Music',			
		'ynmusic' => 'Social Music',
		'ynultimatevideo' => 'Ultimate Video',
	);
	private $arrYNPlugins = array(
			'blog' => 'ynblog',
			'album'=>'advalbum',
			'video'=> 'ynvideo',
			'music'=> 'mp3music',
	);
	
	public function processContest($sec_id, $transactionID){
		
		$table = Engine_Api::_() -> getDbTable('transactions', 'yncontest');
		$transactions = $table->getTranBySec($sec_id);
		
		if(count($transactions) == 0)	 $this->_forward('requireauth', 'error', 'core');			
		$firstTransaction = $transactions[0];			
		$contest = Engine_Api::_() -> getItem('yncontest_contest', $firstTransaction->contest_id);	
		
		if($firstTransaction->option_service == 1){	
			//using publish option
			$this->_buildPublish($transactions, $contest, $transactionID);
		}
		else{
			if($contest -> contest_status = "draft")
			{
				//using publish option
				$this->_buildPublish($transactions, $contest, $transactionID);
			}
			else 
			{
				//using register option
 				$this->_buildRegister($transactions, $contest, $transactionID);
			}
		}			
	}
	
	public function _buildPublish($transactions, $contest, $transactionID){
		
		$approve = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.approval', 0);		
		if($approve == 1){
			$contest->approve_status ='approved';
			$contest->contest_status = 'published';
			$contest->approved_date = date('Y-m-d H:i:s');				
		
			//send notification & mail
			$admin = Engine_Api::_() -> user() -> getUser(1);
			$owner = Engine_Api::_() -> user() -> getUser($contest->user_id);
			$contest->sendNotMailOwner($owner, $admin, 'contest_approved', 'yncontest_new' );
			
		}	
		else{
			$contest->contest_status = 'waiting';
			$contest->approve_status = 'pending';
		}
		$contest->save();
		foreach($transactions as $transaction){				
			$transaction->transaction_status = 'success';
		
			switch ($transaction->option_service) {
					case 2:
						$contest->featured_id = 1;break;
					case 3:
						$contest->premium_id = 1;break;
					case 4:
						$contest->endingsoon_id = 1;break;
			}	
					
			$transaction->payment_type = 1;
			$transaction->params = $transactionID;				
			$transaction->save();
			$contest->save();
		}
		
	}
	public function _buildRegister($transactions, $contest, $transactionID){
				
		//send notification & mail
		$admin = Engine_Api::_() -> user() -> getUser(1);
		$owner = Engine_Api::_() -> user() -> getUser($contest->user_id);
		$contest->sendNotMailOwner($owner, $admin, 'register_service', null );

		
		foreach($transactions as $transaction){
			$transaction->transaction_status = 'success';
			//update transaction
			$transaction->approve_status = 'approved';
			//update contest
			switch ($transaction->option_service) {
				case 2:
					$contest->featured_id = 1;break;
				case 3:
					$contest->premium_id = 1;break;
				case 4:
					$contest->endingsoon_id = 1;break;
			}
			
			$transaction->payment_type = 1;
			$transaction->params = $transactionID;
			$transaction->save();
			
		}
		$contest->save();
	}
	
	public function getGateway($gateway_id)
	{
		return $this -> getPlugin($gateway_id) -> getGateway();
	}
	
	public function getPlugin($gateway_id)
	{
		if (null === $this -> _plugin)
		{
			if (null == ($gateway = Engine_Api::_() -> getItem('payment_gateway', $gateway_id)))
			{
				return null;
			}
			Engine_Loader::loadClass($gateway -> plugin);
			if (!class_exists($gateway -> plugin))
			{
				return null;
			}
			$class = str_replace('Payment', 'Yncontest', $gateway -> plugin);

			Engine_Loader::loadClass($class);
			if (!class_exists($class))
			{
				return null;
			}

			$plugin = new $class($gateway);
			if (!($plugin instanceof Engine_Payment_Plugin_Abstract))
			{
				throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' . 'implement Engine_Payment_Plugin_Abstract', $class));
			}
			$this -> _plugin = $plugin;
		}
		return $this -> _plugin;
	}
	
	public function getPlugins()
	{
		$table = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $table -> select() -> where('enabled = ?', 1) -> where('name in (?)', array_keys($this -> arrPlugins));
		$results = $table -> fetchAll($mselect);
		$arr = array();

		$arrayNoplugins = array();
		foreach ($results as $result)
		{
				
			if(array_key_exists($result -> name, $this->arrYNPlugins)){
					
				$arr[$this->arrYNPlugins[$result -> name]] = $this -> arrPlugins[$result -> name];
			}
			else
				$arr[$result -> name] = $this -> arrPlugins[$result -> name];
		}
		return $arr;
	}
	public function getPluginsAlbum()
	{
		$table = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $table -> select() -> where('enabled = ?', 1) -> where('name in (?)', array_keys($this -> arrPlugins));
		$results = $table -> fetchAll($mselect);
		$arr = array();

		$arrayNoplugins = array();
		$arr = array('album', 'advalbum');
		foreach ($results as $result)
		{				
			if(in_array($result -> name, $arr)){
				return $result -> name;
			}				
		}
	}
	public function getPluginsBlog()
	{
		$table = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $table -> select() -> where('enabled = ?', 1) -> where('name in (?)', array_keys($this -> arrPlugins));
		$results = $table -> fetchAll($mselect);
		$arr = array();
	
		$arrayNoplugins = array();
		$arr = array('blog', 'ynblog');
		foreach ($results as $result)
		{
			if(in_array($result -> name, $arr)){
				return $result -> name;
			}
		}
	}
	public function getPluginsVideo()
	{
		$table = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $table -> select() -> where('enabled = ?', 1) -> where('name in (?)', array_keys($this -> arrPlugins));
		$results = $table -> fetchAll($mselect);
		$arr = array();
	
		$arrayNoplugins = array();
		$arr = array('video', 'ynvideo');
		foreach ($results as $result)
		{
			if(in_array($result -> name, $arr)){
				return $result -> name;
			}
		}
	}
	
	public function getPluginsMusic()
	{
		$table = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $table -> select() -> where('enabled = ?', 1) -> where('name in (?)', array_keys($this -> arrPlugins));
		$results = $table -> fetchAll($mselect);
		$arr = array();
		
		$arrayNoplugins = array();
		$arr = array('music', 'mp3music');
		foreach ($results as $result)
		{
			if(in_array($result -> name, $arr)){
				return $result -> name;
			}
		}
	}
	public function getPluginsSocialMusic()
	{
		$table = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $table -> select() -> where('enabled = ?', 1) -> where('name in (?)', array_keys($this -> arrPlugins));
		$results = $table -> fetchAll($mselect);
		$arr = array('ynmusic');
		foreach ($results as $result)
		{
			if(in_array($result -> name, $arr)){
				return $result -> name;
			}
		}
	}
	public function getPluginsUltimateVideo()
	{
		$modulesTable = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $modulesTable -> select() -> where('enabled = ?', 1) -> where('name  = ?', 'ynultimatevideo');
		$module_result = $modulesTable -> fetchRow($mselect);
		if ($module_result) {
			return 'ynultimatevideo';
		} else {
			return false;
		}
	}

	public function getFeeContest($user, $name)
	{
		$fee = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('contest', $user, $name);
		if ($fee == "")
		{
			$mtable = Engine_Api::_() -> getDbtable('permissions', 'authorization');
			$maselect = $mtable -> select() -> where("type = 'contest'") -> where("level_id = ?", $user -> level_id) -> where("name = ?", $name);

			$mallow_a = $mtable -> fetchRow($maselect);
			if (!empty($mallow_a))
				return $mallow_a['value'];
		}
		else
			return $fee;

		return 0;
	}

	public function getEntryThumnail($entry_type, $item_id)
	{

		$obj = NULL;
		switch($entry_type)
		{
			case 'ynblog' :
				$blogPlugin = $this->getPluginsBlog();				
				$obj = Engine_Api::_() -> getDbtable('blogs', $blogPlugin) -> find($item_id) -> current();
				break;
			case 'advalbum' :
				$albumPlugin = $this->getPluginsAlbum();
				$obj = Engine_Api::_() -> getDbtable('photos', $albumPlugin) -> find($item_id) -> current();
				break;
			case 'ynvideo' :
				$videoPlugin = $this->getPluginsVideo();
				$obj = Engine_Api::_() -> getDbtable('videos', $videoPlugin) -> find($item_id) -> current();
				break;
		}

		return $obj;
	}

	public function checkRule($param = array())
	{
		$rules = Engine_Api::_() -> getDbTable('rules', 'yncontest') -> getRuleByContest($param['contestId']);
		$flag = false;
		foreach ($rules as $rule)
		{
				
			if ($rule -> start_date <= date('Y-m-d H:i:s') && date('Y-m-d H:i:s') <= $rule -> end_date)
			{
				switch ($param['key'])
				{
					case 'submitentries' :
						if ($rule -> submitentries == 1)
						{
							$flag = true;
						}
						break;
					case 'viewentries' :
						if ($rule -> viewentries == 1)
						{
							$flag = true;
						}
						break;
					case 'voteentries' :
						if ($rule -> voteentries == 1)
						{
							$flag = true;
						}
						break;
				}

			}
		}
		return $flag;
	}
	public function checkAge($contest)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$member = Engine_Api::_()->getDbTable('members', 'yncontest')->getMemberContest2(array(
				'contestId'=>$contest->contest_id,
				'user_id'=> $viewer->getIdentity()));

		$flag = false;

		//explode the date to get month, day and year
		$birthDate = explode("-", $member->birth);
		//get age from date or birthdate
		$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[2], $birthDate[0], $birthDate[1] ))) > date("md") ? ((date("Y")-$birthDate[0])-1):(date("Y")-$birthDate[0]));

		if($contest->age_under == null && $contest->age_older == null)
		{
			return true;
		}
		else{

			if($contest->age_older <= $age &&  $contest->age_under >= $age) $flag = true;
				
		}
		return $flag;

	}
	private function getMinMaxEntry($value1, $value2)
	{
		if(empty($value1))
		{
			return $value2;
		}
		elseif(empty($value2))
		{
			return $value1;
		}
		if($value1>$value2)
		{
			return $value2;
		}
		elseif($value1<$value2)
		{
			return $value1;
		}
		else
		{
			return $value1;
		}
	}
	

	public function checkMaxEntries($param = array())
	{
		$settings = Engine_Api::_() -> getDbTable('settings', 'yncontest') -> getSettingByContest($param['contestId']);
		$viewer = Engine_Api::_()->user()->getViewer();
		
		//guest
		if($viewer->getIdentity()==0)
			return false;
		
		$max_entries= Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('contest', $viewer, 'max_entries');
		if($max_entries == "")
	    {
	         $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
	         $maselect = $mtable->select()
	            ->where("type = 'contest'")
	            ->where("level_id = ?",$viewer->level_id)
	            ->where("name = 'max_entries'");
	          $mallow_a = $mtable->fetchRow($maselect);          
	          if (!empty($mallow_a))
	            $max_entries = $mallow_a['value'];
	          else
	             $max_entries = 0;
	    }

		$settings -> max_entries = $this->getMinMaxEntry($settings -> max_entries, $max_entries);
		
		if ($settings -> max_entries == null || $settings -> max_entries == 0){
			return 99;
		}
	
		$entries = Engine_Api::_() -> getItemTable('yncontest_entries') -> getEntriesContest(array(
					'contestID' => $param['contestId'],
					'user_id' => $viewer -> getIdentity()
			));
		

		if($settings -> max_entries > count($entries)) return ($settings -> max_entries - count($entries));

		return false;

	}

	/**
	 *
	 * Return a string after substring
	 * @param string $string
	 * @param int $length
	 * @return string
	 */
	public function subPhrase($string, $length = 0)
	{
		if (strlen($string) <= $length)
			return $string;
		$pos = $length;
		for ($i = $length - 1; $i >= 0; $i--)
		{
			if ($string[$i] == " ")
			{
				$pos = $i + 1;
				break;
			}
		}
		return substr($string, 0, $pos) . "...";
	}
	public function subPhrase2($string, $length = 0)
	{
		if (strlen($string) <= $length)
			return $string;
		$pos = $length;
		for ($i = $length - 1; $i >= 0; $i--)
		{
			if ($string[$i] == " ")
			{
				$pos = $i + 1;
				break;
			}
		}
		return substr($string, 0, $pos);
	}

	/**
	 *
	 * Get getContestPaginator
	 * @param array $params
	 * @return Zend_Paginator
	 */
	public function getContestPaginator($params = array())
	{
		if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased') && !isset($params['admin'])) {
			$params = Engine_Api::_()->ynlocationbased()->mergeWithCookie('yncontest', $params);
		}
		$paginator = Zend_Paginator::factory($this -> getContests($params));
		if (!empty($params['page']))
		{
			$paginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$paginator -> setItemCountPerPage($params['limit']);
		}
		return $paginator;
	}

	public function getEntriesVideo($params = array())
	{				
		$table = Engine_Api::_() -> getItemTable('video');				
		$select = $table -> select() -> where('search = ?', 1) -> where('owner_id =?', $params['owner_id'])-> where('status = ?', 1);
		if(isset($params['item_id'])&& !empty($params['item_id']))
		{
			$select -> where('video_id =?', $params['item_id']);
		}
		return Zend_Paginator::factory($select);		
	}
	public function getEntriesMusic($params = array())
	{
				
		$db = Engine_Db_Table::getDefaultAdapter();	
		//check module music
		$select = "SELECT * FROM engine4_core_modules WHERE name = 'music'";
		$music = $db->fetchRow($select);
		
		$select = "SELECT * FROM engine4_core_modules WHERE name = 'mp3music'";
		$mp3music = $db->fetchRow($select);
		
		if(isset($params['item_id'])&& !empty($params['item_id']))
		{			
			$mp3music_album_song = strpos($params['item_id'],'mp3music_album_song');	
			if ($mp3music_album_song === false) {
			    $mp3music['enabled'] = 0;
				$music['enabled'] = 1;
				$song_id = substr($params['item_id'],19,strlen($params['item_id']));
				//$_SESSION['music_type'] = 'music_playlist_song';
			} else {
			    $mp3music['enabled'] = 1;
				$music['enabled'] = 0;
				$song_id = substr($params['item_id'],19,strlen($params['item_id']));
				//$_SESSION['music_type'] = 'mp3music_album_song';
			}					
		}
		
		if(isset($params['item_id'])&& !empty($params['item_id']))
		{			
			if(!empty($mp3music['enabled']) && !empty($music['enabled']))
			{
				$select = "
					SELECT 
						`engine4_mp3music_album_songs`.song_id,
						'mp3music_album_song' as resource_type
					 FROM engine4_mp3music_album_songs
					 WHERE song_id = $song_id
					UNION
					SELECT 
						`engine4_music_playlist_songs`.song_id,
						'music_playlist_song' as resource_type
					 FROM engine4_music_playlist_songs
					 WHERE song_id = $song_id
					 ORDER BY song_id DESC	
				";
			}
			elseif(!empty($mp3music['enabled']))
			{
				$select = "
					SELECT 
						`engine4_mp3music_album_songs`.song_id,
						'mp3music_album_song' as resource_type		
					 FROM engine4_mp3music_album_songs	
					 WHERE song_id = $song_id		
					 ORDER BY song_id DESC	
				";
			}
			else{
				$select = "				
					SELECT 
						`engine4_music_playlist_songs`.song_id,
						'music_playlist_song' as resource_type					
					 FROM engine4_music_playlist_songs
					 WHERE song_id = $song_id
					 ORDER BY song_id DESC
				";
			}	
			
		}
		else{
			$user_id = $params['user_id'];
			if(!empty($mp3music['enabled']) && !empty($music['enabled']))
			{
				$select = "
					SELECT 
						`engine4_mp3music_album_songs`.song_id,
						'mp3music_album_song' as resource_type
					 FROM engine4_mp3music_album_songs
					 	LEFT JOIN engine4_mp3music_albums ON `engine4_mp3music_album_songs`.album_id = `engine4_mp3music_albums`.album_id
					 	WHERE `engine4_mp3music_albums`.user_id = $user_id
					UNION
					SELECT 
						`engine4_music_playlist_songs`.song_id,
						'music_playlist_song' as resource_type
					 FROM engine4_music_playlist_songs
					  	LEFT JOIN engine4_music_playlists ON `engine4_music_playlist_songs`.playlist_id = `engine4_music_playlists`.playlist_id
 						WHERE `engine4_music_playlists`.owner_id = $user_id
					ORDER BY song_id DESC	
				";
			}
			elseif(!empty($mp3music['enabled']))
			{
				$select = "
					SELECT 
						`engine4_mp3music_album_songs`.song_id,
						'mp3music_album_song' as resource_type
					 FROM engine4_mp3music_album_songs
					 	LEFT JOIN engine4_mp3music_albums ON `engine4_mp3music_album_songs`.album_id = `engine4_mp3music_albums`.album_id
					 	WHERE `engine4_mp3music_albums`.user_id = $user_id				
					ORDER BY song_id DESC	
				";
			}
			else{			
				$select = "				
					SELECT 
						`engine4_music_playlist_songs`.song_id,
						'music_playlist_song' as resource_type					
					 FROM engine4_music_playlist_songs
					 	LEFT JOIN engine4_music_playlists ON `engine4_music_playlist_songs`.playlist_id = `engine4_music_playlists`.playlist_id
 						WHERE `engine4_music_playlists`.owner_id = $user_id
					ORDER BY song_id DESC
				";
			}	
		}
		
		//echo $select;die;
		
		$songs = $db->fetchAll($select);
		return Zend_Paginator::factory($songs);
	}

	public function getEntriesSocialMusic($params = array())
	{
		$db = Engine_Db_Table::getDefaultAdapter();
		if(isset($params['item_id'])&& !empty($params['item_id']))
		{
			$song_id = $params['item_id'];
		}
		if(isset($params['item_id'])&& !empty($params['item_id']))
		{
			$select = "
					SELECT
						`engine4_ynmusic_songs`.song_id,
						'ynmusic_song' as resource_type
					FROM engine4_ynmusic_songs
					WHERE song_id = $song_id
					ORDER BY song_id DESC
			";
		}
		else{
			$user_id = $params['user_id'];
			$select = "
					SELECT
						`engine4_ynmusic_songs`.song_id,
						'ynmusic_song' as resource_type
					FROM engine4_ynmusic_songs
					WHERE user_id = $user_id
					ORDER BY song_id DESC
				";
		}
		$songs = $db->fetchAll($select);
		return Zend_Paginator::factory($songs);
	}
	public function getEntriesUltimateVideo($params = array())
	{
		$video_id = 0;
		$user_id = 0;
		$select = '';
		if(isset($params['item_id'])&& !empty($params['item_id']))
		{
			$video_id = $params['item_id'];
		} else if(isset($params['user_id'])&& !empty($params['user_id'])) {
			$user_id = $params['user_id'];
		}
		$table = Engine_Api::_()->getItemTable('ynultimatevideo_video');
		$tableName = $table->info('name');
		if ($video_id) {
			$select = "
					SELECT
						`$tableName`.video_id,
						'ynultimatevideo_video' as resource_type
					FROM $tableName
					WHERE video_id = $video_id
					ORDER BY video_id DESC
			";
		} else if ($user_id) {
			$select = "
					SELECT
						`$tableName`.video_id,
						'ynultimatevideo_video' as resource_type
					FROM $tableName
					WHERE owner_id = $user_id AND status = 1
					ORDER BY video_id DESC
				";
		}
		if ($select) {
			$db = Engine_Db_Table::getDefaultAdapter();
			$videos = $db->fetchAll($select);
			return Zend_Paginator::factory($videos);
		} else {
			return false;
		}
	}
	public function getEntriesAlbum($params = array())
	{			
		$albumPlugin = $this->getPluginsAlbum();
		
		$table = Engine_Api::_() -> getDbtable('photos', $albumPlugin);
		$atable = Engine_Api::_() -> getDbtable('albums', $albumPlugin);
		$Name = $table -> info('name');
		$aName = $atable -> info('name');
		$select = $table -> select() -> from($Name) -> joinLeft($aName, "$Name.album_id = $aName.album_id", '') -> where("search = ?", "1") -> where("$aName.owner_id =?", $params['owner_id']);
		if(isset($params['album_id']) && !empty($params['album_id']))
		{
			$select->where("$Name.album_id = ?", $params['album_id']);
		}		
		
		return Zend_Paginator::factory($select);
	}

	public function getEntriesBlog($params = array())
	{	
		$table = Engine_Api::_() -> getItemTable('blog');
		$select = $table -> select() -> where('search = ?', 1) -> where('owner_id =?', $params['owner_id']) -> where('draft = ?', 0) -> order('creation_date');
		if(isset($params['item_id'])&& !empty($params['item_id']))
		{
			$select -> where('blog_id =?', $params['item_id']);
		}
		return Zend_Paginator::factory($select);		
	}

	public static function partialViewFullPath($partialTemplateFile)
	{
		$ds = DIRECTORY_SEPARATOR;
		return "application{$ds}modules{$ds}Yncontest{$ds}views{$ds}scripts{$ds}{$partialTemplateFile}";
	}

	/**
	 *
	 * Get getContests
	 * @param array $params
	 * @return Zend_Paginator
	 */
	public function getContests($param = null)
	{
		$table = Engine_Api::_() -> getDbtable('contests', 'yncontest');
		$Name = $table -> info('name');

		$target_distance = $base_lat = $base_lng = "";
		if (isset($param['lat'])) {
			$base_lat = $param['lat'];
		}
		if (isset($param['long'])) {
			$base_lng = $param['long'];
		}
		//Get target distance in miles
		if (isset($param['within'])) {
			$target_distance = $param['within'];
		}

		// Get Tagmaps table
		$tags_table = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tags_name = $tags_table -> info('name');

		$select = $table -> select();
		$select -> setIntegrityCheck(false);

		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
			$select -> from($Name, new Zend_Db_Expr("$Name.*,
				( 3959 * acos( cos( radians('$base_lat')) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( latitude ) ) ) ) AS distance,
				(SELECT COUNT(*) FROM engine4_yncontest_membership WHERE engine4_yncontest_membership.resource_id = $Name.contest_id GROUP BY engine4_yncontest_membership.resource_id )AS participants
				,(SELECT COUNT(*) FROM engine4_yncontest_entries en WHERE en.contest_id = $Name.contest_id AND en.approve_status ='approved') AS entries
				,(SELECT SUM(tran.amount) FROM engine4_yncontest_transactions tran WHERE tran.contest_id = $Name.contest_id AND tran.payment_type is not NULL) AS paidFee
				,(TIMESTAMPDIFF(YEAR,now(),$Name.end_date)) AS yearleft
				,(TIMESTAMPDIFF(MONTH,now(),$Name.end_date)) AS monthleft
				,(TIMESTAMPDIFF(DAY,now(),$Name.end_date)) AS dayleft
				,(TIME_FORMAT(TIMEDIFF(engine4_yncontest_contests.end_date,now()),'%H')) AS hourleft
				,(TIME_FORMAT(TIMEDIFF(engine4_yncontest_contests.end_date,now()),'%i')) AS minuteleft
				"));
			$select -> where("latitude <> ''");
			$select -> where("longitude <> ''");
		}
		else {
			$select -> from($Name, new Zend_Db_Expr("$Name.*,
				(SELECT COUNT(*) FROM engine4_yncontest_membership WHERE engine4_yncontest_membership.resource_id = $Name.contest_id GROUP BY engine4_yncontest_membership.resource_id )AS participants
				,(SELECT COUNT(*) FROM engine4_yncontest_entries en WHERE en.contest_id = $Name.contest_id AND en.approve_status ='approved') AS entries
				,(SELECT SUM(tran.amount) FROM engine4_yncontest_transactions tran WHERE tran.contest_id = $Name.contest_id AND tran.payment_type is not NULL) AS paidFee	
				,(TIMESTAMPDIFF(YEAR,now(),$Name.end_date)) AS yearleft				
				,(TIMESTAMPDIFF(MONTH,now(),$Name.end_date)) AS monthleft
				,(TIMESTAMPDIFF(DAY,now(),$Name.end_date)) AS dayleft				
				,(TIME_FORMAT(TIMEDIFF(engine4_yncontest_contests.end_date,now()),'%H')) AS hourleft			
				,(TIME_FORMAT(TIMEDIFF(engine4_yncontest_contests.end_date,now()),'%i')) AS minuteleft
				"));
		}

		if (isset($param['owner']) && $param['owner'] != "")
		{
			$name = $param['owner'];
			$select -> join("engine4_users", "engine4_users.user_id = $Name.user_id", "");
			$select -> where("engine4_users.displayname Like ?", "%$name%");
		}

		if (!empty($param['tags']))
		{
			$select -> joinLeft($tags_name, "$tags_name.resource_id = $Name.contest_id", "") -> where($tags_name . '.resource_type = ?', 'contest') -> where($tags_name . '.tag_id = ?', $param['tags']);
		}
		
		//Seach contest by name
		if ((isset($param['name']) && $param['name'] != "") || isset($param['contest_name']))
		{
		    if (isset($param['contest_name']))
                $name = $param['contest_name'];
            else
			    $name = $param['name'];
			$select -> where("$Name.contest_name Like ?", "%$name%");
		}

		if (isset($param['contest_id']) && $param['contest_id'] != "")
		{
			$select -> where("$Name.contest_id =  ?", $param['contest_id']);
		}


		//Seach contest by contest type
		if (isset($param['contest_type']) && $param['contest_type'] != "0")
		{
			$select -> where("$Name.contest_type =  ?", $param['contest_type']);
		}

		if (isset($param['browseby']) && $param['browseby'] != "")
		{
			if ($param['browseby'] == "featured_contest")
				$select -> where("$Name.featured_id = 1");
			if ($param['browseby'] == "premium_contest")
				$select -> where("$Name.premium_id =  1");
			if ($param['browseby'] == "endingsoon_contest")
				$select -> where("$Name.endingsoon_id =  1");
		}
		
		if (isset($param['browsebylist']) && $param['browsebylist'] != "")
		{
			if ($param['browsebylist'] == "premium")
				$select -> where("$Name.premium_id =  1");
			if ($param['browsebylist'] == "endingsoon")
				$select -> where("$Name.endingsoon_id =  1");
		}
		//search category
		if(!empty($param['category_id']) && $param['category_id'] > 0)
	    {
	   		$select->where("$Name.category_id =?", $param['category_id']);
	    }
	
		if (isset($param['approve_status'])&& $param['approve_status'] != "all")
			$select -> where("$Name.approve_status = ? ",$param['approve_status']);

		if (isset($param['contest_status'])&& $param['contest_status'] != "all")
			$select -> where("$Name.contest_status = ? ",$param['contest_status']);
		if (isset($param['userfriends']))
			$select -> where("$Name.user_id IN (?) ",$param['userfriends']);
		
		//order by string ending contest => widget ending soon
		if(isset($param['order_by_str']))
		{
			$select->where($param['order_by_str']);
		}
		
		//Get owner contest		
		if(!empty($param['owner_id']))
		{
			$select->where("$Name.user_id = ?", $param['owner_id']);
		}
		
		//Get contest is activated
		if(isset($param['activated']))
		{
			$select->where("$Name.activated = ?", $param['activated']);
		}
		// choose endingsoon_id
		if(isset($param['endingsoon_id']))
		{
			$select->where("$Name.endingsoon_id =  ? ", $param['endingsoon_id']);
		}
		// choose premium_id
		if(isset($param['premium_id']))
		{
			$select->where("$Name.premium_id =  ? ", $param['premium_id']);
		}			
		// choose featured_id
		if(isset($param['featured_id']))
		{
			$select->where("$Name.featured_id =  ? ", $param['featured_id']);
		}
		if(isset($param['rand']))
		{
			$select->order("rand()");
		}

		//Seach by public status
		$direction = ' DESC';
		if(isset($param['direction']) && $param['direction'] != "")
			$direction = ' '.$param['direction'];

		//Order by filter
		if (isset($param['orderby']) && $param['orderby'] == 'displayname')
		{
			$select -> join('engine4_users as u', "u.user_id = $Name.user_id", '') -> order("u.displayname " . $direction);
		}
		else
		{
			if (isset($param['filter']) && $param['filter'] != 'all')
			{
				$select->order($Name.".".$param['filter'].' '.$direction);
			}
			else
			{
				$select ->order(!empty($param['orderby'])?$param['orderby'].' '.$param['direction'] :"$Name.start_date ".$direction);
			}
		}
		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
			$select -> having("distance <= $target_distance");
			$select -> order("distance ASC");
		}
		$select -> group("$Name.contest_id");
		return $select;

	}

	public function getEntriesDetailPaginator($params = array())
	{
		$paginator = Zend_Paginator::factory($this -> getEntriesDetai($params));
		if (!empty($params['page']))
		{
			$paginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$paginator -> setItemCountPerPage($params['limit']);
		}
		return $paginator;
	}

	public function getEntriesDetai($param = null)
	{
		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name, new Zend_Db_Expr("$Name.*")) -> setIntegrityCheck(false);


		switch ($param['option']) {
			case '1': // all
					
				;
				break;
			case '2': // entries belong contest
				$select -> where("$Name.user_id = ?", $param['user_id']);
				$select -> where("$Name.contest_id = ?", $param['contest_id']);
				;
				break;
			case '3': //
				$params['contest_id'] = $contest->contest_id
				;
				break;
		}


		$select -> where("$Name.entry_status = 'published' or $Name.entry_status = 'win' ");

		//echo $select;die;

		return $select;

	}

	public function getMyEntriesPaginator($params = array())
	{
		$paginator = Zend_Paginator::factory($this -> getMyEntries($params));
		if (!empty($params['page']))
		{
			$paginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$paginator -> setItemCountPerPage($params['limit']);
		}
		return $paginator;
	}

	public function getMyEntries($param = null)
	{
		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name, new Zend_Db_Expr("$Name.*")) -> setIntegrityCheck(false);

		// Search  entry by name
		if (isset($param['entry_name']) && $param['entry_name'] != "")
		{
			$name = $param['entry_name'];
			$select -> where("$Name.entry_name Like ?", "%$name%");
		}
		if (isset($param['user_id']) && $param['user_id'] != "")
		{
			$user_id = $param['user_id'];
			$select -> where("$Name.user_id = ?", $user_id);
		}

		//$select->join("engine4_yncontest_contests","engine4_yncontest_contests.contest_id = $Name.contest_id");

		if (isset($param['entry_status']) && $param['entry_status'] != "")
		{
			$select -> where("$Name.entry_status = ?", $param['entry_status']);
		}

		//Seach contest by name
		if (isset($param['entry_name']) && $param['entry_name'] != "")
		{
			$name = $param['name'];
			$select -> where("$Name.entry_name Like ?", "%$name%");
		}

		$select -> where("$Name.entry_status <> 'delete'");

		//echo $select;die;

		return $select;

	}

	public function getMyContestPaginator($params = array())
	{
		$paginator = Zend_Paginator::factory($this -> getMyContests($params));
		if (!empty($params['page']))
		{
			$paginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$paginator -> setItemCountPerPage($params['limit']);
		}
		return $paginator;
	}

	public function getMyContests($param = null)
	{
		$table = Engine_Api::_() -> getDbtable('contests', 'yncontest');
		$Name = $table -> info('name');

		// Get Tagmaps table
		$tags_table = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tags_name = $tags_table -> info('name');

		$select = $table -> select() -> from($Name, new Zend_Db_Expr("$Name.*,
				(SELECT COUNT(*) FROM engine4_yncontest_members WHERE engine4_yncontest_members.contest_id = $Name.contest_id GROUP BY engine4_yncontest_members.contest_id )AS participants
				,(SELECT COUNT(*) FROM engine4_yncontest_entries en WHERE en.contest_id = $Name.contest_id) AS entries
				,(DATEDIFF(now(),$Name.start_date)) AS dayleft
				")) -> setIntegrityCheck(false);

		//Get owner
		if (isset($param['user_id']) && $param['user_id'] != "")
		{
			$user_id = $param['user_id'];
			$select -> where("$Name.user_id = ?", $user_id);
		}

		if (isset($param['owner']) && $param['owner'] != "")
		{
			$name = $param['owner'];
			$select -> join("engine4_users", "engine4_users.user_id = $Name.user_id", "");
			$select -> where("engine4_users.displayname Like ?", "%$name%");
		}

		if (isset($param['tags']) && $param['tags'] != "")
		{
			$tags = $param['tags'];
			$arr = explode(",", $tags);
			$where = "";
			$count = 0;
			foreach ($arr AS $key => $val)
			{
				if ($count < count($arr) - 1)
					$where .= " $Name.tags LIKE '%" . $val . "%' OR";
				else
				{
					$where .= " $Name.tags LIKE '%" . $val . "%'";
				}
				$count++;
			}

			$select -> where($where);
		}

		//Seach by public status
		if (isset($param['contest_status']) && $param['admin'] != "")
			$select -> where("$Name.contest_status = ?", $param['contest_status']);

		//Featured contests
		if (isset($param['featured']))
		{
			$select -> where("$Name.featured_id = 1");
		}

		//Ending Soon contests
		if (isset($param['endingsoon']))
		{
			$select -> where("$Name.endingsoon_id = 1");
		}

		//Premium contests
		if (isset($param['premium']))
		{
			$select -> where("$Name.premium_id = 1");
		}

		//Seach contest by name
		if (isset($param['name']) && $param['name'] != "")
		{
			$name = $param['name'];
			$select -> where("$Name.contest_name Like ?", "%$name%");
		}

		//Seach contest by name
		if (isset($param['location_id']) && $param['location_id'] != "")
		{
			$select -> where("$Name.location =  ?", $param['location_id']);
		}

		//Seach contest by contest type
		if (isset($param['contest_type']) && $param['contest_type'] != "0")
		{
			$select -> where("$Name.contest_type =  ?", $param['contest_type']);
		}

		//Seach contest by category
		if (isset($param['category']) && $param['category'] != "")
		{
			$select -> where("$Name.category_id =  ?", $param['category']);
		}

		//award text
		if (isset($param['award']) && $param['award'] != "")
		{
			$name = $param['award'];
			$select -> join("engine4_yncontest_awards", "engine4_yncontest_awards.contest_id = $Name.contest_id AND engine4_yncontest_awards.award_name Like '%$name%'");
			//$select->where("engine4_yncontest_awards.award_name Like ?","%$name%");
		}

		//Direction
		if (!isset($param['direction']))
			$param['direction'] = "DESC";

		//Order by filter
		if (isset($param['orderby']) && $param['orderby'] == 'displayname')
		{
			$select -> join('engine4_users as u', "u.user_id = $Name.user_id", '') -> order("u.displayname " . $param['direction']);
		}
		else
		{
			if (isset($param['filter']) && $param['filter'] != 'all')
			{
				//$select->order($Name.".".$param['filter'].' '.$param['direction']);
			}
			else
			{
				// $select ->order(!empty($param['orderby'])?$param['orderby'].' '.$param['direction'] :'start_date
				// '.$param['direction']);
			}
		}

		$select -> where("$Name.contest_status <> 'delete'");

		echo $select;

		return $select;

	}

	/**
	 *
	 * Get getContestPaginator
	 * @param array $params
	 * @return Zend_Paginator
	 */
	public function getEntryPaginator($params = array())
	{
		$paginator = Zend_Paginator::factory($this -> getEntries($params));
		if (!empty($params['page']))
		{
			$paginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$paginator -> setItemCountPerPage($params['limit']);
		}
		return $paginator;
	}

	/**
	 *
	 * Get getContests
	 * @param array $params
	 * @return Zend_Paginator
	 */
	public function getEntries($param = null)
	{
		if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
			$param = Engine_Api::_()->ynlocationbased()->mergeWithCookie('yncontest', $param);
		}

		$target_distance = $base_lat = $base_lng = "";
		if (isset($param['lat'])) {
			$base_lat = $param['lat'];
		}
		if (isset($param['long'])) {
			$base_lng = $param['long'];
		}
		//Get target distance in miles
		if (isset($param['within'])) {
			$target_distance = $param['within'];
		}

		$contestTbl = Engine_Api::_()->getDbTable('contests', 'yncontest');
		$contestTblName = $contestTbl->info('name');

		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name, new Zend_Db_Expr("$Name.*")) -> setIntegrityCheck(false);

		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
			$select -> joinLeft($contestTblName, "engine4_yncontest_contests.contest_id = $Name.contest_id", array("engine4_yncontest_contests.contest_name AS contest_name", "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( latitude ) ) ) ) AS distance"));
			$select -> where("$contestTblName.latitude <> ''");
			$select -> where("$contestTblName.longitude <> ''");
		}
		else {
			$select -> joinLeft($contestTblName, "$contestTblName.contest_id = $Name.contest_id", "$contestTblName.contest_name AS contest_name");
		}

		// Search  entry by name
		if (isset($param['entry_name']) && $param['entry_name'] != "")
		{
			$name = $param['entry_name'];
			$select -> where("$Name.entry_name Like ?", "%$name%");
		}	
		
		// vote_count >0
		if (isset($param['votelarge']))	
		{
			$select -> where ('vote_count > 0');
		}

		//Seach contest by contest type
		if (isset($param['entry_type']) && $param['entry_type'] != "0")
		{
			$select -> where("$Name.entry_type =  ?", $param['entry_type']);
		}
		if (isset($param['compare']) && $param['compare'] == "1")
		{
			$select -> where("$Name.entry_type = 'advalbum'");
			$select -> where("$Name.entry_id <> ?",$param['entry_id']);
		}

		$select -> where("$Name.approve_status= 'approved'");	
	
		if (isset($param['status']) && $param['status'] != "")
		{
			$select -> where("$Name.entry_status =  ?",$param['status']);
		}

		if (isset($param['user_id']) && $param['user_id'] != "")
		{
			$user_id = $param['user_id'];
			$select -> where("$Name.user_id = ?", $user_id);
		}
		else{
			$select -> where("$Name.activated = 1");
		}


		if (isset($param['owner']) && $param['owner'] != "")
		{
			$name = $param['owner'];
			$select -> join("engine4_users", "engine4_users.user_id = $Name.user_id", "");
			$select -> where("engine4_users.displayname Like ?", "%$name%");
		}

		if (isset($param['contest_id']) && $param['contest_id'] != "")
		{
			$user_id = $param['contest_id'];
			$select -> where("$Name.contest_id = ?", $user_id);
		}

		if (isset($param['awards']) && $param['awards'] != "" && $param['awards'] != "0")
		{
			$awards = $param['awards'];
			$select -> where("$Name.award_id = ?", $awards);
		}
		
		if (isset($param['max']) && !empty($param['max']) )
		{
			$select ->limit($param['max']);
		}
		if (isset($param['browseby']) && $param['browseby'] != 'all')
		{
			$select->order($Name.".".$param['browseby'].' DESC');
		}
		else{
			$select ->order('approved_date desc');
		}

		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
			$select -> having("distance <= $target_distance");
			$select -> order("distance ASC");
		}

		return $select;
	}

	public function getEntryPaginator3($params = array())
	{
		$paginator = Zend_Paginator::factory($this -> getEntries3($params));
		if (!empty($params['page']))
		{
			$paginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$paginator -> setItemCountPerPage($params['limit']);
		}
		return $paginator;
	}
	public function getEntries3($param = null)
	{
		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name, new Zend_Db_Expr("$Name.*")) -> setIntegrityCheck(false);
		
		// Search  entry by name
		if (isset($param['entry_name']) && $param['entry_name'] != "")
		{
			$name = $param['entry_name'];
			$select -> where("$Name.entry_name Like ?", "%$name%");
		}
		if (isset($param['entry_id']) && $param['entry_id'] != "")
		{
			$select -> where("$Name.entry_id = ?", $param['entry_id']);
		}

		//Seach entry by location_id
		if (isset($param['location_id']) && $param['location_id'] != "")
		{
			$select -> where("$Name.location_id =  ?", $param['location_id']);
		}

		//Seach contest by contest type
		if (isset($param['entry_type']) && $param['entry_type'] != "0")
		{
			$select -> where("$Name.entry_type =  ?", $param['entry_type']);
		}

		if (isset($param['approve_status']) && $param['approve_status'] != "all")
		{
			$select -> where("$Name.approve_status = ?",$param['approve_status']);
		}

		if (isset($param['user_id']) && $param['user_id'] != "")
		{
			$user_id = $param['user_id'];
			$select -> where("$Name.user_id = ?", $user_id);
			$select -> where("$Name.hidden = 0 ");
		}

		if (isset($param['owner']) && $param['owner'] != "")
		{
			$name = $param['owner'];
			$select -> join("engine4_users", "engine4_users.user_id = $Name.user_id", "");
			$select -> where("engine4_users.displayname Like ?", "%$name%");
		}
		
		if(!isset($param['direction']))
			$param['direction'] = ' DESC';
		
		if (isset($param['orderby']) && $param['orderby'] == 'displayname')
		{
			$select -> join('engine4_users as u', "u.user_id = $Name.user_id", '') -> order("u.displayname " . $param['direction']);
		}
		else
		{
			if (isset($param['filter']) && $param['filter'] != 'all')
			{
				$select->order($Name.".".$param['filter'].' '.$param['direction']);
			}
			else
			{
				$select ->order(!empty($param['orderby'])?$param['orderby'].' '.$param['direction'] :'start_date'.$param['direction']);
			}
		}

		if (isset($param['contest_id']) && $param['contest_id'] != "")
		{
			$user_id = $param['contest_id'];
			$select -> where("$Name.contest_id = ?", $user_id);
		}

		if (isset($param['awards']) && $param['awards'] != "" && $param['awards'] != "0")
		{
			$awards = $param['awards'];
			$select -> where("$Name.award_id = ?", $awards);
		}

		//echo $select;die;
		return $select;
	}
	public function getFollow($user_id = null, $contest_id = null)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$followTable = Engine_Api::_() -> getDbtable('follows', 'yncontest');
		$select = $followTable -> select() -> where('contest_id = ?', $contest_id) -> where('user_id = ?', $user_id);
		$row = $followTable -> fetchRow($select);
		return $row;
	}
	public function getEntryFollow($user_id = null, $contest_id = null, $entry_id = null)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$followTable = Engine_Api::_() -> getDbtable('entriesfollows', 'yncontest');
		$select = $followTable -> select() -> where('contest_id = ?', $contest_id) -> where('user_id = ?', $user_id) -> where('entry_id = ?', $entry_id);
		$row = $followTable -> fetchRow($select);
		return $row;
	}

	public function getFavourite($user_id = null, $contest_id = null)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$favouriteTable = Engine_Api::_() -> getDbtable('favourites', 'yncontest');
		$select = $favouriteTable -> select() -> where('contest_id = ?', $contest_id) -> where('user_id = ?', $user_id);
		$row = $favouriteTable -> fetchRow($select);
		return $row;
	}
	public function getFavouriteEntry($user_id = null, $contest_id = null, $entry_id = null)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$favouriteTable = Engine_Api::_() -> getDbtable('entriesfavourites', 'yncontest');
		$select = $favouriteTable -> select() -> where('contest_id = ?', $contest_id) -> where('entry_id = ?', $entry_id)-> where('user_id = ?', $user_id);
		$row = $favouriteTable -> fetchRow($select);
		return $row;
	}

	public function getAwardByContest($contest_id)
	{
		$model = new Yncontest_Model_DbTable_Awards;
		$select = $model -> select() -> where('contest_id = ?', $contest_id) -> limit(3);

		$award = $model -> fetchAll($select);
		return $award;

	}
	public function getAllAwardByContest($contest_id)
	{
		$model = new Yncontest_Model_DbTable_Awards;
		$select = $model -> select() -> where('contest_id = ?', $contest_id);
		$award = $model -> fetchAll($select);
		return $award;

	}
	static public function getDefaultCurrency()
	{
		return Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncontest.currency', 'USD');
	}

	static public function getCurrencySymbol()
	{
		$name = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncontest.currency', 'USD');
		$currency = new Yncontest_Model_DbTable_Currencies;
		$select = $currency -> select() -> where('code = ?', $name);
		return $currency -> fetchRow($select) -> symbol;
	}

	static public function getSymbol($name)
	{
		$currency = new Yncontest_Model_DbTable_Currencies;
		$select = $currency -> select() -> where('code = ?', $name);
		return $currency -> fetchRow($select) -> symbol;
	}

	/**
	 * Check service
	 * return Bool
	 */
	public function checkLiveService($contest_id, $type)
	{
		$model = new Yncontest_Model_DbTable_Transactions;
		$select = $model -> select() -> where('contest_id = ?', $contest_id) -> where("transaction_status = 'success'") -> where("approve_status = 'pending'") -> where("option_service =?", $type);

		$row = $model -> fetchRow($select);
		if (is_object($row))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Returns a category item
	 *
	 * @param
	 * @return Zend_Db_Table_Select
	 */
	public function getCategories()
	{
		$table = Engine_Api::_() -> getDbtable('categories', 'yncontest');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> order('name ASC');
		return $table -> fetchAll($select);
	}

	public function getEntriesById($entry_id)
	{
		$table = Engine_Api::_() -> getDbtable('entries', 'yncontest');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> where("entry_id =?", $entry_id);
		return $table -> fetchRow($select);
	}

	/**
	 *
	 * Get getContestPaginator
	 * @param array $params
	 * @return Zend_Paginator
	 */
	public function getMemberPaginator($params = array())
	{
		$paginator = Zend_Paginator::factory($this -> getMembers($params));
		if (!empty($params['page']))
		{
			$paginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$paginator -> setItemCountPerPage($params['limit']);
		}
		return $paginator;
	}

	/**
	 *
	 * Get getContests
	 * @param array $params
	 * @return Zend_Paginator
	 */
	public function getMembers($param = null)
	{
		$table = Engine_Api::_() -> getDbtable('members', 'yncontest');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name, new Zend_Db_Expr("$Name.*")) -> setIntegrityCheck(false);
		$select->joinLeft("engine4_yncontest_locations","engine4_yncontest_locations.location_id = $Name.location_id");
		$select->joinLeft("engine4_yncontest_contests","engine4_yncontest_contests.contest_id = $Name.contest_id","engine4_yncontest_contests.contest_name");

		// member type 1: participant 2: organizer
		if(isset($param['member_type'])  && $param['member_type'] != ''){
			$meber_type = intval($param['member_type']);
			$select->where("$Name.member_type = ?",$meber_type);
		}

		if(isset($param['user_id'])  && $param['user_id'] != ''){
			$user_id = intval($param['user_id']);
			$select->where("$Name.member_id = ?",$user_id);
		}

		if(isset($param['contest_id'])  && $param['contest_id'] != ''){
			$contest_id = intval($param['contest_id']);
			$select->where("$Name.contest_id = ?",$contest_id);
		}

		if(isset($param['user_name']) &&  $param['user_name'] != ""){
			$user_name = ($param['user_name']);
			$select->where("$Name.full_name like ? ","%$user_name%");
		}

		if(isset($param['status']) && $param['status'] != ""){
			$status = ($param['status']);
			$select->where("$Name.member_status = ?",$status);
		}

		if(isset($param['gender']) && $param['gender'] != ""){
			$gender = ($param['gender']);
			$select->where("$Name.sex = ?",$gender);
		}

		if(isset($param['from']) && $param['from'] !=""){
			$from = ($param['from']);
			//$select->where("DATEDIFF(now(),$Name.birth) >= ?",$from);
			$select->where("YEAR(now()) - YEAR($Name.birth) >= ?",$from);
		}
		if(isset($param['to']) && $param['to'] != ""){
			$to = ($param['to']);
			//$select->where("DATEDIFF(now(),$Name.birth) <= ?",$to);
			$select->where("YEAR(now())- YEAR($Name.birth) <= ?",$to);
		}
		//echo $select;
		return $select;
	}
	
	
	public function createPhoto($params, $file)
	{
		if( $file instanceof Storage_Model_File )
		{
			$params['file_id'] = $file->getIdentity();
		}
	
		else
		{		
			// Get image info and resize
			$name = basename($file['tmp_name']);
			$path = dirname($file['tmp_name']);
			$extension = ltrim(strrchr($file['name'], '.'), '.');
	
			$mainName = $path.'/m_'.$name . '.' . $extension;
			$profileName = $path.'/t_'.$name . '.' . $extension;
			$iNormal = $path.'/n_'.$name . '.' . $extension;
			$iNormal1 = $path.'/n1_'.$name . '.' . $extension;
	
			$image = Engine_Image::factory();
			$image->open($file['tmp_name'])			
				->write($mainName)
				->destroy();
			// Resize image (profile)
			$image = Engine_Image::factory();
			$image->open($file['tmp_name'])
				->resize(200, 140)
				->write($profileName)
				->destroy();
			$image = Engine_Image::factory();
			$image->open($file['tmp_name'])
				->resize(140,105)
				->write($iNormal)
				->destroy();
			 
			$image = Engine_Image::factory();
			$image->open($file['tmp_name'])
			->resize(85, 65)
			->write($iNormal1)
			->destroy();
				
			$photo_params = array(
					'album_id'=>$params['album_id'] ,
					'parent_type' => 'contest', 
					'parent_id' => $params['contest_id']
			);
			$photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
			$profileFile = Engine_Api::_()->storage()->create($profileName, $photo_params);
			$thumbFile = Engine_Api::_()->storage()->create($iNormal, $photo_params);
			$thumbFile1 = Engine_Api::_()->storage()->create($iNormal1, $photo_params);
			$photoFile->bridge($profileFile, 'thumb.profile');
			$photoFile->bridge($thumbFile, 'thumb.normal');
			$photoFile->bridge($thumbFile1, 'thumb.normal1');
			$params['file_id'] = $photoFile->file_id; // This might be wrong
			$params['photo_id'] = $photoFile->file_id;
	
			// Remove temp files
			@unlink($mainName);
			@unlink($profileName);
			@unlink($iNormal);
			@unlink($iNormal1);
			 
		}
		$row = Engine_Api::_()->getDbtable('photos','yncontest')->createRow();
		$row->setFromArray($params);
		$row->save();
		return $row;
	}
	public function gettimeleft($contest_id, $time_id)
	{
		$table = Engine_Api::_()->getDbtable('contests', 'yncontest');
        $Name = $table->info('name');
		
		$select = $table->select()->from($Name,"
				(TIMESTAMPDIFF(YEAR,now(),$Name.$time_id)) AS yearleft				
				,(TIMESTAMPDIFF(MONTH,now(),$Name.$time_id)) AS monthleft
				,(TIMESTAMPDIFF(DAY,now(),$Name.$time_id)) AS dayleft				
				,(TIME_FORMAT(TIMEDIFF($Name.$time_id,now()),'%H')) AS hourleft			
				,(TIME_FORMAT(TIMEDIFF($Name.$time_id,now()),'%i')) AS minuteleft
				")->setIntegrityCheck(false);
		$select->where("contest_id =?",$contest_id );	
		//echo $select
		return $table -> fetchRow($select);		
	}
	
}