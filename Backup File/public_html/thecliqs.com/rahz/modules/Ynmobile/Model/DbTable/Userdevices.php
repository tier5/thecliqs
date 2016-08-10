<?php

class Ynmobile_Model_DbTable_Userdevices extends Engine_Db_Table
{
	protected $_name = 'ynmobile_userdevices';
	protected $_rowClass = 'Ynmobile_Model_Userdevice';

	/**
	 * add or update
	 * @param string $iUserId
	 * @param string $sDeviceId
	 * @param string $platform
	 * @return void
	 */
	function addUserDevice($iUserId, $sDeviceId, $sPlatform = 'android')
	{
		global $token;
	
		if (!$token)
		{
			return ;
		}
		
		$id = md5($sDeviceId);
				
		$select = $this->select()
			->where('userdevice_id = ?', $id)
			->order('userdevice_id ASC')
			->limit(1);
			
		$device = $this->fetchRow($select);
		
		if (null !== $device)
		{
			$device->user_id = $iUserId;
			$device->token = $token;
			$device->timestamp = time();
			$device->save();
		}
		else
		{
			$aData = array(
				'userdevice_id' => $id,
				'user_id' => $iUserId,
				'token' => $token,
				'device_id' => $sDeviceId,
				'timestamp' => time(),
				'platform' => $sPlatform
			);
			
			$this->insert($aData);
			
		}
	}
	
	/**
	 * Remove device id from unregister
	 * @param string $sToken
	 * @return void
	 */
	function removeUserDeviceByToken($sToken = null)
	{
		global $token;
	
		if (null == $sToken)
		{
			$sToken = $token;
		}
	
		if (!$sToken)
		{
			return;
		}
		return	$this->delete("token='$sToken'");
	}
	
	/**
	 * @param string $deviceId
	 */
	function removeUserDevice($sDeviceId)
	{
		/**
		 * @var int
		 */
		$id = md5($sDeviceId);
		return	$this->delete("userdevice_id='$id'");
	}
	
	
	/**
	 * Get device id by $iUserId
	 * @param int $iUserId Phpfox user id that
	 * @param int $iUserId Phpfox user id that
	 * @return array
	 */
	function getUserDevices($iUserId)
	{
		$select = $this->select()
		->where('user_id = ?', $iUserId);
		//->where('platform = ?', $platform)
	
		return $this->fetchAll($select);	
	}

}
