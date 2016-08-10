<?php
class Ynlistings_Model_DbTable_Listings extends Engine_Db_Table {
    protected $_rowClass = 'Ynlistings_Model_Listing';
    
	public function getTotalListingsByUser($user_id)
	{
		$select = $this->select()->where('user_id = ?', $user_id);
		return count($this->fetchAll($select));
	}
	
	public function getAllChildrenListingsByCategory($node)
	{
		$return_arr = array();
		$cur_arr = array();
		$list_categories = array();
		Engine_Api::_()->getItemTable('ynlistings_category') -> appendChildToTree($node, $list_categories);
		foreach($list_categories as $category)
		{
			$select = $this->select()->where('category_id = ?', $category -> category_id);
			$cur_arr = $this->fetchAll($select);
			if(count($cur_arr) > 0)
			{
				$return_arr[] = $cur_arr;
			}
		}
		return $return_arr;
	}
	
	public function getListingsByCategory($category_id)
	{
		$select = $this->select()->where('category_id = ?', $category_id);
		return $this->fetchAll($select);
	}
	
	public function getTotalComments()
	{
		$select =  $this->select()->from($this, new Zend_Db_Expr("SUM(comment_count) as total_comment_count"));
		return $this->fetchRow($select)->total_comment_count;
	}
	
	public function getTotalListings()
	{
		return count($this->fetchAll($this->select()));
	}
	
	public function getPublishedListings()
	{
		$select = $this -> select() -> where("status = 'open'") -> where("approved_status = 'approved'");
		return count($this->fetchAll($select));
	}
	
	public function getDraftListings()
	{
		$select = $this -> select() -> where("status = 'draft'");
		return count($this->fetchAll($select));
	}
	
	public function getClosedListings()
	{
		$select = $this -> select() -> where("status = 'closed'");
		return count($this->fetchAll($select));
	}
	
	public function getOpenListings()
	{
		$select = $this -> select() -> where("status = 'open'");
		return count($this->fetchAll($select));
	}
	
	public function getApprovedListings()
	{
		$select = $this -> select() -> where("approved_status = 'approved'");
		return count($this->fetchAll($select));
	}
	
	public function getDisApprovedListings()
	{
		$select = $this -> select() -> where("approved_status = 'denied'");
		return count($this->fetchAll($select));
	}
	public function getFeaturedListings()
	{
		$select = $this -> select() -> where("featured = '1'");
		return count($this->fetchAll($select));
	}
	
    public function getListingsPaginator($params = array()) {
        return Zend_Paginator::factory($this->getListingsSelect($params));
    }
  
  public function getListingsSelect($params = array()) {
    $listingTbl = Engine_Api::_() -> getItemTable('ynlistings_listing');
    $listingTblName = $listingTbl -> info('name');
    
    $searchTable = Engine_Api::_()->fields()->getTable('ynlistings_listing', 'search');
    $searchTableName = $searchTable->info('name');
    
    $userTbl = Engine_Api::_() -> getDbtable('users', 'user');
    $userTblName = $userTbl -> info('name');

    $categoryTbl = Engine_Api::_() -> getItemTable('ynlistings_category');
    $categoryTblName = $categoryTbl -> info('name');
    
    $tagsTbl = Engine_Api::_() -> getDbtable('TagMaps', 'core');
    $tagsTblName = $tagsTbl -> info('name');
    
    $postTable = Engine_Api::_()->getItemTable('ynlistings_post');
    $postTblName = $postTable->info('name');
        
    $target_distance = $base_lat = $base_lng = "";
    if (isset($params['lat'])) {
        $base_lat = $params['lat'];
    }
    if (isset($params['long'])) {
        $base_lng = $params['long'];
    }
    //Get target distance in miles
    if (isset($params['within'])) {
        $target_distance = $params['within'];
    }
    
    $select = $listingTbl -> select();
    $select -> setIntegrityCheck(false); 
    
    if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
        $select -> from("$listingTblName as listing", "listing.*, COUNT($postTblName.post_id) as discuss_count, ( 3959 * acos( cos( radians('$base_lat')) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( latitude ) ) ) ) AS distance");
        $select -> where("latitude <> ''");
        $select -> where("longitude <> ''");
    }
    else {
        $select -> from("$listingTblName as listing", "listing.*, COUNT($postTblName.post_id) as discuss_count");
    }
    $select
    -> joinLeft("$postTblName","$postTblName.listing_id = listing.listing_id", "")
    -> joinLeft("$userTblName as user", "user.user_id = listing.user_id", "") 
    -> joinLeft("$categoryTblName as category", "category.category_id = listing.category_id", "")
    -> joinLeft("$searchTableName as search", "search.item_id = listing.listing_id", "");
    
