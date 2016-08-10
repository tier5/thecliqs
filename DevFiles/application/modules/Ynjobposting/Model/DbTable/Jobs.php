<?php
class Ynjobposting_Model_DbTable_Jobs extends Engine_Db_Table {
	protected $_rowClass = 'Ynjobposting_Model_Job';
	// protected $_serializedColumns = array('photo_ids');

	public function getJobsByCompanyId($company_id)
	{
		$select = $this -> select() -> where('company_id = ?', $company_id);
		return $this -> fetchAll($select);
	}

	public function getJobsPaginator($params = array()) {
		return Zend_Paginator::factory($this->getJobsSelect($params));
	}

	public function getJobsSelect($params = array())
	{
		$jobTbl = Engine_Api::_() -> getItemTable('ynjobposting_job');
		$jobTblName = $jobTbl -> info('name');

		$companyTbl = Engine_Api::_() -> getItemTable('ynjobposting_company');
		$companyTblName = $companyTbl -> info('name');

		$industryTbl = Engine_Api::_() -> getItemTable('ynjobposting_industry');
		$industryTblName = $industryTbl -> info('name');

		$featureTbl = Engine_Api::_()->getDbTable('features', 'ynjobposting');
		$featureTblName = $featureTbl->info('name');

		$tagsTbl = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tagsTblName = $tagsTbl -> info('name');

		$select = $jobTbl -> select();
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
		else {
			$target_distance = 50;
		}

		$select -> setIntegrityCheck(false);
		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance))
		{
			$select -> from("$jobTblName as job", array("job.*", "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( job.latitude ) ) * cos( radians( job.longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( job.latitude ) ) ) ) AS distance"));
			$select -> where("job.latitude <> ''");
			$select -> where("job.longitude <> ''");

		} else {
			$select -> from("$jobTblName as job", "job.*");
		}

		$select
		-> joinLeft("$companyTblName as company","company.company_id = job.company_id", "name as company_name")
		-> joinLeft("$industryTblName as industry","industry.industry_id = job.industry_id", "");

		if (isset($params['job_title']) && $params['job_title'] != '') {
			$select->where('job.title LIKE ?', '%'.$params['job_title'].'%');
		}
		if (isset($params['company_id']) && $params['company_id'] != '0') {
			$select->where('job.company_id = ?', $params['company_id']);
		}
		if (isset($params['company_name']) && $params['company_name'] != '') {
			$select->where('company.name LIKE ?', '%'.$params['company_name'].'%');
		}

        if (isset($params['industry_id']) && $params['industry_id'] != 'all') {
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
                $select->where('job.industry_id IN (?)', $industries);
            }
        }
        
		if (isset($params['level']) && $params['level'] != 'all') {
			$select->where('job.level = ?', $params['level']);
		}
		if (isset($params['type']) && $params['type'] != 'all') {
			$select->where('job.type = ?', $params['type']);
		}
		if (isset($params['salary_from']) && $params['salary_from'] > 0)
		{
			$select-> where('job.salary_from >= ?', $params['salary_from']);
			if (isset($params['salary_currency']))
            {
                $select-> where('job.salary_currency = ?', $params['salary_currency']);
            }
		}
		if (isset($params['featured']) && $params['featured'] != 'all')
		{
			$featureSelect = $featureTbl->select();
			$featureSelect->from($featureTblName, "$featureTblName.job_id");
			$featureSelect->where('active = ?', 1);
			$rawData = $featureTbl->fetchAll($featureSelect);
			$featureList = array();
			foreach ($rawData as $feature) {
				array_push($featureList, $feature->job_id);
			}
			if ($params['featured']) {
				$select->where('job.job_id IN (?)', $featureList);
			}
			else {
				$select->where('job.job_id NOT IN (?)', $featureList);
			}
		}

		if (isset($params['expire_before']) && !empty($params['expire_before'])) {
			$expire_date = Engine_Api::_() -> ynjobposting() -> getFromDaySearch($params['expire_before']);
			if ($expire_date) {
				$select -> where("expiration_date < ?", $expire_date);
			}
		}

		if(isset($params['user_id'])) {
			$select->where('job.user_id = ?', $params['user_id']);
		}
		else
		{
			if (!isset($params['admin']) || !$params['admin'])
			{
				$select->where('job.status IN (?)',array('published', 'expired'));
			}
			else {
				//$select->where('job.status <> ?', 'draft');
			}
		}

		if (isset($params['status']) && $params['status'] != 'all') {
			$select->where('job.status = ?', $params['status']);
		}
		$select->where('job.status <> ?', 'deleted');

		//Tags
		if (!empty($params['tag'])) {
			$select -> setIntegrityCheck(false) -> joinLeft($tagsTblName, "$tagsTblName.resource_id = job.job_id", "") -> where($tagsTblName . '.resource_type = ?', 'ynjobposting_job') -> where($tagsTblName . '.tag_id = ?', $params['tag']);
		}

		if (isset($params['order'])) {
			if ($params['order'] == 'newest') {
				$params['order'] = 'job.approved_date';
				$params['direction'] = 'DESC';
			}
			else if ($params['order'] == 'oldest') {
				$params['order'] = 'job.approved_date';
				$params['direction'] = 'ASC';
			}

			if (empty($params['direction'])) {
				$params['direction'] = ($params['order'] == 'job.title') ? 'ASC' : 'DESC';
			}

			$select->order($params['order'].' '.$params['direction']);
		}
		 
		// Order
		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
			$select -> having("distance <= $target_distance");
			$select -> order("distance ASC");
		}
		return $select;
	}
}
