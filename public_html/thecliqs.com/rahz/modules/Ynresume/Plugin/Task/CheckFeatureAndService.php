<?php
class Ynresume_Plugin_Task_CheckFeatureAndService extends Core_Plugin_Task_Abstract
{
	public function execute()
	{
		$resumeTable = Engine_Api::_() -> getItemTable('ynresume_resume');
		$current_date = strtotime(date("Y-m-d H:i:s"));
		
		//check service
		$serviceResumes = $resumeTable -> getResumesByType('serviced');
		foreach($serviceResumes as $serviceResume)
		{
			$service_expiration_date = strtotime($serviceResume -> service_expiration_date);
				if($service_expiration_date > 0)
				{
					if($current_date > $service_expiration_date)
					{
						$serviceResume -> serviced = false;
						$serviceResume -> service_expiration_date = null;
						$serviceResume -> save();
					}
				}
		}	
		
		//check feature
		$featureResumes = $resumeTable -> getResumesByType('featured');
		foreach($featureResumes as $featureResume)
		{
			$feature_expiration_date = strtotime($featureResume -> feature_expiration_date);
				if($feature_expiration_date > 0)
				{
					if($current_date > $feature_expiration_date)
					{
						$featureResume -> featured = false;
						$featureResume -> feature_expiration_date = null;
						$featureResume -> save();
					}
				}
		}	
	}
}
