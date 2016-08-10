<?php
class Ynjobposting_Model_DbTable_Jobapplies extends Engine_Db_Table 
{
	protected $_rowClass = 'Ynjobposting_Model_Jobapply';
	
	public function getHotCompany($limit = null)
	{
		$db = $this->getDefaultAdapter();
		$sql = "SELECT DISTINCT engine4_ynjobposting_jobs.company_id
            FROM engine4_ynjobposting_jobs 
            LEFT JOIN (
	           SELECT
	               `engine4_ynjobposting_jobapplies`.`job_id`,
                    COUNT(*) AS `count_candidate`
	           FROM `engine4_ynjobposting_jobapplies`
	           GROUP BY `job_id`
	       ) AS t
            ON engine4_ynjobposting_jobs.job_id = t.job_id
            ORDER BY t.`count_candidate` DESC ";
		
		$stmt = $db->query($sql);
		$result = $stmt->fetchAll() ;
		$companies = array();
		$i = 0;
		foreach($result as $row)
		{
			$company = Engine_Api::_()->getItem('ynjobposting_company', $row['company_id']);
			if ($company->status == 'published')
			{
				$companies[] = $company;
				$i++;	
			}
			if (!is_null($limit))
			{
				if ($i == $limit)
				{
					break;
				}
			}
		}
		return $companies;
	}
    
    public function getMyAppliedJobs($user_id = null) {
        if (is_null($user_id)) {
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $select = $this->select()->where('user_id  = ?', $user_id);
            $rows = $this->fetchAll($select);
            $jobs = array();
            foreach ($rows as $row) {
                array_push($jobs, $row->job_id);
            }
            return $jobs;
        }
    }
    
    public function getLastAppliedJob($user_id = null) {
        if (is_null($user_id)) {
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $select = $this->select()->where('user_id  = ?', $user_id)->order('jobapply_id DESC');
            $row = $this->fetchRow($select);
            if ($row) {
                return Engine_Api::_()->getItem('ynjobposting_job', $row->job_id);
            } 
            else return null;
        }
    }
}
