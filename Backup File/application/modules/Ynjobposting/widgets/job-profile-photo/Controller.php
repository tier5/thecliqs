<?php
class Ynjobposting_Widget_JobProfilePhotoController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject('ynjobposting_job'))
		{
			return $this->setNoRender();
		}
		$job = Engine_Api::_()->core()->getSubject('ynjobposting_job');
		$company = $job->getCompany();
		if (is_null($company))
		{
			return $this->setNoRender();
		}
		$this->view->company = $company;
	}
}
