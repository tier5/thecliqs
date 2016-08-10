<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Device.php longl $
 * @author     Long Le
 */
class Ynmobile_Api_Device extends Core_Api_Abstract
{
	/**
	 * called from mobile after login successful.
	 * @param array $aData
	 */
	function register($aData)
	{
		if (!isset($aData['sDeviceId']))
		{
			return array (
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Invalid Device Id")
			);
		}
		
		$sDeviceId = $aData['sDeviceId'];
		$sPlatform = isset($aData['sPlatform']) ? $aData['sPlatform'] : 'android';
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$iUserId = $viewer->getIdentity();

		$deviceTable = Engine_Api::_() -> getDbtable('userdevices', 'ynmobile');
		$deviceTable->addUserDevice($iUserId, $sDeviceId,$sPlatform);

		return array(
				'success' => 1,
				'iUserId' => $iUserId,
				'device_id' => $sDeviceId
		);
	}

	/**
	 * unregister device
	 * @param array $aData
	 * @return array
	 */
	function unregister($aData)
	{
		if (!isset($aData['sDeviceId']))
		{
			return array (
					'result' => 0,
					'error_code' => 1,
					'error_message' => $translate->_("Invalid Device Id")
			);
		}
		
		$this -> removeUserDevice($aData['sDeviceId']);
		return array(
				'success' => 1
		);
	}
}
