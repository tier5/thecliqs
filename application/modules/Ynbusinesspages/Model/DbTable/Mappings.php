<?php
class Ynbusinesspages_Model_DbTable_Mappings extends Engine_Db_Table
{
  	protected $_name = 'ynbusinesspages_mappings';
	
	public function getAlbumsPaginator($params = array()) {
   	 	$paginator = Zend_Paginator::factory($this->getAlbumsSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
  	
	public function countItem($arr_type, $business_id)
	{
		$select = $this -> select();
     	$select -> from($this, array('count(*) as amount'));
		$select -> where('type IN (?)', $arr_type)
				-> where('business_id = ?', $business_id);
		$row = $this -> fetchRow($select); 
		return $row -> amount;
	}
	public function getItemIdsMapping($type, $params = array())
	{
		$select = $this -> select() -> from($this, new Zend_Db_Expr("`item_id`"));
		$select -> where("type = ?", $type);
        if (isset($params['business_id'])) {
            $select -> where("business_id = ?", $params['business_id']);
        }
        if (isset($params['user_id'])) {
            $select -> where("owner_id = ?", $params['user_id']);
        }
        $select -> order("creation_date DESC");
		$mapping_ids = $this->fetchAll($select);
		$ids = array();
		foreach($mapping_ids as $mapping_id)
		{
			$ids[] = $mapping_id -> item_id;
		}
		return $ids;
	}
	
	public function deleteItem($params = array()){
		$tableName = $this -> info('name');
		$db = $this -> getAdapter();
		$db -> beginTransaction();
		try
		{
			if(in_array($params['type'], array('ynwiki_page')))
			{
				$item = Engine_Api::_() -> getItem($params['type'], $params['item_id']);
				if($item)
				{
					$item -> parent_type = 'user';
					$item -> parent_id = $item -> user_id;
					$item -> save();
				}
			}
			else
			{
				$db->delete($tableName, array(
				    'type = ?' => $params['type'],
				    'item_id = ?' => $params['item_id']
				));
			}
			$db -> commit();
			
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return $e;
		}
		return "true";
	}
	
    public function checkItem($type, $id) {
        $select = $this->select()->where('type = ?', $type)->where('item_id = ?', $id);
        $result = $this->fetchRow($select);
        return ($result) ? true : false;
    }
    
	public function getOwner($item)
	{
	    $type = $item -> getType();
        if ($type == 'contest') $type = 'yncontest_contest';
		$select = $this -> select() -> where('type = ?', $type) -> where('item_id = ?', $item -> getIdentity());
		$row = $this -> fetchRow($select);
		if($row)
		{
		    
			$owner = Engine_Api::_() -> getItem($row -> owner_type, $row -> owner_id);
		}
		else
		{
			$owner = $item -> getOwner();
		}
        return $owner;
	}
    
    public function getBusinesses($item) {
        $type = $item -> getType();
        if ($type == 'contest') $type = 'yncontest_contest';
        $select = $this -> select() -> where('type = ?', $type) -> where('item_id = ?', $item -> getIdentity());
        $data = $this -> fetchAll($select);
        $businesses = array();
        foreach ($data as $row) {
            $business = Engine_Api::_() -> getItem('ynbusinesspages_business', $row -> business_id);
            if ($business && ($business->status == 'published'))
                $businesses[] = $business;
        }
        return $businesses;
    }
    
    public function getAlbumsSelect($params = array()){
        //Get album table
        $table_music = Engine_Api::_()->getItemTable($params['ItemTable']);
        $select = $table_music->select();
        if (!isset($params['ItemTable'])) {
            $params['ItemTable'] = 'music_playlist';
        }
        $ids = $this->getItemIdsMapping($params['ItemTable'], $params);
        if (!empty($ids)) {
            if( $params['ItemTable'] == 'mp3music_album')  {
                $select -> where('album_id IN (?)', $ids);
            }
            else {
                $select -> where('playlist_id IN (?)', $ids);
            }
        }
        else {
            if( $params['ItemTable'] == 'mp3music_album')  {
                $select -> where('album_id IN (0)', $ids);
            }
            else {
                $select -> where('playlist_id IN (0)', $ids);
            }
        }
        
        //Search
        if(!empty($params['search'])){
            $select->where('title LIKE ? OR description LIKE ?','%'.$params['search'].'%');
        }
    
        // Order
        switch( $params['order'] ) {
            case 'comment':
                $select -> order ('comment_count DESC');
                break;
            case 'play':
                $select -> order ('play_count DESC');
                break;
            case 'recent':
            default:
                $select -> order('creation_date DESC');
                break;
        }
        return $select;
    }

    public function getBlogsPaginator($params = array()){
        $paginator = Zend_Paginator::factory($this->getBlogsSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
    
    public function getBlogsSelect($params = array()) {
        
        $table_blog = Engine_Api::_()->getItemTable('blog');
        $select = $table_blog->select();
        if (!isset($params['ItemTable'])) {
            $params['ItemTable'] = 'blog';
        }
        $ids = $this->getItemIdsMapping($params['ItemTable'], $params);
        if (!empty($ids)) {
            $select -> where('blog_id IN (?)', $ids);    
        }    
        else {
            $select -> where('blog_id IN (0)', $ids);  
        }
        //Search
        if(!empty($params['search'])){
            $select->where('title LIKE ? OR body LIKE ?','%'.$params['search'].'%');
        }
        
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (isset($params['manage']) && $params['manage']) {
            $select->where(new Zend_Db_Expr("IF(owner_id = $user_id, 1, draft = 0 AND search = 1)"));
        }
        
        else {
            $select
                ->where('draft = ?', 0)
                ->where('search = ?', 1);
        }
        
        if (Engine_Api::_()->hasModuleBootstrap('ynblog')) {
            if (isset($params['manage']) && $params['manage']) {
                $select->where(new Zend_Db_Expr("IF(owner_id = $user_id, 1, is_approved = 1)"));
            }
            
            else {
                $select
                    ->where('is_approved = ?', 1);
            }
        }
        
        // Order
        switch( $params['order'] ) {
            case 'recent':
                $select -> order('creation_date DESC');
                break;
            case 'view':
                $select -> order('view_count DESC');
                break;
            default:
                $select -> order('creation_date DESC');
                break;
        }
        return $select;
    }

    public function getClassifiedsPaginator($params = array()){
        $paginator = Zend_Paginator::factory($this->getClassifiedsSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
    
    public function getClassifiedsSelect($params = array()) {
        
        $table = Engine_Api::_()->getItemTable('classified');
        $select = $table->select();
        if (!isset($params['ItemTable'])) {
            $params['ItemTable'] = 'classified';
        }
        $ids = $this->getItemIdsMapping($params['ItemTable'], $params);
        if (!empty($ids)) {
            $select -> where('classified_id IN (?)', $ids);    
        }    
        else {
            $select -> where('classified_id IN (0)', $ids);  
        }
        //Search
        if(!empty($params['search'])){
            $select->where('title LIKE ? OR body LIKE ?','%'.$params['search'].'%');
        }
        
        // Order
        switch( $params['order'] ) {
            case 'recent':
                $select -> order('creation_date DESC');
                break;
            case 'view':
                $select -> order('view_count DESC');
                break;
            default:
                $select -> order('creation_date DESC');
                break;
        }
        return $select;
    }

    public function getGroupbuysPaginator($params = array()){
        $paginator = Zend_Paginator::factory($this->getGroupbuysSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
    
    public function getGroupbuysSelect($params = array()) {
        
        $table = Engine_Api::_()->getItemTable('groupbuy_deal');
        $rName = $table->info('name');
        $select = $table->select()->from("$rName as d")->setIntegrityCheck(false);
        $select->joinLeft('engine4_groupbuy_locations', "d.location_id = engine4_groupbuy_locations.location_id", 'engine4_groupbuy_locations.title as location_title');
        if (!isset($params['ItemTable'])) {
            $params['ItemTable'] = 'groupbuy_deal';
        }
        $ids = $this->getItemIdsMapping($params['ItemTable'], $params);
        if (!empty($ids)) {
            $select -> where('d.deal_id IN (?)', $ids);    
        }    
        else {
            $select -> where('d.deal_id IN (0)', $ids);  
        }
        //Search
        if(!empty($params['search'])){
            $select->where('d.title LIKE ? OR d.description LIKE ?','%'.$params['search'].'%');
        }
        
        $select->where('d.is_delete = ?', 0);
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();
        $cur_time = Groupbuy_Api_Core::getCurrentServerTime();
        if (isset($params['manage']) && $params['manage']) {
            $select->where(new Zend_Db_Expr("IF(d.user_id = $user_id, 1, d.published = 20 AND d.status IN (20,30) AND d.start_time <= '$cur_time' AND d.end_time >= '$cur_time' AND d.current_sold < d.max_sold)"));
        }
        else {
            $select->where("d.published = 20 AND d.status IN (20,30) AND d.start_time <= '$cur_time' AND d.end_time >= '$cur_time' AND d.current_sold < d.max_sold");
        }

        // Order
        switch( $params['order'] ) {
            case 'recent':
                $select -> order('d.creation_date DESC');
                break;
            case 'feature':
                $select -> order('d.featured DESC');
                break;
            case 'rate':
                $select -> order('d.rates DESC');
                break;
            default:
                $select -> order('d.creation_date DESC');
                break;
        }
        return $select;
    }
    
    public function getContestsPaginator($params = array()){
        $paginator = Zend_Paginator::factory($this->getContestsSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
    
    public function getContestsSelect($params = array()) {
        
        $table = Engine_Api::_()->getItemTable('yncontest_contest');
        $Name = $table -> info('name');
        $select = $table -> select() -> from($Name, "$Name.*,
            (SELECT COUNT(*) FROM engine4_yncontest_membership WHERE engine4_yncontest_membership.resource_id = $Name.contest_id GROUP BY engine4_yncontest_membership.resource_id )AS participants
            ,(SELECT COUNT(*) FROM engine4_yncontest_entries en WHERE en.contest_id = $Name.contest_id AND en.approve_status ='approved') AS entries
            ,(SELECT SUM(tran.amount) FROM engine4_yncontest_transactions tran WHERE tran.contest_id = $Name.contest_id AND tran.payment_type is not NULL) AS paidFee   
            ,(TIMESTAMPDIFF(YEAR,now(),$Name.end_date)) AS yearleft             
            ,(TIMESTAMPDIFF(MONTH,now(),$Name.end_date)) AS monthleft
            ,(TIMESTAMPDIFF(DAY,now(),$Name.end_date)) AS dayleft               
            ,(TIME_FORMAT(TIMEDIFF(engine4_yncontest_contests.end_date,now()),'%H')) AS hourleft            
            ,(TIME_FORMAT(TIMEDIFF(engine4_yncontest_contests.end_date,now()),'%i')) AS minuteleft
            ");
        if (!isset($params['ItemTable'])) {
            $params['ItemTable'] = 'yncontest_contest';
        }
        $ids = $this->getItemIdsMapping($params['ItemTable'], $params);
        if (!empty($ids)) {
            $select -> where('contest_id IN (?)', $ids);    
        }    
        else {
            $select -> where('contest_id IN (0)', $ids);  
        }
        //Search
        if(!empty($params['search'])){
            $select->where('contest_name LIKE ?','%'.$params['search'].'%');
        }
        
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (isset($params['manage']) && $params['manage']) {
            $select->where(new Zend_Db_Expr("IF(user_id = $user_id, 1, approve_status = 'approved')"));
        }
        
        else {
            $select->where('approve_status = ?', 'approved');
        }
        
        // Browse by
        if (isset($param['browseby']) && $param['browseby'] != "") {
            if ($param['browseby'] == "feature")
                $select -> where("featured_id = 1");
            if ($param['browseby'] == "premium")
                $select -> where("premium_id =  1");
            if ($param['browseby'] == "endingsoon")
                $select -> where("endingsoon_id =  1");
        }
        $select -> order('creation_date DESC');
        return $select;
    }

    public function getListingsPaginator($params = array()){
        $paginator = Zend_Paginator::factory($this->getListingsSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
    
    public function getListingsSelect($params = array()) {
        
        $table = Engine_Api::_()->getItemTable('ynlistings_listing');
        $Name = $table -> info('name');
        
        $postTable = Engine_Api::_()->getItemTable('ynlistings_post');
        $postTblName = $postTable->info('name');
        $select = $table -> select();
        $select -> setIntegrityCheck(false); 
        $select -> from("$Name as listing", "listing.*, COUNT($postTblName.post_id) as discuss_count");
        $select -> joinLeft("$postTblName","$postTblName.listing_id = listing.listing_id", "");
        $select -> group('listing.listing_id');
        if (!isset($params['ItemTable'])) {
            $params['ItemTable'] = 'ynlistings_listing';
        }
        $ids = $this->getItemIdsMapping($params['ItemTable'], $params);
        if (!empty($ids)) {
            $select -> where('listing.listing_id IN (?)', $ids);    
        }    
        else {
            $select -> where('listing.listing_id IN (0)', $ids);  
        }
        //Search
        if(!empty($params['search'])){
            $select->where('listing.title LIKE ?','%'.$params['search'].'%');
        }
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $user_id = $viewer->getIdentity();
        if (isset($params['manage']) && $params['manage']) {
            $select->where(new Zend_Db_Expr("IF(listing.user_id = $user_id, 1, listing.search = 1 AND listing.status = 'open' AND listing.approved_status = 'approved')"));
        }
        
        else {
            $select
                ->where('listing.search = ?', 1)
                ->where('listing.status = ?', 'open')
                ->where('listing.approved_status = ?', 'approved');
        }
        // Order
        switch( $params['order'] ) {
            case 'recent':
                $select -> order('listing.listing_id DESC');
                break;
            case 'view':
                $select -> order('listing.view_count DESC');
                break;
            case 'like':
                $select -> order('listing.like_count DESC');
                break;
            case 'discussion':
                $select -> order('discuss_count DESC');
                break;
            case 'title':
                $select -> order('listing.title ASC');
                break;
            default:
                $select -> order('listing.listing_id DESC');
                break;
        }
        
        return $select;
    }

    public function getPollsPaginator($params = array()){
        $paginator = Zend_Paginator::factory($this->getPollsSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
    
    public function getPollsSelect($params = array()) {
        
        $table_poll = Engine_Api::_()->getItemTable('poll');
        $select = $table_poll->select();
        if (!isset($params['ItemTable'])) {
            $params['ItemTable'] = 'poll';
        }
        $ids = $this->getItemIdsMapping($params['ItemTable'], $params);
        if (!empty($ids)) {
            $select -> where('poll_id IN (?)', $ids);    
        }    
        else {
            $select -> where('poll_id IN (0)', $ids);  
        }
        //Search
        if(!empty($params['search'])){
            $select->where('title LIKE ? OR description LIKE ?','%'.$params['search'].'%');
        }
        
        // Closed
        if(isset($params['closed']) && $params['closed'] != '') {
            $select->where('closed = ?', $params['closed']);
        }
        
        // Order
        switch( $params['order'] ) {
            case 'recent':
                $select -> order('creation_date DESC');
                break;
            case 'popular':
                $select
                  ->order('vote_count DESC')
                  ->order('view_count DESC');
                break;
            default:
                $select -> order('creation_date DESC');
                break;
        }
        return $select;
    }

    public function getJobsPaginator($params = array()){
        $paginator = Zend_Paginator::factory($this->getJobsSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
    
    public function getJobsSelect($params = array()) {
        
        $table_poll = Engine_Api::_()->getItemTable('ynjobposting_job');
        $select = $table_poll->select();
        if (!isset($params['ItemTable'])) {
            $params['ItemTable'] = 'ynjobposting_job';
        }
        $ids = $this->getItemIdsMapping($params['ItemTable'], $params);
        if (!empty($ids)) {
            $select -> where('job_id IN (?)', $ids);    
        }    
        else {
            $select -> where('job_id IN (0)', $ids);  
        }
        //Search
        if(!empty($params['search'])){
            $select->where('title LIKE ? OR description LIKE ?','%'.$params['search'].'%');
        }
        
        if (isset($params['status']) && $params['status'] != 'all') {
            $select->where('status = ?', $params['status']);
        }
        else {
            $select->where('status IN (?)',array('published', 'expired'));
        }
        
        if (!empty($ids)) {
            $ids_str = implode(',', $ids);
            $select -> order(new Zend_Db_Expr("FIELD(job_id, $ids_str)"));
        }
        return $select;
    }

	public function getEventsPaginator($params = array())
	{
		$table = Engine_Api::_() -> getItemTable('event');
		$tableName = $table -> info('name');
		//Get data from table Mappings
		$ids = $this -> getItemIdsMapping('event', $params);
		$select = $table -> select();
		if(!$ids)
		{
			$select -> where("`{$tableName}`.event_id IN (0)");
		}
		else 
		{
			$select -> where("`{$tableName}`.event_id IN (?)", $ids);
		}
		if (!empty($params['text']))
		{
			$select -> where("`{$tableName}`.title LIKE ?", '%' . $params['text'] . '%');
		}
		if (!empty($params['order']))
		{
			$select -> order($params['order']);
		}
		else
			$select -> order('creation_date DESC');
		
		if (!empty($params['limit']))
		{
			$select -> limit($params['limit']);
		}
		return Zend_Paginator::factory($select);
	}
	
	public function getVideosPaginator($params = array(), $order_by = true) {
        $paginator = Zend_Paginator::factory($this->getVideosSelect($params, $order_by));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    public function getVideosSelect($params = array(), $order_by = true) {
        $table = Engine_Api::_()->getItemTable('video');
        $rName = $table->info('name');
        $select = $table->select()->from($rName)->setIntegrityCheck(false);
		
		$mappings_p = $params;
        if (isset($mappings_p['user_id'])) unset($mappings_p['user_id']);
		$ids = $this -> getItemIdsMapping('video', $mappings_p);
		if (!empty($ids) && count($ids) > 0) {
            $select->where('video_id IN (?)', $ids);
        }
		else {
			$select->where('video_id = 0');
		}
		
        if (!empty($params['orderby'])) {
            if (isset($params['order'])) {
                $order = $params['order'];
            } else {
                $order = '';
            }
            switch ($params['orderby']) {
                case 'most_liked' :
                    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
                    $likeTableName = $likeTable->info('name');
                    $likeVideoTableSelect = $likeTable->select()->where('resource_type = ?', 'video');
                    $select->joinLeft($likeVideoTableSelect, "t.resource_id = $rName.video_id");
                    $select->group("$rName.video_id");
                    $select->order("count(t.like_id) DESC");
                    break;
                case 'most_commented' :
                    $commentTable = Engine_Api::_()->getDbTable('comments', 'core');
                    $commentTableName = $commentTable->info('name');
                    $commentVideoTableSelect = $commentTable->select()->where('resource_type = ?', 'video');
                    $select->join($commentVideoTableSelect, "t.resource_id = $rName.video_id");
                    $select->group("$rName.video_id");
                    $select->order("count(t.comment_id) DESC");
                    break;
                case 'featured' :
                    $select->where('featured = ?', 1);
                    $select->order("$rName.creation_date DESC");
                    break;
                default :
                    $select->order("$rName.{$params['orderby']} DESC");
            }
        } else {
            if ($order_by) {
                $select->order("$rName.creation_date DESC");
            }
        }

        if (!empty($params['title'])) {
            $select->where("$rName.title LIKE ?", "%{$params['title']}%");
        }

        if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
            $select->where($rName . '.owner_id = ?', $params['user_id']);
        }

        if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
            $select->where($rName . '.owner_id = ?', $params['user_id']->getIdentity());
        }
        return $select;
    }
    
    public function getYnmusicPaginator($params = array()){
        $paginator = Zend_Paginator::factory($this->getYnmusicSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
    
    public function getYnmusicSelect($params = array()) {
        
        $table_music = Engine_Api::_()->getItemTable($params['ItemTable']);
        $select = $table_music->select();
        $ids = $this->getItemIdsMapping($params['ItemTable'], $params);
		$type = $params['type'];
        if (!empty($ids)) {
            $select -> where($type."_id IN (?)", $ids);    
        }    
        else {
            $select -> where($type."_id IN (0)");  
        }
        //Search
        if(!empty($params['search'])){
            $select->where('title LIKE ? OR description LIKE ?','%'.$params['search'].'%');
        }
        
        // Order
        if (!empty($params['browse_by'])) {
			switch ($params['browse_by']) {
				case 'recently_created':
					$params['order'] = $type."_id";
					$params['direction'] = 'DESC';
					break;
					
				case 'most_liked':
					$params['order'] = 'like_count';
					$params['direction'] = 'DESC';
					break;
				
				case 'most_discussed':
					$params['order'] = 'comment_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_viewed':
					$params['order'] = 'view_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'most_played':
					$params['order'] = 'play_count';
					$params['direction'] = 'DESC';
					break;
					
				case 'a_z':
					$params['order'] = 'title';
					$params['direction'] = 'ASC';
					break;
					
				case 'z_a':
					$params['order'] = 'title';
					$params['direction'] = 'DESC';
					break;
				
				default:
					break;
			}
		}
		
		if(empty($params['direction'])) {
			$params['direction'] = 'DESC';
		}
		
	    if(!empty($params['order'])) {
			$select -> order($params['order'] . ' ' . $params['direction']);
		} else {
			$select -> order($type."_id DESC");
		}
        return $select;
    }

    public function getYnultimatevideoPaginator($params = array()){
        $paginator = Zend_Paginator::factory($this->getYnultimatevideoSelect($params));
        if( !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    public function getYnultimatevideoSelect($params = array()) {

        $table = Engine_Api::_()->getItemTable('ynultimatevideo_video');
        $rName = $table->info('name');
        $select = $table->select()->from($rName)->setIntegrityCheck(false);

        $mappings_p = $params;
        if (isset($mappings_p['user_id'])) unset($mappings_p['user_id']);
        $ids = $this -> getItemIdsMapping('ynultimatevideo_video', $mappings_p);
        if (!empty($ids) && count($ids) > 0) {
            $select->where('video_id IN (?)', $ids);
        }
        else {
            $select->where('video_id = 0');
        }

        //Search
        if(!empty($params['search'])){
            $select->where('title LIKE ?','%'.$params['search'].'%');
        }

        // Order
        if (!empty($params['browse_by'])) {
            switch ($params['browse_by']) {
                case 'recently_created':
                    $params['order'] = 'video_id';
                    $params['direction'] = 'DESC';
                    break;

                case 'most_liked':
                    $params['order'] = 'like_count';
                    $params['direction'] = 'DESC';
                    break;

                case 'most_commented':
                    $params['order'] = 'comment_count';
                    $params['direction'] = 'DESC';
                    break;

                case 'most_viewed':
                    $params['order'] = 'view_count';
                    $params['direction'] = 'DESC';
                    break;

                default:
                    break;
            }
        }

        if(!empty($params['order'])) {
            $select -> order($params['order'] . ' ' . $params['direction']);
        } else {
            $select -> order('video_id DESC');
        }
        return $select;
    }
}