<?php


class Ynmobile_Api_PushNotification
{
	/**
	 * Url of google could messge to send
	 */
	CONST GOOGLE_SEND_URL = 'https://android.googleapis.com/gcm/send';
	CONST APPLE_SEND_URL = 'ssl://gateway.push.apple.com:2195';
	CONST DEBUG = TRUE;
	
	/**
	 * <code>
	 * require: ....
	 * $bool =  Engine_Api::_('PushNotification','Ynmobile')->PushNotificationforIOS($iosArray=array(), $iUserId = 3);
	 * <code>
	 * @see 
	 * @param array $aData send data must be array to parse by json_decode
	 * @param array $aDeviceIds
	 * @return bool true if message is sent or false otherwise
	 */
	public function PushNotificationforIpad($aDeviceIds, $aData)
	{
		try
		{
			$sURL = self::APPLE_SEND_URL;
			$local_cert = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmobile.apple.ipad.cert.filepath', '');
			if ($local_cert == '')
			{
				$local_cert = APPLICATION_PATH . '/application/modules/Ynmobile/push_imse_ipad.pem';
			}
			
			$passphrase = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmobile.apple.ipad.pass', '');
			if ($passphrase == '')
			{
				$passphrase = 'Appl3@YN';
			}
			$params = array('ssl' => array(
					'local_cert' => $local_cert,
					'passphrase' => $passphrase,
			));
			
			$context = stream_context_create($params);
			$fp = stream_socket_client($sURL, $nError, $sError, 50000, STREAM_CLIENT_CONNECT, $context);
			self::DEBUG && print("Connecting with $sURL error : {$nError} {$sError} {$context}\n");
			
			if (!$fp)
			{
				return FALSE;
			}
			
			$payload = json_encode($aData);

			foreach ($aDeviceIds as $device_id)
			{
				$msg = chr(0) . pack('n', 32) . pack('H*', $device_id) . pack('n', strlen($payload)) . $payload;
				$result = fwrite($fp, $msg, strlen($msg));
				self::DEBUG &&  print("Send $sURL msg ({$msg}) result ({$result})\n");
			}
			
			fclose($fp);
			self::DEBUG && print ("sent messages successfully to apns.");
			
			return TRUE;
		}
		catch(Exception $ex)
		{
		  if(APPLICATION_ENV == 'DEVELOPMENT')
		  {
		  	print("{$ex->getMessage()}");
		  }

		}
		return FALSE;
	}

	public function PushNotificationforIOS($aDeviceIds, $aData)
	{
		try
		{
			$sURL = self::APPLE_SEND_URL;
				
			//$local_cert  = '/push_imse.pem';
			$local_cert = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmobile.apple.cert.filepath', '');	
			if ($local_cert == '')
			{
				$local_cert = APPLICATION_PATH . '/application/modules/Ynmobile/push_imse.pem';
			}
				
			//$passphrase = 'Appl3@YN';
			$passphrase = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmobile.apple.pass', '');
			if ($passphrase == '')
			{
				$passphrase = 'Appl3@YN';
			}
			$params = array('ssl' => array(
					//'verify_peer' => true,
					'local_cert' => $local_cert,
					'passphrase' => $passphrase,
			));
	
			$context = stream_context_create($params);
			
			// var_dump($params, $context);
			
			$fp = @stream_socket_client($sURL, $nError, $sError, 30000, STREAM_CLIENT_CONNECT, $context);
			
			self::DEBUG && print("Connecting $sURL error: {$nError} {$sError}\n");
				
			if (!$fp)
			{
				self::DEBUG && print("Could not connect $sURL error: {$nError} {$sError}\n");
				return FALSE;
			}
				
			$payload = json_encode($aData);
	
			foreach ($aDeviceIds as $device_id)
			{
				$msg = chr(0) . pack('n', 32) . pack('H*', $device_id) . pack('n', strlen($payload)) . $payload;
				$result = fwrite($fp, $msg, strlen($msg));
				self::DEBUG &&  print("Send $sURL msg ({$msg}) result ({$result})\n");
			}
				
			fclose($fp);
			self::DEBUG && print ("sent messages successfully to apns.");
			
			return TRUE;
		}
		catch(Exception $ex)
		{
			if(APPLICATION_ENV == 'DEVELOPMENT')
			{
				print("{$ex->getMessage()}");
			}
	
		}
		return FALSE;
	}
	
