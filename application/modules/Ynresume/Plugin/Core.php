<?php
class Ynresume_Plugin_Core {
	
	public function onYnjobpostingIndustryCreateAfter($event)
	{
		$settings = Engine_Api::_()->getApi('settings', 'core');
        $settings->setSetting('ynresume_job_industry_change', 1);
	}
	
	public function onYnjobpostingIndustryUpdateAfter($event)
	{
		$settings = Engine_Api::_()->getApi('settings', 'core');
        $settings->setSetting('ynresume_job_industry_change', 1);
	}
	
	public function onUserUpdateAfter($event)
	{
		$user = $event -> getPayload();
		$resumeTable = Engine_Api::_() -> getItemTable('ynresume_resume');
		$resume = $resumeTable -> getResume($user -> getIdentity());
		if($resume)
		{
			$resume -> photo_id = $user -> photo_id;
			$resume -> name = $user -> displayname;
			$resume -> save();
		}
	}
	
	public function onUserDeleteBefore($event) {
		$payload = $event -> getPayload();
		if ($payload instanceof User_Model_User) {
			Engine_Api::_()->getDbTable('recommendations', 'ynresume')->removeRecommendationsOfGiver($payload->getIdentity());
			$resume = Engine_Api::_()->ynresume()->getUserResume($payload->getIdentity());
			if ($resume) {
				$resume->delete();
			}
		}
	}
}
