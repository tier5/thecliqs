<?php
class Ynjobposting_Plugin_Task_CheckFeaturedStatus extends Core_Plugin_Task_Abstract
{
	public function execute()
	{
		$this->checkSponsors();
		$this->checkFeaturedJobs();
		$this->checkExpiredJobs();
	}
	
	protected function checkSponsors()
	{
		$now = date("Y-m-d H:i:s");
		$sponsorTbl = Engine_Api::_()->getItemTable('ynjobposting_sponsor');
		$select = $sponsorTbl -> select() 
		-> where("active = ? ", '1')
		-> where("expiration_date < '$now'")
		;
		$sponsors = $sponsorTbl -> fetchAll($select);
		if (count($sponsors))
		{
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			foreach ($sponsors as $sponsor)
			{
				$sponsor -> active = 0;
				$sponsor -> save();
				$company = Engine_Api::_()->getItem('ynjobposting_company', $sponsor->company_id);
				//send notifications
				$owner = $company -> getOwner();
				$notifyApi -> addNotification($owner, $owner, $company, 'ynjobposting_company_unsponsored');
			}
		}
	}
	
	protected function checkFeaturedJobs()
	{
		$now = date("Y-m-d H:i:s");
		$featureTbl = Engine_Api::_()->getItemTable('ynjobposting_feature');
		$select = $featureTbl -> select() 
		-> where("active = ? ", '1')
		-> where("expiration_date < '$now'")
		;
		$features = $featureTbl -> fetchAll($select);
		if (count($features))
		{
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			foreach ($features as $feature)
			{
				$job = Engine_Api::_()->getItem('ynjobposting_job', $feature->job_id);
				if (!is_null($job))
				{
					$job -> featured = 0;
					$job -> number_day = 0;
					$job -> save();
					
					//send notifications
					$owner = $job -> getOwner();
					$notifyApi -> addNotification($owner, $owner, $job, 'ynjobposting_job_unfeatured');
				}
				$feature -> active = 0;
				$feature -> save();
			}
		}
	}
	
	protected function checkExpiredJobs()
	{
		$now = date("Y-m-d H:i:s");
		$jobTbl = Engine_Api::_()->getItemTable('ynjobposting_job');
		$select = $jobTbl -> select() 
		-> where("status IN (?) ", array('ended','published'))
		-> where("expiration_date < '$now'")
		;
		$jobs = $jobTbl -> fetchAll($select);
		if (count($jobs))
		{
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			foreach ($jobs as $job)
			{
				$job -> status = 'expired';
				$job -> save();
				//send notifications
				$owner = $job -> getOwner();
				$notifyApi -> addNotification($owner, $owner, $job, 'ynjobposting_job_expired');
			}
		}
	}
	
}
