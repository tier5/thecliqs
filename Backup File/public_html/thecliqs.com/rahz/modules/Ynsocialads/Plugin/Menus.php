<?php
class Ynsocialads_Plugin_Menus
{
	public function onMenuInitialize_CoreMainYnsocialads()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}

	public function onMenuInitialize_YnsocialadsMainCreateAd()
	{
		if (!Engine_Api::_() -> authorization() -> isAllowed('ynsocialads_ad', null, 'create'))
		{
			return false;
		}
		return true;
	}

	public function onMenuInitialize_YnsocialadsMainCampaigns()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}

	public function onMenuInitialize_YnsocialadsMainAccount()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}

	public function onMenuInitialize_YnsocialadsMainReport()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}

	public function onMenuInitialize_YnsocialadsMainMyAds()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}

	public function onMenuInitialize_YnsocialadsAccountVirtualMoney()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}
	public function onMenuInitialize_YnsocialadsAccountPaymentTransaction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return false;
		}
		return true;
	}
}
