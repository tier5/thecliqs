<?php
class Ynbusinesspages_Model_DbTable_Business extends Engine_Db_Table {
	protected $_name = 'ynbusinesspages_business';
	protected $_rowClass = 'Ynbusinesspages_Model_Business';
	protected $_serializedColumns = array('phone', 'fax', 'web_address');
	
	
	public function getAllChildrenBusinessesByCategory($node) {
		$return_arr = array();
		$cur_arr = array();
		$list_categories = array();
		Engine_Api::_() -> getItemTable('ynbusinesspages_category') -> appendChildToTree($node, $list_categories);
		foreach ($list_categories as $category) {
			$tableCategoryMap = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
			$select = $tableCategoryMap -> select() -> where('category_id = ?', $category -> category_id);
			$cur_arr = $tableCategoryMap -> fetchAll($select);
			if (count($cur_arr) > 0) {
				$return_arr[] = $cur_arr;
			}
		}
		return $return_arr;
	}
	
	public function getBusinessesByCategory($category_id) {
		$tableCategoryMap = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
		$select = $tableCategoryMap -> select() -> where('category_id = ?', $category_id);
		return $tableCategoryMap -> fetchAll($select);
	}

	public function getBusinessByNameEmail($name, $email)
	{
		$select = $this -> select() -> where('name = ?', $name) -> where('email =?', $email) -> where('deleted = ?', 0) -> limit(1);
		return $this -> fetchRow($select);
	}
	
	public function getBusinessesPaginator($params = array()) {
		
		return Zend_Paginator::factory($this -> getBusinessesSelect($params));
	}
	
	public function getBusinessesSelect($params = array()) {
       
		$businessTbl = Engine_Api::_() -> getItemTable('ynbusinesspages_business');
		$businessTblName = $businessTbl -> info('name');

		$userTbl = Engine_Api::_() -> getDbtable('users', 'user');
		$userTblName = $userTbl -> info('name');

		$categorymapsTbl = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
		$categorymapsTblName = $categorymapsTbl -> info('name');
        
        $locationTbl = Engine_Api::_() -> getDbTable('locations', 'ynbusinesspages');
        $locationTblName = $locationTbl -> info('name');
        
        $categoryTbl = Engine_Api::_() -> getItemTable('ynbusinesspages_category');
        $categoryTblName = $categoryTbl -> info('name');
        
        $searchTable = Engine_Api::_()->fields()->getTable('ynbusinesspages_business', 'search');
        $searchTableName = $searchTable->info('name');
		
        $tagsTbl = Engine_Api::_() -> getDbtable('TagMaps', 'core');
    	$tagsTblName = $tagsTbl -> info('name');
        
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
    
		$select = $businessTbl -> select();
		$select -> setIntegrityCheck(false);

		$select -> from("$businessTblName as business", "business.*");
		
        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
            $select -> joinLeft("$locationTblName as location", "location.business_id = business.business_id", "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( location.latitude ) ) * cos( radians( location.longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( location.latitude ) ) ) ) AS distance"); 
            $select -> where('location.main = ?', 1);
        }
        
		$select -> joinLeft("$userTblName as user", "user.user_id = business.user_id", null) 
				-> joinLeft("$categorymapsTblName as categorymap", "categorymap.business_id = business.business_id",null)
		        -> joinLeft("$searchTableName as search", "search.item_id = business.business_id", null);
        $select->group("business.business_id");
        
