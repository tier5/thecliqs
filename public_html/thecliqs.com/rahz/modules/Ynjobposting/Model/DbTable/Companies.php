<?php
class Ynjobposting_Model_DbTable_Companies extends Engine_Db_Table {
	protected $_rowClass = 'Ynjobposting_Model_Company';
	
	public function getAllChildrenCompaniesByIndustry($node) {
		$return_arr = array();
		$cur_arr = array();
		$list_industries = array();
		Engine_Api::_() -> getItemTable('ynjobposting_industry') -> appendChildToTree($node, $list_industries);
		foreach ($list_industries as $industry) {
			$tableIndustryMap = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
			$select = $tableIndustryMap -> select() -> where('industry_id = ?', $industry -> industry_id);
			$cur_arr = $tableIndustryMap -> fetchAll($select);
			if (count($cur_arr) > 0) {
				$return_arr[] = $cur_arr;
			}
		}
		return $return_arr;
	}

	public function getCompaniesByIndustry($industry_id) {
		$tableIndustryMap = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
		$select = $tableIndustryMap -> select() -> where('industry_id = ?', $industry_id);
		return $tableIndustryMap -> fetchAll($select);
	}

	public function getMyCompanies($user_id = null) {
		if (!$user_id) {
			$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		}
		$table = Engine_Api::_() -> getItemTable('ynjobposting_company');
		$select = $table -> select() -> where('user_id = ?', $user_id) -> where('status = ?', 'published') -> where('deleted = ?', 0);
		$rawData = $table -> fetchAll($select);
		$result = array();
		foreach ($rawData as $company) {
			$result[$company -> getIdentity()] = $company -> getTitle();
		}
		return $result;
	}

	public function getCompaniesPaginator($params = array()) {
		return Zend_Paginator::factory($this -> getCompaniesSelect($params));
	}

	public function getCompaniesSelect($params = array()) {
		$companyTbl = Engine_Api::_() -> getItemTable('ynjobposting_company');
		$companyTblName = $companyTbl -> info('name');

		$userTbl = Engine_Api::_() -> getDbtable('users', 'user');
		$userTblName = $userTbl -> info('name');

		$industrymapsTbl = Engine_Api::_() -> getDbTable('industrymaps', 'ynjobposting');
		$industrymapsTblName = $industrymapsTbl -> info('name');
		
		$industryTbl = Engine_Api::_() -> getItemTable('ynjobposting_industry');
        $indsutryTblName = $industryTbl -> info('name');
		
		$select = $companyTbl -> select();
		$select -> setIntegrityCheck(false);
		
		//Get your location
		$target_distance = $base_lat = $base_lng = "";
		if (isset($params['lat']))
			$base_lat = $params['lat'];
		if (isset($params['long']))
			$base_lng = $params['long'];

		//Get target distance in miles
		if (isset($params['within']))
			$target_distance = $params['within'];
		else{
			$target_distance = 50;
		}
		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) 
		{
			$select -> from("$companyTblName as company", array("company.*", "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( company.latitude ) ) * cos( radians( company.longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( company.latitude ) ) ) ) AS distance"));
			$select -> where("company.latitude <> ''");
			$select -> where("company.longitude <> ''");
		}
		else 
		{
			$select -> from("$companyTblName as company", "company.*");
		}
		
		$select -> joinLeft("$userTblName as user", "user.user_id = company.user_id", null) 
				-> joinLeft("$industrymapsTblName as industrymap", "industrymap.company_id = company.company_id",null);
				
		if (isset($params['company_name']) && $params['company_name'] != '') {
			$select -> where('company.name LIKE ?', '%' . $params['company_name'] . '%');
		}
		
		if (isset($params['keyword']) && $params['keyword'] != '') {
			$keyword = $params['keyword'];
			$select -> where("company.name LIKE '%{$keyword}%' OR company.description LIKE '%{$keyword}%'");
		} 
		
		if (isset($params['owner']) && $params['owner'] != '') {
			$select -> where('user.displayname LIKE ?', '%' . $params['owner'] . '%');
		}
		
		if (isset($params['industry_id']) && $params['industry_id'] != 'all' && $params['industry_id']) {
			$industrySelect = $industryTbl->select()->where('industry_id = ?', $params['industry_id']);
            $industry = $industryTbl->fetchRow($industrySelect);
            if ($industry) {
                $tree = array();
                $node = $industryTbl -> getNode($industry->getIdentity());
                $industryTbl -> appendChildToTree($node, $tree);
                $industries = array();
                foreach ($tree as $node) {
                    array_push($industries, $node->industry_id);
                }
                $select->where('industrymap.industry_id IN (?)', $industries);
            }
		}
		
		if (isset($params['status']) && $params['status'] != 'all') {
			$select -> where('company.status = ?', $params['status']);
		}
		else {
			$select -> where('company.status = ?', 'published');
		}
		
		if (isset($params['sponsored']) && $params['sponsored'] != 'all') {
			$select -> where('company.sponsored = ?', $params['sponsored']);
		}
		
		if (isset($params['size']) && $params['size']) {
			$size = (int)$params['size'];
			$select -> where('company.from_employee < ?', $params['size']);
			$select -> where('company.to_employee > ?', $params['size']);
		}
		
		if (isset($params['order'])) {
	        if (empty($params['direction'])) {
	            $params['direction'] = ($params['order'] == 'company.title') ? 'ASC' : 'DESC';
	        }
            $select->order($params['order'].' '.$params['direction']);
		}
		else {
			if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
				$select -> having("distance <= $target_distance");
				$select -> order("distance ASC");
			}
	        else if (!empty($params['direction'])) {
	            $select->order('company.company_id'.' '.$params['direction']);
	        }
	    }
		$select -> group('company.company_id');
		return $select;
	}

}
