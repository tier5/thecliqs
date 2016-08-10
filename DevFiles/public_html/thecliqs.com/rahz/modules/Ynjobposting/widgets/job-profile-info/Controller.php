<?php
class Ynjobposting_Widget_JobProfileInfoController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject('ynjobposting_job'))
		{
			return $this->setNoRender();
		}
		$this->view->job = $job = Engine_Api::_()->core()->getSubject('ynjobposting_job');
	}
}
