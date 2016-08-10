<?php

class Ynmediaimporter_Api_Core
{
    public function getAdapter($name)
    {

    }
	/**
	 * Check Social Bridge Plugin
	 */
	public function checkSocialBridgePlugin()
	{
		$module = 'socialbridge';
		$modulesTable = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $modulesTable -> select() -> where('enabled = ?', 1) -> where('name  = ?', $module);
		$module_result = $modulesTable -> fetchRow($mselect);
		if (count($module_result) > 0)
		{
			return true;
		}
		return false;
	}
	
	public function checkFacebookApp(){
		$name = 'facebook';
		$apiSetting = Engine_Api::_() -> getDbtable('apisettings', 'socialbridge');
		$select = $apiSetting->select()->where('api_name = ?',$name);
		$provider = $apiSetting->fetchRow($select);
		if ($provider == null)
		{
			return false;
		}
		$api_params = unserialize($provider -> api_params);
		$appId = $api_params['key'];
		$secret = $api_params['secret'];
		if ($appId && $secret)
			return true;
		return false;
	}
	
}