        $options = $params;
        $tmp = array();
        foreach( $options as $k => $v ) {
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
        $options = $tmp;
        
		if (isset($params['title']) && $params['title'] != '') {
			$select -> where('business.name LIKE ?', '%' . $params['title'] . '%');
		}
		
		if (isset($params['owner']) && $params['owner'] != '') {
			$select -> where('user.displayname LIKE ?', '%' . $params['owner'] . '%');
		}
		
		if (isset($params['category']) && $params['category'] != 'all') {
            $categorySelect = $categoryTbl->select()->where('option_id = ?', $params['category']);
            $category = $categoryTbl->fetchRow($categorySelect);
            if ($category) {
                $tree = array();
                $node = $categoryTbl -> getNode($category->getIdentity());
                $categoryTbl -> appendChildToTree($node, $tree);
                $categories = array();
                foreach ($tree as $node) {
                    array_push($categories, $node->category_id);
                }
                $select->where('categorymap.category_id IN (?)', $categories);
            }
        }
		
		if (isset($params['categories']) && count($params['categories'])) {
            $categorySelect = $categoryTbl->select()->where('option_id IN (?)', $params['categories']);
            $categories = $categoryTbl->fetchAll($categorySelect);
            if (count($categories))
            {
            	$categoryArr = array();
            	foreach ($categories as $category)
            	{
	            	$tree = array();
	                $node = $categoryTbl -> getNode($category->getIdentity());
	                $categoryTbl -> appendChildToTree($node, $tree);
	                foreach ($tree as $node) {
	                    array_push($categoryArr, $node->category_id);
	                }
            	}
            	$categoryArr = array_unique($categoryArr);
            	if (count($categoryArr))
            	{
            		$select->where('categorymap.category_id IN (?)', $categoryArr);	
            	}
            }
        }
        
        if (isset($params['category_id']) && $params['category_id'] != 'all') {
            $node = $categoryTbl -> getNode($params['category_id']);
            if ($node) 
            {
                $tree = array();
                $categoryTbl -> appendChildToTree($node, $tree);
                $categories = array();
                foreach ($tree as $node) {
                    array_push($categories, $node->category_id);
                }
                $select->where('categorymap.category_id IN (?)', $categories);
            }
        }
        
        if (isset($params['status']))
        {
        	if ($params['status'] != 'all')
        	{
        		$select -> where('business.status = ?', $params['status']);
        	}
        }
        else 
        {
        	if(!isset($params['claimer_id']) || !isset($params['claim']))
        	{
        		$select->where("business.status = 'published' || business.status = 'unclaimed'");
        	}
        }
		
		$select->where('business.status <> ?', 'deleted');
		
		if (isset($params['featured']) && $params['featured'] != 'all') {
			$select -> where('business.featured = ?', $params['featured']);
		}
		
		if(!empty($params['from_date']))
		{
			$select->where('business.creation_date >= ?', $params['from_date']->get('yyyy-MM-dd'));
		}
		
		if(!empty($params['to_date'])) {
			$select->where('business.creation_date <= ?', $params['to_date']->get('yyyy-MM-dd'));
		}
		
        if(isset($params['user_id'])) {
            $select->where('business.user_id = ?', $params['user_id']);
        }
        else {
            if (!isset($params['admin'])) {
                $select
                    ->where('business.search = ?', 1);
            }
        }
		
		//my claim
		if(isset($params['claimer_id']) && isset($params['claim']))
		{
			$requestTable = Engine_Api::_() -> getDbTable('claimrequests', 'ynbusinesspages');
			$businessIds = array();
			foreach($requestTable -> getClaimRequestByUser($params['claimer_id']) as $business)
			{
				$itemBusiness = Engine_Api::_() -> getItem('ynbusinesspages_business', $business -> business_id);
				if(!empty($itemBusiness))
				{
					if(in_array($itemBusiness -> status, array('claimed', 'unclaimed')))
					{
						$businessIds[] = $business -> business_id;
					}
				}
			}
			if(!$businessIds)
			{
				$businessIds = '';
			}
			$select -> where("business.business_id IN (?)", $businessIds);
		}
		
		// my following
		if(isset($params['follower_id']) && isset($params['follow']))
		{
			$followTable = Engine_Api::_() -> getDbTable('follows', 'ynbusinesspages');
			$businessIds = array();
			foreach($followTable -> getFollowBusinesses($params['follower_id']) as $business)
			{
				$businessIds[] = $business -> business_id;
			}
			if(!$businessIds)
			{
				$businessIds = '';
			}
			$select -> where("business.business_id IN (?)", $businessIds);
		}
		
		// my favourite
		if(isset($params['favouriter_id']) && isset($params['favourite']))
		{
			$followTable = Engine_Api::_() -> getDbTable('favourites', 'ynbusinesspages');
			$businessIds = array();
			foreach($followTable -> getFavouriteBusinesses($params['favouriter_id']) as $business)
			{
				$businessIds[] = $business -> business_id;
			}
			if(!$businessIds)
			{
				$businessIds = '';
			}
			$select -> where("business.business_id IN (?)", $businessIds);
		}
        
		if(isset($params['status_claimed']) && $params['status_claimed'] != 'all')
		{
			$select -> where("business.is_claimed = ?", $params['status_claimed']);
		}
		
		//Tags
	    if (!empty($params['tag'])) 
	    {
	        $select -> setIntegrityCheck(false) 
	        -> joinLeft($tagsTblName, "$tagsTblName.resource_id = business.business_id", "") 
	        -> where($tagsTblName . '.resource_type = ?', 'ynbusinesspages_business') 
	        -> where($tagsTblName . '.tag_id = ?', $params['tag']);
	        $select->where("business.status = 'published' || business.status = 'unclaimed'");
	    }
		
		if (isset($params['featured']) && $params['featured'] != 'all') {
			$select->order('rand()');
		}
		else if (isset($params['order'])) {
	        if (empty($params['direction'])) {
	            $params['direction'] = ($params['order'] == 'business.name') ? 'ASC' : 'DESC';
	        }
            $select->order($params['order'].' '.$params['direction']);
		}
		else {
	        if (!empty($params['direction'])) {
	            $select->order('business.business_id'.' '.$params['direction']);
	        }
			else{
				$select->order('business.business_id DESC');
			}
	    }
    
        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) 
        {
        	if(Engine_Api::_()->getApi('settings', 'core') -> getSetting('ynbusinesspages_radius_unit', 0))
			{
				// kilometer = ? mile
				$target_distance = $target_distance / 1.609344;
			}
            $select -> having("distance <= $target_distance");
            $select -> order("distance ASC");
        }
		return $select;
	}
}
