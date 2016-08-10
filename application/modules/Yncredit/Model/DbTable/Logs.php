<?php
class Yncredit_Model_DbTable_Logs extends Engine_Db_Table
{
	protected $_name = "yncredit_logs";
	protected $_rowClass = "Yncredit_Model_Log";
	
	public function checkCreditInPeriod($credit, $user)
	{
		if ($credit->action_type == 'signup')
		{
			return true;
		}
		if ($credit == null || $credit->module == null)
		{
			return false;
		}
	
		$select = $this->select()
			-> from($this->info('name'), new Zend_Db_Expr("SUM(credit)"))
			-> where('user_id = ?', $user->getIdentity())
			-> where('credit_id = ?', $credit->credit_id);
	
		if ($credit->period)
		{
			$select->where('creation_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL {$credit->period} DAY)"));
		}
		$total = $select->query()->fetchColumn();
		return ($total < $credit->max_credit) ? true : false;
	}
	
	public function getRemainingCredits($credit, $user)
	{
		if ($credit == null || $credit->module == null)
		{
			return 0;
		}
	
		$select = $this->select()
			-> from($this->info('name'), new Zend_Db_Expr("SUM(credit)"))
			-> where('user_id = ?', $user->getIdentity())
			-> where('credit_id = ?', $credit->credit_id);
	
		if ($credit->period)
		{
			$select->where('creation_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL {$credit->period} DAY)"));
		}
	
		$total = $select->query()->fetchColumn();
		return $credit->max_credit - $total;
	}
	
	// "group_join", "advgroup_join", "event_join", "ynevent_join", "network_join"
	public function checkJoinAction($credit, $obj)
	{
		$select = $this->select()
			->where('credit_id = ?', $credit->credit_id)
			->where('user_id = ?', $obj->subject_id)
			->where('object_id = ?', $obj->object_id)
			->where('object_type = ?', $obj->object_type)
			->limit(1);
	
		if ($this->fetchRow($select) !== null) 
		{
			return true;
		}
		return false;
	}
	
	public function checkProfileCompleted($credit, $obj)
	{
		$select = $this->select()
			->where('credit_id = ?', $credit->credit_id)
			->where('user_id = ?', $obj->getIdentity())
			->where('object_id = ?', $obj->getIdentity())
			->where('object_type = ?', $obj->getType())
			->limit(1);
		if ($this->fetchRow($select) !== null) 
		{
			return true;
		}
		return false;
	}
	
	public function checkSignupAction($credit, $obj)
	{
		$select = $this->select()
			->where('credit_id = ?', $credit->credit_id)
			->where('user_id = ?', $obj->getIdentity())
			->where('object_id = ?', $obj->getIdentity())
			->where('object_type = ?', 'user')
			->limit(1);
	
		if ($this->fetchRow($select) !== null) 
		{
			return true;
		}
		return false;
	}
	public function checkLikeAction($credit, $obj)
	{
		if ($obj->getType() == 'core_like') 
		{
			$object_type = $obj->resource_type;
		} 
		elseif ($obj->getType() == 'activity_like') 
		{
			$object_type = 'activity_action';
		} 
		else 
		{
			return false;
		}
		
		$select = $this->select()
			->where('credit_id = ?', $credit->credit_id)
			->where('user_id = ?', $obj->poster_id)
			->where('object_id = ?', $obj->resource_id)
			->where('object_type = ?', $object_type)
			->limit(1);
		
		if ($this->fetchRow($select) !== null) 
		{
			return true;
		}
		return false;
	}
	
	
	public function getTranactionsPaginator($params = array())
	{
		$select = $this -> getTranactionsSelect($params);
		return Zend_Paginator::factory($select);
	}
	
	public function getTranactionsSelect($params = array())
	{
		$logName = $this -> info('name');
		
		$typeTable = Engine_Api::_() -> getDbTable('types', 'yncredit');
		$typeName = $typeTable -> info('name');
		
		$disableModules = array();
		if(isset($params['type']) && $params['type'] == 'user')
	    {
			$levelId = $params['level_id'];
	    	$modules = Engine_Api::_() -> getDbTable('modules', 'yncredit') -> getModulesDisabled($levelId);
			foreach($modules as $module)
			{
				$disableModules[] = $module['name'];
			}
	    }
		
		$moduleTbl = Engine_Api::_()->getDbTable("modules", "core");
		$modules = $moduleTbl->select()->where("enabled = ?", 1)->query()->fetchAll();
		$enabledModules = array();
		foreach ($modules as $key => $module)
		{
			if(!in_array($module['name'], $disableModules))
				$enabledModules[] = $module['name'];
		}
		
		$enableNames = "";
		if($enabledModules)
			$enableNames = array_unique($enabledModules);
		
		$select = $this -> select() 
			-> from($logName, "$logName.*") 
			-> setIntegrityCheck(false)
			-> joinLeft($typeName, "$logName.type_id = $typeName.type_id", "$typeName.*")
			-> where("$typeName.module in (?)", $enableNames)
			;
		// Check mp3 music and mp3 music selling
		if(in_array('mp3music', $enabledModules))
		{
			$select_mp3music = $moduleTbl->select()->where("name = 'mp3music'");
			$mp3music = $moduleTbl -> fetchRow($select_mp3music);
			if($mp3music && strpos($mp3music -> version, "s") == FALSE)
			{
				$select -> where("$typeName.`action_type` <> 'buy_mp3music'");
			}
		}
		
		$userTable = Engine_Api::_()->getItemTable('user');
		$userName = $userTable -> info('name');	
		$select -> joinLeft($userName, "$userName.user_id = $logName.user_id", "");
		if(!empty($params['member']))
		{
			$select -> where("$userName.displayname LIKE ?", "%".$params['member']."%");
		}
		if(!empty($params['user_id']))
		{
			$select -> where("$logName.user_id = ?", $params['user_id']);
		}
		if(!empty($params['group']))
		{
			$select -> where("$typeName.group = ?", $params['group']);
		}
		if(!empty($params['modu']))
		{
			$select -> where("$typeName.module = ?", $params['modu']);
		}
		if(!empty($params['action_type']))
		{
			$select -> where("$typeName.type_id = ?", $params['action_type']);
		}
		
		if(!empty($params['time']))
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$oldTz = date_default_timezone_get();
			if($viewer -> getIdentity())
				date_default_timezone_set($viewer -> timezone);
			$date = date('Y-m-d H:i:s');
			$sub3monthTime = strtotime('-3 months', time());
			date_default_timezone_set($oldTz);
			
			$time = strtotime($date);
			$sub = $time - time();
			switch ($params['time']) {
				case 'today':
					$select ->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date)+ {$sub})) = YEAR('{$date}')")
			       			->where("MONTH(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date)+ {$sub})) = MONTH('{$date}')")
			       			->where("DAY(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date)+ {$sub})) = DAY('{$date}')");
					break;
				case 'week':
					$select ->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date) + {$sub})) = YEAR('{$date}')")   
	       					->where("WEEK(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date) + {$sub})) = WEEK('{$date}')");
					break;
				case 'month':
					$select ->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date) + {$sub})) = YEAR('{$date}')")
	       					->where("MONTH(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date) + {$sub})) = MONTH('{$date}')");
					break;
				case '3month':
					$select ->where("FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date) + {$sub}) > {$sub3monthTime}");
					break;
			}
		}
		// From date
		if (!empty($params['start_date']) && empty($params['end_date']))
		{
			$fromdate = $this -> _getFromDaySearch($params['start_date']);
			if (!$fromdate)
			{
				$select -> where("false");
				return $select;
			}
			$select -> where("($logName.creation_date >= ?)", $fromdate);
		}
		// To date
		if (!empty($params['end_date']) && empty($params['start_date']))
		{
			$todate = $this -> _getToDaySearch($params['end_date']);
			if (!$todate)
			{
				$select -> where("false");
				return $select;
			}
			$select -> where("(logName.creation_date <= ?)", $todate);
		}
		if (!empty($params['start_date']) && !empty($params['end_date']))
		{
			$fromdate = $this -> _getFromDaySearch($params['start_date']);
			$todate = $this -> _getToDaySearch($params['end_date']);
			$select -> where(sprintf("$logName.creation_date between '$fromdate' and '$todate'"));
		}
		if (isset($params['order']) && !empty($params['order']))
		{
			$direction = ($params['direction'] != "") ? $params['direction'] : "DESC";
			$select -> order($params['order'] . " " . $direction);
		}
		else
		{
			$select -> order("$logName.creation_date DESC");
		}
		return $select;
	}

	private function _getFromDaySearch($day)
	{
		$day = $day . " 00:00:00";
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;

		}
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($user_tz);
		$start = strtotime($day);
		date_default_timezone_set($oldTz);
		$fromdate = date('Y-m-d H:i:s', $start);
		return $fromdate;
	}
	private function _getToDaySearch($day)
	{
		$day = $day . " 00:00:00";
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;
		}
		$oldTz = date_default_timezone_get();
		//user time zone
		date_default_timezone_set($user_tz);
		$d_temp = strtotime($day);
		if ($d_temp == false)
		{
			return null;
		}
		$toDateObject = new Zend_Date(strtotime($day));
		$toDateObject -> add('1', Zend_Date::DAY);
		$toDateObject -> sub('1', Zend_Date::SECOND);
		date_default_timezone_set($oldTz);
		$toDateObject -> setTimezone(date_default_timezone_get());
		return $todate = $toDateObject -> get('yyyy-MM-dd HH:mm:ss');
	}

	public function getRemaining($user, $type = 'send')
	{
		$max = Engine_Api::_()->authorization()->getPermission($user -> level_id, 'yncredit', 'max_'.$type);
		$period = Engine_Api::_()->authorization()->getPermission($user -> level_id, 'yncredit', 'period_'.$type);
		$logName = $this -> info('name');
		$typesTable = Engine_Api::_() -> getDbTable('types', 'yncredit');
		$typeName = $typesTable -> info('name');
		$select = $this->select()
			-> from($this->info('name'), new Zend_Db_Expr("SUM(credit)"))
			-> joinLeft($typeName, "$logName.type_id = $typeName.type_id", '')
			-> where('user_id = ?', $user->getIdentity())
			-> where('`group` = ?', $type);
			
		$oldTz = date_default_timezone_get();
		if($user -> getIdentity())
			date_default_timezone_set($user -> timezone);
		$date = date('Y-m-d H:i:s');
		date_default_timezone_set($oldTz);
		
		$time = strtotime($date);
		$sub = $time - time();
		switch ($period) {
			case 'day':
				$select ->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date)+ {$sub})) = YEAR('{$date}')")
		       			->where("MONTH(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date)+ {$sub})) = MONTH('{$date}')")
		       			->where("DAY(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date)+ {$sub})) = DAY('{$date}')");
				break;
			case 'week':
				$select ->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date) + {$sub})) = YEAR('{$date}')")   
       					->where("WEEK(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date) + {$sub})) = WEEK('{$date}')");
				break;
			case 'month':
				$select ->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date) + {$sub})) = YEAR('{$date}')")
       					->where("MONTH(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date) + {$sub})) = MONTH('{$date}')");
				break;
			case 'year':
				$select ->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP($logName.creation_date) + {$sub})) = YEAR('{$date}')");
				break;
		}
	
		$total = $select->query()->fetchColumn();
		return $max - abs($total);
	}
}