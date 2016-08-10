<?php
class Ynjobposting_Plugin_Menus
{
	public function onMenuInitialize_YnjobpostingMainManageJob()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}	
	
	public function onMenuInitialize_YnjobpostingMainManageCompany()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}	
	
	public function onMenuInitialize_YnjobpostingMainManageFollowCompany()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}	
	
	public function onMenuInitialize_YnjobpostingMainCreateJob()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}	
	
	public function onMenuInitialize_YnjobpostingMainCreateCompany()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}	
	
}
