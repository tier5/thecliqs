<?php
class Ynsocialads_Api_Core extends  Core_Api_Abstract
{
	public function checkYouNetPlugin($name)
	{
		$table = Engine_Api::_() -> getDbTable('modules', 'core');
		$select = $table -> select() -> where('name = ?', $name) -> where('enabled  = 1');
		$result = $table -> fetchRow($select);
		if ($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getGateway($gateway_id)
	{
		return $this -> getPlugin($gateway_id) -> getGateway();
	}

	public static function partialViewFullPath($partialTemplateFile)
	{
		$ds = DIRECTORY_SEPARATOR;
		return "application{$ds}modules{$ds}Ynsocialads{$ds}views{$ds}scripts{$ds}{$partialTemplateFile}";
	}

	public function getPlugin($gateway_id)
	{
		if (null === $this -> _plugin)
		{
			if (null == ($gateway = Engine_Api::_() -> getItem('payment_gateway', $gateway_id)))
			{
				return null;
			}
			Engine_Loader::loadClass($gateway -> plugin);
			if (!class_exists($gateway -> plugin))
			{
				return null;
			}
			if (in_array($gateway -> title, array(
				'Authorize.Net',
				'iTransact'
			)))
			{
				$class = str_replace('Ynpayment', 'Ynsocialads', $gateway -> plugin);
			}
			else
			{
				$class = str_replace('Payment', 'Ynsocialads', $gateway -> plugin);
			}

			Engine_Loader::loadClass($class);
			if (!class_exists($class))
			{
				return null;
			}

			$plugin = new $class($gateway);
			if (!($plugin instanceof Engine_Payment_Plugin_Abstract))
			{
				throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' . 'implement Engine_Payment_Plugin_Abstract', $class));
			}
			$this -> _plugin = $plugin;
		}
		return $this -> _plugin;
	}

	public function checkAndUpdateStatus($ad)
	{

		$create_date = strtotime($ad -> creation_date);
		$start_date = strtotime($ad -> start_date);
		$end_date = strtotime($ad -> end_date);
		$current_date = $today = strtotime(date("Y-m-d H:i:s"));

		$virtualTable = Engine_Api::_() -> getItemTable('ynsocialads_virtual');
		$virtual_bank = $virtualTable -> fetchRow($virtualTable -> select() -> where('user_id = ?', $ad -> user_id) -> limit(1));

		$isPayLater = 0;
		$isPayLaterApproved = 0;
		//check if ad is paid later
		$transactionTable = Engine_Api::_() -> getDbTable('transactions', 'ynsocialads');
		$transAd = $transactionTable -> fetchRow($transactionTable -> select() -> where('ad_id = ?', $ad -> getIdentity()) -> limit(1));
		if (($transAd -> status == 'initialized') && ($transAd -> gateway_id == '-2'))
		{
			$isPayLater = 1;
		}
		if (($transAd -> status == 'completed') && ($transAd -> gateway_id == '-2'))
		{
			$isPayLaterApproved = 1;
		}

		// If as was created and paid later and admin confirmed
		if ($isPayLaterApproved && $ad -> approved)
		{
			if (($start_date < $current_date) && ($current_date < $end_date))
			{
				$ad -> status = 'running';
				$ad -> running_date = date("Y-m-d H:i:s");
			}
			if ($current_date < $start_date)
			{
				$ad -> approved = '1';
			}
			if ($current_date > $end_date)
			{
				$ad -> status = 'completed';
			}
			
		}

		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$paylaterexpiretime = $settings -> getSetting('ynsocialads_paylaterexpiretime');

		if ($isPayLater)
		{
			$creation_date = new DateTime($transAd -> creation_date);
			$now = new DateTime;
			$diff = date_diff($creation_date, $now);

			if ($diff -> format('%a') > $paylaterexpiretime)
			{
				$ad -> status = 'denied';
				$ad -> approved = '0';
				$transAd -> status = 'expired';
			}
		}

		// If ad was created and placed order successfully and auto approve
		elseif ($ad -> approved && ($ad -> status == 'pending' || $ad -> status == 'approved'))
		{
			if(empty($start_date))
			{
				$ad -> status = 'running';
				$ad -> running_date = date("Y-m-d H:i:s");
			}
			else {
			     
				if (($start_date < $current_date) && ($current_date < $end_date))
				{
					$ad -> status = 'running';
					$ad -> running_date = date("Y-m-d H:i:s");
				}
				if ($current_date < $start_date)
				{
					$ad -> approved = '1';
				}

				if ($current_date > $end_date)
				{
				   
					$ad -> status = 'completed';
				}
			}
		}

		$remain = $ad -> getRemain();
		//If ad meets the end date or meet the goal of clocks/impressions -> completed
		if ($remain <= 0)
		{
			$ad -> status = 'completed';
		}

		if ($ad -> status == 'completed')
		{
			// handle if ad did not reach target
			if ($remain > 0)
			{
				if ($virtual_bank)
				{
					$virtual_bank -> total += $ad -> getTotalTarget();
					$virtual_bank -> remain += $ad -> getTotalTarget();
					$virtual_bank -> save();
				}
				else
				{
					$virtual_bank = $virtualTable -> createRow();
					$virtual_bank -> user_id = $ad -> user_id;
					$virtual_bank -> total += $ad -> getTotalTarget();
					$virtual_bank -> remain += $ad -> getTotalTarget();
					$virtual_bank -> save();
				}
			}
		}
		$ad -> save();
		if($transAd){
			$transAd->save();
		}	
	}

	public function getAudiences($options) {

            
		$success = true;
		$table = Engine_Api::_() -> getItemTable('user');
		$userTableName = $table -> info('name');

		$searchTable = Engine_Api::_() -> fields() -> getTable('user', 'search');
		$searchTableName = $searchTable -> info('name');
		$targetAvailable = $searchTable -> info('cols');
		$select = $table -> select()
		// -> setIntegrityCheck(false)
		-> from($userTableName) -> joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
		// -> group("{$userTableName}.user_id")
		-> where("{$userTableName}.search = ?", 1) -> where("{$userTableName}.enabled = ?", 1);
		
		if ($options['birthdate']['min'] == '0'){
		    $options['birthdate']['min'] = null;
		}
        if ($options['birthdate']['max'] == '0'){
            $options['birthdate']['max'] = null;
        }
        if ($options['gender'] == '0'){
            unset($options['gender']);
        }
        if ($options['profile_type'] == '0'){
            unset($options['profile_type']);
        }
        
         
		if (in_array('city', $targetAvailable))
		{
			if (!empty($options['cities']))
			{
			    
				$cities = explode(',', $options['cities']);
				$trimmed_cities = array_map('trim', $cities);
				unset($options['cities']);
				if (count($trimmed_cities))
				{
					$select -> where('city IN (?)', $trimmed_cities);
				}
			}
		}

		if (in_array('country', $targetAvailable))
		{
		    
			if (isset($options['countries']))
			{
				$countries = $options['countries'];
				unset($options['countries']);
				if (count($countries))
				{
					$select -> where('country IN (?)', $countries);
				}
			}
		}

		if (in_array('interests', $targetAvailable))
		{
			if ($options['interests'])
			{
				$interests = explode(',', $options['interests']);
				$trimmed_interests = array_map('trim', $interests);
				unset($options['interests']);
				if (count($trimmed_interests))
					$select -> where('interests IN (?)', $trimmed_interests);
			}
		}

		if ($options['birthday'])
		{
			$current = new Zend_Date();
			$current -> setLocale();
			$select -> where('birthdate = ?', $current -> get('YYYY-MM-dd'));
		}

		if (isset($options['networks']))
		{
			$networks = $options['networks'];
			if (count($networks))
			{
				$membershipTbl = Engine_Api::_() -> getDbtable('membership', 'network');
				$membershipTblName = $membershipTbl -> info('name');
				$users = array();
				$newSelect = $membershipTbl -> select() -> from($membershipTblName, 'user_id') -> where('resource_id IN (?)', $networks);
				$usersRes = $membershipTbl -> fetchAll($newSelect);
				$usersRes = $usersRes -> toArray();
				foreach ($usersRes as $user)
				{
					$users[] = $user['user_id'];
				}
				if (count($user))
					$select -> where('user_id IN (?)', $users);
				else
				{
					$success = false;
				}
			}
		}


		$searchParts = Engine_Api::_() -> fields() -> getSearchQuery('user', $options);
		foreach ($searchParts as $k => $v)
		{
			$select -> where("`{$searchTableName}`.{$k}", $v);
		}
		if ($success)
		{
           
			$result = $table -> fetchAll($select);
			return $result;
		}
		else
		{
			return ( array());
		}
	}

}