	public function PushNotificationforAndroid($aDeviceIds, $aData, $sServerApiKey)
	{
		
		$fields = array(
			'registration_ids' => $aDeviceIds,
			'data' => $aData,
		);

		$headers = array(
			'Authorization: key=' . $sServerApiKey,
			'Content-Type: application/json'
		);

		// Open connection
		$ch = curl_init();

		// Set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, self::GOOGLE_SEND_URL);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

		// Execute post
		$response = curl_exec($ch);
		
		/**
		 * missing response failed.
		 * what happend when null === $response; 
		 */

// 		if ($_SERVER['REMOTE_ADDR'] == "192.168.11.87"){
// 			var_dump(curl_error($ch));
// 			var_dump($response); exit;
// 		}
		// Close connection
		
		curl_close($ch);
		$result = json_decode($response, 1);
		

		return $result;
	}
	
	/**
	 * <code>
	 * list($androidMessage, $iosMesssage) =  $this->send($array = $listOfIOSDeviceIds, $sendToiUserID);
	 * <code>
	 * 
	 * @see shema engine4_ynmobile_userdivices
	 * @param array $aData
	 * @param int $iUserId
	 * @return array  (0=>androidResponseMessage, 1=>iosResponseMessage)
	 * @throws Exception
	 * 
	 */
	function send($aData, $iUserId)
	{
		
		/**
		 * etc: AIzaSyB3un2VRYz6LHmTVl8AvWRd-R7udZgTYDU
		 * @var string
		 */
		$sServerApiKey = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynmobile.google.api', '');
		//$sServerApiKey = "AIzaSyAbT_waGAuZ-LqLjcTQWzY3dJ8RJbovPeI";
		
		$aDataIOS = isset($aData['ios']) ? $aData['ios'] : null;
		$aDataAndroid = isset($aData['android']) ? $aData['android'] : null;
		
		$enabled_android = true;
		
		
		if (strlen($sServerApiKey) < 8)
		{
			$enabled_android = false;
			//return array('message' => 'google api key is empty!');
		}

		/**
		 * Registration Ids of devices, also called "devices id"
		 * @var array
		 */
		
		/**
		 * 1. get all device_id by user_id
		 * 2. filter device_id by platform: => $ios_devices, $android_devices
		 * 3. using PushNotificationforAndroid-> send data
		 * 4. using PushNotificationforIOS-> send data
		 */
		
		$deviceTable = Engine_Api::_() -> getDbtable('userdevices', 'ynmobile');
		$devices = $deviceTable->getUserDevices($iUserId);
		
		if (count($devices) == 0)
			return;
		
		$ios_devices = $android_devices = array();
		
		foreach ($devices as $device)
		{
			if ($device->platform == 'android')
			{
				$android_devices[] = $device->device_id;
			}
			elseif ($device->platform == 'ios')
			{
				$ios_devices[] = $device->device_id;
			}
			elseif ($device->platform == 'ipad')
			{
				$ipad_devices[] = $device->device_id;
			}
		}
		
		
		
		if ($enabled_android && count($android_devices) > 0)
			$response_android = $this->PushNotificationforAndroid($android_devices, $aDataAndroid, $sServerApiKey);
			
		if (count($ios_devices) > 0)
			$response_ios = $this->PushNotificationforIOS($ios_devices, $aDataIOS);
			
		if (count($ipad_devices) > 0)
			$response_ipad = $this->PushNotificationforIpad($ipad_devices, $aDataIOS);
		
		return array(
			$response_android,
			$response_ios,
			$response_ipad
		);
	}
}