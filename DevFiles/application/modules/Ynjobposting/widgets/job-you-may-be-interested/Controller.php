<?php
class Ynjobposting_Widget_JobYouMayBeInterestedController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $table = Engine_Api::_()->getItemTable('ynjobposting_job');
        $jobTblName = $table->info('name');
        $num_of_jobs = $this->_getParam('num_of_jobs', 3);
        $select = $table->select()
            ->where('status = ?', 'published')
            ->order(new Zend_Db_Expr(('rand()')))
            ->limit($num_of_jobs);
        $viewer = Engine_Api::_()->user()->getViewer();
        $selectionJobs = array();
        $appliedJob = array();
        if ($viewer->getIdentity()) {
            $appliedJob = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting')->getMyAppliedJobs();
            $email = $viewer->email;
            $alertTbl = Engine_Api::_()->getDbTable('alerts', 'ynjobposting');
            $alert = $alertTbl->getLatestRowByEmail($email);
            if ($alert) {
                $alertSelect = $table->select()
                ->where('status = ?', 'published')
                ->order(new Zend_Db_Expr(('rand()')))
                ->limit($num_of_jobs);
                $alertSelect->where('industry_id = ?', $alert->industry_id);
                if ($alert->level) {
                    $alertSelect->where('level = ?', $alert->level);
                }
                if ($alert->type) {
                    $alertSelect->where('type = ?', $alert->type);
                }
                
                if ($alert->salary > 0) {
                    $alertSelect->where("salary_from >= $alert->salary AND salary_currency = '$alert->currency'");
                }
                
                if (!empty($appliedJob)) {
                    $alertSelect->where('job_id NOT IN (?)', $appliedJob);
                }
                if ($alert->longitude && $alert->latitude && $alert->within) {
                    $alertSelect -> from("$jobTblName", "$jobTblName.*,( 3959 * acos( cos( radians('$alert->latitude')) * cos( radians( $jobTblName.latitude ) ) * cos( radians( $jobTblName.longitude ) - radians('$alert->longitude') ) + sin( radians('$alert->latitude') ) * sin( radians( $jobTblName.latitude ) ) ) ) AS distance");
                    $alertSelect -> where("latitude <> ''");
                    $alertSelect -> where("longitude <> ''");
                    $alertSelect -> having("distance <= $alert->within");
                    $alertSelect -> order("distance ASC");
                }
                $alertJobs = $table->fetchAll($alertSelect);
                $selectionJobs = array_merge($selectionJobs, $alertJobs->toArray());
            }
            if (!empty($appliedJob)) {
                $lastAppliedJob = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting')->getLastAppliedJob();
                if (!is_null($lastAppliedJob)) {
                    $lastAppliedSelect = $table->select()
                    ->where('status = ?', 'published')
                    ->order(new Zend_Db_Expr(('rand()')))
                    ->limit($num_of_jobs);
                    $lastAppliedSelect->where('industry_id = ?', $lastAppliedJob->industry_id);
                    if ($lastAppliedJob->level) {
                        $lastAppliedSelect->where('level = ?', $lastAppliedJob->level);
                    }
                    if ($lastAppliedJob->type) {
                        $lastAppliedSelect->where('type = ?', $lastAppliedJob->type);
                    }
                    
                    $lastAppliedSelect->where('job_id NOT IN (?)', $appliedJob);
                    
                    $lastAppliedJobs = $table->fetchAll($lastAppliedSelect);
                    $selectionJobs = array_merge($selectionJobs, $lastAppliedJobs->toArray());
                }
            }
        }
        $jobs = array();
        
        if (!empty($selectionJobs))
            $jobs = array_unique($selectionJobs);
        if (!empty($jobs) && count($jobs) > $num_of_jobs)
            $jobs = array_rand($jobs, $num_of_jobs);
        if (sizeof($jobs) < $num_of_jobs) {
            foreach ($jobs as $job) {
                array_push($appliedJob, $job['job_id']);
            }
            if (!empty($appliedJob))
                $select->where('job_id NOT IN (?)', $appliedJob)->limit($num_of_jobs - sizeof($jobs));
            $ranJobs = $table->fetchAll($select)->toArray();
            $jobs = array_merge($jobs, $ranJobs);
        }
        $this->view->jobs = $jobs; 
		
		if (empty($jobs)) {
			$this->setNoRender();
		}
    }
}
