<?php
class Ynjobposting_Plugin_Task_JobAlert extends Core_Plugin_Task_Abstract
{
	public function execute()
	{
		Engine_Api::_() -> ynjobposting() -> sendJobAlertMail();
	}
}