    $select->group("listing.listing_id");
    $tmp = array();
    $originalParams = $params;
    foreach( $params as $k => $v ) {
      if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
        continue;
      } else if( false !== strpos($k, '_field_') ) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if( false !== strpos($k, '_alias_') ) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $params = $tmp;
    
    if (isset($params['listing_title']) && $params['listing_title'] != '') {
        $select->where('listing.title LIKE ?', '%'.$params['listing_title'].'%');
    }
    if (isset($params['owner']) && $params['owner'] != '') {
        $select->where('user.displayname LIKE ?', '%'.$params['owner'].'%');
    }
    if (isset($params['category']) && $params['category'] != 'all') {
        $categorySelect = $categoryTbl->select()->where('option_id = ?', $params['category']);
        $category = $categoryTbl->fetchRow($categorySelect);
        if ($category) {
            $tree = array();
            $node = $categoryTbl -> getNode($category->getIdentity());
            Engine_Api::_() -> getItemTable('ynlistings_category') -> appendChildToTree($node, $tree);
            $categories = array();
            foreach ($tree as $node) {
                array_push($categories, $node->category_id);
            }
            $select->where('listing.category_id IN (?)', $categories);
        }
    }
    if (isset($params['category_id']) && $params['category_id'] != 'all') {
        $node = $categoryTbl -> getNode($params['category_id']);
        if ($node) {
            $tree = array();
            Engine_Api::_() -> getItemTable('ynlistings_category') -> appendChildToTree($node, $tree);
            $categories = array();
            foreach ($tree as $node) {
                array_push($categories, $node->category_id);
            }
            $select->where('listing.category_id IN (?)', $categories);
        }
    }
    if (isset($params['approved_status']) && $params['approved_status'] != 'all') {
        $select->where('listing.approved_status = ?', $params['approved_status']);
    }
    if (isset($params['status']) && $params['status'] != 'all') {
        $select->where('listing.status = ?', $params['status']);
    }
    if (isset($params['featured']) && $params['featured'] != 'all') {
        $select->where('listing.featured = ?', $params['featured']);
    }
    if(isset($params['user_id'])) {
        $select->where('listing.user_id = ?', $params['user_id']);
    }
    else {
        if ($params['admin'] == null) {
            $select
                ->where('listing.search = ?', 1)
                ->where('listing.status = ?', 'open')
                ->where('listing.approved_status = ?', 'approved');
        }
    } 

    //Tags
    if (!empty($params['tag'])) {
        $select -> setIntegrityCheck(false) -> joinLeft($tagsTblName, "$tagsTblName.resource_id = listing.listing_id", "") -> where($tagsTblName . '.resource_type = ?', 'ynlistings_listing') -> where($tagsTblName . '.tag_id = ?', $params['tag']);
    }
    
    if (isset($params['order'])) {
        if (empty($params['direction'])) {
            $params['direction'] = ($params['order'] == 'listing.title') ? 'ASC' : 'DESC';
        }
        
        if ($params['order'] == 'discuss_count') {
            $select->order("COUNT($postTblName.post_id)".' '.$params['direction']);
        }
        else {
            $select->order($params['order'].' '.$params['direction']);
        }
    }
    else {
        if (!empty($params['direction'])) {
            $select->order('listing.listing_id'.' '.$params['direction']);
        }
    }
    
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('ynlistings_listing', $params);
    foreach( $searchParts as $k => $v ) {
        $select->where("search.$k", $v);
    }
    
    if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
        $select -> having("distance <= $target_distance");
        $select -> order("distance ASC");
    }
    
    return $select;
  }

    
}